# Guia de Configuração DHCP - Windows Server
**Projeto SSI/IP - InovaSoft**

Este guia detalha a configuração do serviço DHCP no Windows Server 2019/2022 para o servidor SRV-MATRIZ.

---

## 1. Pré-requisitos

- Windows Server 2019 ou 2022 instalado
- IP estático configurado: `192.168.10.10/24`
- Gateway: `192.168.10.1`
- Acesso administrativo ao servidor

---

## 2. Instalar Função DHCP

### 2.1 Via Server Manager (GUI)

1. Abrir **Server Manager**
2. Clicar em **Manage** → **Add Roles and Features**
3. **Before You Begin:** Next
4. **Installation Type:** Role-based or feature-based installation → Next
5. **Server Selection:** Selecionar servidor local → Next
6. **Server Roles:** Marcar **DHCP Server** → Next
7. **Features:** Next (manter padrão)
8. **DHCP Server:** Next
9. **Confirmation:** Install
10. Aguardar instalação (1-3 minutos)
11. Clicar em **Complete DHCP configuration** (link amarelo no topo)

### 2.2 Via PowerShell (alternativa rápida)

```powershell
# Executar como Administrador
Install-WindowsFeature -Name DHCP -IncludeManagementTools
```

---

## 3. Configuração Pós-Instalação

1. No wizard "DHCP Post-Install configuration":
   - **Description:** Deixar em branco ou adicionar nota
   - **Authorization:** Usar credenciais do administrador local
   - Commit → Close

2. Verificar autorização:
   - Abrir **DHCP Console** (dhcpmgmt.msc)
   - Servidor deve aparecer com seta verde (autorizado)

---

## 4. Criar Escopos

### 4.1 Escopo 1: Rede Matriz (192.168.10.0/24)

1. Abrir **DHCP Console** → Expandir servidor → Clicar com botão direito em **IPv4** → **New Scope**
2. **Wizard:**
   - **Name:** `Escopo-Matriz`
   - **Description:** `Pool DHCP para rede da matriz (Bauru)`
   - Next

3. **IP Address Range:**
   - Start IP: `192.168.10.50`
   - End IP: `192.168.10.200`
   - Length: `24` (ou Subnet mask: `255.255.255.0`)
   - Next

4. **Add Exclusions:**
   - Start: `192.168.10.1`
   - End: `192.168.10.49`
   - Add → Next
   - *(Exclui IPs reservados para roteador, servidores e impressoras)*

5. **Lease Duration:**
   - Days: `0`
   - Hours: `8`
   - Minutes: `0`
   - Next
   - *(8 horas é adequado para escritório; renova a cada 4h)*

6. **Configure DHCP Options:** Yes, I want to configure these options now → Next

7. **Router (Default Gateway):**
   - IP: `192.168.10.1`
   - Add → Next

8. **Domain Name and DNS Servers:**
   - Parent domain: `inovasoft.local`
   - Server name: `srv-matriz` ou IP: `192.168.10.10`
   - Add → Next

9. **WINS Servers:** Next (deixar vazio, não usamos WINS)

10. **Activate Scope:** Yes, I want to activate this scope now → Next

11. Finish

### 4.2 Escopo 2: Filial A (192.168.20.0/24)

Repetir processo com:

| Campo | Valor |
|-------|-------|
| Name | `Escopo-FilialA` |
| Description | `Pool DHCP para Filial A (São Paulo)` |
| Start IP | `192.168.20.50` |
| End IP | `192.168.20.200` |
| Subnet Mask | `255.255.255.0` |
| Exclusions | `192.168.20.1` a `192.168.20.49` |
| Lease | 8 horas |
| Gateway | `192.168.20.1` |
| DNS | `192.168.10.10` (primário), `192.168.20.10` (secundário opcional) |
| Domain | `inovasoft.local` |

### 4.3 Escopo 3: Filial B (192.168.30.0/24)

