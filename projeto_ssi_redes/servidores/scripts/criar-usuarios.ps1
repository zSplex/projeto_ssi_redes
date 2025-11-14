# ========================================
# Script: Criar Usuários e Grupos 
# Projeto SSI/IP - InovaSoft
# Feito por: Eric Santos (com ajuda do João)
# Data: 06/10/2025
# ========================================

# IMPORTANTE: Tem que rodar como Administrador senão não funciona!
# Botão direito no PowerShell > Executar como Administrador

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " Criação de Usuários e Grupos - InovaSoft" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Verificar se está executando como Administrador
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
$isAdmin = $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "❌ ERRO: Este script precisa ser executado como Administrador!" -ForegroundColor Red
    Write-Host "Clique com botão direito no PowerShell e selecione 'Executar como Administrador'" -ForegroundColor Yellow
    exit 1
}

# ========================================
# PARTE 1: Criar Grupos Locais
# ========================================

Write-Host "[ETAPA 1/3] Criando grupos locais..." -ForegroundColor Yellow

$grupos = @(
    @{Nome="GRP-TI"; Descricao="Equipe de Tecnologia da Informação - Acesso administrativo"},
    @{Nome="GRP-RH"; Descricao="Departamento de Recursos Humanos - Dados sensíveis"},
    @{Nome="GRP-Financeiro"; Descricao="Departamento Financeiro - Dados confidenciais"},
    @{Nome="GRP-Comercial"; Descricao="Equipe Comercial e Vendas"},
    @{Nome="GRP-Publico"; Descricao="Todos os usuários - Acesso a documentos públicos"}
)

foreach ($grupo in $grupos) {
    try {
        $existente = Get-LocalGroup -Name $grupo.Nome -ErrorAction SilentlyContinue
        if ($existente) {
            Write-Host "  ⚠️  Grupo '$($grupo.Nome)' já existe, pulando..." -ForegroundColor DarkYellow
        } else {
            New-LocalGroup -Name $grupo.Nome -Description $grupo.Descricao | Out-Null
            Write-Host "  ✅ Grupo '$($grupo.Nome)' criado com sucesso" -ForegroundColor Green
        }
    } catch {
        Write-Host "  ❌ Erro ao criar grupo '$($grupo.Nome)': $($_.Exception.Message)" -ForegroundColor Red
    }
}

# ========================================
# PARTE 2: Criar Usuários
# ========================================

Write-Host "`n[ETAPA 2/3] Criando usuários..." -ForegroundColor Yellow

# Senha padrão (apenas para ambiente de testes/acadêmico)
# Em produção, gerar senhas individuais e forçar troca no primeiro logon
$senhaTexto = "Senha@123"
$senha = ConvertTo-SecureString $senhaTexto -AsPlainText -Force

$usuarios = @(
    @{
        Nome="eric.santos"
        NomeCompleto="Eric Santos"
        Descricao="Administrador de TI - Infraestrutura"
        Grupo="GRP-TI"
        Admin=$true
    },
    @{
        Nome="erik.doca"
        NomeCompleto="Erik Doca"
        Descricao="Analista Comercial - Vendas e Projetos"
        Grupo="GRP-Comercial"
        Admin=$false
    },
    @{
        Nome="emilly.goncalves"
        NomeCompleto="Emilly Gonçalves"
        Descricao="Analista de Recursos Humanos - Recrutamento"
        Grupo="GRP-RH"
        Admin=$false
    },
    @{
        Nome="joao.vianna"
        NomeCompleto="João Pedro Vianna"
        Descricao="Analista Financeiro - Contas a Pagar/Receber"
        Grupo="GRP-Financeiro"
        Admin=$false
    }
)

