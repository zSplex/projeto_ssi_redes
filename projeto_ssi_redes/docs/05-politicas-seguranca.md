# Políticas de Segurança e Boas Práticas
**Projeto SSI/IP - InovaSoft Desenvolvimento de Sistemas**

## 1. Introdução

Este documento estabelece as políticas de segurança da informação da InovaSoft, alinhadas aos três pilares fundamentais:

- **Confidencialidade:** Garantir que apenas pessoas autorizadas acessem informações sensíveis
- **Integridade:** Assegurar que dados não sejam alterados ou corrompidos indevidamente
- **Disponibilidade:** Manter serviços e dados acessíveis quando necessário

As políticas aqui definidas são aplicáveis a todos os usuários, sistemas e dispositivos da rede corporativa.

---

## 2. Política de Senhas

### 2.1 Requisitos Obrigatórios

Todas as senhas de usuários da rede InovaSoft devem atender aos seguintes critérios:

| Requisito | Especificação | Justificativa |
|-----------|---------------|---------------|
| **Tamanho mínimo** | 8 caracteres | Dificulta ataques de força bruta |
| **Complexidade** | Mínimo 3 dos 4 tipos*: maiúsculas, minúsculas, números, símbolos | Aumenta entropia da senha |
| **Histórico** | Não pode repetir últimas 5 senhas | Evita reuso de senhas comprometidas |
| **Expiração** | 90 dias (3 meses) | Limita janela de comprometimento |
| **Tentativas de login** | Máximo 5 tentativas incorretas | Proteção contra brute force |
| **Bloqueio de conta** | 15 minutos após 5 tentativas | Tempo suficiente para inibir ataques automatizados |

\* Exemplos válidos: `Inovasoft@2025`, `P@ssw0rd!2024`, `C0mpl3x#Senha`

### 2.2 Senhas Proibidas

É **proibido** utilizar:
- Senhas padrão (`senha123`, `Password`, `admin`)
- Dados pessoais (nome, data de nascimento, CPF)
- Palavras do dicionário sem modificação
- Sequências simples (`12345678`, `abcdefgh`)
- Mesma senha em múltiplos sistemas

### 2.3 Armazenamento e Compartilhamento

- **NUNCA** anotar senhas em papel ou arquivos não criptografados
- **NUNCA** compartilhar senhas por e-mail, WhatsApp ou verbalmente
- Usar gerenciador de senhas quando necessário (ex: KeePass, Bitwarden)
- Em ambientes de teste/acadêmico: documentar senhas temporárias em local seguro (ex: cofre de equipe)

### 2.4 Implementação Técnica (Windows Server)

```powershell
# Configurar política de senha local
# (Em produção com AD, usar Group Policy)

# Tamanho mínimo: 8 caracteres
net accounts /minpwlen:8

# Expiração: 90 dias
net accounts /maxpwage:90

# Histórico: 5 senhas
# (Requer GPO em AD, não aplicável em contas locais standalone)

# Bloqueio após 5 tentativas por 15 minutos
net accounts /lockoutthreshold:5
net accounts /lockoutduration:15
net accounts /lockoutwindow:15
```

### 2.5 Senhas de Teste do Projeto

Para fins de demonstração acadêmica, os usuários de teste utilizam a senha padrão `Senha@123`.

**⚠️ IMPORTANTE:** Esta senha é apenas para ambiente controlado de laboratório. Em produção:
1. Gerar senhas individuais aleatórias
2. Forçar troca no primeiro logon
3. Implementar autenticação multifator (MFA) quando possível

---

## 3. Política de Contas de Usuário

### 3.1 Criação de Contas

- Contas são criadas **apenas** mediante solicitação formal (e-mail de gestor ou RH)
- Nome de usuário segue padrão: `nome.sobrenome` (ex: `joao.silva`)
- Cada usuário pertence a **no mínimo 1 grupo** de departamento
- Novo funcionário recebe credenciais em envelope lacrado ou e-mail criptografado

### 3.2 Contas Inativas

**Critério de inatividade:** Conta sem login por **60 dias consecutivos**.

**Procedimento:**
1. **Dia 50:** TI envia e-mail de aviso ao usuário e gestor
2. **Dia 60:** Conta é **desabilitada** (não excluída)
3. **Dia 90:** Após confirmação de RH, conta é **excluída**

