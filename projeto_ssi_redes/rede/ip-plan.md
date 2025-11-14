# Plano de Endereçamento IP
**Projeto SSI/IP - InovaSoft**

## 1. Resumo Executivo

| Site | Rede | Máscara | Gateway | DNS Primário | DNS Secundário |
|------|------|---------|---------|--------------|----------------|
| Matriz (Bauru) | 192.168.10.0/24 | 255.255.255.0 | 192.168.10.1 | 192.168.10.10 | 8.8.8.8 |
| Filial A (São Paulo) | 192.168.20.0/24 | 255.255.255.0 | 192.168.20.1 | 192.168.10.10 | 192.168.20.10 |
| Filial B (Campinas) | 192.168.30.0/24 | 255.255.255.0 | 192.168.30.1 | 192.168.10.10 | 192.168.30.10 |
| Link Matriz-FilialA | 10.0.1.0/30 | 255.255.255.252 | N/A | N/A | N/A |
| Link Matriz-FilialB | 10.0.2.0/30 | 255.255.255.252 | N/A | N/A | N/A |

## 2. Detalhamento por Site

### 2.1 Matriz (Bauru) - 192.168.10.0/24

| Faixa | Início | Fim | Uso | Método |
|-------|--------|-----|-----|--------|
| Rede | 192.168.10.0 | - | Identificador de rede | - |
| Gateway | 192.168.10.1 | - | Roteador (RTR-MATRIZ) | Estático |
| Servidores | 192.168.10.10 | 192.168.10.20 | SRV-MATRIZ, futuros servidores | Estático |
| Impressoras | 192.168.10.21 | 192.168.10.30 | Impressoras de rede (se houver) | Estático/Reserva |
| Reservado | 192.168.10.31 | 192.168.10.49 | Equipamentos de infraestrutura | - |
| **Pool DHCP** | 192.168.10.50 | 192.168.10.200 | Estações de trabalho | **DHCP** |
| Reservado Futuro | 192.168.10.201 | 192.168.10.254 | Expansão | - |
| Broadcast | 192.168.10.255 | - | Endereço de broadcast | - |

**Dispositivos Específicos:**

| Hostname | IP | Tipo | Descrição |
|----------|-----|------|-----------|
| RTR-MATRIZ | 192.168.10.1 | Roteador | Gateway padrão da matriz |
| SRV-MATRIZ | 192.168.10.10 | Servidor | DHCP, DNS, Arquivos, Web (ProjectHub) |
| PC-MATRIZ-01 a 20 | 192.168.10.50 - .69 | Estações | IPs atribuídos via DHCP |

---

### 2.2 Filial A (São Paulo) - 192.168.20.0/24

| Faixa | Início | Fim | Uso | Método |
|-------|--------|-----|-----|--------|
| Rede | 192.168.20.0 | - | Identificador de rede | - |
| Gateway | 192.168.20.1 | - | Roteador (RTR-FILIAL-A) | Estático |
| Servidores | 192.168.20.10 | 192.168.20.20 | SRV-FILIAL-A | Estático |
| Impressoras | 192.168.20.21 | 192.168.20.30 | Impressoras locais | Estático/Reserva |
| Reservado | 192.168.20.31 | 192.168.20.49 | Equipamentos de infraestrutura | - |
| **Pool DHCP** | 192.168.20.50 | 192.168.20.200 | Estações de trabalho | **DHCP** |
| Reservado Futuro | 192.168.20.201 | 192.168.20.254 | Expansão | - |
| Broadcast | 192.168.20.255 | - | Endereço de broadcast | - |

**Dispositivos Específicos:**

| Hostname | IP | Tipo | Descrição |
|----------|-----|------|-----------|
| RTR-FILIAL-A | 192.168.20.1 | Roteador | Gateway padrão da filial A |
| SRV-FILIAL-A | 192.168.20.10 | Servidor | DHCP secundário, DNS cache |
| PC-FILIALA-01 a 15 | 192.168.20.50 - .64 | Estações | IPs atribuídos via DHCP |

---

### 2.3 Filial B (Campinas) - 192.168.30.0/24

