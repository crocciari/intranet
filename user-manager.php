<?php
require_once("user-config.php");

// Verificar se existem usuários
$users_dir = __DIR__ . '/_intra_users_json';
$has_users = false;
$has_admin = false;

if (is_dir($users_dir)) {
    $files = glob($users_dir . '/*.json');
    $has_users = count($files) > 0;
    
    // Verificar se existe admin
    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user && isset($user['role']) && $user['role'] === 'admin') {
            $has_admin = true;
            break;
        }
    }
}

// Se não tem admin, forçar criação
$force_setup = !$has_admin;

// Obter IP atual (agora usando a função do user-config.php)
$current_ip = getClientIP();
$current_user = getCurrentUser();
$is_admin = ($current_user && $current_user['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - IntraConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .setup-banner {
            background: linear-gradient(135deg, #7c5cfc 0%, #5c3cfc 100%);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            color: white;
        }
        
        .setup-banner h3 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .setup-banner p {
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .setup-banner .btn-light {
            background: white;
            color: #7c5cfc;
            font-weight: 600;
            padding: 10px 30px;
            border-radius: 8px;
        }
        
        .setup-banner .btn-light:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .user-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .user-card:hover {
            transform: translateY(-2px);
            border-color: var(--accent-primary);
            box-shadow: 0 8px 30px rgba(124, 92, 252, 0.15);
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid var(--accent-primary);
            object-fit: cover;
        }
        
        .badge-admin {
            background: #ff1744;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .badge-you {
            background: #00c853;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--text-secondary);
            opacity: 0.5;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--text-secondary);
        }
        
        .alert-info {
            background: rgba(124, 92, 252, 0.1);
            border: 1px solid var(--accent-primary);
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Se não houver admin, mostrar banner de configuração -->
        <?php if ($force_setup): ?>
        <div class="setup-banner">
            <i class="bi bi-flag-fill" style="font-size: 2rem;"></i>
            <h3>🚀 Configuração Inicial</h3>
            <p>Nenhum administrador encontrado. Crie o primeiro usuário administrador para começar.</p>
            <button class="btn btn-light" onclick="openSetupUser()">
                <i class="bi bi-person-plus me-2"></i>Criar Administrador
            </button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2><i class="bi bi-people-fill me-2"></i>Gerenciar Usuários</h2>
                    <div>
                        <?php if ($is_admin): ?>
                            <button class="btn btn-primary" onclick="openUserEditor()">
                                <i class="bi bi-person-plus me-1"></i>Novo Usuário
                            </button>
                        <?php endif; ?>
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                </div>
                
                <?php if ($current_user): ?>
                <div class="alert alert-info">
                    <i class="bi bi-person-circle me-2"></i>
                    Logado como: <strong><?= htmlspecialchars($current_user['name']) ?></strong>
                    (<?= htmlspecialchars($current_user['ip']) ?>)
                    <?php if ($is_admin): ?>
                        <span class="badge-admin ms-2">ADMIN</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i>Lista de Usuários
                        <span class="ms-auto badge badge-info" id="userCount">0</span>
                    </div>
                    <div class="card-body" id="usersList">
                        <p class="text-secondary text-center py-3">Carregando usuários...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DO EDITOR DE USUÁRIO -->
    <div class="modal fade" id="userEditorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title mono">
                        <i class="bi bi-person-fill me-2" style="color: var(--accent-primary);"></i>
                        <span id="modalTitle">$> Novo Usuário</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: var(--btn-close-filter);"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="editUserIp">
                        <div class="mb-3">
                            <label class="form-label mono">Nome *</label>
                            <input type="text" class="form-control" id="userName" placeholder="Nome completo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">Email *</label>
                            <input type="email" class="form-control" id="userEmail" placeholder="email@dominio.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">IP *</label>
                            <input type="text" class="form-control" id="userIp" placeholder="192.168.100.xxx" required>
                            <small class="text-secondary">Seu IP atual: <strong id="currentIpDisplay"><?= $current_ip ?></strong></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">Departamento</label>
                            <input type="text" class="form-control" id="userDepartment" placeholder="TI, Design, Marketing...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">Bio</label>
                            <textarea class="form-control" id="userBio" rows="2" placeholder="Breve descrição"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">Role</label>
                            <select class="form-control" id="userRole">
                                <option value="user">Usuário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <?php if ($force_setup): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Este será o <strong>primeiro administrador</strong> do sistema.
                        </div>
                        <?php endif; ?>
                    </form>
                    <div id="userResponse"></div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">
                        <i class="bi bi-check-circle me-1"></i>Salvar Usuário
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="user-manager.js"></script>
    
    <script>
        // Função para abrir criação do primeiro admin
        function openSetupUser() {
            const modal = document.getElementById('userEditorModal');
            const modalTitle = document.getElementById('modalTitle');
            
            // Pré-preencher IP atual
            document.getElementById('userIp').value = '<?= $current_ip ?>';
            document.getElementById('currentIpDisplay').textContent = '<?= $current_ip ?>';
            
            // Forçar role como admin
            document.getElementById('userRole').value = 'admin';
            document.getElementById('userRole').disabled = true;
            
            modalTitle.textContent = '$> Criar Administrador do Sistema';
            
            // Mostrar modal
            if (!userEditorModal) {
                userEditorModal = new bootstrap.Modal(modal);
            }
            userEditorModal.show();
        }
        
        // Quando o modal fechar, reativar o select role se estava desabilitado
        document.getElementById('userEditorModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('userRole').disabled = false;
        });
    </script>
</body>
</html>