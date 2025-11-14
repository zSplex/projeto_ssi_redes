<?php
/**
 * Funções Auxiliares - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

/**
 * Escapar output para prevenir XSS
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Gerar token CSRF
 */
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function csrf_check() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token = $_POST['csrf_token'] ?? '';
    
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Token CSRF inválido. Recarregue a página e tente novamente.');
    }
}

/**
 * Redirecionar para outra página
 */
function redirect($path) {
    $baseUrl = rtrim(APP_URL, '/');
    header("Location: $baseUrl/$path");
    exit;
}

/**
 * Redirecionar de volta com mensagem
 */
function back_with_message($message, $type = 'error') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    
    header("Location: " . $_SERVER['HTTP_REFERER'] ?? '/');
    exit;
}

/**
 * Exibir mensagem flash (uma vez)
 */
function flash_message() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        $alertClass = match($type) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info'
        };
        
        echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>";
        echo e($message);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
    }
}

/**
 * Validar entrada (exemplo básico)
 */
function validate_required($field, $value) {
    if (empty($value)) {
        back_with_message("O campo '$field' é obrigatório.");
    }
}

/**
 * Sanitizar string
 */
function sanitize($string) {
    return trim(strip_tags($string));
}

