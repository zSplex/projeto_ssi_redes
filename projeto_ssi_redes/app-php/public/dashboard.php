<?php
/**
 * Dashboard Principal - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/middleware.php';
require_once __DIR__ . '/../src/helpers.php';

// Exigir login
require_login();

$user = current_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ProjectHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">ProjectHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Painel</a>
                    </li>
                    
                    <?php if (has_group('RH')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="rh.php">RH</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (has_group('Financeiro')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="financeiro.php">Financeiro</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (has_group('Comercial')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="comercial.php">Comercial</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (is_admin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin</a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Bem-vindo ao ProjectHub, <?= e($user['name']) ?>!</h2>
                <p class="text-muted">Departamento: <span class="badge bg-primary"><?= e($user['group_name']) ?></span></p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Seu Perfil</h5>
                        <ul class="list-unstyled">
                            <li><strong>Nome:</strong> <?= e($user['name']) ?></li>
                            <li><strong>Usuário:</strong> <?= e($user['username']) ?></li>
                            <li><strong>Departamento:</strong> <?= e($user['group_name']) ?></li>
                            <li><strong>Privilégios:</strong> <?= $user['is_admin'] ? 'Administrador' : 'Usuário' ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Acesso Rápido</h5>
                        <div class="row">
                            <?php if (has_group('TI')): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">TI - Infraestrutura</h6>
                                        <p class="card-text small">Gerenciamento de sistemas e rede</p>
                                        <?php if (is_admin()): ?>
                                            <a href="admin.php" class="btn btn-sm btn-primary">Gerenciar Usuários</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (has_group('RH')): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Recursos Humanos</h6>
                                        <p class="card-text small">Gestão de pessoal e documentos confidenciais</p>
                                        <a href="rh.php" class="btn btn-sm btn-success">Acessar</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (has_group('Financeiro')): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Financeiro</h6>
                                        <p class="card-text small">Contas a pagar/receber e relatórios</p>
                                        <a href="financeiro.php" class="btn btn-sm btn-warning">Acessar</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (has_group('Comercial')): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Comercial</h6>
                                        <p class="card-text small">Gestão de projetos e clientes</p>
                                        <a href="comercial.php" class="btn btn-sm btn-info">Acessar</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Avisos</h6>
                    <ul class="mb-0">
                        <li>Manutenção programada para domingo, 03/11, das 00h às 06h.</li>
                        <li>Não se esqueça de fazer backup local de documentos importantes.</li>
                        <li>Em caso de problemas, contate a equipe de TI: eric.santos@inovasoft.local</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 py-3 bg-white border-top">
        <div class="container text-center text-muted small">
            <p>© 2025 InovaSoft Desenvolvimento de Sistemas - Projeto SSI/IP</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

