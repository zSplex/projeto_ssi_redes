# Guia de Montagem no Cisco Packet Tracer
**Projeto SSI/IP - InovaSoft**

Este documento detalha o passo-a-passo para montar a topologia no Packet Tracer Student versão 8.x ou superior.

---

## 1. Preparação Inicial

### 1.1 Download e Instalação
- Baixar Packet Tracer Student em: https://www.netacad.com/
- Versão recomendada: 8.2 ou superior
- Criar conta Cisco NetAcad (gratuita para estudantes)

### 1.2 Criar Novo Projeto
1. Abrir Packet Tracer
2. File → New (ou Ctrl+N)
3. Salvar imediatamente como: `InovaSoft-SSI-v1.pkt`

---

## 2. Adicionar Equipamentos

### 2.1 Roteadores (3 unidades)

**Dispositivo:** Router 2911 (recomendado) ou 4331

**Como adicionar:**
1. Na barra inferior, clicar em **Network Devices** (ícone com roteador)
2. Selecionar **Routers**
3. Arrastar **2911** para a área de trabalho **3 vezes**
4. Renomear:
   - Duplo clique no dispositivo → aba **Config** → campo **Display Name**
   - Roteador 1: `RTR-MATRIZ`
   - Roteador 2: `RTR-FILIAL-A`
   - Roteador 3: `RTR-FILIAL-B`

**Módulos adicionais (se necessário):**
- Se o roteador não tiver interfaces Serial: desligar o roteador (botão power) e adicionar módulo HWIC-2T na aba **Physical**
- Ligar novamente após inserir módulo

### 2.2 Switches (3 unidades)

**Dispositivo:** Switch 2960-24TT

**Como adicionar:**
1. Network Devices → Switches → **2960-24TT**
2. Arrastar 3 vezes
3. Renomear:
   - `SW-MATRIZ`
   - `SW-FILIAL-A`
   - `SW-FILIAL-B`

### 2.3 Servidores (3 unidades)

**Dispositivo:** Server-PT

**Como adicionar:**
1. End Devices (ícone com computador) → **Server-PT**
2. Arrastar 3 vezes
3. Renomear:
   - `SRV-MATRIZ`
   - `SRV-FILIAL-A`
   - `SRV-FILIAL-B`

### 2.4 Estações de Trabalho

**Dispositivo:** PC-PT

**Quantidade:**
- Matriz: 6 PCs (representando os 20 reais, para não poluir o diagrama)
- Filial A: 4 PCs (representando 15)
- Filial B: 4 PCs (representando 15)

**Nomenclatura:**
- `PC-MATRIZ-01`, `PC-MATRIZ-02`, ...
- `PC-FILIALA-01`, `PC-FILIALA-02`, ...
- `PC-FILIALB-01`, `PC-FILIALB-02`, ...

---

## 3. Conectar Cabos

### 3.1 Tipos de Cabo

| Conexão | Tipo de Cabo | Cor (sugestão) |
|---------|--------------|----------------|
| Roteador ↔ Switch | Straight-Through (cobre) | Preto |
| Switch ↔ PC/Server | Straight-Through | Preto |
| Roteador ↔ Roteador (WAN) | Serial DCE/DTE | Vermelho |

**Atalho:** Selecionar o ícone do raio (Automatically Choose Connection Type) para PT escolher automaticamente.

### 3.2 Cabeamento da Matriz

| De | Interface | Para | Interface |
|----|-----------|------|-----------|
| RTR-MATRIZ | GigabitEthernet0/0 | SW-MATRIZ | GigabitEthernet0/1 |
| SW-MATRIZ | FastEthernet0/1 | SRV-MATRIZ | FastEthernet0 |
| SW-MATRIZ | FastEthernet0/2 | PC-MATRIZ-01 | FastEthernet0 |
| SW-MATRIZ | FastEthernet0/3 | PC-MATRIZ-02 | FastEthernet0 |
| ... | ... | ... | ... |

### 3.3 Cabeamento Filial A

| De | Interface | Para | Interface |
|----|-----------|------|-----------|
| RTR-FILIAL-A | GigabitEthernet0/0 | SW-FILIAL-A | GigabitEthernet0/1 |
| SW-FILIAL-A | FastEthernet0/1 | SRV-FILIAL-A | FastEthernet0 |
| SW-FILIAL-A | FastEthernet0/2 | PC-FILIALA-01 | FastEthernet0 |
| ... | ... | ... | ... |

### 3.4 Cabeamento Filial B

Similar à Filial A, adaptando nomes.

### 3.5 Links WAN (Inter-Roteadores)

**Opção 1: Serial (preferencial)**

| De | Interface | Para | Interface | Clock Rate |
|----|-----------|------|-----------|------------|
| RTR-MATRIZ | Serial0/0/0 (DCE) | RTR-FILIAL-A | Serial0/0/0 (DTE) | 128000 |
| RTR-MATRIZ | Serial0/0/1 (DCE) | RTR-FILIAL-B | Serial0/0/0 (DTE) | 128000 |

