# Projeto SSI/IP - InovaSoft Desenvolvimento de Sistemas
**Infraestrutura de Rede Segura com os Pilares CID**

---

## 📌 Informações da Equipe

**Integrantes:**
- Eric Santos (Infraestrutura e Servidores)
- Erik Doca (Desenvolvimento Web)
- Emilly Gonçalves (Documentação e Políticas)
- João Pedro Vianna (Topologia de Rede)

**Disciplina:** Segurança de Sistemas de Informação e Redes  
**Turma:** Desenvolvimento de Sistemas - 3º Módulo  
**Data:** Novembro/2025

---

## 🏢 A Empresa (Fictícia)

**Nome:** InovaSoft Desenvolvimento de Sistemas Ltda.  
**CNPJ:** 34.567.890/0001-12  
**Setor:** Desenvolvimento de software sob medida

**Estrutura:**
- **Matriz:** Bauru/SP (20 funcionários)
- **Filial A:** São Paulo/SP (15 funcionários)
- **Filial B:** Campinas/SP (15 funcionários)
- **Total:** 50 colaboradores

**Departamentos:** TI, RH, Financeiro, Comercial

---

## 🎯 Objetivo do Projeto

Implementar uma infraestrutura de rede corporativa segura que conecte as 3 localidades da InovaSoft, garantindo os três pilares da segurança da informação:

- **Confidencialidade:** Apenas pessoas autorizadas acessam informações sensíveis
- **Integridade:** Dados não são alterados indevidamente
- **Disponibilidade:** Serviços e dados ficam acessíveis quando necessário

---

## 🌐 O Que Foi Implementado

### 1. Topologia de Rede (Cisco Packet Tracer)

**Arquitetura:** Hub-and-Spoke (matriz como centro)

```
                  RTR-MATRIZ (Bauru)
                  192.168.10.1
                       |
           +-----------+-----------+
           |                       |
    RTR-FILIAL-A            RTR-FILIAL-B
    192.168.20.1            192.168.30.1
    (São Paulo)             (Campinas)
```

**Equipamentos:**
- 3 Roteadores (2911 ou similar)
- 3 Switches (2960)
- 3 Servidores
- ~15 PCs (representando os 50 funcionários)

**Roteamento:** Estático (mais simples para 3 sites)

**Endereçamento IP:**
- Matriz: `192.168.10.0/24`
- Filial A: `192.168.20.0/24`
- Filial B: `192.168.30.0/24`
- Links WAN: `10.0.1.0/30` e `10.0.2.0/30`

### 2. Serviços de Rede

#### DHCP (Servidor Centralizado)
- Servidor principal: SRV-MATRIZ (192.168.10.10)
- Atende as 3 redes via DHCP Relay
- Pools: .50 até .200 em cada rede
- Lease time: 8 horas

#### DNS (Zona Interna)
- Domínio: `inovasoft.local`
- Servidor primário: SRV-MATRIZ
- Registros principais:
  - srv-matriz.inovasoft.local → 192.168.10.10
  - srv-filiala.inovasoft.local → 192.168.20.10
  - srv-filialb.inovasoft.local → 192.168.30.10
  - projecthub.inovasoft.local → 192.168.10.10

