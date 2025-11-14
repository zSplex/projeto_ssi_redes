-- ========================================
-- Schema do Banco de Dados - ProjectHub
-- Projeto SSI/IP - InovaSoft
-- ========================================

-- Criar banco de dados (executar apenas se não existir)
CREATE DATABASE IF NOT EXISTS projeto_ssi 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE projeto_ssi;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Nome completo do usuário',
    username VARCHAR(64) NOT NULL UNIQUE COMMENT 'Login único (ex: eric.santos)',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt da senha',
    group_name ENUM('TI', 'RH', 'Financeiro', 'Comercial') NOT NULL COMMENT 'Departamento do usuário',
    is_admin TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = administrador, 0 = usuário padrão',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação do registro',
    INDEX idx_username (username),
    INDEX idx_group (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de usuários do sistema';

-- Tabela de sessões (opcional, para controle adicional)
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de sessões ativas (opcional)';

-- ========================================
-- Verificação da estrutura
-- ========================================

SHOW TABLES;
DESCRIBE users;