**Como saber qual ponta é DCE:**
- Ao conectar o cabo Serial, PT mostra um "relógio" na ponta DCE
- DCE é responsável por gerar clock (configurar `clock rate`)

**Opção 2: Ethernet (se Serial não disponível)**

| De | Interface | Para | Interface |
|----|-----------|------|-----------|
| RTR-MATRIZ | GigabitEthernet0/1 | RTR-FILIAL-A | GigabitEthernet0/1 |
| RTR-MATRIZ | GigabitEthernet0/2 | RTR-FILIAL-B | GigabitEthernet0/1 |

**Tipo de cabo:** Cross-over (cabo laranja no PT)

---

## 4. Configurar Endereços IP

### 4.1 Configurar Roteadores via CLI

1. Clicar no roteador
2. Aba **CLI**
3. Aguardar boot (pode pressionar Enter para pular perguntas iniciais)
4. Copiar e colar os comandos do arquivo `comandos-roteadores.md`

**Dica:** No Packet Tracer, é possível copiar (Ctrl+C) comandos de um editor de texto e colar (Ctrl+V ou botão direito → Paste) no CLI.

### 4.2 Configurar Servidores

#### SRV-MATRIZ (192.168.10.10)

1. Clicar em SRV-MATRIZ → aba **Config**
2. **FastEthernet0:**
   - IP Address: `192.168.10.10`
   - Subnet Mask: `255.255.255.0`
   - Default Gateway: `192.168.10.1`
3. **DNS:**
   - DNS Server: `127.0.0.1` (próprio servidor)
4. Aba **Services:**
   - **DHCP:** ON (configurar escopos — ver seção 5)
   - **DNS:** ON (configurar zona — ver seção 6)
   - **HTTP:** ON (para ProjectHub)

#### SRV-FILIAL-A (192.168.20.10)

1. Configurar IP: `192.168.20.10`
2. Gateway: `192.168.20.1`
3. DNS: `192.168.10.10` (servidor matriz)

#### SRV-FILIAL-B (192.168.30.10)

1. Configurar IP: `192.168.30.10`
2. Gateway: `192.168.30.1`
3. DNS: `192.168.10.10`

### 4.3 Configurar PCs (DHCP)

1. Clicar no PC → aba **Config** → **FastEthernet0**
2. Marcar **DHCP** (em vez de Static)
3. Após configurar servidor DHCP, o PC receberá IP automaticamente

**Alternativa para testes iniciais (IP estático temporário):**
- PC-MATRIZ-01: `192.168.10.50`, GW `192.168.10.1`
- Depois trocar para DHCP

---

## 5. Configurar DHCP no SRV-MATRIZ

1. Clicar em SRV-MATRIZ → aba **Services** → **DHCP**
2. Ativar serviço: **Service: ON**

### Pool 1: Matriz

- **Pool Name:** `Pool-Matriz`
- **Default Gateway:** `192.168.10.1`
- **DNS Server:** `192.168.10.10`
- **Start IP Address:** `192.168.10.50`
- **Subnet Mask:** `255.255.255.0`
- **Max Number of Users:** `150`
- Clicar em **Add** para salvar

### Pool 2: Filial A

- **Pool Name:** `Pool-FilialA`
- **Default Gateway:** `192.168.20.1`
- **DNS Server:** `192.168.10.10`
- **Start IP Address:** `192.168.20.50`
- **Subnet Mask:** `255.255.255.0`
- **Max Number of Users:** `150`
- **Add**

### Pool 3: Filial B

- **Pool Name:** `Pool-FilialB`
- **Default Gateway:** `192.168.30.1`
- **DNS Server:** `192.168.10.10`
- **Start IP Address:** `192.168.30.50`
- **Subnet Mask:** `255.255.255.0`
- **Max Number of Users:** `150`
- **Add**

**Observação:** Para DHCP funcionar nas filiais, os roteadores precisam de `ip helper-address` (já incluído em `comandos-roteadores.md`).

---

## 6. Configurar DNS no SRV-MATRIZ

1. SRV-MATRIZ → aba **Services** → **DNS**
2. Ativar: **DNS Service: ON**

### Adicionar Registros

| Name | Type | Address | Ação |
|------|------|---------|------|
| inovasoft.local | A | 192.168.10.10 | Add |
| srv-matriz.inovasoft.local | A | 192.168.10.10 | Add |
| srv-filiala.inovasoft.local | A | 192.168.20.10 | Add |
| srv-filialb.inovasoft.local | A | 192.168.30.10 | Add |
| projecthub.inovasoft.local | A | 192.168.10.10 | Add |

**Nota:** Packet Tracer não suporta CNAME nativamente na interface gráfica. Para simplificar, usaremos registros A duplicados.

---

## 7. Testar Conectividade

### 7.1 Teste de Ping entre Roteadores

1. RTR-MATRIZ → CLI
2. Executar:
```
ping 10.0.1.2    ! RTR-FILIAL-A
ping 10.0.2.2    ! RTR-FILIAL-B
```
**Resultado esperado:** 5/5 pacotes com sucesso (!!!!!).

