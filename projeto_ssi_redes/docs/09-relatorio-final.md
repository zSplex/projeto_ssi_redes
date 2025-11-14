# Relatório Final - Projeto SSI/IP
**InovaSoft Desenvolvimento de Sistemas**

---

## 1. Identificação da Equipe

**Nome do Projeto:** Infraestrutura Segura de Rede Corporativa para a InovaSoft

**Integrantes da Equipe:**
- Eric Santos (Infraestrutura e Servidores)
- Erik Doca (Desenvolvimento Web - Backend)
- Emilly Gonçalves (Documentação e Políticas de Segurança)
- João Pedro Vianna (Topologia de Rede e Análise de Segurança)

**Turma:** Desenvolvimento de Sistemas - 3º Módulo  
**Disciplina:** Segurança de Sistemas de Informação e Internet/Protocolos (SSI/IP)  
**Data de Entrega:** 04 de Novembro de 2025  
**Professor Orientador:** [Nome do Professor]

**AVISO:** Este é um projeto acadêmico desenvolvido para fins educacionais. As senhas, configurações e alguns procedimentos foram simplificados para o ambiente de laboratório. Não deve ser usado diretamente em produção sem as devidas adaptações de segurança.

---

## 2. Introdução

### 2.1 Descrição da Empresa

**InovaSoft Desenvolvimento de Sistemas Ltda.**

- **CNPJ:** 34.567.890/0001-12
- **Setor:** Desenvolvimento de software sob medida e sistemas de gestão
- **Matriz:** Bauru/SP
- **Filiais:** São Paulo/SP e Campinas/SP
- **Funcionários:** 50 colaboradores (20 na matriz, 15 em cada filial)

**Estrutura Departamental:**

| Departamento | Matriz | Filial SP | Filial Campinas | Total |
|--------------|--------|-----------|-----------------|-------|
| TI | 3 | 1 | 1 | 5 |
| Desenvolvimento | 8 | 7 | 7 | 22 |
| Comercial | 4 | 3 | 3 | 10 |
| Financeiro | 3 | 2 | 2 | 7 |
| RH | 2 | 2 | 2 | 6 |
| **Total** | **20** | **15** | **15** | **50** |

**Contexto de Negócio:**

A InovaSoft é uma empresa que desenvolve sistemas personalizados (ERP, CRM e gestão de projetos) para empresas pequenas e médias no Brasil. Com o crescimento nos últimos anos e a abertura de duas filiais, a empresa percebeu que precisava urgentemente de uma rede mais organizada e segura. 

Os principais problemas que motivaram este projeto foram:
- As três unidades trabalhavam de forma isolada, dificultando compartilhamento de arquivos
- Não havia controle adequado de quem acessava o quê (qualquer um podia ver documentos confidenciais)
- Falta de backup estruturado (já houve perda de dados importantes)
- Sem procedimentos claros caso algo desse errado (ex: servidor cair)

Por isso decidimos implementar uma infraestrutura que garanta os três pilares da segurança: Confidencialidade, Integridade e Disponibilidade (CID).

### 2.2 Objetivo do Projeto

Implementar uma infraestrutura de rede corporativa segura interligando matriz e filiais, seguindo os princípios de **Confidencialidade, Integridade e Disponibilidade (CID)**, com:

- Topologia de rede funcional no Cisco Packet Tracer
- Serviços centralizados (DHCP, DNS, Compartilhamento de Arquivos)
- Controle de acesso baseado em grupos por departamento
- Aplicação web interna (ProjectHub) com autenticação e autorização
- Políticas de segurança documentadas e aplicáveis
- Procedimentos de backup e recuperação de desastres

### 2.3 Justificativa da Proposta

**Por que escolhemos essa solução?**

Depois de estudar as opções disponíveis, optamos por essa configuração pelos seguintes motivos:

1. **Simplicidade:** Como são apenas 3 localidades, usar roteamento estático foi mais fácil de configurar e entender do que protocolos dinâmicos tipo RIP ou OSPF. A gente consegue debugar melhor quando algo dá errado.

2. **Custo Zero (ou quase):** Usamos ferramentas gratuitas e open source sempre que possível - Packet Tracer Student é grátis, PHP e MySQL também. Isso mostra que dá pra fazer segurança sem gastar muito.

