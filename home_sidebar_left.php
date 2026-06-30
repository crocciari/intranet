<!-- SIDEBAR ESQUERDA -->
<div class="col-lg-3 col-md-4">
    <!-- SERVIDORES -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-server me-2"></i>Servers
            <button class="btn btn-primary btn-sm ms-auto" onclick="openServerEditor()" title="Gerenciar Servidores">
                <i class="bi bi-gear"></i>
            </button>
        </div>
        <div class="card-body p-0" id="serversList">
            <div class="text-center text-secondary py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                Carregando servidores...
            </div>
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

<!-- MODAL DO EDITOR DE SERVIDORES -->
<div class="modal fade" id="serverEditorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title mono">
                    <i class="bi bi-server me-2" style="color: var(--accent-primary);"></i>
                    <span id="serverModalTitle">$> Gerenciar Servidores</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: var(--btn-close-filter);"></button>
            </div>
            <div class="modal-body">
                <!-- Lista de servidores -->
                <div id="serverListContainer">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Servidores</h6>
                        <button class="btn btn-primary btn-sm" onclick="addServer()">
                            <i class="bi bi-plus-circle me-1"></i>Novo Servidor
                        </button>
                    </div>
                    <div id="serverManagerList">
                        <p class="text-secondary text-center py-3">Carregando servidores...</p>
                    </div>
                </div>
                
                <!-- Formulário de edição -->
                <div id="serverFormContainer" style="display: none;">
                    <hr>
                    <h6 id="serverFormTitle"><i class="bi bi-pencil me-2"></i>Editar Servidor</h6>
                    <form id="serverForm">
                        <input type="hidden" id="editServerId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label mono">Nome do Servidor *</label>
                                <input type="text" class="form-control" id="serverName" placeholder="Ex: Server-One" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label mono">IP *</label>
                                <input type="text" class="form-control" id="serverIp" placeholder="Ex: 106" required>
                                <small class="text-secondary">Apenas o último octeto (ex: 106)</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">Base IP</label>
                            <input type="text" class="form-control" id="serverBaseIp" placeholder="192.168.100." value="192.168.100.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label mono">Serviços</label>
                            <div id="servicesList">
                                <div class="service-item d-flex gap-2 mb-2">
                                    <input type="text" class="form-control service-name" placeholder="Nome do serviço">
                                    <input type="text" class="form-control service-url" placeholder="URL">
                                    <input type="text" class="form-control service-icon" placeholder="Ícone (bi-...)" value="bi-box-arrow-up-right">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeService(this)">×</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addServiceField()">
                                <i class="bi bi-plus me-1"></i>Adicionar Serviço
                            </button>
                        </div>
                        <div id="serverFormResponse"></div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-secondary" onclick="cancelServerForm()">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="saveServer()">Salvar Servidor</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script para carregar servidores e conexões -->
<script>
// ============================================
// SERVER MANAGER
// ============================================

let serverEditorModal = null;
let editingServerId = null;

