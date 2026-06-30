<!-- SIDEBAR ESQUERDA -->
<div class="col-lg-3 col-md-4">
    <!-- SERVIDORES -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-server me-2"></i>Servers
        </div>
        <div class="card-body p-0">
            <?php
            $servers = [
                'Server-One' => '106',
                'CA-Lili' => '104',
                'CA-Dan' => '101'
            ];
            
            foreach ($servers as $name => $ip):
            ?>
            <div class="server-group">
                <h6>
                    <span class="badge-server me-2">🖥️</span>
                    <?= $name ?>
                </h6>
                <ul class="list-unstyled mb-0">
                    <li><a href="https://192.168.100.<?= $ip ?>" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Localhost</a></li>
                    <li><a href="https://192.168.100.<?= $ip ?>:2344/app/login" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Careh App</a></li>
                    <li><a href="https://192.168.100.<?= $ip ?>:2344" target="_blank"><i class="bi bi-globe me-1"></i>Careh Site</a></li>
                    <li><a href="https://192.168.100.<?= $ip ?>:3143" target="_blank"><i class="bi bi-briefcase me-1"></i>Cayba</a></li>
                    <li><a href="https://192.168.100.<?= $ip ?>:3345/admin/analytics" target="_blank"><i class="bi bi-envelope me-1"></i>CAMailer</a></li>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PASTAS - LISTA A PARTIR DA RAIZ -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-folder-fill me-2"></i>Files
            <span class="ms-auto" style="font-size: 0.7rem; opacity: 0.5;">raiz</span>
        </div>
        <ul class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
            <?php 
            // Listar a partir da raiz do projeto
            $root_dir = dirname(__DIR__);
            listarArquivos($root_dir); 
            ?>
        </ul>
    </div>

    <!-- WORKFLOW -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-calendar-event me-2"></i>Workflow
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Team Building</h6>
                <small class="text-secondary"><?= date('j \d\e F \d\e Y') ?></small>
            </li>
            <li class="list-group-item">
                <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Tech Talk</h6>
                <small class="text-secondary"><?= date('H:i:s') ?></small>
            </li>
        </ul>
    </div>

    <!-- CONEXÕES - USUÁRIOS DO SISTEMA -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-people-fill me-2"></i>Connections
            <span class="ms-auto" style="font-size: 0.7rem; opacity: 0.5;" id="userCountSidebar">0</span>
        </div>
        <ul class="list-group list-group-flush" id="connectionsList">
            <li class="list-group-item text-center text-secondary py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                Carregando usuários...
            </li>
        </ul>
    </div>
</div>

<!-- Script para carregar conexões -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadConnections();
});

async function loadConnections() {
    const connectionsList = document.getElementById('connectionsList');
    const userCountSidebar = document.getElementById('userCountSidebar');
    
    if (!connectionsList) return;
    
    try {
        const response = await fetch('/intranet/list-users.php');
        const data = await response.json();
        
        if (data.success && data.users && data.users.length > 0) {
            // Atualizar contador
            if (userCountSidebar) {
                userCountSidebar.textContent = data.total;
            }
            
            // Pegar o IP atual (do elemento ou via função)
            const currentIp = getCurrentIPFromPage();
            
            connectionsList.innerHTML = data.users.map(user => {
                const isCurrentUser = user.ip === currentIp;
                const statusDot = isCurrentUser ? '🟢' : '⚪';
                const statusText = isCurrentUser ? ' (Você)' : '';
                
                // Gerar avatar se não tiver
                const avatar = user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=7c5cfc&color=fff&size=64`;
                
                return `
                <li class="list-group-item d-flex justify-content-between align-items-center" style="padding: 10px 16px;">
                    <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                        <img src="${avatar}" alt="${user.name}" class="avatar-circle" style="width: 36px; height: 36px; border-width: 2px; flex-shrink: 0;">
                        <div style="min-width: 0;">
                            <h6 class="mb-0" style="font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${escapeHtml(user.name)} ${statusText}
                            </h6>
                            <small class="text-secondary" style="font-size: 0.7rem;">
                                ${user.department ? escapeHtml(user.department) : 'Sem departamento'}
                                ${user.role === 'admin' ? ' • <span style="color: #ff1744;">Admin</span>' : ''}
                            </small>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                        <span style="font-size: 0.7rem; opacity: 0.5;">${statusDot}</span>
                        ${!isCurrentUser ? `
                            <button class="btn btn-primary btn-sm" onclick="connectUser('${user.ip}')" style="padding: 2px 8px; font-size: 0.7rem;">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        ` : ''}
                    </div>
                </li>
            `}).join('');
        } else {
            connectionsList.innerHTML = `
                <li class="list-group-item text-center text-secondary py-3">
                    <i class="bi bi-people" style="font-size: 1.5rem; display: block; margin-bottom: 8px; opacity: 0.3;"></i>
                    Nenhum usuário cadastrado
                </li>
            `;
            if (userCountSidebar) {
                userCountSidebar.textContent = '0';
            }
        }
    } catch (error) {
        console.error('Erro ao carregar conexões:', error);
        connectionsList.innerHTML = `
            <li class="list-group-item text-center text-danger py-3">
                <i class="bi bi-exclamation-triangle"></i>
                Erro ao carregar usuários
            </li>
        `;
    }
}

// Função para obter o IP atual da página
function getCurrentIPFromPage() {
    // Tentar pegar do elemento na página
    const ipDisplay = document.getElementById('currentIpDisplay');
    if (ipDisplay) {
        return ipDisplay.textContent.trim();
    }
    
    // Tentar pegar do atributo data
    const body = document.body;
    if (body && body.dataset.currentIp) {
        return body.dataset.currentIp;
    }
    
    // Fallback: tentar via AJAX
    return '0.0.0.0';
}

// Função para escapar HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Função para conectar com usuário
function connectUser(ip) {
    alert('🔗 Solicitação de conexão enviada para o usuário com IP: ' + ip);
    // Aqui você pode implementar a lógica de conexão
    // Por exemplo, abrir um chat, enviar notificação, etc.
    console.log('Conectando com usuário:', ip);
}
</script>