| Faixa | Início | Fim | Uso | Método |
|-------|--------|-----|-----|--------|
| Rede | 192.168.30.0 | - | Identificador de rede | - |
| Gateway | 192.168.30.1 | - | Roteador (RTR-FILIAL-B) | Estático |
| Servidores | 192.168.30.10 | 192.168.30.20 | SRV-FILIAL-B | Estático |
| Impressoras | 192.168.30.21 | 192.168.30.30 | Impressoras locais | Estático/Reserva |
| Reservado | 192.168.30.31 | 192.168.30.49 | Equipamentos de infraestrutura | - |
| **Pool DHCP** | 192.168.30.50 | 192.168.30.200 | Estações de trabalho | **DHCP** |
| Reservado Futuro | 192.168.30.201 | 192.168.30.254 | Expansão | - |
| Broadcast | 192.168.30.255 | - | Endereço de broadcast | - |

**Dispositivos Específicos:**

| Hostname | IP | Tipo | Descrição |
|----------|-----|------|-----------|
| RTR-FILIAL-B | 192.168.30.1 | Roteador | Gateway padrão da filial B |
| SRV-FILIAL-B | 192.168.30.10 | Servidor | DHCP secundário, DNS cache |
| PC-FILIALB-01 a 15 | 192.168.30.50 - .64 | Estações | IPs atribuídos via DHCP |

---

## 3. Redes WAN (Interconexão)

### 3.1 Link Matriz ↔ Filial A: 10.0.1.0/30

| Endereço | Máscara | Usável? | Atribuição |
|----------|---------|---------|------------|
| 10.0.1.0 | 255.255.255.252 | ❌ | Rede |
| 10.0.1.1 | 255.255.255.252 | ✅ | RTR-MATRIZ Serial 0/0/0 (ou Gig0/1) |
| 10.0.1.2 | 255.255.255.252 | ✅ | RTR-FILIAL-A Serial 0/0/0 (ou Gig0/1) |
| 10.0.1.3 | 255.255.255.252 | ❌ | Broadcast |

### 3.2 Link Matriz ↔ Filial B: 10.0.2.0/30

| Endereço | Máscara | Usável? | Atribuição |
|----------|---------|---------|------------|
| 10.0.2.0 | 255.255.255.252 | ❌ | Rede |
| 10.0.2.1 | 255.255.255.252 | ✅ | RTR-MATRIZ Serial 0/0/1 (ou Gig0/2) |
| 10.0.2.2 | 255.255.255.252 | ✅ | RTR-FILIAL-B Serial 0/0/0 (ou Gig0/1) |
| 10.0.2.3 | 255.255.255.252 | ❌ | Broadcast |

**Justificativa do /30:** Redes ponto-a-ponto entre roteadores precisam apenas de 2 IPs úteis. Máscara /30 fornece exatamente isso, economizando endereços.

---

## 4. Configuração DHCP

### 4.1 Escopo na Matriz (SRV-MATRIZ)

O servidor DHCP na matriz atenderá **todas as 3 redes** através de DHCP Relay configurado nos roteadores.

| Rede | Escopo | Pool Início | Pool Fim | Gateway | DNS 1 | DNS 2 | Domínio | Lease |
|------|--------|-------------|----------|---------|-------|-------|---------|-------|
| 192.168.10.0/24 | Matriz | 192.168.10.50 | 192.168.10.200 | 192.168.10.1 | 192.168.10.10 | 8.8.8.8 | inovasoft.local | 8h |
| 192.168.20.0/24 | Filial A | 192.168.20.50 | 192.168.20.200 | 192.168.20.1 | 192.168.10.10 | 192.168.20.10 | inovasoft.local | 8h |
| 192.168.30.0/24 | Filial B | 192.168.30.50 | 192.168.30.200 | 192.168.30.1 | 192.168.10.10 | 192.168.30.10 | inovasoft.local | 8h |

**Exclusões (IPs que não serão atribuídos pelo DHCP):**
- Matriz: 192.168.10.1 - 192.168.10.49
- Filial A: 192.168.20.1 - 192.168.20.49
- Filial B: 192.168.30.1 - 192.168.30.49

**Reservas (opcional, para testes):**
- PC-MATRIZ-ADM: MAC xx:xx:xx:xx:xx:01 → 192.168.10.100

### 4.2 DHCP Relay nos Roteadores

Para que PCs das filiais recebam IPs do servidor central, configuramos **ip helper-address** nas interfaces LAN dos roteadores das filiais.

**RTR-FILIAL-A:**
```
interface GigabitEthernet0/0
 ip helper-address 192.168.10.10
```

**RTR-FILIAL-B:**
```
interface GigabitEthernet0/0
 ip helper-address 192.168.10.10
```

---

## 5. Configuração DNS

### 5.1 Zona Primária (SRV-MATRIZ)