**Script de Verificação Automática:**
```powershell
# Script: Verificar-Contas-Inativas.ps1
# Executa semanalmente via Task Scheduler

$dataLimite = (Get-Date).AddDays(-60)

Get-LocalUser | Where-Object {
    $_.Enabled -eq $true -and 
    $_.LastLogon -lt $dataLimite
} | ForEach-Object {
    Write-Host "⚠️ Conta inativa: $($_.Name) - Último login: $($_.LastLogon)" -ForegroundColor Yellow
    
    # Desabilitar conta (descomentar para aplicar)
    # Disable-LocalUser -Name $_.Name
    # Send-MailMessage -To "ti@inovasoft.local" -Subject "Conta desabilitada" -Body "Usuário $($_.Name) foi desabilitado por inatividade"
}
```

### 3.3 Desligamento de Funcionário

**Processo Imediato (dia do desligamento):**
1. RH notifica TI via e-mail com cópia para gestor
2. TI desabilita conta do usuário **imediatamente**
3. Alterar senhas de sistemas compartilhados se aplicável
4. Remover acesso físico (cartão, chaves)

**Backup de Dados:**
- Antes de excluir conta (após 30 dias), fazer backup de arquivos pessoais do usuário
- Transferir propriedade de arquivos compartilhados para gestor da área

### 3.4 Princípio do Menor Privilégio

- Usuários recebem **apenas** as permissões necessárias para suas funções
- Contas administrativas (grupo Administrators) são **restritas** à equipe de TI
- Usar conta padrão para tarefas cotidianas, admin apenas quando necessário

---

## 4. Política de Controle de Acesso

### 4.1 Classificação de Informações

| Nível | Descrição | Exemplos | Acesso |
|-------|-----------|----------|--------|
| **Público** | Informações sem restrição | Manuais, comunicados gerais | Todos (leitura) |
| **Interno** | Uso corporativo geral | Processos, templates | Funcionários (leitura) |
| **Confidencial** | Dados sensíveis de negócio | Contratos, folha de pagamento | Apenas departamento específico |
| **Restrito** | Altamente sensível | Senhas de sistemas, chaves de API | Apenas TI ou diretoria |

### 4.2 Controle de Acesso por Grupo

Implementado através de ACLs em pastas compartilhadas:

| Pasta | Classificação | Grupos com Acesso | Tipo de Permissão |
|-------|---------------|-------------------|-------------------|
| `\\SRV-MATRIZ\Publico` | Público | Todos | Leitura |
| `\\SRV-MATRIZ\Comercial` | Interno | GRP-Comercial, GRP-TI | Modificar, Leitura |
| `\\SRV-MATRIZ\RH` | Confidencial | GRP-RH | Modificar |
| `\\SRV-MATRIZ\Financeiro` | Confidencial | GRP-Financeiro | Modificar |
| `\\SRV-MATRIZ\TI` | Restrito | GRP-TI | Controle Total |

### 4.3 Auditoria de Acesso

- Logs de acesso a pastas confidenciais são registrados automaticamente pelo Windows
- Revisão mensal dos logs por responsável de TI
- Anomalias (acessos fora do horário, tentativas negadas repetidas) são investigadas

**Habilitar auditoria (GPO ou Segurança Avançada):**
```powershell
# Habilitar auditoria de acesso a objetos
auditpol /set /subcategory:"File System" /success:enable /failure:enable

# Configurar auditoria em pasta específica
$acl = Get-Acl "C:\Compartilhados\RH"
$auditRule = New-Object System.Security.AccessControl.FileSystemAuditRule("Everyone", "Read,Write,Delete", "ContainerInherit,ObjectInherit", "None", "Success,Failure")
$acl.AddAuditRule($auditRule)
Set-Acl "C:\Compartilhados\RH" $acl
```

---

## 5. Política de Backup

### 5.1 Estratégia de Backup (Regra 3-2-1)

**Ideal (Produção):**
- **3** cópias dos dados (original + 2 backups)
- **2** tipos de mídia diferentes (HD + nuvem, ou HD + fita)
- **1** cópia offsite (fora da empresa)

**Aplicação no Projeto (Simulado):**
- 1 cópia na pasta local `C:\Backups\`
- 1 cópia em drive externo simulado `D:\Backup-Externo\`
- Backup incremental diário + full semanal

### 5.2 Escopo do Backup

| Item | Frequência | Retenção | Responsável |
|------|------------|----------|-------------|
| Pastas compartilhadas (`C:\Compartilhados\`) | Diário (incremental) | 30 dias | TI |
| Configurações de servidores (DHCP, DNS, IIS) | Semanal (full) | 90 dias | TI |
| Banco de dados MySQL (ProjectHub) | Diário | 30 dias | TI |
| Arquivos de configuração de rede (.pkt, scripts) | Após cada mudança | 1 ano | TI |

### 5.3 Script de Backup Simulado

**Backup-Compartilhados.ps1:**
```powershell
# ========================================
# Script de Backup Simulado - InovaSoft
# Executar via Task Scheduler (diariamente 23h)
# ========================================

