# Projeto SSI/IP - InovaSoft
**Infraestrutura de Rede Corporativa Segura**

---

## 📌 Sobre o Projeto

Projeto acadêmico de Segurança de Sistemas de Informação e Redes desenvolvido por alunos do 3º módulo de Desenvolvimento de Sistemas. 

**Objetivo:** Implementar uma infraestrutura de rede segura para empresa fictícia com 3 localidades, aplicando os pilares CID (Confidencialidade, Integridade, Disponibilidade).

---

## 🚀 Início Rápido

### 1. Ver Apresentação Completa

📄 **[PROJETO-SSI-APRESENTACAO.md](PROJETO-SSI-APRESENTACAO.md)** ← **COMECE AQUI!**

Este documento tem tudo: objetivos, implementação, testes, demonstração e como rodar.

### 2. Rodar a Aplicação Web (ProjectHub)

```bash
# Criar banco de dados
cd app-php
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql

# Configurar
cp env.example .env
# Editar .env com suas credenciais MySQL

# Iniciar
php -S localhost:8080 -t public
```

**Acessar:** http://localhost:8080  
**Login:** `eric.santos` / `Senha@123` (admin)

### 3. Configurar Servidores (Windows)

```powershell
# PowerShell como Administrador

# Criar usuários e grupos
.\servidores\scripts\criar-usuarios.ps1

# Backup automático
.\servidores\scripts\backup-simulado.ps1
```

---

## 📁 Estrutura do Projeto

```
projeto_ssi_redes/
│
├── PROJETO-SSI-APRESENTACAO.md    ⭐ DOCUMENTO PRINCIPAL
│
├── app-php/                        # Aplicação Web (PHP + MySQL)
│   ├── database/                   # SQL schema e seeds
│   ├── public/                     # Páginas web
│   ├── src/                        # Backend (auth, middleware, etc)
│   └── README.md                   # Instruções detalhadas
│
├── docs/                           # Documentação Técnica
│   ├── 05-politicas-seguranca.md   # Políticas (senhas, backup, etc)
│   ├── 06-analise-seguranca.md     # Análise CID + testes
│   └── 09-relatorio-final.md       # Relatório completo (18 páginas)
│
├── rede/                           # Configuração de Rede
│   ├── comandos-roteadores.md      # Comandos Cisco IOS
│   ├── ip-plan.md                  # Plano de endereçamento IP
│   └── packet-tracer-notes.md      # Guia do Packet Tracer
│
└── servidores/                     # Configuração de Serviços
    ├── dhcp/                       # Guia DHCP Windows
    ├── dns/                        # Guia DNS Windows
    ├── arquivos/                   # ACLs e compartilhamento
    └── scripts/                    # PowerShell (usuários, backup)
```

---

## 🎯 O Que Implementamos

### Rede (Cisco Packet Tracer)
- 3 sites interligados (Bauru, São Paulo, Campinas)
- Roteamento estático
- DHCP centralizado com relay
- DNS com zona `inovasoft.local`

### Segurança
- Controle de acesso por grupos (TI, RH, Financeiro, Comercial)
- ACLs em pastas compartilhadas
- Aplicação web com autenticação e autorização
- Proteção SQL Injection, XSS, CSRF

### Serviços
- DHCP: 3 escopos (uma por rede)
- DNS: 7 registros principais
- Compartilhamento: 5 pastas com permissões
- Backup: Script automatizado

---

## 📊 Resultados (Scores CID)

- **Confidencialidade:** 8/10 ✅
- **Integridade:** 7/10 ✅
- **Disponibilidade:** 6/10 ⚠️

**Testes realizados:** 10+ cenários  
**Todos os testes passaram!**

---

## 👥 Equipe

- Eric Santos (Infraestrutura)
- Erik Doca (Desenvolvimento Web)
- Emilly Gonçalves (Documentação)
- João Pedro Vianna (Redes)

---

## 🛠️ Tecnologias

- **Rede:** Cisco Packet Tracer 8.x
- **Backend:** PHP 8.1+
- **Banco:** MySQL 8.0+
- **Frontend:** Bootstrap 5.3
- **SO:** Windows Server (simulado)
- **Scripts:** PowerShell 7.x

---

## 📖 Documentação

| Documento | Descrição | Páginas |
|-----------|-----------|---------|
| [PROJETO-SSI-APRESENTACAO.md](PROJETO-SSI-APRESENTACAO.md) | **Documento principal** (começe aqui) | 12 |
| [docs/09-relatorio-final.md](docs/09-relatorio-final.md) | Relatório completo do projeto | 18 |
| [docs/05-politicas-seguranca.md](docs/05-politicas-seguranca.md) | Políticas de segurança | 10 |
| [docs/06-analise-seguranca.md](docs/06-analise-seguranca.md) | Análise CID + testes | 12 |
| [rede/ip-plan.md](rede/ip-plan.md) | Plano de endereçamento IP | 8 |
| [app-php/README.md](app-php/README.md) | Instruções da aplicação web | 6 |

---

## ⚠️ Avisos Importantes

🔴 **Projeto Acadêmico:** Configurações simplificadas para laboratório  
🔴 **Senhas Fracas:** `Senha@123` só para testes  
🔴 **Sem HTTPS:** HTTP é ok no lab, mas NÃO em produção  
🔴 **Limitações:** Reconhecidas e documentadas

**NÃO usar em produção sem adaptações de segurança!**

---

## 📞 Suporte

Dúvidas sobre o projeto? Consulte:
1. [PROJETO-SSI-APRESENTACAO.md](PROJETO-SSI-APRESENTACAO.md) - Visão geral
2. [app-php/README.md](app-php/README.md) - Aplicação web
3. Documentos em `docs/` - Detalhes técnicos

---

## 📝 Licença

Projeto acadêmico - Uso educacional apenas  
Novembro/2025 - Curso de Desenvolvimento de Sistemas

---

**⭐ Comece pelo arquivo [PROJETO-SSI-APRESENTACAO.md](PROJETO-SSI-APRESENTACAO.md)**