// Carregar servidores
async function loadServers() {
    const serversList = document.getElementById('serversList');
    const serverManagerList = document.getElementById('serverManagerList');
    
    if (!serversList) return;
    
    try {
        const response = await fetch('list-servers.php');
        const data = await response.json();
        
        if (data.success && data.servers && data.servers.length > 0) {
            // Renderizar na sidebar
            serversList.innerHTML = data.servers.map(server => `
                <div class="server-group">
                    <h6>
                        <span class="badge-server me-2">🖥️</span>
                        ${escapeHtml(server.name)}
                        <small class="text-secondary" style="font-size: 0.7rem;">(${escapeHtml(server.ip)})</small>
                    </h6>
                    <ul class="list-unstyled mb-0">
                        ${server.services.map(service => `
                            <li>
                                <a href="${escapeHtml(service.url)}" target="_blank">
                                    <i class="bi ${escapeHtml(service.icon || 'bi-box-arrow-up-right')} me-1"></i>
                                    ${escapeHtml(service.name)}
                                </a>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `).join('');
            
            // Renderizar no gerenciador
            if (serverManagerList) {
                serverManagerList.innerHTML = data.servers.map(server => `
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2" style="background: var(--bg-secondary); border-radius: 8px; border: 1px solid var(--border-color);">
                        <div>
                            <strong>${escapeHtml(server.name)}</strong>
                            <span class="text-secondary ms-2" style="font-size: 0.8rem;">${escapeHtml(server.ip)}</span>
                            <span class="text-secondary ms-2" style="font-size: 0.7rem;">${server.services.length} serviços</span>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary btn-sm me-1" onclick="editServer('${escapeHtml(server.id)}')" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteServer('${escapeHtml(server.id)}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }
        } else {
            serversList.innerHTML = `
                <div class="server-group text-center text-secondary py-3">
                    <i class="bi bi-server" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mb-0">Nenhum servidor cadastrado</p>
                    <button class="btn btn-primary btn-sm mt-2" onclick="openServerEditor()">
                        <i class="bi bi-plus-circle me-1"></i>Adicionar
                    </button>
                </div>
            `;
            if (serverManagerList) {
                serverManagerList.innerHTML = `
                    <div class="text-center text-secondary py-3">
                        <i class="bi bi-server" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mb-0">Nenhum servidor cadastrado</p>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Erro ao carregar servidores:', error);
        serversList.innerHTML = `
            <div class="server-group text-danger text-center py-3">
                <i class="bi bi-exclamation-triangle"></i>
                Erro ao carregar servidores
            </div>
        `;
    }
}

// Abrir gerenciador de servidores
function openServerEditor() {
    const modal = document.getElementById('serverEditorModal');
    if (!serverEditorModal) {
        serverEditorModal = new bootstrap.Modal(modal);
    }
    
    // Resetar formulário
    document.getElementById('serverFormContainer').style.display = 'none';
    document.getElementById('serverListContainer').style.display = 'block';
    document.getElementById('serverModalTitle').textContent = '$> Gerenciar Servidores';
    
    // Carregar lista
    loadServers();
    serverEditorModal.show();
}

// Adicionar novo servidor
function addServer() {
    document.getElementById('serverListContainer').style.display = 'none';
    document.getElementById('serverFormContainer').style.display = 'block';
    document.getElementById('serverFormTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Novo Servidor';
    document.getElementById('editServerId').value = '';
    document.getElementById('serverName').value = '';
    document.getElementById('serverIp').value = '';
    document.getElementById('serverBaseIp').value = '192.168.100.';
    document.getElementById('serverFormResponse').innerHTML = '';
    
    // Resetar serviços
    const servicesList = document.getElementById('servicesList');
    servicesList.innerHTML = `
        <div class="service-item d-flex gap-2 mb-2">
            <input type="text" class="form-control service-name" placeholder="Nome do serviço">
            <input type="text" class="form-control service-url" placeholder="URL">
            <input type="text" class="form-control service-icon" placeholder="Ícone (bi-...)" value="bi-box-arrow-up-right">
            <button type="button" class="btn btn-outline-danger" onclick="removeService(this)">×</button>
        </div>
    `;
}

// Editar servidor
async function editServer(serverId) {
    try {
        const response = await fetch('list-servers.php');
        const data = await response.json();
        
        if (data.success) {
            const server = data.servers.find(s => s.id === serverId);
            if (server) {
                editingServerId = serverId;
                document.getElementById('serverListContainer').style.display = 'none';
                document.getElementById('serverFormContainer').style.display = 'block';
                document.getElementById('serverFormTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Servidor';
                document.getElementById('editServerId').value = server.id;
                document.getElementById('serverName').value = server.name || '';
                document.getElementById('serverIp').value = server.ip || '';
                document.getElementById('serverBaseIp').value = server.base_ip || '192.168.100.';
                document.getElementById('serverFormResponse').innerHTML = '';
                
                // Preencher serviços
                const servicesList = document.getElementById('servicesList');
                servicesList.innerHTML = '';
                if (server.services && server.services.length > 0) {
                    server.services.forEach(service => {
                        addServiceField(service.name, service.url, service.icon);
                    });
                } else {
                    addServiceField();
                }
            }
        }
    } catch (error) {
        console.error('Erro ao carregar servidor:', error);
        alert('Erro ao carregar dados do servidor');
    }
}

// Cancelar formulário
function cancelServerForm() {
    document.getElementById('serverFormContainer').style.display = 'none';
    document.getElementById('serverListContainer').style.display = 'block';
    loadServers();
}

// Adicionar campo de serviço
function addServiceField(name = '', url = '', icon = 'bi-box-arrow-up-right') {
    const servicesList = document.getElementById('servicesList');
    const div = document.createElement('div');
    div.className = 'service-item d-flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" class="form-control service-name" placeholder="Nome do serviço" value="${escapeHtml(name)}">
        <input type="text" class="form-control service-url" placeholder="URL" value="${escapeHtml(url)}">
        <input type="text" class="form-control service-icon" placeholder="Ícone (bi-...)" value="${escapeHtml(icon)}">
        <button type="button" class="btn btn-outline-danger" onclick="removeService(this)">×</button>
    `;
    servicesList.appendChild(div);
}

// Remover serviço
function removeService(button) {
    const item = button.closest('.service-item');
    if (item) {
        const items = document.querySelectorAll('.service-item');
        if (items.length > 1) {
            item.remove();
        } else {
            alert('É necessário ter pelo menos um serviço.');
        }
    }
}

// Salvar servidor
async function saveServer() {
    const id = document.getElementById('editServerId').value;
    const name = document.getElementById('serverName').value.trim();
    const ip = document.getElementById('serverIp').value.trim();
    const baseIp = document.getElementById('serverBaseIp').value.trim();
    const responseDiv = document.getElementById('serverFormResponse');
    
    if (!name) {
        responseDiv.innerHTML = `<div class="alert alert-danger">Nome do servidor é obrigatório</div>`;
        document.getElementById('serverName').focus();
        return;
    }
    
    if (!ip) {
        responseDiv.innerHTML = `<div class="alert alert-danger">IP é obrigatório</div>`;
        document.getElementById('serverIp').focus();
        return;
    }
    
    // Coletar serviços
    const serviceItems = document.querySelectorAll('.service-item');
    const services = [];
    serviceItems.forEach(item => {
        const nameInput = item.querySelector('.service-name');
        const urlInput = item.querySelector('.service-url');
        const iconInput = item.querySelector('.service-icon');
        if (nameInput && urlInput && nameInput.value.trim() && urlInput.value.trim()) {
            services.push({
                name: nameInput.value.trim(),
                url: urlInput.value.trim(),
                icon: iconInput ? iconInput.value.trim() || 'bi-box-arrow-up-right' : 'bi-box-arrow-up-right'
            });
        }
    });
    
    if (services.length === 0) {
        responseDiv.innerHTML = `<div class="alert alert-danger">É necessário ter pelo menos um serviço</div>`;
        return;
    }
    
    responseDiv.innerHTML = `<div class="alert alert-info">Salvando servidor...</div>`;
    
    try {
        const payload = {
            id: id || undefined,
            name: name,
            ip: ip,
            base_ip: baseIp || '192.168.100.',
            services: services
        };
        
        const response = await fetch('save-server.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            responseDiv.innerHTML = `<div class="alert alert-success">✅ ${result.message}</div>`;
            setTimeout(() => {
                cancelServerForm();
                loadServers();
            }, 1500);
        } else {
            responseDiv.innerHTML = `<div class="alert alert-danger">❌ ${result.error || 'Erro ao salvar'}</div>`;
        }
    } catch (error) {
        console.error('Erro:', error);
        responseDiv.innerHTML = `<div class="alert alert-danger">❌ Erro ao salvar servidor: ${error.message}</div>`;
    }
}

// Deletar servidor
async function deleteServer(serverId) {
    if (!confirm('Tem certeza que deseja excluir este servidor?')) {
        return;
    }
    
    try {
        const response = await fetch('delete-server.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: serverId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Servidor excluído com sucesso!');
            loadServers();
        } else {
            alert('Erro: ' + (result.error || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao excluir servidor:', error);
        alert('Erro ao excluir servidor: ' + error.message);
    }
}

// ============================================
// CONNECTIONS (mantido do original)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    loadServers();
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
            if (userCountSidebar) {
                userCountSidebar.textContent = data.total;
            }
            
            const currentIp = getCurrentIPFromPage();
            
            connectionsList.innerHTML = data.users.map(user => {
                const isCurrentUser = user.ip === currentIp;
                const statusDot = isCurrentUser ? '🟢' : '⚪';
                const statusText = isCurrentUser ? ' (Você)' : '';
                
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

function getCurrentIPFromPage() {
    const ipDisplay = document.getElementById('currentIpDisplay');
    if (ipDisplay) {
        return ipDisplay.textContent.trim();
    }
    const body = document.body;
    if (body && body.dataset.currentIp) {
        return body.dataset.currentIp;
    }
    return '0.0.0.0';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function connectUser(ip) {
    alert('🔗 Solicitação de conexão enviada para o usuário com IP: ' + ip);
    console.log('Conectando com usuário:', ip);
}
</script>