3. **Segurança em Várias Camadas:** Não adianta só proteger a rede. A gente implementou controle de acesso nas pastas (ACLs do Windows), na aplicação web (guards por grupo) e até no banco de dados (prepared statements). Se um nível falhar, tem outros de backup.

4. **Escopo Realista:** Sabemos que em 8 semanas não dá pra fazer um projeto enterprise completo. Focamos em fazer um MVP que funciona e demonstra os conceitos principais que aprendemos no curso.

5. **Aplicação dos Pilares CID:**
   - **Confidencialidade:** Controle quem vê o quê (ACLs + login)
   - **Integridade:** Protege contra alterações indevidas (backup + validação de entrada)
   - **Disponibilidade:** Garante que os serviços fiquem no ar (redundância + procedimentos)

---

## 3. Desenho da Rede (Packet Tracer)

### 3.1 Mapa da Rede

**Topologia:** Hub-and-Spoke (estrela) com matriz como hub central

```
                    INTERNET (Simulado)
                           |
              +------------+------------+
              |      RTR-MATRIZ         |
              | GW: 192.168.10.1        |
              +------------+------------+
                   |               |
          LAN Matriz          WAN Interligação
       (192.168.10.0/24)     (10.0.x.0/30)
            |                     |
      +-----+------+         +----+----+
      | SW-MATRIZ  |         |         |
      +-----+------+    RTR-FILIAL-A   RTR-FILIAL-B
            |          (192.168.20.1)  (192.168.30.1)
        Servidores          |               |
        Estações       SW-FILIAL-A     SW-FILIAL-B
                            |               |
                      Servidor/PCs    Servidor/PCs
```

**Equipamentos por Site:**

| Site | Roteador | Switch | Servidor | Estações | Total Dispositivos |
|------|----------|--------|----------|----------|-------------------|
| Matriz | 1x 2911 | 1x 2960 | 1x Server-PT | 6 (representa 20) | 9 |
| Filial A | 1x 2911 | 1x 2960 | 1x Server-PT | 4 (representa 15) | 7 |
| Filial B | 1x 2911 | 1x 2960 | 1x Server-PT | 4 (representa 15) | 7 |
| **Total** | **3** | **3** | **3** | **14** | **23** |

**Observação:** No Packet Tracer, reduzimos o número de PCs para evitar lentidão, mas a configuração representa a rede completa.

### 3.2 Endereçamento IP

#### Redes LAN (uma por site)

| Rede | CIDR | Máscara | Gateway | Pool DHCP | Broadcast |
|------|------|---------|---------|-----------|-----------|
| Matriz | 192.168.10.0/24 | 255.255.255.0 | .1 | .50 - .200 | .255 |
| Filial A | 192.168.20.0/24 | 255.255.255.0 | .1 | .50 - .200 | .255 |
| Filial B | 192.168.30.0/24 | 255.255.255.0 | .1 | .50 - .200 | .255 |

#### Redes WAN (links ponto-a-ponto)

| Link | Rede | Máscara | RTR-MATRIZ | RTR-FILIAL |
|------|------|---------|------------|------------|
| Matriz ↔ Filial A | 10.0.1.0/30 | 255.255.255.252 | 10.0.1.1 | 10.0.1.2 |
| Matriz ↔ Filial B | 10.0.2.0/30 | 255.255.255.252 | 10.0.2.1 | 10.0.2.2 |

**Por que /30?** Quando aprendemos sobre máscaras de sub-rede, vimos que /30 dá exatamente 2 IPs úteis (mais rede e broadcast). Como cada link WAN conecta só 2 roteadores, não faz sentido desperdiçar IPs usando /24 (que daria 254 hosts). É tipo economizar endereços IP.

#### Servidores (IPs Estáticos)

| Servidor | IP | Gateway | DNS | Função |
|----------|-----|---------|-----|--------|
| SRV-MATRIZ | 192.168.10.10 | 192.168.10.1 | 127.0.0.1 | DHCP, DNS, Arquivos, Web |
| SRV-FILIAL-A | 192.168.20.10 | 192.168.20.1 | 192.168.10.10 | DHCP backup, DNS cache |
| SRV-FILIAL-B | 192.168.30.10 | 192.168.30.1 | 192.168.10.10 | DHCP backup, DNS cache |

### 3.3 Roteamento entre Filiais

**Método Escolhido:** Roteamento Estático

**Por que estático e não dinâmico (RIP/OSPF)?**