foreach ($usuario in $usuarios) {
    try {
        $existente = Get-LocalUser -Name $usuario.Nome -ErrorAction SilentlyContinue
        if ($existente) {
            Write-Host "  ⚠️  Usuário '$($usuario.Nome)' já existe, pulando criação..." -ForegroundColor DarkYellow
        } else {
            # Criar usuário
            New-LocalUser -Name $usuario.Nome `
                         -FullName $usuario.NomeCompleto `
                         -Description $usuario.Descricao `
                         -Password $senha `
                         -PasswordNeverExpires `
                         -AccountNeverExpires | Out-Null
            
            Write-Host "  ✅ Usuário '$($usuario.Nome)' criado" -ForegroundColor Green
        }
    } catch {
        Write-Host "  ❌ Erro ao criar usuário '$($usuario.Nome)': $($_.Exception.Message)" -ForegroundColor Red
        continue
    }
}

# ========================================
# PARTE 3: Adicionar Usuários aos Grupos
# ========================================

Write-Host "`n[ETAPA 3/3] Adicionando usuários aos grupos..." -ForegroundColor Yellow

foreach ($usuario in $usuarios) {
    try {
        # Adicionar ao grupo principal do departamento
        $membro = Get-LocalGroupMember -Group $usuario.Grupo -Member $usuario.Nome -ErrorAction SilentlyContinue
        if (-not $membro) {
            Add-LocalGroupMember -Group $usuario.Grupo -Member $usuario.Nome
            Write-Host "  ✅ '$($usuario.Nome)' adicionado ao grupo '$($usuario.Grupo)'" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  '$($usuario.Nome)' já está no grupo '$($usuario.Grupo)'" -ForegroundColor DarkYellow
        }
        
        # Adicionar ao grupo GRP-Publico (todos têm acesso)
        $membroPublico = Get-LocalGroupMember -Group "GRP-Publico" -Member $usuario.Nome -ErrorAction SilentlyContinue
        if (-not $membroPublico) {
            Add-LocalGroupMember -Group "GRP-Publico" -Member $usuario.Nome
            Write-Host "  ✅ '$($usuario.Nome)' adicionado ao grupo 'GRP-Publico'" -ForegroundColor Green
        }
        
        # Se for admin, adicionar ao grupo Administrators
        if ($usuario.Admin) {
            $membroAdmin = Get-LocalGroupMember -Group "Administrators" -Member $usuario.Nome -ErrorAction SilentlyContinue
            if (-not $membroAdmin) {
                Add-LocalGroupMember -Group "Administrators" -Member $usuario.Nome
                Write-Host "  ✅ '$($usuario.Nome)' adicionado ao grupo 'Administrators'" -ForegroundColor Green
            }
        }
        
    } catch {
        Write-Host "  ❌ Erro ao adicionar '$($usuario.Nome)' aos grupos: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# ========================================
# PARTE 4: Verificação e Resumo
# ========================================

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host " Resumo da Configuração" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "📋 Grupos criados:" -ForegroundColor Cyan
Get-LocalGroup | Where-Object {$_.Name -like "GRP-*"} | Format-Table Name, Description -AutoSize

Write-Host "`n👥 Usuários criados:" -ForegroundColor Cyan
Get-LocalUser | Where-Object {$_.Name -in @("eric.santos", "erik.doca", "emilly.goncalves", "joao.vianna")} | Format-Table Name, FullName, Description, Enabled -AutoSize

Write-Host "`n🔐 Membros por Grupo:" -ForegroundColor Cyan

foreach ($grupo in $grupos) {
    Write-Host "`n  Grupo: $($grupo.Nome)" -ForegroundColor Yellow
    try {
        $membros = Get-LocalGroupMember -Group $grupo.Nome -ErrorAction SilentlyContinue
        if ($membros) {
            $membros | ForEach-Object { Write-Host "    - $($_.Name)" -ForegroundColor Gray }
        } else {
            Write-Host "    (Nenhum membro)" -ForegroundColor DarkGray
        }
    } catch {
        Write-Host "    (Erro ao listar membros)" -ForegroundColor Red
    }
}

# ========================================
# Credenciais de Teste
# ========================================

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host " Credenciais para Testes" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "⚠️  ATENÇÃO: Estas são credenciais temporárias para ambiente de TESTES." -ForegroundColor Yellow
Write-Host "Em produção, use senhas individuais fortes e exija troca no primeiro logon.`n" -ForegroundColor Yellow

Write-Host "Usuário: eric.santos         | Senha: $senhaTexto | Grupo: TI (Admin)" -ForegroundColor White
Write-Host "Usuário: erik.doca           | Senha: $senhaTexto | Grupo: Comercial" -ForegroundColor White
Write-Host "Usuário: emilly.goncalves    | Senha: $senhaTexto | Grupo: RH" -ForegroundColor White
Write-Host "Usuário: joao.vianna         | Senha: $senhaTexto | Grupo: Financeiro" -ForegroundColor White

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host " ✅ Script Concluído com Sucesso!" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Próximos passos:" -ForegroundColor Cyan
Write-Host "  1. Configurar compartilhamento de arquivos (executar Configurar-Compartilhamento-Completo.ps1)" -ForegroundColor Gray
Write-Host "  2. Testar login com os usuários criados" -ForegroundColor Gray
Write-Host "  3. Verificar acesso às pastas compartilhadas conforme matriz de permissões`n" -ForegroundColor Gray