### 7.2 Teste de Ping entre Redes

1. PC-MATRIZ-01 → **Desktop** → **Command Prompt**
2. Executar:
```
ipconfig         ! Verificar IP obtido via DHCP
ping 192.168.10.1   ! Gateway
ping 192.168.10.10  ! Servidor
ping 192.168.20.10  ! Servidor Filial A
ping 192.168.30.10  ! Servidor Filial B
```

### 7.3 Teste de DNS

1. PC-MATRIZ-01 → **Desktop** → **Command Prompt**
2. Executar:
```
nslookup srv-matriz.inovasoft.local
! Deve retornar: 192.168.10.10
```

### 7.4 Teste de DHCP

1. PC-FILIALA-01 → **Desktop** → **IP Configuration**
2. Verificar se IP está na faixa `192.168.20.50 - 192.168.20.200`
3. Clicar em **DHCP** para renovar, ou **Static** e voltar para **DHCP**

---

## 8. Troubleshooting Visual

### Problema: Cabo vermelho com "X"

**Causa:** Interface desligada ou incompatibilidade de cabo.

**Solução:**
- Verificar `no shutdown` nos roteadores
- Conferir tipo de cabo (usar Automatically Choose)

### Problema: Cabo laranja (âmbar)

**Causa:** Interface em processo de ativação (normal nos primeiros segundos).

**Solução:** Aguardar 10-30 segundos. Se persistir, verificar configuração da interface.

### Problema: PC não recebe IP via DHCP

**Diagnóstico:**
- Verificar se serviço DHCP está ON no servidor
- Testar ping do PC para o gateway (`ping 192.168.20.1`)
- Verificar se roteador tem `ip helper-address` configurado

**Solução:**
- Adicionar helper-address no roteador
- Aguardar 10 segundos e clicar em DHCP novamente no PC

### Problema: DNS não resolve nomes

**Diagnóstico:**
- Verificar se PC está configurado com DNS correto (`ipconfig /all`)
- Testar ping direto para IP do servidor DNS (`ping 192.168.10.10`)

**Solução:**
- Corrigir DNS no escopo DHCP
- Renovar IP do PC

---

## 9. Salvar e Documentar

### 9.1 Salvar Versões

- **v1:** Topologia básica com cabos
- **v2:** IPs configurados, roteamento OK
- **v3:** DHCP e DNS funcionando
- **v4:** Versão final com testes validados

File → Save As → `InovaSoft-SSI-v4-final.pkt`

### 9.2 Capturar Screenshots

1. Usar ferramenta de captura do Windows (Win+Shift+S)
2. Screenshots necessários:
   - Visão geral da topologia (Logical Workspace)
   - `show ip route` de cada roteador
   - `ipconfig` de PCs de cada site
   - `nslookup` funcionando
   - DHCP pools configurados

Salvar em: `docs/screenshots/`

### 9.3 Adicionar Anotações no PT

1. Clicar em **Place Note** (ícone de "T" na barra lateral)
2. Adicionar notas explicativas:
   - "Rede Matriz 192.168.10.0/24"
   - "Link WAN 10.0.1.0/30"
   - "DHCP Relay configurado"

---

## 10. Checklist Final

- [ ] 3 roteadores configurados e salvos (`write memory`)
- [ ] 3 switches conectados corretamente
- [ ] 3 servidores com IPs estáticos
- [ ] PCs recebendo IP via DHCP em todas as redes
- [ ] Ping entre todos os sites funciona
- [ ] DNS resolvendo nomes da zona inovasoft.local
- [ ] Screenshots capturados
- [ ] Arquivo .pkt salvo e versionado
- [ ] Documentação dos comandos em `comandos-roteadores.md` atualizada

---

## 11. Dicas de Performance

- **Não adicionar mais de 10 PCs por rede** no Packet Tracer (para não travar)
- **Desabilitar animações:** Options → Preferences → Misc → desmarcar "Show Link Lights"
- **Salvar frequentemente:** Ctrl+S a cada alteração importante
- **Fechar programas pesados** enquanto usa PT (Chrome, etc.)

---

## 12. Limitações do Packet Tracer

Recursos **não suportados** na versão Student:

- ❌ VPNs IPSec site-to-site
- ❌ NAT overload em alguns modelos
- ❌ AAA com RADIUS/TACACS+
- ❌ VLANs privadas (PVLAN)
- ❌ Spanning Tree avançado (RSTP, MST)
- ❌ Registros DNS tipo CNAME (usar A record duplicado)

Recursos **suportados** e utilizados no projeto:

- ✅ Roteamento estático/RIP/EIGRP/OSPF básico
- ✅ DHCP Server e Relay
- ✅ DNS com registros A
- ✅ ACLs padrão e estendidas
- ✅ NAT estático/dinâmico simples
- ✅ HTTP/HTTPS básico

---

**Versão:** 1.2  
**Última atualização:** 22/09/2025  
**Responsável:** João Pedro Vianna

