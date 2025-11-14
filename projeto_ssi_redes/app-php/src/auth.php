<?php
/**
 * Autenticação de Usuários - ProjectHub
 * Projeto SSI/IP - InovaSoft
 * 
 * Esse arquivo tem as funções de login/logout
 * Usa password_hash e password_verify pra segurança
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/UserRepository.php';

/**
 * Tenta fazer login do usuário
 * Retorna os dados do usuário se deu certo, ou false se senha errada
 */
function attempt_login($username, $password) {
    $repo = new UserRepository();
    $user = $repo->findByUsername($username);
    
    if (!$user) {
        return false;
    }
    
    // Checa a senha de forma segura (usa timing-safe comparison)
    // NUNCA compara senha diretamente com ==, sempre usa password_verify!
    if (!password_verify($password, $user['password_hash'])) {
        return false; // senha errada
    }
    
    // Se chegou aqui, login OK!
    return $user;
}

/**
 * Fazer login do usuário (criar sessão)
 * 
 * @param array $user
 */
function login_user($user) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerar ID da sessão para prevenir session fixation
    session_regenerate_id(true);
    
    // Armazenar dados do usuário na sessão (sem senha!)
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'username' => $user['username'],
        'group_name' => $user['group_name'],
        'is_admin' => (int)$user['is_admin']
    ];
    
    $_SESSION['authenticated'] = true;
    $_SESSION['login_time'] = time();
}

/**
 * Fazer logout (destruir sessão)
 */
function logout_user() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Limpar todas as variáveis de sessão
    $_SESSION = [];
    
    // Destruir o cookie de sessão
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir a sessão
    session_destroy();
}

/**
 * Verificar se o usuário está autenticado
 * 
 * @return bool
 */
function is_authenticated() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

/**
 * Obter usuário logado
 * 
 * @return array|null
 */
function current_user() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['user'] ?? null;
}

/**
 * Verificar se usuário é admin
 * 
 * @return bool
 */
function is_admin() {
    $user = current_user();
    return $user && $user['is_admin'] === 1;
}

/**
 * Verificar se usuário pertence a um grupo específico
 * 
 * @param string|array $groups
 * @return bool
 */
function has_group($groups) {
    $user = current_user();
    
    if (!$user) {
        return false;
    }
    
    // Admin tem acesso a tudo
    if ($user['is_admin'] === 1) {
        return true;
    }
    
    // Normalizar para array
    $groups = is_array($groups) ? $groups : [$groups];
    
    return in_array($user['group_name'], $groups, true);
}