Nas aulas a gente aprendeu sobre RIP e OSPF que ajustam rotas automaticamente, mas decidimos usar roteamento estático por alguns motivos práticos:

- São só 3 roteadores - não precisa de automação complexa
- Rotas estáticas são mais fáceis de entender e debugar (a gente é iniciante ainda)
- No Packet Tracer, RIP às vezes demora pra convergir e pode confundir
- Em empresa pequena como a InovaSoft, estático resolve bem

*Observação: Sabemos que se a empresa crescer muito (tipo ter 10+ filiais), aí sim vale a pena partir pra OSPF.*

**Tabelas de Rotas:**

**RTR-MATRIZ:**
```cisco
ip route 192.168.20.0 255.255.255.0 10.0.1.2
ip route 192.168.30.0 255.255.255.0 10.0.2.2
```

**RTR-FILIAL-A e RTR-FILIAL-B:**
```cisco
ip route 0.0.0.0 0.0.0.0 10.0.1.1  # Rota padrão para matriz
```

**Vantagem da Rota Padrão nas Filiais:** Como todo tráfego externo (incluindo acesso à outra filial) passa pela matriz, usar default route simplifica a configuração.

### 3.4 Validação da Rede

**Testes Realizados:**

| Teste | Origem | Destino | Resultado |
|-------|--------|---------|-----------|
| Conectividade LAN | PC-MATRIZ-01 | 192.168.10.1 (GW) | ✅ 5/5 pacotes |
| Conectividade WAN | RTR-MATRIZ | 10.0.1.2 (RTR-FILIAL-A) | ✅ 5/5 pacotes |
| Roteamento Inter-Rede | PC-MATRIZ-01 | 192.168.20.10 (SRV-FILIAL-A) | ✅ 5/5 pacotes |
| Traceroute | PC-FILIALA-01 | 192.168.30.10 (SRV-FILIAL-B) | ✅ 3 hops (via matriz) |

**Evidências:** Screenshots salvos em `docs/screenshots/`.

---

## 4. Serviços Configurados

### 4.1 DHCP (Dynamic Host Configuration Protocol)

**Função:** Atribuir automaticamente endereços IP, gateway, DNS e domínio para estações de trabalho.

**Servidor Principal:** SRV-MATRIZ (192.168.10.10)

**Escopos Configurados:**

| Escopo | Rede | Faixa | Gateway | DNS Primário | DNS Secundário | Lease |
|--------|------|-------|---------|--------------|----------------|-------|
| Matriz | 192.168.10.0/24 | .50 - .200 | .1 | 192.168.10.10 | 8.8.8.8 | 8h |
| Filial A | 192.168.20.0/24 | .50 - .200 | .20.1 | 192.168.10.10 | 192.168.20.10 | 8h |
| Filial B | 192.168.30.0/24 | .50 - .200 | .30.1 | 192.168.10.10 | 192.168.30.10 | 8h |

**DHCP Relay:** Configurado nos roteadores das filiais (`ip helper-address 192.168.10.10`) para encaminhar broadcasts DHCP ao servidor central.

**Redundância:** Servidores SRV-FILIAL-A e SRV-FILIAL-B podem assumir com escopos de emergência se matriz cair.

**Teste de Validação:**
```cmd
ipconfig /renew
ipconfig /all
# Resultado: IP na faixa esperada, Gateway e DNS corretos
```

### 4.2 DNS (Domain Name System)

**Função:** Resolver nomes de domínio interno (`inovasoft.local`) para endereços IP.

**Servidor Principal:** SRV-MATRIZ (192.168.10.10)  
**Servidores Secundários:** SRV-FILIAL-A e SRV-FILIAL-B (forwarders/cache)

**Zona Configurada:** `inovasoft.local` (zona primária)

**Registros DNS:**

| Nome (FQDN) | Tipo | Endereço IP | Descrição |
|-------------|------|-------------|-----------|
| inovasoft.local | A | 192.168.10.10 | Domínio raiz |
| srv-matriz.inovasoft.local | A | 192.168.10.10 | Servidor matriz |
| srv-filiala.inovasoft.local | A | 192.168.20.10 | Servidor filial A |
| srv-filialb.inovasoft.local | A | 192.168.30.10 | Servidor filial B |
| projecthub.inovasoft.local | A | 192.168.10.10 | Aplicação web |
| www.inovasoft.local | A | 192.168.10.10 | Alias web |
| arquivos.inovasoft.local | A | 192.168.10.10 | Compartilhamento |

