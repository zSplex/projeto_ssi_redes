# Guia de Configuração DNS - Windows Server
**Projeto SSI/IP - InovaSoft**

Este guia detalha a configuração do serviço DNS no Windows Server para criar a zona `inovasoft.local`.

---

## 1. Pré-requisitos

- Windows Server 2019/2022
- IP estático: `192.168.10.10`
- Função DNS instalada (geralmente instalada junto com DHCP)

---

## 2. Instalar Função DNS

### 2.1 Via Server Manager

1. **Server Manager** → **Manage** → **Add Roles and Features**
2. **Server Roles:** Marcar **DNS Server**
3. **Add Features** → Next → Install
4. Aguardar conclusão

### 2.2 Via PowerShell

```powershell
Install-WindowsFeature -Name DNS -IncludeManagementTools
```

---

## 3. Abrir Console DNS

1. **Server Manager** → **Tools** → **DNS**
2. Ou executar: `dnsmgmt.msc`
3. Expandir o servidor (SRV-MATRIZ)

---

## 4. Criar Zona de Pesquisa Direta (Forward Lookup Zone)

### 4.1 Wizard de Criação

1. DNS Console → Expandir **SRV-MATRIZ** → Clicar com botão direito em **Forward Lookup Zones** → **New Zone**

2. **Zone Type:**
   - Selecionar: **Primary zone**
   - *(Sem Active Directory, pois estamos usando servidor standalone)*
   - Next

3. **Zone Name:**
   - Name: `inovasoft.local`
   - Next

4. **Zone File:**
   - Criar novo arquivo: `inovasoft.local.dns`
   - Next

5. **Dynamic Update:**
   - Selecionar: **Do not allow dynamic updates** (para ambiente controlado)
   - *(Em produção com AD, usaríamos "Allow only secure dynamic updates")*
   - Next

6. Finish

### 4.2 Resultado

A zona `inovasoft.local` aparece na lista de Forward Lookup Zones.

---

## 5. Adicionar Registros DNS (Tipo A)

### 5.1 Registro para o próprio servidor (srv-matriz)

1. Expandir **Forward Lookup Zones** → **inovasoft.local**
2. Botão direito na zona → **New Host (A or AAAA)**
3. **Name:** `srv-matriz`
4. **IP Address:** `192.168.10.10`
5. ✅ **Create associated pointer (PTR) record** (marcar)
6. **Add Host**
7. OK → Done

### 5.2 Registro para domínio raiz (inovasoft.local)

1. Botão direito na zona → **New Host (A or AAAA)**
2. **Name:** (deixar vazio para criar registro @ = raiz)
3. **IP Address:** `192.168.10.10`
4. Add Host → Done

### 5.3 Registros para servidores das filiais

**SRV-FILIAL-A:**
- Name: `srv-filiala`
- IP: `192.168.20.10`
- Add Host

**SRV-FILIAL-B:**
- Name: `srv-filialb`
- IP: `192.168.30.10`
- Add Host

### 5.4 Registro para aplicação web (ProjectHub)

- Name: `projecthub`
- IP: `192.168.10.10` (aponta para servidor da matriz)
- Add Host

### 5.5 Alias www

- Name: `www`
- IP: `192.168.10.10`
- Add Host

### 5.6 Alias para compartilhamento de arquivos

- Name: `arquivos`
- IP: `192.168.10.10`
- Add Host

---

## 6. Criar Registros CNAME (Alias)

CNAME permite criar apelidos para registros A existentes.

### Exemplo: www como alias

1. Botão direito na zona → **New Alias (CNAME)**
2. **Alias name:** `www2`
3. **Fully qualified domain name (FQDN) for target host:** `srv-matriz.inovasoft.local`
4. OK

**Observação:** No nosso caso, já criamos `www` como registro A diretamente, mas CNAME seria uma alternativa.

---

## 7. Criar Zona de Pesquisa Reversa (Reverse Lookup Zone)

Zona reversa permite consultas PTR (IP → nome).

### 7.1 Wizard de Criação

1. Botão direito em **Reverse Lookup Zones** → **New Zone**
2. **Zone Type:** Primary zone → Next
3. **Reverse Lookup Zone Name:**
   - IPv4 Reverse Lookup Zone → Next
   - **Network ID:** `192.168.10` (apenas os 3 primeiros octetos)
   - Next
