# ========================================
# Script: Backup Automático das Pastas Compartilhadas
# Projeto SSI/IP - InovaSoft
# Feito por: Eric Santos
# Data: 13/10/2025
# ========================================

# Esse script faz backup das pastas compartilhadas automaticamente
# É uma versão simplificada pra projeto acadêmico
# Numa empresa real usaria programas tipo Veeam ou Backup Exec

param(
    [string]$Origem = "C:\Compartilhados",
    [string]$Destino = "C:\Backups",
    [switch]$Full = $false,  # Se $true, faz backup full; senão, incremental
    [int]$RetencaoDias = 30
)

# ========================================
# Funções Auxiliares
# ========================================

function Write-Log {
    param([string]$Mensagem, [string]$Tipo = "INFO")
    
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $cor = switch ($Tipo) {
        "INFO" { "White" }
        "SUCESSO" { "Green" }
        "AVISO" { "Yellow" }
        "ERRO" { "Red" }
        default { "Gray" }
    }
    
    $prefixo = switch ($Tipo) {
        "INFO" { "ℹ️" }
        "SUCESSO" { "✅" }
        "AVISO" { "⚠️" }
        "ERRO" { "❌" }
        default { "•" }
    }
    
    Write-Host "[$timestamp] $prefixo $Mensagem" -ForegroundColor $cor
}

# ========================================
# Verificações Iniciais
# ========================================

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  BACKUP SIMULADO - InovaSoft" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Log "Verificando diretórios..."

# Verificar se origem existe
if (-not (Test-Path $Origem)) {
    Write-Log "Diretório de origem não encontrado: $Origem" "ERRO"
    Write-Log "Crie a pasta ou ajuste o parâmetro -Origem" "AVISO"
    exit 1
}

# Criar diretório de destino se não existir
if (-not (Test-Path $Destino)) {
    Write-Log "Criando diretório de destino: $Destino" "AVISO"
    New-Item -Path $Destino -ItemType Directory -Force | Out-Null
}

# ========================================
# Preparação do Backup
# ========================================

$timestamp = Get-Date -Format "yyyy-MM-dd_HHmmss"
$tipoBackup = if ($Full) { "Full" } else { "Incremental" }
$nomeBackup = "Backup_${tipoBackup}_$timestamp"
$pastaTemp = "$Destino\$nomeBackup"

Write-Log "Tipo de backup: $tipoBackup"
Write-Log "Origem: $Origem"
Write-Log "Destino temporário: $pastaTemp"

# ========================================
# Executar Backup
# ========================================

Write-Host "`n--- Iniciando Cópia de Arquivos ---`n" -ForegroundColor Yellow

try {
    # Criar pasta temporária
    New-Item -Path $pastaTemp -ItemType Directory -Force | Out-Null
    
    # Contar arquivos na origem
    $totalArquivos = (Get-ChildItem -Path $Origem -Recurse -File).Count
    Write-Log "Total de arquivos na origem: $totalArquivos"
    
    # Copiar arquivos
    if ($Full) {
        # Backup Full: copia tudo
        Write-Log "Copiando todos os arquivos..."
        Copy-Item -Path "$Origem\*" -Destination $pastaTemp -Recurse -Force
    } else {
        # Backup Incremental: apenas arquivos modificados nas últimas 24h
        Write-Log "Copiando arquivos modificados nas últimas 24 horas..."
        $dataLimite = (Get-Date).AddHours(-24)
        
        Get-ChildItem -Path $Origem -Recurse -File | Where-Object {
            $_.LastWriteTime -gt $dataLimite
        } | ForEach-Object {
            $destino = $_.FullName.Replace($Origem, $pastaTemp)
            $pastaDestino = Split-Path $destino
            
            if (-not (Test-Path $pastaDestino)) {
                New-Item -Path $pastaDestino -ItemType Directory -Force | Out-Null
            }
            
            Copy-Item -Path $_.FullName -Destination $destino -Force
        }
    }
    
    # Contar arquivos copiados
    $arquivosCopiados = (Get-ChildItem -Path $pastaTemp -Recurse -File).Count
    Write-Log "Arquivos copiados: $arquivosCopiados" "SUCESSO"
    
    # ========================================
    # Compactar Backup
    # ========================================
    
    Write-Host "`n--- Compactando Backup ---`n" -ForegroundColor Yellow
    
    $arquivoZip = "$Destino\$nomeBackup.zip"
    Write-Log "Gerando arquivo ZIP: $nomeBackup.zip"
    
    Compress-Archive -Path "$pastaTemp\*" -DestinationPath $arquivoZip -CompressionLevel Optimal -Force
    
    # Verificar tamanho do ZIP
    $tamanhoZip = (Get-Item $arquivoZip).Length / 1MB
    Write-Log "Backup compactado com sucesso!" "SUCESSO"
    Write-Log "Tamanho do arquivo: $([math]::Round($tamanhoZip, 2)) MB" "INFO"
    
    # Remover pasta temporária (manter apenas ZIP)
    Remove-Item -Path $pastaTemp -Recurse -Force
    Write-Log "Pasta temporária removida"
    
} catch {
    Write-Log "ERRO durante o backup: $($_.Exception.Message)" "ERRO"
    Write-Log "Stack Trace: $($_.ScriptStackTrace)" "ERRO"
    
    # Tentar limpar pasta temporária em caso de erro
    if (Test-Path $pastaTemp) {
        Remove-Item -Path $pastaTemp -Recurse -Force -ErrorAction SilentlyContinue
    }
    
    exit 1
}

