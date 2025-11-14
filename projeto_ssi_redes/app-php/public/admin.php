<?php
/**
 * Painel Administrativo - ProjectHub
 * CRUD de Usuários (apenas para admins)
 * Projeto SSI/IP - InovaSoft
 */

require_once __DIR__ . '/../src/middleware.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/UserRepository.php';

// Apenas administradores
require_admin();

$user = current_user();
$repo = new UserRepository();

// Processar ações (criar, editar, excluir)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $data = [
                'name' => sanitize($_POST['name']),
                'username' => sanitize($_POST['username']),
                'password' => $_POST['password'],
                'group_name' => $_POST['group_name'],
                'is_admin' => isset($_POST['is_admin']) ? 1 : 0
            ];
            
            if ($repo->usernameExists($data['username'])) {
                back_with_message('Nome de usuário já existe!', 'error');
            }
            
            if ($repo->create($data)) {
                back_with_message('Usuário criado com sucesso!', 'success');
            } else {
                back_with_message('Erro ao criar usuário.', 'error');
            }
            break;
        
        case 'delete':
            $id = (int)$_POST['id'];
            if ($id === $user['id']) {
                back_with_message('Você não pode deletar sua própria conta!', 'error');
            }
            
            if ($repo->delete($id)) {
                back_with_message('Usuário excluído com sucesso!', 'success');
            } else {
                back_with_message('Erro ao excluir usuário.', 'error');
            }
            break;
    }
}

// Listar usuários
$users = $repo->all();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ProjectHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">ProjectHub</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Painel</a></li>
                    <li class="nav-item"><a class="nav-link active" href="admin.php">Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Painel Administrativo</h2>
                <p class="text-muted">Gerenciamento de Usuários</p>
            </div>
        </div>
        
        <?php flash_message(); ?>
        
        <!-- Botão para abrir modal de criação -->
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCriar">
                    + Novo Usuário
                </button>
            </div>
        </div>
        
        <!-- Tabela de usuários -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Usuário</th>
                                    <th>Departamento</th>
                                    <th>Admin?</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= e($u['id']) ?></td>
                                    <td><?= e($u['name']) ?></td>
                                    <td><code><?= e($u['username']) ?></code></td>
                                    <td><span class="badge bg-info"><?= e($u['group_name']) ?></span></td>
                                    <td><?= $u['is_admin'] ? '✅ Sim' : '❌ Não' ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                                    <td>
                                        <?php if ($u['id'] !== $user['id']): ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                        <?php else: ?>
                                        <span class="text-muted small">(Você)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal: Criar Usuário -->
    <div class="modal fade" id="modalCriar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Criar Novo Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nome de Usuário *</label>
                            <input type="text" class="form-control" name="username" placeholder="ex: joao.silva" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Senha *</label>
                            <input type="password" class="form-control" name="password" minlength="8" required>
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Departamento *</label>
                            <select class="form-select" name="group_name" required>
                                <option value="TI">TI</option>
                                <option value="RH">RH</option>
                                <option value="Financeiro">Financeiro</option>
                                <option value="Comercial">Comercial</option>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin">
                            <label class="form-check-label" for="is_admin">
                                Conceder privilégios de administrador
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Usuário</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 py-3 bg-white border-top">
        <div class="container text-center text-muted small">
            <p>© 2025 InovaSoft - Painel Administrativo</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