**Zona:** inovasoft.local  
**Tipo:** Primária (autoritativa)

| Nome (FQDN) | Tipo | IP | Descrição |
|-------------|------|-----|-----------|
| inovasoft.local | A | 192.168.10.10 | Aponta para servidor principal |
| srv-matriz.inovasoft.local | A | 192.168.10.10 | Servidor da matriz |
| srv-filiala.inovasoft.local | A | 192.168.20.10 | Servidor filial A |
| srv-filialb.inovasoft.local | A | 192.168.30.10 | Servidor filial B |
| projecthub.inovasoft.local | CNAME | srv-matriz.inovasoft.local | Alias para app web |
| www.inovasoft.local | CNAME | srv-matriz.inovasoft.local | Alias web |
| arquivos.inovasoft.local | CNAME | srv-matriz.inovasoft.local | Compartilhamento de arquivos |

### 5.2 Zona Reversa (PTR)

**Zona:** 10.168.192.in-addr.arpa (para 192.168.10.0/24)

| IP | PTR | FQDN |
|----|-----|------|
| 192.168.10.10 | 10 | srv-matriz.inovasoft.local |

**Obs.:** Zonas reversas para 192.168.20.x e 192.168.30.x podem ser criadas de forma similar se necessário.

### 5.3 Forwarders (Servidores Filiais)

SRV-FILIAL-A e SRV-FILIAL-B atuam como **DNS cache/forwarder**:
- Consultas para `*.inovasoft.local` → encaminhadas para 192.168.10.10
- Outras consultas → 8.8.8.8 (Google DNS) ou 1.1.1.1 (Cloudflare)

---

## 6. Roteamento Estático

### 6.1 Tabela de Rotas - RTR-MATRIZ

| Rede Destino | Máscara | Next Hop | Interface | Distância Admin |
|--------------|---------|----------|-----------|-----------------|
| 192.168.10.0 | /24 | - | GigabitEthernet0/0 | 0 (conectado) |
| 192.168.20.0 | /24 | 10.0.1.2 | Serial0/0/0 | 1 |
| 192.168.30.0 | /24 | 10.0.2.2 | Serial0/0/1 | 1 |
| 10.0.1.0 | /30 | - | Serial0/0/0 | 0 (conectado) |
| 10.0.2.0 | /30 | - | Serial0/0/1 | 0 (conectado) |

**Comandos IOS:**
```cisco
ip route 192.168.20.0 255.255.255.0 10.0.1.2
ip route 192.168.30.0 255.255.255.0 10.0.2.2
```

### 6.2 Tabela de Rotas - RTR-FILIAL-A

| Rede Destino | Máscara | Next Hop | Interface | Distância Admin |
|--------------|---------|----------|-----------|-----------------|
| 192.168.20.0 | /24 | - | GigabitEthernet0/0 | 0 (conectado) |
| 10.0.1.0 | /30 | - | Serial0/0/0 | 0 (conectado) |
| 0.0.0.0 | /0 | 10.0.1.1 | Serial0/0/0 | 1 (default) |

**Comandos IOS:**
```cisco
ip route 0.0.0.0 0.0.0.0 10.0.1.1
! Rota padrão: todo tráfego desconhecido vai para a matriz
```

### 6.3 Tabela de Rotas - RTR-FILIAL-B

| Rede Destino | Máscara | Next Hop | Interface | Distância Admin |
|--------------|---------|----------|-----------|-----------------|
| 192.168.30.0 | /24 | - | GigabitEthernet0/0 | 0 (conectado) |
| 10.0.2.0 | /30 | - | Serial0/0/0 | 0 (conectado) |
| 0.0.0.0 | /0 | 10.0.2.1 | Serial0/0/0 | 1 (default) |

**Comandos IOS:**
```cisco
ip route 0.0.0.0 0.0.0.0 10.0.2.1
```

**Justificativa da rota padrão nas filiais:** Como toda comunicação externa (incluindo acesso à matriz e à outra filial) passa pela matriz, usar default route simplifica a configuração.

---

## 7. Checklist de Validação

### 7.1 Testes de Conectividade (Camada 3)