**Zona Reversa:** `10.168.192.in-addr.arpa` (PTR para 192.168.10.10 → srv-matriz.inovasoft.local)

**Forwarders:** 8.8.8.8 e 8.8.4.4 (Google DNS) para resolução de domínios externos

**Teste de Validação:**
```cmd
nslookup srv-matriz.inovasoft.local
# Resposta: 192.168.10.10 ✅

nslookup projecthub.inovasoft.local
# Resposta: 192.168.10.10 ✅

ping projecthub.inovasoft.local
# 4/4 pacotes recebidos ✅
```

### 4.3 Compartilhamento de Arquivos com ACLs

**Servidor:** SRV-MATRIZ (\\192.168.10.10)

**Estrutura de Pastas:**

```
C:\Compartilhados\
├── TI\              (Controle Total: GRP-TI)
├── RH\              (Modificar: GRP-RH | Negado: outros)
├── Financeiro\      (Modificar: GRP-Financeiro | Negado: outros)
├── Comercial\       (Modificar: GRP-Comercial | Leitura: GRP-TI)
└── Publico\         (Leitura: Todos | Modificar: GRP-TI)
```

**Compartilhamentos SMB:**

| Nome do Share | Caminho Local | Grupos com Acesso | Permissão |
|---------------|---------------|-------------------|-----------|
| `\\SRV-MATRIZ\TI` | C:\Compartilhados\TI | GRP-TI, Administrators | Full Control |
| `\\SRV-MATRIZ\RH` | C:\Compartilhados\RH | GRP-RH, Administrators | Modify, Full Control |
| `\\SRV-MATRIZ\Financeiro` | C:\Compartilhados\Financeiro | GRP-Financeiro, Administrators | Modify, Full Control |
| `\\SRV-MATRIZ\Comercial` | C:\Compartilhados\Comercial | GRP-Comercial (Modify), GRP-TI (Read) | Modify, Read |
| `\\SRV-MATRIZ\Publico` | C:\Compartilhados\Publico | GRP-Publico (Read), GRP-TI (Modify) | Read, Modify |

**Configuração de ACLs:**

- Herança desabilitada em todas as pastas
- Permissões explícitas por grupo (princípio do menor privilégio)
- Grupo "Users" removido para evitar acesso não intencional

**Script de Configuração:** `servidores/scripts/Configurar-Compartilhamento-Completo.ps1`

### 4.4 Grupos e Usuários Locais

**Grupos Criados:**

| Nome do Grupo | Descrição | Membros |
|---------------|-----------|---------|
| GRP-TI | Equipe de TI - Acesso administrativo | eric.santos |
| GRP-RH | Recursos Humanos - Dados sensíveis | emilly.goncalves |
| GRP-Financeiro | Departamento Financeiro | joao.vianna |
| GRP-Comercial | Equipe Comercial e Vendas | erik.doca |
| GRP-Publico | Todos os usuários - Documentos públicos | Todos os 4 usuários |

**Usuários Criados:**

| Nome Completo | Username | Departamento | Admin | Senha (Teste) |
|---------------|----------|--------------|-------|---------------|
| Eric Santos | eric.santos | TI | ✅ Sim | Senha@123 |
| Erik Doca | erik.doca | Comercial | ❌ Não | Senha@123 |
| Emilly Gonçalves | emilly.goncalves | RH | ❌ Não | Senha@123 |
| João Pedro Vianna | joao.vianna | Financeiro | ❌ Não | Senha@123 |

**⚠️ Observação de Segurança:** Senhas `Senha@123` são temporárias para ambiente de laboratório. Em produção, usar senhas fortes individuais e forçar troca no primeiro logon.

**Script de Criação:** `servidores/scripts/criar-usuarios.ps1`

---

## 5. Segurança da Informação

### 5.1 Confidencialidade

**Medidas Implementadas:**

1. **Controle de Acesso por Grupos:**
   - Pastas compartilhadas com ACLs NTFS + SMB por departamento
   - Usuário do RH não acessa pasta Financeiro (teste negativo validado)

2. **Autenticação Forte:**
   - Usuários autenticam com credenciais únicas (username + senha)
   - Senhas armazenadas com hash bcrypt no banco de dados (ProjectHub)
   - Sessão PHP com regeneração de ID após login (previne session fixation)

