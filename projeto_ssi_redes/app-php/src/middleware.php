<?php
/**
 * Middleware - Proteção de Páginas
 * ProjectHub - InovaSoft
 * 
 * "Guards" são tipo seguranças que não deixam entrar se não tiver permissão
 * Aprendemos esse padrão nas aulas de segurança web
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

/**
 * Garante que o usuário está logado
 * Se não tiver logado, manda de volta pro login
 */
function require_login() {
    if (!is_authenticated()) {
        redirect('index.php');
    }
    
    // Renovar sessão periodicamente (a cada 30 min)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $loginTime = $_SESSION['login_time'] ?? 0;
    if (time() - $loginTime > 1800) { // 30 minutos
        session_regenerate_id(true);
        $_SESSION['login_time'] = time();
    }
}

/**
 * Exigir que usuário pertença a um grupo específico
 * 
 * @param array $allowedGroups Ex: ['TI', 'RH']
 */
function require_group($allowedGroups) {
    require_login(); // Primeiro, precisa estar logado
    
    if (!has_group($allowedGroups)) {
        http_response_code(403);
        render_error_page(403, 'Acesso Negado', 'Você não tem permissão para acessar esta página.');
    }
}

/**
 * Exigir que usuário seja administrador
 */
function require_admin() {
    require_login();
    
    if (!is_admin()) {
        http_response_code(403);
        render_error_page(403, 'Acesso Negado', 'Esta página é restrita a administradores.');
    }
}

/**
 * Renderizar página de erro
 */
function render_error_page($code, $title, $message) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $code ?> - <?= e($title) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <h1 class="display-1 text-danger"><?= $code ?></h1>
                    <h2 class="mb-4"><?= e($title) ?></h2>
                    <p class="text-muted"><?= e($message) ?></p>
                    <a href="dashboard.php" class="btn btn-primary mt-3">Voltar ao Painel</a>
                    <a href="logout.php" class="btn btn-secondary mt-3">Sair</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