| Origem | Destino | IP Destino | Resultado Esperado |
|--------|---------|------------|---------------------|
| PC-MATRIZ-01 | Gateway Matriz | 192.168.10.1 | ✅ Ping sucesso |
| PC-MATRIZ-01 | SRV-MATRIZ | 192.168.10.10 | ✅ Ping sucesso |
| PC-MATRIZ-01 | RTR-FILIAL-A (WAN) | 10.0.1.2 | ✅ Ping sucesso |
| PC-MATRIZ-01 | SRV-FILIAL-A | 192.168.20.10 | ✅ Ping sucesso (via roteamento) |
| PC-FILIALA-01 | Gateway Filial A | 192.168.20.1 | ✅ Ping sucesso |
| PC-FILIALA-01 | SRV-MATRIZ | 192.168.10.10 | ✅ Ping sucesso |
| PC-FILIALB-01 | PC-FILIALA-01 | 192.168.20.x | ✅ Ping sucesso (via matriz) |

### 7.2 Testes de DHCP

| Rede | Teste | Comando | Resultado Esperado |
|------|-------|---------|---------------------|
| Matriz | Obter IP via DHCP | `ipconfig /renew` (PC) | IP na faixa .50-.200 |
| Filial A | Obter IP via DHCP | `ipconfig /renew` | IP na faixa .50-.200 |
| Filial B | Obter IP via DHCP | `ipconfig /renew` | IP na faixa .50-.200 |
| Matriz | Verificar Gateway | `ipconfig /all` | Gateway = 192.168.10.1 |
| Filial A | Verificar DNS | `ipconfig /all` | DNS = 192.168.10.10 |

### 7.3 Testes de DNS

| Origem | Query | Comando | Resultado Esperado |
|--------|-------|---------|---------------------|
| PC-MATRIZ-01 | srv-matriz.inovasoft.local | `nslookup` | 192.168.10.10 |
| PC-FILIALA-01 | projecthub.inovasoft.local | `nslookup` | CNAME → 192.168.10.10 |
| PC-FILIALB-01 | srv-filiala.inovasoft.local | `nslookup` | 192.168.20.10 |
| PC-MATRIZ-01 | google.com | `nslookup` | IP público (DNS externo funciona) |

### 7.4 Testes de Roteamento

| Teste | Comando | Local | Resultado Esperado |
|-------|---------|-------|---------------------|
| Ver rotas ativas | `show ip route` | RTR-MATRIZ | 5 rotas (2 conectadas + 2 estáticas + 1 local) |
| Traceroute matriz→filialA | `tracert 192.168.20.10` | PC-MATRIZ | 2 hops (.10.1 → .20.10) |
| Traceroute filialA→filialB | `tracert 192.168.30.10` | PC-FILIALA | 3 hops (.20.1 → .10.1 → .30.10) |

---

## 8. Decisões e Justificativas

### 8.1 Por que /24 para LANs?

- **Simplicidade:** Máscara conhecida, fácil de calcular manualmente
- **Capacidade:** 254 hosts por rede (suficiente para 20-30 dispositivos + crescimento)
- **Padrão comum:** Alinhado com práticas de redes SMB

### 8.2 Por que /30 para links WAN?

- **Eficiência:** Apenas 2 IPs úteis necessários (um em cada roteador)
- **Economia de endereços:** Não desperdiça IPs em links ponto-a-ponto
- **Padrão RFC:** Recomendado para interconexões seriais

### 8.3 Por que DHCP centralizado?

- **Gestão única:** Mais fácil gerenciar 3 escopos em 1 servidor que 3 servidores independentes
- **Consistência:** Política de lease, opções e domínio unificados
- **Redundância:** Servidores de filial podem assumir com escopos manuais se matriz cair

### 8.4 Por que roteamento estático?

- **Previsibilidade:** Rotas fixas facilitam debug
- **Overhead zero:** Sem pacotes de atualização (RIP/OSPF)
- **Adequado ao tamanho:** 3 roteadores não justificam protocolo dinâmico
- **Escopo acadêmico:** Foco em entender tabela de rotas, não em algoritmo de roteamento

---

## 9. Limitações Conhecidas

1. **Sem redundância de link:** Se o link matriz-filial cair, a filial fica isolada
2. **DHCP relay depende de roteamento:** Se rota cair, DHCP das filiais para de funcionar
3. **DNS único ponto de falha:** SRV-MATRIZ fora do ar = resolução de nomes falha (cache local nas filiais mitiga parcialmente)

**Melhorias futuras:**
- Implementar segundo link WAN (backup)
- DHCP failover real (não apenas escopos reserva)
- DNS secundário autoritativo (não só cache)

---

**Versão:** 1.2  
**Última atualização:** 22/09/2025  
**Responsável:** João Pedro Vianna

