# Comandos de Configuração dos Roteadores
**Projeto SSI/IP - InovaSoft**

Este documento contém os comandos Cisco IOS para configurar os 3 roteadores no Packet Tracer.

---

## RTR-MATRIZ (Roteador da Matriz - Bauru)

### 1. Configurações Básicas

```cisco
enable
configure terminal

! Hostname e senha
hostname RTR-MATRIZ
enable secret Cisco@2025
service password-encryption

! Banner de aviso
banner motd #
****************************************************
* ACESSO RESTRITO - InovaSoft Desenvolvimento     *
* Acesso nao autorizado sera registrado e punido  *
****************************************************
#

! Configurar console
line console 0
 password Console@123
 login
 logging synchronous
 exec-timeout 5 0
 exit

! Configurar VTY (Telnet/SSH)
line vty 0 4
 password VTY@123
 login
 exec-timeout 10 0
 exit
```

### 2. Interfaces e Endereçamento

```cisco
! Interface LAN - conecta ao switch da matriz
interface GigabitEthernet0/0
 description LAN Matriz (192.168.10.0/24)
 ip address 192.168.10.1 255.255.255.0
 no shutdown
 exit

! Interface WAN para Filial A
interface Serial0/0/0
 description Link para RTR-FILIAL-A
 ip address 10.0.1.1 255.255.255.252
 clock rate 128000
 ! Usar 'clock rate' apenas na ponta DCE
 no shutdown
 exit

! Interface WAN para Filial B
interface Serial0/0/1
 description Link para RTR-FILIAL-B
 ip address 10.0.2.1 255.255.255.252
 clock rate 128000
 no shutdown
 exit
```

**Observação:** Se o Packet Tracer não tiver interfaces Serial, use GigabitEthernet extras com cabos cross-over.

**Alternativa com Ethernet (se necessário):**
```cisco
interface GigabitEthernet0/1
 description Link para RTR-FILIAL-A (WAN simulada)
 ip address 10.0.1.1 255.255.255.252
 no shutdown

interface GigabitEthernet0/2
 description Link para RTR-FILIAL-B (WAN simulada)
 ip address 10.0.2.1 255.255.255.252
 no shutdown
```

### 3. Roteamento Estático

```cisco
! Rota para rede da Filial A
ip route 192.168.20.0 255.255.255.0 10.0.1.2

! Rota para rede da Filial B
ip route 192.168.30.0 255.255.255.0 10.0.2.2

! (Opcional) Rota padrão para Internet simulada
! ip route 0.0.0.0 0.0.0.0 GigabitEthernet0/3
```

### 4. Salvar Configuração

```cisco
end
write memory
! ou: copy running-config startup-config
```

### 5. Comandos de Verificação

```cisco
show ip interface brief        ! Ver status e IPs das interfaces
show ip route                  ! Ver tabela de roteamento
show running-config            ! Configuração ativa
ping 10.0.1.2                  ! Testar link para Filial A
ping 10.0.2.2                  ! Testar link para Filial B
ping 192.168.20.1              ! Testar roteamento até Filial A
ping 192.168.30.1              ! Testar roteamento até Filial B
```

---

## RTR-FILIAL-A (Roteador da Filial A - São Paulo)

### 1. Configurações Básicas

```cisco
enable
configure terminal

hostname RTR-FILIAL-A
enable secret Cisco@2025
service password-encryption

banner motd #
****************************************************
* ACESSO RESTRITO - InovaSoft Filial Sao Paulo    *
****************************************************
#

line console 0
 password Console@123
 login
 logging synchronous
 exec-timeout 5 0

line vty 0 4
 password VTY@123
 login
 exec-timeout 10 0
```

### 2. Interfaces e Endereçamento

```cisco
! Interface LAN - conecta ao switch da filial A
interface GigabitEthernet0/0
 description LAN Filial A (192.168.20.0/24)
 ip address 192.168.20.1 255.255.255.0
 ip helper-address 192.168.10.10
 ! Helper address encaminha broadcasts DHCP para servidor central
 no shutdown
 exit

! Interface WAN para Matriz
interface Serial0/0/0
 description Link para RTR-MATRIZ
 ip address 10.0.1.2 255.255.255.252
 ! Sem clock rate aqui (ponta DTE)
 no shutdown
 exit
```

### 3. Roteamento Estático

```cisco
! Rota padrão: todo trafego desconhecido vai para a matriz
ip route 0.0.0.0 0.0.0.0 10.0.1.1

! (Opcional) Rota especifica para matriz se precisar de metrica diferente
! ip route 192.168.10.0 255.255.255.0 10.0.1.1
```

### 4. Salvar Configuração

```cisco
end
write memory
```

### 5. Comandos de Verificação

```cisco
show ip interface brief
show ip route                  ! Deve aparecer rota default (S* 0.0.0.0/0)
ping 10.0.1.1                  ! Testar link para matriz
ping 192.168.10.1              ! Testar roteamento até matriz
ping 192.168.10.10             ! Testar acesso ao servidor matriz
```

