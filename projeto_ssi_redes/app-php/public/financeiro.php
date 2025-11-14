<?php
/**
 * Página do Financeiro - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/middleware.php';
require_once __DIR__ . '/../src/helpers.php';

require_group(['Financeiro']);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financeiro - ProjectHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark" href="dashboard.php">ProjectHub</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-dark" href="dashboard.php">Painel</a></li>
                    <li class="nav-item"><a class="nav-link active text-dark" href="financeiro.php">Financeiro</a></li>
                    <li class="nav-item"><a class="nav-link text-dark" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>Departamento Financeiro</h2>
        <p class="text-muted">Bem-vindo, <?= e($user['name']) ?></p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Receitas (Outubro)</h5>
                        <p class="display-6">R$ 285.400,00</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Despesas (Outubro)</h5>
                        <p class="display-6">R$ 142.300,00</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Saldo</h5>
                        <p class="display-6">R$ 143.100,00</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Contas a Pagar (Próximos 7 dias)</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Fornecedor XYZ - Material</span>
                                <span class="badge bg-danger">R$ 8.500,00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Energia Elétrica</span>
                                <span class="badge bg-warning text-dark">R$ 3.200,00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Aluguel Filial SP</span>
                                <span class="badge bg-danger">R$ 12.000,00</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Contas a Receber</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Cliente ABC Ltda - Projeto A</span>
                                <span class="badge bg-success">R$ 45.000,00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Cliente DEF SA - Consultoria</span>
                                <span class="badge bg-success">R$ 28.000,00</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Compartilhamento de Arquivos</h5>
                    </div>
                    <div class="card-body">
                        <p>Acesse a pasta compartilhada do Financeiro no servidor:</p>
                        <code>\\192.168.10.10\Financeiro</code>
                        <p class="mt-2">
                            <a href="file://192.168.10.10/Financeiro" class="btn btn-sm btn-outline-primary">Abrir Pasta de Rede</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 py-3 bg-white border-top">
        <div class="container text-center text-muted small">
            <p>© 2025 InovaSoft - Departamento Financeiro</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