3. **Segregação de Rede:**
   - 3 redes LAN isoladas (matriz, filial A, filial B)
   - Tráfego entre redes controlado por roteamento

4. **Aplicação Web com Guards:**
   - Middleware `require_group()` bloqueia acesso não autorizado por departamento
   - Administradores têm acesso total, usuários comuns apenas a suas páginas

**Teste de Confidencialidade:**

| Teste | Usuário | Ação | Resultado Esperado | Resultado Obtido |
|-------|---------|------|--------------------|------------------|
| Acesso à pasta RH | emilly.goncalves | Abrir `\\SRV-MATRIZ\RH` | ✅ Acesso permitido | ✅ Permitido |
| Acesso à pasta Financeiro | emilly.goncalves | Abrir `\\SRV-MATRIZ\Financeiro` | ❌ Acesso negado | ❌ Negado |
| Acesso à página RH (web) | emilly.goncalves | Abrir `rh.php` | ✅ Acesso permitido | ✅ Permitido |
| Acesso à página Financeiro (web) | emilly.goncalves | Abrir `financeiro.php` | ❌ HTTP 403 | ❌ 403 Forbidden |

**Conclusão:** Confidencialidade **VALIDADA** ✅

### 5.2 Integridade

**Medidas Implementadas:**

1. **Permissões de Modificação Controladas:**
   - Apenas grupos autorizados podem modificar arquivos em suas pastas
   - Grupo GRP-Publico tem apenas leitura (exceto TI que pode modificar)

2. **Proteção contra SQL Injection:**
   - PDO com prepared statements em 100% das queries do ProjectHub
   - Teste de penetração: tentativa de injeção `admin' OR '1'='1` foi bloqueada

3. **Sanitização de Outputs (XSS):**
   - Função `htmlspecialchars()` aplicada em todos os outputs PHP
   - Previne execução de scripts maliciosos

4. **Proteção CSRF:**
   - Token CSRF em formulários críticos (login, criação de usuário)
   - Validação server-side com `csrf_check()`

5. **Backup Automatizado:**
   - Script `backup-simulado.ps1` executa backup diário
   - Arquivos compactados (.zip) com timestamp
   - Retenção de 30 dias

**Teste de Integridade:**

| Teste | Descrição | Resultado |
|-------|-----------|-----------|
| SQL Injection | Tentar login com `admin' OR '1'='1` | ✅ Bloqueado |
| XSS | Inserir `<script>alert('xss')</script>` em campo nome | ✅ Escapado (exibido como texto) |
| Backup/Restore | Deletar arquivo e restaurar do backup | ✅ Arquivo recuperado intacto |

**Conclusão:** Integridade **VALIDADA** ✅

### 5.3 Disponibilidade

**Medidas Implementadas:**

1. **Redundância de Serviços:**
   - DHCP: Servidor central + escopos de emergência nas filiais
   - DNS: Servidor primário + cache em servidores de filial

2. **Backup Programado:**
   - Backup diário às 23h (Task Scheduler)
   - Teste de restauração mensal obrigatório

3. **Documentação de Contingência:**
   - Procedimentos de retomada para cenários críticos:
     - Servidor DHCP fora do ar (RTO: 30 min)
     - Link WAN interrompido (Workaround: trabalho local)
     - Perda de dados (RPO: 24h via backup)

4. **Definição de RTO/RPO:**

| Serviço | RTO (Recovery Time) | RPO (Perda Máxima de Dados) |
|---------|---------------------|------------------------------|
| DHCP | 30 minutos | 0 (sem perda) |
| DNS | 30 minutos | 0 (sem perda) |
| Compartilhamento de Arquivos | 2 horas | 24 horas (último backup) |
| ProjectHub (Web) | 4 horas | 24 horas |

**Simulação de Falha:**

| Cenário | Sintoma | Diagnóstico | Recuperação | Tempo Real |
|---------|---------|-------------|-------------|------------|
| Servidor DHCP cai | PCs ficam com IP 169.254.x.x | Ping para 192.168.10.10 falha | Ativar DHCP em SRV-FILIAL-A | 25 min ✅ |
| Arquivo deletado | Usuário reporta perda | Verificar backup mais recente | Extrair ZIP e copiar arquivo | 8 min ✅ |

**Conclusão:** Disponibilidade **VALIDADA** com ressalvas (sem redundância de hardware) ⚠️

---

## 6. Políticas de Segurança