# ========================================
# Limpeza de Backups Antigos
# ========================================

Write-Host "`n--- Limpeza de Backups Antigos ---`n" -ForegroundColor Yellow

$dataLimite = (Get-Date).AddDays(-$RetencaoDias)
Write-Log "Removendo backups anteriores a: $(Get-Date $dataLimite -Format 'dd/MM/yyyy')"

$backupsAntigos = Get-ChildItem -Path $Destino -Filter "Backup_*.zip" | Where-Object {
    $_.CreationTime -lt $dataLimite
}

if ($backupsAntigos.Count -gt 0) {
    foreach ($backup in $backupsAntigos) {
        Write-Log "Removendo: $($backup.Name)" "AVISO"
        Remove-Item $backup.FullName -Force
    }
    Write-Log "Total de backups removidos: $($backupsAntigos.Count)" "INFO"
} else {
    Write-Log "Nenhum backup antigo encontrado para remoção" "INFO"
}

# ========================================
# Relatório Final
# ========================================

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  RELATÓRIO DE BACKUP" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Log "Data/Hora: $(Get-Date -Format 'dd/MM/yyyy HH:mm:ss')"
Write-Log "Tipo: $tipoBackup"
Write-Log "Origem: $Origem"
Write-Log "Destino: $arquivoZip"
Write-Log "Arquivos copiados: $arquivosCopiados"
Write-Log "Tamanho final: $([math]::Round($tamanhoZip, 2)) MB"
Write-Log "Retenção: $RetencaoDias dias"

# Listar backups existentes
Write-Host "`n--- Backups Disponíveis ---`n" -ForegroundColor Yellow
Get-ChildItem -Path $Destino -Filter "Backup_*.zip" | Sort-Object CreationTime -Descending | Select-Object -First 10 | Format-Table Name, @{
    Name="Tamanho (MB)"; Expression={[math]::Round($_.Length / 1MB, 2)}
}, CreationTime -AutoSize

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Log "✅ Backup concluído com sucesso!" "SUCESSO"
Write-Host "========================================`n" -ForegroundColor Cyan

# ========================================
# Testar Integridade (Opcional)
# ========================================

Write-Host "Deseja testar a integridade do backup? (S/N): " -NoNewline -ForegroundColor Yellow
$resposta = Read-Host

if ($resposta -eq 'S' -or $resposta -eq 's') {
    Write-Host "`n--- Teste de Integridade ---`n" -ForegroundColor Yellow
    
    $pastaRestauracao = "$Destino\Teste_Restauracao_$timestamp"
    
    try {
        Write-Log "Extraindo backup para teste..."
        Expand-Archive -Path $arquivoZip -DestinationPath $pastaRestauracao -Force
        
        $arquivosRestaurados = (Get-ChildItem -Path $pastaRestauracao -Recurse -File).Count
        
        if ($arquivosRestaurados -eq $arquivosCopiados) {
            Write-Log "✅ TESTE APROVADO: Todos os arquivos foram restaurados corretamente" "SUCESSO"
        } else {
            Write-Log "⚠️ AVISO: Contagem de arquivos difere (Backup: $arquivosCopiados | Restaurado: $arquivosRestaurados)" "AVISO"
        }
        
        # Limpar pasta de teste
        Remove-Item -Path $pastaRestauracao -Recurse -Force
        Write-Log "Pasta de teste removida"
        
    } catch {
        Write-Log "Erro no teste de integridade: $($_.Exception.Message)" "ERRO"
    }
}

# ========================================
# Logging em Arquivo (Opcional)
# ========================================

$logFile = "$Destino\backup-log_$(Get-Date -Format 'yyyy-MM').txt"
$logEntry = @"
========================================
Backup Executado: $(Get-Date -Format 'dd/MM/yyyy HH:mm:ss')
Tipo: $tipoBackup
Arquivos: $arquivosCopiados
Tamanho: $([math]::Round($tamanhoZip, 2)) MB
Arquivo: $nomeBackup.zip
Status: SUCESSO
========================================

"@

Add-Content -Path $logFile -Value $logEntry

Write-Log "Log salvo em: $logFile" "INFO"

