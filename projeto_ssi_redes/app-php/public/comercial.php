<?php
/**
 * Página do Comercial - ProjectHub
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/middleware.php';
require_once __DIR__ . '/../src/helpers.php';

require_group(['Comercial']);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comercial - ProjectHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">ProjectHub</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Painel</a></li>
                    <li class="nav-item"><a class="nav-link active" href="comercial.php">Comercial</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>Departamento Comercial</h2>
        <p class="text-muted">Bem-vindo, <?= e($user['name']) ?></p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Propostas Enviadas</h5>
                        <p class="display-6">12</p>
                        <small>Este mês</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Vendas Fechadas</h5>
                        <p class="display-6">7</p>
                        <small>Taxa de conversão: 58%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Em Negociação</h5>
                        <p class="display-6">5</p>
                        <small>Acompanhar de perto</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Projetos em Andamento</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Projeto</th>
                                    <th>Etapa</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Empresa ABC</td>
                                    <td>Sistema de Estoque</td>
                                    <td><span class="badge bg-primary">Desenvolvimento</span></td>
                                    <td>R$ 45.000</td>
                                </tr>
                                <tr>
                                    <td>Organização XYZ</td>
                                    <td>ERP Customizado</td>
                                    <td><span class="badge bg-warning text-dark">Homologação</span></td>
                                    <td>R$ 120.000</td>
                                </tr>
                                <tr>
                                    <td>Indústria DEF</td>
                                    <td>Integração API</td>
                                    <td><span class="badge bg-success">Entrega</span></td>
                                    <td>R$ 28.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Ações</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary w-100 mb-2" disabled>Nova Proposta</button>
                        <button class="btn btn-secondary w-100 mb-2" disabled>Relatório de Vendas</button>
                        <button class="btn btn-success w-100 mb-2" disabled>Follow-up de Leads</button>
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
                        <p>Acesse a pasta compartilhada do Comercial no servidor:</p>
                        <code>\\192.168.10.10\Comercial</code>
                        <p class="mt-2">
                            <a href="file://192.168.10.10/Comercial" class="btn btn-sm btn-outline-primary">Abrir Pasta de Rede</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 py-3 bg-white border-top">
        <div class="container text-center text-muted small">
            <p>© 2025 InovaSoft - Departamento Comercial</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