Documento completo: `docs/05-politicas-seguranca.md`

### 6.1 Definição de Senhas

**Requisitos Obrigatórios:**

- **Tamanho mínimo:** 8 caracteres
- **Complexidade:** Mínimo 3 de 4 tipos (maiúsculas, minúsculas, números, símbolos)
- **Histórico:** Não repetir últimas 5 senhas
- **Expiração:** 90 dias
- **Bloqueio:** 5 tentativas incorretas → bloqueio por 15 minutos

**Senhas Proibidas:**
- Senhas padrão (senha123, admin, password)
- Dados pessoais (nome, CPF, data de nascimento)
- Sequências simples (12345678, abcdefgh)

**Implementação Técnica (Windows Server):**
```powershell
net accounts /minpwlen:8
net accounts /maxpwage:90
net accounts /lockoutthreshold:5
net accounts /lockoutduration:15
```

### 6.2 Restrições de Acesso

**Princípio do Menor Privilégio:**
- Usuários recebem apenas permissões necessárias para suas funções
- Contas admin restritas à equipe de TI
- Acesso a pastas confidenciais (RH, Financeiro) negado por padrão

**Classificação de Informações:**

| Nível | Exemplo | Acesso |
|-------|---------|--------|
| Público | Comunicados gerais | Todos (leitura) |
| Interno | Templates, processos | Funcionários |
| Confidencial | Contratos, folha de pagamento | Apenas departamento específico |
| Restrito | Senhas de sistemas, chaves de API | Apenas TI ou diretoria |

### 6.3 Outras Boas Práticas Aplicadas

**Contas Inativas:**
- Critério: Sem login por 60 dias consecutivos
- Procedimento: Desabilitar conta no dia 60, excluir no dia 90 (após confirmação de RH)
- Script: `Verificar-Contas-Inativas.ps1`

**Desligamento de Funcionário:**
- RH notifica TI imediatamente
- Conta desabilitada no mesmo dia
- Backup de arquivos pessoais antes de excluir (após 30 dias)

**Backup:**
- Estratégia: Full semanal + incremental diário
- Retenção: 30 dias
- Teste de restauração: Mensal

**Uso Aceitável:**
- ❌ Proibido: Download de software não autorizado, acesso a conteúdo ilegal, compartilhar credenciais
- ✅ Permitido: Uso pessoal mínimo de e-mail/internet (pausas, emergências)

---

## 7. Análise de Segurança

Documento completo: `docs/06-analise-seguranca.md`

### 7.1 Testes Realizados

**Checklist CID:**

| Pilar | Item Testado | Resultado |
|-------|--------------|-----------|
| **Confidencialidade** | Controle de acesso por grupos | ✅ Funcional |
| **Confidencialidade** | Autenticação no ProjectHub | ✅ Funcional |
| **Confidencialidade** | Senhas com hash bcrypt | ✅ Implementado |
| **Integridade** | Proteção SQL Injection | ✅ Bloqueado |
| **Integridade** | Sanitização XSS | ✅ Funcional |
| **Integridade** | Backup e restore | ✅ Testado |
| **Disponibilidade** | DHCP com failover | ✅ Funcional |
| **Disponibilidade** | DNS com cache | ✅ Funcional |
| **Disponibilidade** | Documentação de contingência | ✅ Documentado |

### 7.2 Simulações de Falha e Recuperação

**Cenário 1: Tentativa de Acesso Não Autorizado**

- **Descrição:** Usuário Comercial tenta acessar pasta Financeiro
- **Resultado:** ❌ Acesso negado (conforme esperado)
- **Log:** Event ID 5145 - Security Auditing - Failure: Object Access

**Cenário 2: Servidor DHCP Fora do Ar**

- **Sintoma:** PCs não recebem IP (APIPA 169.254.x.x)
- **Workaround:** Ativar DHCP em servidor de filial manualmente
- **Tempo de Recuperação:** 25 minutos (✅ Dentro do RTO de 30 min)

**Cenário 3: Ataque de SQL Injection**

- **Tentativa:** `admin' OR '1'='1` no campo de login
- **Resultado:** ✅ Login falhou, prepared statements protegeram o banco

**Cenário 4: Backup e Restauração**

- **Teste:** Deletar arquivo crítico e restaurar do backup
- **Resultado:** ✅ Arquivo recuperado com sucesso, sem perda de dados

### 7.3 Pontos Fortes e Fracos

