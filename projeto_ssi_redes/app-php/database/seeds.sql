-- ========================================
-- Seeds (Dados de Teste) - ProjectHub
-- Projeto SSI/IP - InovaSoft
-- ========================================

USE projeto_ssi;

-- Limpar dados existentes (cuidado em produção!)
TRUNCATE TABLE users;

-- ========================================
-- Inserir Usuários de Teste
-- ========================================

-- IMPORTANTE: Os hashes abaixo correspondem à senha: Senha@123
-- Gerados com: password_hash('Senha@123', PASSWORD_DEFAULT)
-- Em produção, usar senhas fortes e únicas!

INSERT INTO users (name, username, password_hash, group_name, is_admin) VALUES
(
    'Eric Santos',
    'eric.santos',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'TI',
    1
),
(
    'Erik Doca',
    'erik.doca',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Comercial',
    0
),
(
    'Emilly Gonçalves',
    'emilly.goncalves',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'RH',
    0
),
(
    'João Pedro Vianna',
    'joao.vianna',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Financeiro',
    0
);

-- ========================================
-- Verificação
-- ========================================

SELECT 
    id,
    name,
    username,
    group_name,
    is_admin,
    created_at
FROM users
ORDER BY id;

-- Exibir contagem
SELECT 
    group_name,
    COUNT(*) as total,
    SUM(is_admin) as admins
FROM users
GROUP BY group_name;