| Campo | Valor |
|-------|-------|
| Name | `Escopo-FilialB` |
| Description | `Pool DHCP para Filial B (Campinas)` |
| Start IP | `192.168.30.50` |
| End IP | `192.168.30.200` |
| Subnet Mask | `255.255.255.0` |
| Exclusions | `192.168.30.1` a `192.168.30.49` |
| Lease | 8 horas |
| Gateway | `192.168.30.1` |
| DNS | `192.168.10.10`, `192.168.30.10` |
| Domain | `inovasoft.local` |

---

## 5. Criar Reservas (Opcional)

Reservas garantem que dispositivos específicos sempre recebam o mesmo IP.

### Exemplo: Reservar IP para impressora da matriz

1. DHCP Console → IPv4 → **Escopo-Matriz** → Clicar com botão direito em **Reservations** → **New Reservation**
2. **Reservation Name:** `Impressora-Matriz-HP`
3. **IP Address:** `192.168.10.25`
4. **MAC Address:** `00-11-22-33-44-55` (obtido da impressora)
5. **Description:** `Impressora HP LaserJet Piso 1`
6. **Supported types:** Both (DHCP e BOOTP)
7. Add → Close

### Como descobrir MAC Address:
- **Windows:** `ipconfig /all` (Physical Address)
- **Linux:** `ip addr` ou `ifconfig` (ether)
- **Impressora:** Imprimir página de configuração

---

## 6. Configurar Opções Avançadas (Scope Options)

Para configurar opções adicionais em cada escopo:

1. DHCP Console → Escopo → **Scope Options** → Botão direito → **Configure Options**
2. Opções úteis:

| Código | Opção | Valor Sugerido | Descrição |
|--------|-------|----------------|-----------|
| 003 | Router | 192.168.10.1 | Gateway padrão |
| 006 | DNS Servers | 192.168.10.10 | Servidor DNS |
| 015 | DNS Domain Name | inovasoft.local | Domínio interno |
| 042 | NTP Servers | 192.168.10.10 | Servidor de tempo (opcional) |
| 044 | WINS/NBNS Servers | - | Deixar vazio |

---

## 7. Testar DHCP

### 7.1 Cliente Windows

1. Abrir **Prompt de Comando** (cmd) como Administrador
2. Executar:
```cmd
ipconfig /release
ipconfig /renew
ipconfig /all
```

**Resultado esperado:**
- IP na faixa do pool (ex: 192.168.10.50 - .200)
- Gateway correto
- DNS correto
- Sufixo DNS: `inovasoft.local`

### 7.2 Verificar no Servidor

1. DHCP Console → Escopo-Matriz → **Address Leases**
2. Deve aparecer o IP concedido, hostname do cliente e tempo de expiração

### 7.3 Logs de DHCP

Localização: `C:\Windows\System32\dhcp\`

Arquivos importantes:
- `DhcpSrvLog-Dom.log` (log do domingo)
- Eventos de atribuição/renovação/liberação

---

## 8. Troubleshooting

### Problema: Cliente não recebe IP (fica em 169.254.x.x)

**Causas possíveis:**
1. Servidor DHCP não está ativo
2. Escopo não está ativado
3. Firewall bloqueando portas 67/68 UDP
4. Roteador sem `ip helper-address` (para redes remotas)

**Diagnóstico:**
```powershell
# No servidor DHCP
Get-DhcpServerv4Scope
# Verificar se Status = Active

Get-DhcpServerv4Lease -ScopeId 192.168.10.0
# Ver leases concedidos

