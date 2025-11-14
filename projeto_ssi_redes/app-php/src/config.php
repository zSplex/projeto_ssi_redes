<?php
/**
 * Configuração da Aplicação - ProjectHub
 * Carrega variáveis de ambiente do arquivo .env
 */

// Carregar arquivo .env (método simples, sem biblioteca externa)
function loadEnv($path) {
    if (!file_exists($path)) {
        die("Arquivo de configuração não encontrado. Copie env.example para .env e configure.");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentários
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse linha KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remover aspas se existirem
            $value = trim($value, '"\'');
            
            // Setar variável de ambiente
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Carregar .env
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../env.example';
}
loadEnv($envPath);

// Função helper para pegar config
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

// Configurações da aplicação
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'projeto_ssi'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', '0') === '1');
define('APP_URL', env('APP_URL', 'http://localhost'));

// Configuração de erros
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// Configuração de sessão
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
// Em produção, usar HTTPS e habilitar:
// ini_set('session.cookie_secure', '1');

