<?php
/**
 * Página do RH - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/middleware.php';
require_once __DIR__ . '/../src/helpers.php';

// Apenas usuários do grupo RH (ou admin) podem acessar
require_group(['RH']);

$user = current_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RH - ProjectHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">ProjectHub</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Painel</a></li>
                    <li class="nav-item"><a class="nav-link active" href="rh.php">RH</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>Recursos Humanos</h2>
        <p class="text-muted">Bem-vindo, <?= e($user['name']) ?></p>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Documentos Confidenciais</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">📄 Folha de Pagamento - Outubro 2025</li>
                            <li class="list-group-item">📄 Contratos de Trabalho</li>
                            <li class="list-group-item">📄 Avaliações de Desempenho</li>
                            <li class="list-group-item">📄 Plano de Benefícios</li>
                        </ul>
                        <p class="text-muted small mt-2">
                            <strong>⚠️ Aviso:</strong> Estes documentos são confidenciais. Acesso restrito ao departamento de RH.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Ações Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary w-100 mb-2" disabled>Cadastrar Novo Funcionário</button>
                        <button class="btn btn-secondary w-100 mb-2" disabled>Gerar Relatório de Férias</button>
                        <button class="btn btn-success w-100 mb-2" disabled>Atualizar Dados de Contato</button>
                        <p class="text-muted small mt-3">
                            <em>Funcionalidades em desenvolvimento</em>
                        </p>
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
                        <p>Acesse a pasta compartilhada do RH no servidor:</p>
                        <code>\\192.168.10.10\RH</code>
                        <p class="mt-2">
                            <a href="file://192.168.10.10/RH" class="btn btn-sm btn-outline-primary">Abrir Pasta de Rede</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 py-3 bg-white border-top">
        <div class="container text-center text-muted small">
            <p>© 2025 InovaSoft - Departamento de RH</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