# Testar porta DHCP aberta
Test-NetConnection -ComputerName 192.168.10.10 -Port 67
```

**Solução:**
- Ativar escopo: Botão direito no escopo → **Activate**
- Desabilitar firewall temporariamente para testar
- Adicionar regra de firewall:
  ```powershell
  New-NetFirewallRule -DisplayName "DHCP Server" -Direction Inbound -Protocol UDP -LocalPort 67 -Action Allow
  ```

### Problema: Filiais não recebem IP do servidor central

**Causa:** Roteador não encaminha broadcasts DHCP.

**Solução:** Configurar DHCP Relay (ip helper-address) nos roteadores das filiais (já descrito em `comandos-roteadores.md`).

### Problema: Lease não renova automaticamente

**Diagnóstico:**
- Verificar tempo de lease configurado
- Lease renova automaticamente em 50% do tempo (ex: lease de 8h renova após 4h)

**Solução:**
- Ajustar tempo de lease se necessário
- Forçar renovação manual: `ipconfig /renew`

---

## 9. Backup da Configuração DHCP

### 9.1 Backup Manual

1. DHCP Console → Clicar com botão direito no servidor → **Backup**
2. Escolher pasta: `C:\DHCPBackup`
3. OK

### 9.2 Backup via PowerShell

```powershell
Backup-DhcpServer -Path "C:\DHCPBackup" -ComputerName SRV-MATRIZ
```

### 9.3 Restaurar Backup

```powershell
Restore-DhcpServer -Path "C:\DHCPBackup" -ComputerName SRV-MATRIZ
```

---

## 10. Script PowerShell de Criação Automatizada

Para replicar a configuração rapidamente:

```powershell
# Script: Criar-Escopos-DHCP.ps1
# Executar como Administrador

# Escopo Matriz
Add-DhcpServerv4Scope -Name "Escopo-Matriz" `
  -StartRange 192.168.10.50 `
  -EndRange 192.168.10.200 `
  -SubnetMask 255.255.255.0 `
  -Description "Pool para Matriz Bauru" `
  -State Active

Add-DhcpServerv4ExclusionRange -ScopeId 192.168.10.0 `
  -StartRange 192.168.10.1 `
  -EndRange 192.168.10.49

Set-DhcpServerv4OptionValue -ScopeId 192.168.10.0 `
  -Router 192.168.10.1 `
  -DnsServer 192.168.10.10 `
  -DnsDomain "inovasoft.local"

# Escopo Filial A
Add-DhcpServerv4Scope -Name "Escopo-FilialA" `
  -StartRange 192.168.20.50 `
  -EndRange 192.168.20.200 `
  -SubnetMask 255.255.255.0 `
  -Description "Pool para Filial A Sao Paulo" `
  -State Active

Add-DhcpServerv4ExclusionRange -ScopeId 192.168.20.0 `
  -StartRange 192.168.20.1 `
  -EndRange 192.168.20.49

Set-DhcpServerv4OptionValue -ScopeId 192.168.20.0 `
  -Router 192.168.20.1 `
  -DnsServer 192.168.10.10,192.168.20.10 `
  -DnsDomain "inovasoft.local"

# Escopo Filial B
Add-DhcpServerv4Scope -Name "Escopo-FilialB" `
  -StartRange 192.168.30.50 `
  -EndRange 192.168.30.200 `
  -SubnetMask 255.255.255.0 `
  -Description "Pool para Filial B Campinas" `
  -State Active

Add-DhcpServerv4ExclusionRange -ScopeId 192.168.30.0 `
  -StartRange 192.168.30.1 `
  -EndRange 192.168.30.49

Set-DhcpServerv4OptionValue -ScopeId 192.168.30.0 `
  -Router 192.168.30.1 `
  -DnsServer 192.168.10.10,192.168.30.10 `
  -DnsDomain "inovasoft.local"

Write-Host "Escopos DHCP criados com sucesso!" -ForegroundColor Green
```

**Uso:**
```powershell
.\Criar-Escopos-DHCP.ps1
```

---

## 11. Checklist de Configuração

- [ ] Função DHCP instalada e autorizada
- [ ] 3 escopos criados (Matriz, Filial A, Filial B)
- [ ] Exclusões configuradas (IPs 1-49 de cada rede)
- [ ] Gateway correto em cada escopo
- [ ] DNS primário configurado (192.168.10.10)
- [ ] Domínio DNS (inovasoft.local) definido
- [ ] Escopos ativados
- [ ] Teste de renovação bem-sucedido em cliente de cada rede
- [ ] Backup da configuração realizado

---

**Versão:** 1.0  
**Última atualização:** 04/10/2025  
**Responsável:** Eric Santos