4. **Zone File:** `10.168.192.in-addr.arpa.dns` (gerado automaticamente) → Next
5. **Dynamic Update:** Do not allow → Next
6. Finish

### 7.2 Adicionar Registro PTR Manual (se não criado automaticamente)

1. Expandir **Reverse Lookup Zones** → **10.168.192.in-addr.arpa**
2. Botão direito → **New Pointer (PTR)**
3. **Host IP Address:** `192.168.10.10`
4. **Host name:** `srv-matriz.inovasoft.local.`
5. OK

### 7.3 Zonas reversas para filiais (opcional)

Repetir processo para:
- Rede 192.168.20.0: zona `20.168.192.in-addr.arpa`
- Rede 192.168.30.0: zona `30.168.192.in-addr.arpa`

---

## 8. Configurar Forwarders (DNS Recursivo)

Forwarders encaminham consultas de domínios externos (ex: google.com) para DNS públicos.

1. DNS Console → Botão direito em **SRV-MATRIZ** → **Properties**
2. Aba **Forwarders**
3. **Edit** → **Add:**
   - `8.8.8.8` (Google DNS)
   - `8.8.4.4` (Google DNS secundário)
   - `1.1.1.1` (Cloudflare, opcional)
4. OK → Apply → OK

**Efeito:** Consultas para domínios fora de `inovasoft.local` serão encaminhadas para 8.8.8.8.

---

## 9. Configurar Root Hints (Dicas de Raiz)

Root hints são usados para resolução recursiva. Geralmente já vêm pré-configurados.

1. DNS Console → **SRV-MATRIZ** → **Root Hints** (aba nas Properties)
2. Verificar se há servidores raiz listados (ex: a.root-servers.net)
3. Se vazio, clicar em **Add** e adicionar:
   - `198.41.0.4` (a.root-servers.net)
   - `199.9.14.201` (b.root-servers.net)

**Obs:** Para rede interna sem internet, root hints não são críticos se usamos apenas forwarders.

---

## 10. Testar Resolução DNS

### 10.1 Teste Local (no próprio servidor)

Abrir **PowerShell** e executar:

```powershell
# Testar zona local
Resolve-DnsName srv-matriz.inovasoft.local
# Resultado esperado: 192.168.10.10

Resolve-DnsName projecthub.inovasoft.local
# Resultado esperado: 192.168.10.10

Resolve-DnsName srv-filiala.inovasoft.local
# Resultado esperado: 192.168.20.10

# Testar zona reversa
Resolve-DnsName 192.168.10.10
# Resultado esperado: srv-matriz.inovasoft.local

# Testar forwarder (internet)
Resolve-DnsName google.com
# Resultado esperado: IPs públicos do Google
```

### 10.2 Teste de Cliente Windows

Em um PC da rede (192.168.10.x):

1. Configurar DNS do cliente para: `192.168.10.10`
2. Abrir **cmd** ou **PowerShell**
3. Executar:

```cmd
nslookup srv-matriz.inovasoft.local
# Server: srv-matriz.inovasoft.local
# Address: 192.168.10.10
# Name: srv-matriz.inovasoft.local
# Address: 192.168.10.10

nslookup projecthub.inovasoft.local

ping projecthub.inovasoft.local
# Deve pingar 192.168.10.10

nslookup www.google.com
# Deve resolver via forwarder (8.8.8.8)
```

---

## 11. Logs e Monitoramento

### 11.1 Habilitar Debug Logging (apenas para troubleshooting)

1. DNS Console → Botão direito em **SRV-MATRIZ** → **Properties**
2. Aba **Debug Logging**
3. Marcar:
   - **Log packets for debugging**
   - **Queries/Transfers** → **Queries**
   - **Direction of packets** → **Incoming** e **Outgoing**
4. **File path:** `C:\DNSDebug.log`
5. Apply → OK

**⚠️ Atenção:** Debug logging consome espaço em disco. Habilitar apenas temporariamente.

### 11.2 Event Viewer

- **Windows Logs** → **Application**
- Filtrar por **Source: DNS-Server-Service**
- Eventos importantes: erros de zona, falhas de query

---

## 12. Troubleshooting

### Problema: Cliente não resolve nomes da zona local

**Diagnóstico:**
```cmd
ipconfig /all
# Verificar se DNS Server está configurado como 192.168.10.10

nslookup
> server 192.168.10.10
> srv-matriz.inovasoft.local
```