$origem = "C:\Compartilhados"
$destino = "C:\Backups"
$timestamp = Get-Date -Format "yyyy-MM-dd_HHmmss"
$pastaBackup = "$destino\Backup_$timestamp"

Write-Host "=== Iniciando Backup ===" -ForegroundColor Cyan
Write-Host "Origem: $origem" -ForegroundColor Gray
Write-Host "Destino: $pastaBackup`n" -ForegroundColor Gray

try {
    # Criar pasta de destino
    New-Item -Path $pastaBackup -ItemType Directory -Force | Out-Null
    
    # Copiar arquivos (simula backup incremental)
    Copy-Item -Path "$origem\*" -Destination $pastaBackup -Recurse -Force
    
    # Compactar backup (economizar espaço)
    $arquivoZip = "$destino\Backup_$timestamp.zip"
    Compress-Archive -Path $pastaBackup -DestinationPath $arquivoZip -Force
    
    # Remover pasta temporária (manter apenas ZIP)
    Remove-Item -Path $pastaBackup -Recurse -Force
    
    Write-Host "✅ Backup concluído com sucesso!" -ForegroundColor Green
    Write-Host "Arquivo: $arquivoZip" -ForegroundColor Gray
    Write-Host "Tamanho: $((Get-Item $arquivoZip).Length / 1MB) MB`n" -ForegroundColor Gray
    
    # Limpar backups antigos (manter apenas últimos 30 dias)
    $dataLimite = (Get-Date).AddDays(-30)
    Get-ChildItem -Path $destino -Filter "Backup_*.zip" | Where-Object {
        $_.CreationTime -lt $dataLimite
    } | ForEach-Object {
        Write-Host "🗑️ Removendo backup antigo: $($_.Name)" -ForegroundColor DarkYellow
        Remove-Item $_.FullName -Force
    }
    
} catch {
    Write-Host "❌ ERRO no backup: $($_.Exception.Message)" -ForegroundColor Red
    # Em produção: enviar e-mail de alerta para TI
}

