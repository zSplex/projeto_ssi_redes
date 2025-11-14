# ProjectHub - Sistema Interno da InovaSoft
**Projeto de Segurança de Sistemas e Redes**

## 📋 O que é isso?

O ProjectHub é a aplicação web que a gente desenvolveu pro projeto de SSI. É um sistema simples de controle de acesso onde cada departamento (RH, Financeiro, Comercial, TI) tem sua própria área protegida.

**Tecnologias usadas:**
- PHP 8.1 (backend)
- MySQL 8.0 (banco de dados)
- Bootstrap 5.3 (deixar bonito sem muito CSS)
- Servidor embutido do PHP pra testes (ou XAMPP)

---

## 🚀 Como Instalar

### Antes de começar

Você vai precisar de:
- PHP 8.1 ou mais novo (roda no XAMPP)
- MySQL 8.0 (ou MariaDB que vem no XAMPP também)
- Um terminal/prompt de comando
- Paciência (sempre tem algum erro bobeira no caminho rs)

### Passo 1: Checar se tem PHP e MySQL instalados

Abre o terminal/cmd e digita:

```bash
php -v
# Tem que aparecer algo como "PHP 8.1.x"

mysql --version
# Tem que aparecer "mysql Ver 8.0" ou "MariaDB"
```

Se não aparecer, instala o XAMPP primeiro (é mais fácil).

### Passo 2: Criar Banco de Dados

```bash
# Acessar MySQL
mysql -u root -p

# Executar
CREATE DATABASE projeto_ssi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### Passo 3: Importar Schema e Seeds

```bash
# Importar estrutura das tabelas
mysql -u root -p projeto_ssi < database/schema.sql

# Importar dados de teste
mysql -u root -p projeto_ssi < database/seeds.sql
```

### Passo 4: Configurar Ambiente

Copiar o arquivo de exemplo e ajustar credenciais:

```bash
cp .env.example .env
```

Editar `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=projeto_ssi
DB_USER=root
DB_PASS=sua_senha_mysql

APP_ENV=development
APP_DEBUG=1
APP_URL=http://localhost:8080
```

### Passo 5: Iniciar Servidor

**Opção A: Servidor Embutido PHP (Desenvolvimento)**

```bash
cd app-php
php -S localhost:8080 -t public
```

Acessar: http://localhost:8080

**Opção B: XAMPP/WAMP**

1. Copiar pasta `app-php` para `C:\xampp\htdocs\projecthub`
2. Configurar `DocumentRoot` para `public/`
3. Acessar: http://localhost/projecthub

**Opção C: Apache Configurado**

VirtualHost exemplo:

```apache
<VirtualHost *:80>
    ServerName projecthub.inovasoft.local
    DocumentRoot "C:/projetos/app-php/public"
    
    <Directory "C:/projetos/app-php/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/projecthub-error.log"
    CustomLog "logs/projecthub-access.log" common
</VirtualHost>
```

---

## 👥 Usuários de Teste

**Credenciais padrão (senha: `Senha@123` para todos):**

| Username | Senha | Departamento | Privilégios |
|----------|-------|--------------|-------------|
| eric.santos | Senha@123 | TI | Administrador (acesso total) |
| erik.doca | Senha@123 | Comercial | Usuário padrão |
| emilly.goncalves | Senha@123 | RH | Usuário padrão |
| joao.vianna | Senha@123 | Financeiro | Usuário padrão |

**⚠️ IMPORTANTE:** Essas senhas são apenas para ambiente de TESTE. Em produção:
1. Gerar senhas fortes individuais
2. Forçar troca no primeiro login
3. Implementar autenticação multifator (MFA)

---

## 🗂️ Estrutura de Pastas

```
app-php/
├── public/                 # DocumentRoot (arquivos públicos)
│   ├── index.php          # Formulário de login
│   ├── dashboard.php      # Painel principal
│   ├── rh.php             # Página do RH
│   ├── financeiro.php     # Página Financeiro
│   ├── comercial.php      # Página Comercial
│   ├── admin.php          # Painel administrativo (CRUD usuários)
│   ├── logout.php         # Encerrar sessão
│   └── assets/
│       └── style.css      # Estilos customizados
├── src/                   # Código-fonte (fora do webroot)
│   ├── config.php         # Carregamento de .env
│   ├── db.php             # Conexão PDO com MySQL
│   ├── auth.php           # Funções de autenticação
│   ├── middleware.php     # Guards de autorização
│   ├── UserRepository.php # Operações com tabela users
│   └── helpers.php        # Funções auxiliares (CSRF, sanitização)
├── database/
│   ├── schema.sql         # DDL (CREATE TABLE)
│   └── seeds.sql          # INSERT de dados de teste
├── .env.example           # Template de configuração
├── .env                   # Configuração real (não versionar!)
└── README.md              # Este arquivo
```

---

## 🔒 Funcionalidades de Segurança

### Autenticação
- ✅ Login com usuário e senha
- ✅ Senhas armazenadas com `password_hash()` (bcrypt)
- ✅ Verificação com `password_verify()`
- ✅ Regeneração de ID de sessão após login (`session_regenerate_id(true)`)

### Autorização
- ✅ Middleware `require_login()` — protege páginas autenticadas
- ✅ Middleware `require_group(['TI', 'RH'])` — restringe por departamento
- ✅ Usuários admin podem acessar todas as páginas

### Proteção contra Vulnerabilidades
- ✅ **SQL Injection:** PDO com prepared statements
- ✅ **XSS:** `htmlspecialchars()` em todos os outputs
- ✅ **CSRF:** Token CSRF em formulários críticos
- ✅ **Session Fixation:** `session_regenerate_id()` após login
- ❌ **HTTPS:** Não configurado (usar apenas HTTP em lab, HTTPS obrigatório em produção)
- ❌ **Rate Limiting:** Não implementado (vulnerável a brute force)

---

## 🧪 Testes de Aceitação

### Teste 1: Login Inválido
1. Acessar `http://localhost:8080`
2. Tentar login com `usuario_invalido` / `senha_errada`
3. **Esperado:** Mensagem "Credenciais inválidas"