**Pontos Fortes ✅:**

1. Segregação efetiva de acesso por departamento
2. Aplicação web protegida contra vulnerabilidades comuns (OWASP Top 10)
3. Backup automatizado e testado
4. Documentação clara de procedimentos de recuperação
5. Princípio do menor privilégio aplicado consistentemente

**Pontos Fracos ⚠️:**

1. **Falta de HTTPS:** Tráfego HTTP não criptografado (credenciais podem ser interceptadas)
2. **Senhas Fracas:** Senhas de teste (`Senha@123`) facilitam brute force
3. **Sem Redundância de Hardware:** Servidor matriz é ponto único de falha
4. **Ausência de IDS/IPS:** Ataques não detectados em tempo real
5. **Sem MFA:** Credenciais comprometidas garantem acesso total
6. **Logs Não Centralizados:** Dificulta análise forense
7. **Firewall Entre Redes:** Roteador permite tráfego irrestrito entre filiais

**Melhorias Recomendadas (Roadmap):**

**Curto Prazo (1-3 meses):**
- [ ] Implementar HTTPS no ProjectHub (Let's Encrypt)
- [ ] Substituir senhas de teste por senhas fortes individuais
- [ ] Habilitar auditoria de acesso em pastas confidenciais

**Médio Prazo (3-6 meses):**
- [ ] Implementar MFA para usuários admin
- [ ] Configurar IDS (Snort) em modo passivo
- [ ] Migrar para Active Directory

**Longo Prazo (6-12 meses):**
- [ ] Servidor de backup secundário (offsite ou nuvem)
- [ ] Link WAN redundante
- [ ] SIEM para análise de logs (Splunk/ELK)

### 7.4 Scores CID

- **Confidencialidade:** 8/10 ✅
- **Integridade:** 7/10 ✅
- **Disponibilidade:** 6/10 ⚠️

**Nota Geral:** 7/10 (Bom, com oportunidades de melhoria)

---

## 8. Conclusão

### 8.1 Aprendizados

Durante o projeto, aprendemos bastante coisa que não fica tão clara nas aulas teóricas:

1. **Segurança não é uma coisa só:** A gente achava que bastava ter firewall ou antivírus, mas descobrimos que precisa proteger em vários níveis. Se alguém passar da rede, ainda tem o controle de acesso nas pastas. Se passar disso, tem a autenticação na aplicação. É tipo ter várias portas trancadas.

2. **Documentar salva tempo depois:** No começo a gente reclamou de ter que escrever tudo, mas quando simulamos a falha do DHCP, foi só seguir o procedimento que tava documentado e resolvemos em 25 minutos. Se não tivesse escrito, ia ser bem mais difícil lembrar o que fazer.

3. **Sempre tem que escolher entre uma coisa e outra:** Por exemplo, roteamento estático é mais simples mas menos flexível. HTTP funciona no lab mas não é seguro pra produção. A gente aprendeu que não existe solução perfeita, tem que escolher o que faz mais sentido pra cada situação.

4. **Testar é tão importante quanto fazer:** Fazer os testes de SQL injection e acesso não autorizado foi legal porque a gente viu na prática que as proteções realmente funcionam. Não é só teoria.

5. **Backup sem teste não serve:** Um dos professores sempre falava isso e a gente comprovou. Fizemos o teste de restaurar um arquivo deletado e funcionou. Isso deu segurança de que o backup tá servindo pra alguma coisa.

### 8.2 Dificuldades Enfrentadas

Nem tudo foi fácil. Algumas coisas que deram trabalho:

1. **Packet Tracer travando:** No começo a gente colocou muitos PCs na rede e o programa ficou super lento. Tivemos que reduzir pra uns 10-15 PCs no total, mas documentamos que representa uma rede maior. Ah, e configurar o DHCP relay foi complicado - precisamos assistir vários vídeos no YouTube até entender onde colocar o comando.

2. **Fazer PHP se conectar com MySQL:** Parece simples mas demorou pra gente entender a diferença entre `mysql_` (velho e inseguro) e PDO (novo e correto). Teve muita pesquisa na documentação do PHP e alguns erros de "connection refused" até acertar o charset e as opções certas.

3. **Permissões do Windows são confusas:** Aquele negócio de herança de permissão do Windows é meio chatinho. A primeira vez que a gente tentou, todo mundo ainda conseguia acessar tudo. Depois descobrimos que tinha que desabilitar a herança E remover os grupos que vêm por padrão. No fim fizemos um script PowerShell pra não ter que clicar em milhões de caixinhas.

4. **Organizar quem faz o quê:** Com 4 pessoas trabalhando, teve hora que um esperava o outro terminar alguma coisa. Criamos um cronograma mais realista depois da primeira semana, com marcos bem definidos. Ajudou bastante.

5. **Packet Tracer não simula tudo:** A gente queria testar latência da WAN, mas o PT não tem isso direito. Também não dá pra simular ataque DDoS ou coisa assim. Então focamos em documentar bem o que faríamos se tivesse equipamento real.

### 8.3 Melhorias Futuras

**O que gostaríamos de fazer se tivesse mais tempo ou fosse um projeto real:**

1. **Colocar HTTPS na aplicação:** Deixamos em HTTP porque é mais simples pro laboratório, mas sabemos que em produção tem que ser HTTPS. Dá pra usar Let's Encrypt que é grátis.

2. **VPN entre as filiais:** Seria legal criptografar o tráfego entre as unidades. Aprendemos sobre VPN IPSec nas aulas mas não deu tempo de implementar no projeto.

3. **Firewall de verdade:** Os roteadores deixam passar todo o tráfego. Seria bom ter ACLs bloqueando portas desnecessárias ou até um pfSense entre as redes.

4. **Monitoramento automático:** Imagina receber um alerta no celular quando o servidor DHCP cair? Ferramentas tipo Nagios ou Zabbix fazem isso, mas são complexas pra configurar.

5. **Melhorar a aplicação web:** A gente fez o básico (login, CRUD, páginas por departamento). Seria legal adicionar upload de arquivos, gráficos no dashboard, histórico de quem acessou o quê.

**Se a empresa crescer:**

- Adicionar mais filiais (o roteamento estático começa a ficar complicado com muitos sites)
- Usar Active Directory em vez de usuários locais (mais profissional)
- VLANs separando os departamentos dentro de cada escritório
- Servidor de e-mail próprio (Exchange ou Postfix)

---

## 9. Anexos

### Anexo A: Screenshots

Pasta: `docs/screenshots/`

1. `topologia-packet-tracer.png` - Visão geral da rede
2. `show-ip-route-matriz.png` - Tabela de rotas do RTR-MATRIZ
3. `ping-matriz-filialA.png` - Teste de conectividade
4. `nslookup-projecthub.png` - Resolução DNS funcionando
5. `ipconfig-dhcp.png` - PC recebendo IP via DHCP
6. `acl-pasta-rh.png` - Permissões da pasta RH
7. `login-projecthub.png` - Tela de login da aplicação
8. `dashboard-projecthub.png` - Dashboard após login
9. `admin-crud-usuarios.png` - Painel administrativo
10. `teste-acesso-negado.png` - Erro 403 ao tentar acessar página não autorizada

### Anexo B: Arquivos de Configuração

**Localização:** `rede/` e `servidores/`

- `comandos-roteadores.md` - Comandos Cisco IOS completos
- `ip-plan.md` - Tabela detalhada de endereçamento
- `criar-usuarios.ps1` - Script de criação de usuários e grupos
- `Configurar-Compartilhamento-Completo.ps1` - Script de ACLs e shares
- `backup-simulado.ps1` - Script de backup automatizado

### Anexo C: Código-Fonte do ProjectHub

**Localização:** `app-php/`

- Estrutura completa da aplicação web
- README.md com instruções de instalação
- Schema SQL e seeds de dados de teste
- Código PHP comentado seguindo boas práticas

### Anexo D: Documentação Complementar

- `00-empresa-opcoes.md` - Definição da empresa fictícia
- `01-visao-escopo.md` - Objetivos e critérios de aceite
- `02-cronograma.md` - Planejamento do projeto
- `03-arquitetura-fisica.md` - Detalhamento da infraestrutura
- `05-politicas-seguranca.md` - Políticas completas
- `06-analise-seguranca.md` - Análise detalhada CID

---

**Assinaturas:**

Eric Santos - Infraestrutura  
Erik Doca - Desenvolvimento Web  
Emilly Gonçalves - Documentação  
João Pedro Vianna - Redes

Data: 04/11/2025

---

**Versão:** 1.0  
**Total de Páginas:** 18  
**Anexos:** 4  
**Arquivos Entregues:** 47