Write-Host "=== Backup Finalizado ===" -ForegroundColor Cyan
```

### 5.4 Teste de Restauração

**Obrigatório:** Testar restauração de backup **mensalmente**.

**Procedimento:**
1. Escolher arquivo de backup aleatório
2. Extrair em pasta temporária `C:\Restore-Teste\`
3. Verificar integridade (abrir arquivos, conferir permissões)
4. Documentar resultado no log de testes

**Critério de Sucesso:** 100% dos arquivos recuperáveis e legíveis.

---

## 6. Política de Disponibilidade

### 6.1 Serviços Críticos e RTO/RPO

| Serviço | Criticidade | RTO* | RPO** | Plano de Contingência |
|---------|-------------|------|-------|------------------------|
| DHCP | Alta | 30 min | 0 | DHCP secundário nas filiais |
| DNS | Alta | 30 min | 0 | DNS cache nos servidores de filial |
| Compartilhamento de Arquivos | Alta | 2h | 24h | Restaurar do backup diário |
| ProjectHub (Web) | Média | 4h | 24h | Reinstalar app, restaurar BD |
| Roteamento inter-filial | Crítica | 1h | N/A | Configurar rotas manualmente se necessário |

\* **RTO (Recovery Time Objective):** Tempo máximo aceitável para restaurar o serviço  
\** **RPO (Recovery Point Objective):** Perda máxima de dados aceitável (tempo desde último backup)

### 6.2 Procedimento de Retomada de Serviços

**Cenário 1: Servidor DHCP da Matriz Fora do Ar**

1. **Detecção:** PCs não recebem IP (ficam em 169.254.x.x)
2. **Diagnóstico:** Ping para 192.168.10.10 falha
3. **Workaround:** Ativar DHCP nos servidores das filiais
   ```powershell
   # No SRV-FILIAL-A
   Install-WindowsFeature DHCP
   Add-DhcpServerv4Scope -Name "Escopo-Emergencia" -StartRange 192.168.20.100 -EndRange 192.168.20.150 -SubnetMask 255.255.255.0
   ```
4. **Recuperação:** Reiniciar servidor matriz ou migrar serviço para servidor backup
5. **Validação:** Renovar IP em PC de cada rede (`ipconfig /renew`)

**Cenário 2: Link WAN Matriz-Filial Cai**

1. **Detecção:** Filial não consegue acessar `\\SRV-MATRIZ\`
2. **Diagnóstico:** Traceroute para 192.168.10.10 falha no roteador
3. **Workaround:** Filial trabalha localmente, dados sincronizam após retorno
4. **Recuperação:** Contatar provedor WAN ou reconfigurar link
5. **Validação:** Ping entre redes bem-sucedido

### 6.3 Manutenção Preventiva

- **Janela de manutenção:** Domingos, 00h às 06h
- **Notificação prévia:** 72 horas de antecedência via e-mail para todos os usuários
- **Checklist de manutenção:**
  - [ ] Backup completo antes de iniciar
  - [ ] Aplicar atualizações de segurança do Windows
  - [ ] Verificar logs de eventos (erros críticos)
  - [ ] Testar conectividade de todos os serviços após reiniciar
  - [ ] Documentar alterações realizadas

---

## 7. Política de Atualizações e Patches

### 7.1 Classificação de Atualizações

| Tipo | Criticidade | Prazo para Aplicação | Exemplo |
|------|-------------|----------------------|---------|
| Crítico de Segurança | Urgente | 48h após lançamento | Patch para vulnerabilidade zero-day |
| Segurança Importante | Alta | 7 dias | Correção de falha conhecida |
| Atualização Regular | Média | 30 dias | Service Pack, feature update |
| Opcional/Preview | Baixa | Avaliação em ambiente de teste | Updates experimentais |

### 7.2 Processo de Atualização

1. **Teste em ambiente controlado** (se possível, em VM ou PC de teste)
2. **Backup completo** antes de aplicar
3. **Aplicar em horário de baixo uso** (madrugada/finais de semana)
4. **Validar funcionamento** após atualização
5. **Documentar** versão instalada e data

---

## 8. Política de Uso Aceitável

### 8.1 Uso Permitido

- Acesso a recursos da rede **exclusivamente** para atividades profissionais
- Uso pessoal mínimo de e-mail/internet (pausas, emergências) com bom senso

### 8.2 Uso Proibido

- ❌ Download de software não autorizado (cracks, pirataria)
- ❌ Acesso a conteúdo ilegal, ofensivo ou inadequado
- ❌ Compartilhar credenciais de acesso
- ❌ Conectar dispositivos não autorizados à rede (pendrives não verificados, HDs externos)
- ❌ Tentar burlar controles de segurança (desabilitar antivírus, usar proxies não autorizados)

### 8.3 Monitoramento e Consequências

- A empresa se reserva o direito de monitorar tráfego de rede e acesso a recursos
- Violações da política podem resultar em:
  - Advertência verbal/escrita
  - Suspensão de acesso
  - Demissão por justa causa (casos graves)

---

## 9. Responsabilidades

| Papel | Responsabilidades |
|-------|-------------------|
| **Equipe de TI** | Implementar políticas técnicas, gerenciar acessos, monitorar logs, realizar backups |
| **RH** | Notificar TI sobre admissões/desligamentos, auxiliar na conscientização de usuários |
| **Gestores de Área** | Aprovar solicitações de acesso, revisar permissões de sua equipe trimestralmente |
| **Todos os Usuários** | Seguir políticas, reportar incidentes de segurança, proteger suas credenciais |

---

## 10. Revisão da Política

- **Frequência:** Anual (ou após incidente de segurança significativo)
- **Responsável:** Coordenador de TI + RH
- **Aprovação:** Diretoria

**Última revisão:** Outubro/2025  
**Próxima revisão programada:** Outubro/2026

---

## 11. Checklist de Conformidade

Para verificar se a infraestrutura está em conformidade com as políticas:

- [ ] Senhas de todos os usuários atendem requisitos mínimos (8 caracteres, complexidade)
- [ ] Contas inativas (>60 dias) foram desabilitadas
- [ ] Permissões de pastas estão alinhadas com matriz de controle de acesso
- [ ] Backup automático está configurado e testado mensalmente
- [ ] Logs de auditoria estão habilitados para pastas confidenciais
- [ ] Serviços críticos têm plano de contingência documentado
- [ ] Usuários receberam treinamento sobre políticas (ou leram este documento)

---

**Versão:** 1.0  
**Data de Publicação:** 13/10/2025  
**Responsáveis:** Eric Santos (TI), Emilly Gonçalves (Documentação)  
**Aprovado por:** Equipe InovaSoft SSI/IP