### Teste 2: Login Válido
1. Login com `eric.santos` / `Senha@123`
2. **Esperado:** Redireciona para `dashboard.php`
3. **Esperado:** Exibe "Bem-vindo, Eric Santos" e grupo "TI"

### Teste 3: Controle de Acesso por Grupo
1. Login como `emilly.goncalves` (RH)
2. Acessar `rh.php` — **Esperado:** Acesso permitido
3. Tentar acessar `financeiro.php` — **Esperado:** "Acesso negado" (403)

### Teste 4: Painel Admin (CRUD de Usuários)
1. Login como `eric.santos` (admin)
2. Acessar `admin.php` — **Esperado:** Lista de usuários
3. Criar novo usuário "Teste Silva" — **Esperado:** Usuário adicionado ao banco
4. Login como `erik.doca` (não admin)
5. Tentar acessar `admin.php` — **Esperado:** "Acesso negado"

### Teste 5: Logout
1. Fazer login
2. Clicar em "Sair"
3. Tentar acessar `dashboard.php` diretamente pela URL
4. **Esperado:** Redireciona para `index.php` (login)

### Teste 6: Proteção contra SQL Injection
1. No formulário de login, tentar username: `admin' OR '1'='1`
2. **Esperado:** Login falha (prepared statements impedem injeção)

---

## 🛠️ Desenvolvimento

### Adicionar Nova Página Protegida

1. Criar arquivo em `public/`, ex: `ti.php`
2. Adicionar no início do arquivo:

```php
<?php
require_once __DIR__ . '/../src/middleware.php';
require_group(['TI']);  // Apenas grupo TI
?>
<!DOCTYPE html>
<html>
<head>
    <title>TI - ProjectHub</title>
</head>
<body>
    <h1>Painel de TI</h1>
    <p>Olá, <?= e($_SESSION['user']['name']) ?></p>
</body>
</html>
```

### Gerar Hash de Senha (para Seeds)

```bash
php -r "echo password_hash('Senha@123', PASSWORD_DEFAULT), PHP_EOL;"
```

Copiar o hash e usar no `INSERT` em `database/seeds.sql`.

---

## 📊 Banco de Dados

### Tabela: `users`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | Chave primária |
| name | VARCHAR(100) | Nome completo |
| username | VARCHAR(64) UNIQUE | Login (ex: eric.santos) |
| password_hash | VARCHAR(255) | Hash bcrypt da senha |
| group_name | ENUM | TI, RH, Financeiro, Comercial |
| is_admin | TINYINT(1) | 1 = admin, 0 = usuário padrão |
| created_at | TIMESTAMP | Data de criação |

---

## 📝 Limitações Conhecidas

- **Sem recuperação de senha:** Não há fluxo de "Esqueci minha senha"
- **Sem logs de auditoria:** Não registra logins/acessos em tabela separada
- **Sem paginação:** Lista de usuários em `admin.php` sem limite
- **Sem upload de arquivos:** Não há funcionalidade de anexar documentos
- **Sem internacionalização:** Interface apenas em PT-BR
- **Layout simples:** UI funcional, mas não polida

---

## 🚧 Melhorias Futuras

- [ ] Adicionar autenticação multifator (TOTP com Google Authenticator)
- [ ] Implementar recuperação de senha por e-mail
- [ ] Logs de auditoria (quem acessou o quê, quando)
- [ ] Paginação e busca na lista de usuários
- [ ] Perfis de usuário editáveis (avatar, e-mail, telefone)
- [ ] Gráficos no dashboard (projetos por departamento, etc.)
- [ ] Migrar para framework moderno (Laravel, Symfony)

---

## 📞 Suporte

**Equipe de Desenvolvimento:**
- Eric Santos (Infraestrutura) - eric.santos@inovasoft.local
- Erik Doca (Backend) - erik.doca@inovasoft.local

**Documentação Adicional:**
- [Políticas de Segurança](../docs/05-politicas-seguranca.md)
- [Análise de Segurança](../docs/06-analise-seguranca.md)

---

**Versão:** 1.0  
**Última atualização:** 27/10/2025  
**Licença:** Uso Acadêmico - InovaSoft

