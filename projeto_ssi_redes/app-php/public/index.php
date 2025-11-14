<?php
/**
 * Página de Login - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

// Se já estiver logado, redirecionar para dashboard
if (is_authenticated()) {
    redirect('dashboard.php');
}

$error = null;

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $user = attempt_login($username, $password);
        
        if ($user) {
            login_user($user);
            redirect('dashboard.php');
        } else {
            $error = 'Credenciais inválidas. Verifique seu usuário e senha.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ProjectHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 text-primary fw-bold">ProjectHub</h1>
                            <p class="text-muted">Sistema Interno - InovaSoft</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= e($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuário</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    placeholder="ex: eric.santos"
                                    value="<?= e($_POST['username'] ?? '') ?>"
                                    required
                                    autofocus
                                >
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Senha</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password"
                                    placeholder="Digite sua senha"
                                    required
                                >
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Entrar
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center text-muted small">
                            <p class="mb-1">Usuários de teste:</p>
                            <ul class="list-unstyled">
                                <li><code>eric.santos</code> (TI - Admin)</li>
                                <li><code>emilly.goncalves</code> (RH)</li>
                                <li><code>joao.vianna</code> (Financeiro)</li>
                                <li><code>erik.doca</code> (Comercial)</li>
                            </ul>
                            <p class="mt-2">Senha padrão: <code>Senha@123</code></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3 text-muted small">
                    <p>© 2025 InovaSoft - Projeto SSI/IP</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