#### Compartilhamento de Arquivos
- Servidor: SRV-MATRIZ (`\\192.168.10.10\`)
- Estrutura de pastas:
  ```
  \\SRV-MATRIZ\TI          (Acesso: GRP-TI)
  \\SRV-MATRIZ\RH          (Acesso: GRP-RH)
  \\SRV-MATRIZ\Financeiro  (Acesso: GRP-Financeiro)
  \\SRV-MATRIZ\Comercial   (Acesso: GRP-Comercial)
  \\SRV-MATRIZ\Publico     (Leitura: Todos)
  ```

**Controle de Acesso (ACLs do Windows):**
- Cada departamento acessa apenas sua pasta
- Permissões NTFS + SMB configuradas
- Herança desabilitada para segurança

### 3. Usuários e Grupos Locais

**Grupos Criados:**
- GRP-TI
- GRP-RH
- GRP-Financeiro
- GRP-Comercial
- GRP-Publico

**Usuários de Teste:**

| Nome | Username | Departamento | Admin | Senha (Teste) |
|------|----------|--------------|-------|---------------|
| Eric Santos | eric.santos | TI | Sim | Senha@123 |
| Erik Doca | erik.doca | Comercial | Não | Senha@123 |
| Emilly Gonçalves | emilly.goncalves | RH | Não | Senha@123 |
| João Pedro Vianna | joao.vianna | Financeiro | Não | Senha@123 |

⚠️ **Senhas fracas apenas para ambiente de teste!**

### 4. Aplicação Web - ProjectHub

**Stack Tecnológica:**
- PHP 8.1+ (Backend)
- MySQL 8.0+ (Banco de Dados)
- Bootstrap 5.3 (Interface)

**Funcionalidades:**
- ✅ Login com autenticação segura (bcrypt)
- ✅ Dashboard personalizado por departamento
- ✅ Páginas protegidas por grupo (RH, Financeiro, Comercial)
- ✅ Painel Admin para gerenciar usuários (apenas TI)
- ✅ Logout seguro

**Segurança Implementada:**
- ✅ Prepared Statements (proteção SQL Injection)
- ✅ Sanitização de outputs (proteção XSS)
- ✅ Tokens CSRF em formulários
- ✅ Sessões com regeneração de ID após login

---

## 🔒 Pilares de Segurança (CID)

### Confidencialidade ✅ (8/10)

**O que fizemos:**
- Controle de acesso por grupos (ACLs em pastas)
- Autenticação obrigatória na aplicação web
- Senhas com hash bcrypt (não em texto claro)
- Sessões PHP protegidas

**Teste Validado:**
- ✅ Usuário RH não consegue acessar pasta Financeiro
- ✅ Usuário Comercial não acessa página do RH (HTTP 403)

### Integridade ✅ (7/10)

**O que fizemos:**
- PDO com prepared statements (SQL Injection bloqueado)
- Sanitização com `htmlspecialchars()` (XSS bloqueado)
- Proteção CSRF com tokens
- Backup automatizado diário

**Teste Validado:**
- ✅ Tentativa de SQL Injection bloqueada
- ✅ Script malicioso XSS escapado
- ✅ Arquivo deletado restaurado do backup

### Disponibilidade ✅ (6/10)

**O que fizemos:**
- DHCP com failover documentado
- DNS com cache nos servidores de filial
- Procedimentos de recuperação definidos
- RTO/RPO estabelecidos

**Teste Validado:**
- ✅ Simulação de falha do DHCP: recuperação em 25 min (RTO: 30 min)
- ✅ Restauração de backup funcionou

**Limitações reconhecidas:**
- Sem redundância física de hardware
- Sem monitoramento proativo (IDS/IPS)

---

## 📋 Políticas de Segurança

### Política de Senhas
- **Tamanho mínimo:** 8 caracteres
- **Complexidade:** Letras, números e símbolos
- **Expiração:** 90 dias
- **Bloqueio:** 5 tentativas incorretas → bloqueio 15 min

### Política de Backup
- **Frequência:** Diário (23h via Task Scheduler)
- **Tipo:** Full semanal + incremental diário
- **Retenção:** 30 dias
- **Teste:** Mensal obrigatório

### Contas Inativas
- **Critério:** 60 dias sem login
- **Ação:** Desabilitar conta
- **Exclusão:** Após 90 dias (com aprovação RH)

---

## 🧪 Testes Realizados

### Conectividade
✅ Ping entre todas as redes funcionando  
✅ Resolução DNS ok (`nslookup projecthub.inovasoft.local`)  
✅ DHCP atribuindo IPs automaticamente  

### Segurança
✅ SQL Injection bloqueado  
✅ XSS bloqueado  
✅ Acesso não autorizado negado (teste negativo ok)  
✅ Autenticação obrigatória funcionando  

### Disponibilidade
✅ Falha de DHCP simulada: recuperado em 25 min  
✅ Backup e restore testados com sucesso  

---

## 💡 Aprendizados

1. **Segurança em camadas funciona:** Se um nível falhar, outros protegem
2. **Documentação economiza tempo:** Procedimentos escritos agilizaram recuperação
3. **Testar é essencial:** Backup não testado não serve pra nada
4. **Simplicidade às vezes é melhor:** Roteamento estático foi suficiente
5. **Sempre tem trade-offs:** HTTP vs HTTPS, estático vs dinâmico, etc.

---

## 😅 Dificuldades Enfrentadas

1. **Packet Tracer travando:** Reduzimos número de PCs
2. **DHCP relay confuso:** Assistimos vários vídeos no YouTube
3. **PHP + MySQL:** Levou tempo entender PDO corretamente
4. **Permissões Windows:** Herança é chatinha de configurar
5. **Gerenciar 4 pessoas:** Cronograma ajudou muito

---

## 🚀 Melhorias Futuras

Se tivéssemos mais tempo ou fosse produção:

1. **HTTPS:** Certificado SSL/TLS (Let's Encrypt)
2. **VPN:** Criptografar tráfego entre filiais (IPSec)
3. **Firewall:** ACLs nos roteadores ou pfSense
4. **Monitoramento:** Nagios/Zabbix para alertas
5. **MFA:** Autenticação de dois fatores
6. **Active Directory:** Em vez de usuários locais
7. **Redundância:** Segundo servidor ou cluster

---

## 📂 Estrutura de Arquivos do Projeto

```
projeto_ssi_redes/
├── app-php/                    # Aplicação Web
│   ├── database/               # SQL (schema + seeds)
│   ├── public/                 # Páginas web
│   └── src/                    # Código backend
├── docs/                       # Documentação
│   ├── 05-politicas-seguranca.md
│   ├── 06-analise-seguranca.md
│   └── 09-relatorio-final.md
├── rede/                       # Configs de rede
│   ├── comandos-roteadores.md
│   ├── ip-plan.md
│   └── packet-tracer-notes.md
├── servidores/                 # Guias de serviços
│   ├── dhcp/
│   ├── dns/
│   ├── arquivos/
│   └── scripts/
└── README.md                   # Guia principal
```

---

## 🎬 Demonstração ao Vivo

### Roteiro (8-10 minutos)

**1. Introdução (1 min)** - Equipe e objetivo

**2. Rede no Packet Tracer (2 min)**
- Mostrar topologia
- `show ip route` no roteador
- Ping entre sites

**3. Serviços Configurados (2 min)**
- Comandos PowerShell (DHCP, DNS)
- Testar acesso negado em pasta

**4. Aplicação Web (3 min)**
- Login como RH → ver dashboard
- Tentar acessar Financeiro → 403
- Login como Admin → CRUD usuários

**5. Conclusão (1 min)**
- Recapitular CID
- Aprendizados
- Perguntas

---

## 🔧 Como Rodar o Projeto

### Aplicação Web

```bash
# 1. Criar banco de dados
mysql -u root -p < app-php/database/schema.sql
mysql -u root -p < app-php/database/seeds.sql

# 2. Configurar .env
cd app-php
cp env.example .env
# Editar .env com suas credenciais MySQL

# 3. Iniciar servidor
php -S localhost:8080 -t public

# 4. Acessar
# http://localhost:8080
# Login: eric.santos / Senha@123
```

### Scripts PowerShell (Windows Server)

```powershell
# Criar usuários e grupos
.\servidores\scripts\criar-usuarios.ps1

# Backup automático
.\servidores\scripts\backup-simulado.ps1
```

---

## ⚠️ Limitações Conhecidas

1. **HTTP:** Não usa HTTPS (ok para lab, mas NÃO para produção)
2. **Senhas Fracas:** `Senha@123` é só pra teste
3. **Sem Redundância:** Servidor matriz é ponto único de falha
4. **Sem IDS/IPS:** Ataques não são detectados automaticamente
5. **Logs Locais:** Não há centralização de logs
6. **Sem MFA:** Senha comprometida = acesso total

**Essas limitações são aceitáveis no contexto acadêmico, mas não em produção.**

---

## 📊 Resumo de Entregas

✅ **Rede:** Topologia funcional no Packet Tracer  
✅ **Serviços:** DHCP, DNS, Compartilhamento com ACLs  
✅ **App Web:** PHP + MySQL com segurança básica  
✅ **Documentação:** Relatório final + políticas + análise  
✅ **Testes:** CID validado com evidências  
✅ **Scripts:** PowerShell comentados e funcionais  

**Tempo investido:** ~60-80 horas ao longo de 8 semanas  
**Linhas de código:** ~2.000 (PHP + SQL + PowerShell)  
**Páginas de doc:** ~80 páginas

---

## 📞 Contato

**Equipe InovaSoft SSI/IP**  
Projeto Acadêmico - Desenvolvimento de Sistemas  
Novembro/2025

---

## 🎓 Referências Utilizadas

- Documentação oficial do PHP (php.net)
- Cisco Networking Academy (Packet Tracer)
- Microsoft Docs (Windows Server, PowerShell)
- OWASP Top 10 (boas práticas de segurança web)
- Tutoriais no YouTube (configuração DHCP relay, etc.)
- Anotações das aulas de SSI/IP

---

**Este é um projeto acadêmico para fins educacionais. As configurações foram simplificadas para ambiente de laboratório.**

✨ **Obrigado pela atenção!** ✨