---

## RTR-FILIAL-B (Roteador da Filial B - Campinas)

### 1. Configurações Básicas

```cisco
enable
configure terminal

hostname RTR-FILIAL-B
enable secret Cisco@2025
service password-encryption

banner motd #
****************************************************
* ACESSO RESTRITO - InovaSoft Filial Campinas     *
****************************************************
#

line console 0
 password Console@123
 login
 logging synchronous
 exec-timeout 5 0

line vty 0 4
 password VTY@123
 login
 exec-timeout 10 0
```

### 2. Interfaces e Endereçamento

```cisco
! Interface LAN - conecta ao switch da filial B
interface GigabitEthernet0/0
 description LAN Filial B (192.168.30.0/24)
 ip address 192.168.30.1 255.255.255.0
 ip helper-address 192.168.10.10
 no shutdown
 exit

! Interface WAN para Matriz
interface Serial0/0/0
 description Link para RTR-MATRIZ
 ip address 10.0.2.2 255.255.255.252
 no shutdown
 exit
```

### 3. Roteamento Estático

```cisco
! Rota padrão
ip route 0.0.0.0 0.0.0.0 10.0.2.1
```

### 4. Salvar Configuração

```cisco
end
write memory
```

### 5. Comandos de Verificação

```cisco
show ip interface brief
show ip route
ping 10.0.2.1                  ! Testar link para matriz
ping 192.168.10.1              ! Testar roteamento até matriz
ping 192.168.10.10             ! Testar acesso ao servidor
ping 192.168.20.10             ! Testar comunicacao com Filial A (via matriz)
```

---

## Troubleshooting Comum

### Problema: PCs não conseguem fazer ping para outras redes

**Diagnóstico:**
```cisco
! No roteador:
show ip route              ! Verificar se rotas estão presentes
show ip interface brief    ! Verificar se interfaces estão up/up
```

**Soluções:**
- Verificar se rotas estáticas estão configuradas
- Conferir se IPs dos next-hops estão corretos
- Testar ping direto do roteador (`ping 192.168.20.10 source 192.168.10.1`)

### Problema: DHCP não funciona nas filiais

**Diagnóstico:**
```cisco
! No roteador da filial:
show running-config interface GigabitEthernet0/0
! Verificar se há 'ip helper-address 192.168.10.10'
```

**Solução:**
- Adicionar `ip helper-address` na interface LAN do roteador da filial
- Confirmar que servidor DHCP tem escopos para todas as redes

### Problema: Interface não sobe (status down)

**Soluções:**
- Verificar cabo conectado corretamente no Packet Tracer
- Conferir se `no shutdown` foi executado
- Em interfaces Serial, verificar se clock rate está configurado na ponta DCE

### Problema: Esquecer senha enable

**Recuperação (Packet Tracer):**
- Desligar e ligar o roteador
- Durante boot, pressionar Ctrl+Break para entrar no ROMmon
- Executar `confreg 0x2142` (bypass startup-config)
- `reset`
- Após boot, `enable`, `copy startup-config running-config`
- `configure terminal`, definir nova senha, `config-register 0x2102`, `write`

---

## Resumo de IPs dos Roteadores

| Roteador | Interface | IP | Rede |
|----------|-----------|-----|------|
| RTR-MATRIZ | Gi0/0 | 192.168.10.1 | LAN Matriz |
| RTR-MATRIZ | Se0/0/0 | 10.0.1.1 | WAN Matriz-FilialA |
| RTR-MATRIZ | Se0/0/1 | 10.0.2.1 | WAN Matriz-FilialB |
| RTR-FILIAL-A | Gi0/0 | 192.168.20.1 | LAN Filial A |
| RTR-FILIAL-A | Se0/0/0 | 10.0.1.2 | WAN Matriz-FilialA |
| RTR-FILIAL-B | Gi0/0 | 192.168.30.1 | LAN Filial B |
| RTR-FILIAL-B | Se0/0/0 | 10.0.2.2 | WAN Matriz-FilialB |

---

## Checklist de Configuração

- [ ] RTR-MATRIZ: hostname, senhas, interfaces, rotas estáticas
- [ ] RTR-FILIAL-A: hostname, senhas, interfaces, rota padrão, helper-address
- [ ] RTR-FILIAL-B: hostname, senhas, interfaces, rota padrão, helper-address
- [ ] Todas as interfaces com descrição (`description`)
- [ ] Clock rate nas interfaces Serial DCE
- [ ] Configuração salva com `write memory`
- [ ] Ping entre todos os roteadores funciona
- [ ] Tabela de rotas correta (`show ip route`)

---

**Versão:** 1.1  
**Última atualização:** 22/09/2025  
**Responsável:** João Pedro Vianna, Eric Santos