**Solução:**
- Corrigir configuração DNS do cliente (via DHCP ou manual)
- Limpar cache DNS: `ipconfig /flushdns`
- No servidor, verificar se zona está carregada: `Get-DnsServerZone`

### Problema: Consultas externas não funcionam (ex: google.com)

**Diagnóstico:**
- Testar ping para 8.8.8.8 (conectividade com forwarder)
- Verificar forwarders configurados:
  ```powershell
  Get-DnsServerForwarder
  ```

**Solução:**
- Adicionar forwarders manualmente (seção 8)
- Verificar firewall permitindo porta 53 UDP/TCP

### Problema: Zona reversa não funciona

**Sintoma:** `nslookup 192.168.10.10` retorna erro.

**Solução:**
- Criar zona reversa (seção 7)
- Adicionar registros PTR
- Marcar checkbox "Create PTR" ao adicionar novos hosts

---

## 13. Script PowerShell de Configuração Automatizada

```powershell
# Script: Criar-Zona-DNS.ps1
# Executar como Administrador no SRV-MATRIZ

# Criar zona primária
Add-DnsServerPrimaryZone -Name "inovasoft.local" -ZoneFile "inovasoft.local.dns"

# Adicionar registros A
Add-DnsServerResourceRecordA -Name "srv-matriz" -ZoneName "inovasoft.local" -IPv4Address "192.168.10.10" -CreatePtr
Add-DnsServerResourceRecordA -Name "srv-filiala" -ZoneName "inovasoft.local" -IPv4Address "192.168.20.10"
Add-DnsServerResourceRecordA -Name "srv-filialb" -ZoneName "inovasoft.local" -IPv4Address "192.168.30.10"
Add-DnsServerResourceRecordA -Name "projecthub" -ZoneName "inovasoft.local" -IPv4Address "192.168.10.10"
Add-DnsServerResourceRecordA -Name "www" -ZoneName "inovasoft.local" -IPv4Address "192.168.10.10"
Add-DnsServerResourceRecordA -Name "arquivos" -ZoneName "inovasoft.local" -IPv4Address "192.168.10.10"

# Registro para domínio raiz (@)
Add-DnsServerResourceRecordA -Name "@" -ZoneName "inovasoft.local" -IPv4Address "192.168.10.10"

# Criar zona reversa
Add-DnsServerPrimaryZone -NetworkId "192.168.10.0/24" -ZoneFile "10.168.192.in-addr.arpa.dns"

# Adicionar forwarders
Add-DnsServerForwarder -IPAddress "8.8.8.8","8.8.4.4"

Write-Host "Zona DNS inovasoft.local criada com sucesso!" -ForegroundColor Green

# Testar resolução
Resolve-DnsName srv-matriz.inovasoft.local
Resolve-DnsName projecthub.inovasoft.local
```

**Uso:**
```powershell
.\Criar-Zona-DNS.ps1
```

---

## 14. Resumo de Registros DNS Criados

| Nome (FQDN) | Tipo | Endereço IP | Descrição |
|-------------|------|-------------|-----------|
| inovasoft.local | A | 192.168.10.10 | Domínio raiz |
| srv-matriz.inovasoft.local | A | 192.168.10.10 | Servidor matriz |
| srv-filiala.inovasoft.local | A | 192.168.20.10 | Servidor filial A |
| srv-filialb.inovasoft.local | A | 192.168.30.10 | Servidor filial B |
| projecthub.inovasoft.local | A | 192.168.10.10 | Aplicação web |
| www.inovasoft.local | A | 192.168.10.10 | Web (alias) |
| arquivos.inovasoft.local | A | 192.168.10.10 | Compartilhamento |

**Zona Reversa:**
- 10.168.192.in-addr.arpa (PTR para 192.168.10.10 → srv-matriz.inovasoft.local)

---

## 15. Checklist de Configuração

- [ ] Função DNS instalada
- [ ] Zona `inovasoft.local` criada (Forward Lookup)
- [ ] Mínimo 7 registros A adicionados
- [ ] Zona reversa `10.168.192.in-addr.arpa` criada
- [ ] Forwarders configurados (8.8.8.8, 8.8.4.4)
- [ ] Teste local de resolução bem-sucedido (`Resolve-DnsName`)
- [ ] Teste de cliente externo bem-sucedido (`nslookup` de PC)
- [ ] Consultas externas funcionando (google.com)

---

**Versão:** 1.0  
**Última atualização:** 04/10/2025  
**Responsável:** Eric Santos

