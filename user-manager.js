// ============================================
// USER MANAGER - JavaScript
// ============================================

let userEditorModal = null;
let editingUserIp = null;
let isFirstUser = false;

// Carregar usuários
async function loadUsers() {
    const usersList = document.getElementById('usersList');
    const userCount = document.getElementById('userCount');
    
    if (!usersList) return;
    
    try {
        const response = await fetch('list-users.php');
        const data = await response.json();
        
        if (data.success && data.users.length > 0) {
            usersList.innerHTML = data.users.map(user => `
                <div class="user-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center gap-3">
                            <img src="${user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=7c5cfc&color=fff&size=64'}" 
                                 alt="${user.name}" 
                                 class="user-avatar">
                            <div>
                                <h6 class="mb-0">
                                    <i class="bi bi-person-fill me-1" style="color: var(--accent-primary);"></i>
                                    ${escapeHtml(user.name)}
                                    ${user.role === 'admin' ? ' <span class="badge-admin">ADMIN</span>' : ''}
                                    ${user.ip === getCurrentIP() ? ' <span class="badge-you">VOCÊ</span>' : ''}
                                    ${user.is_first_user ? ' <span class="badge badge-info">Fundador</span>' : ''}
                                </h6>
                                <small class="text-secondary">
                                    <i class="bi bi-envelope me-1"></i>${escapeHtml(user.email)}
                                </small>
                                <br>
                                <small class="text-secondary">
                                    <i class="bi bi-hdd-network me-1"></i>${escapeHtml(user.ip)}
                                    ${user.department ? ` • <i class="bi bi-building me-1"></i>${escapeHtml(user.department)}` : ''}
                                </small>
                                ${user.bio ? `<p class="mb-0 text-secondary mt-1" style="font-size: 0.85rem;">${escapeHtml(user.bio)}</p>` : ''}
                            </div>
                        </div>
                        <div class="btn-group ms-2" style="flex-shrink: 0;">
                            <button class="btn btn-outline-primary btn-sm" onclick="editUser('${user.ip}')" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteUser('${user.ip}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
            userCount.textContent = data.total;
        } else {
            usersList.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <h4>Nenhum usuário cadastrado</h4>
                    <p>Clique em "Novo Usuário" para adicionar o primeiro usuário.</p>
                </div>
            `;
            userCount.textContent = '0';
        }
    } catch (error) {
        console.error('Erro ao carregar usuários:', error);
        usersList.innerHTML = `<p class="text-danger text-center py-3">Erro ao carregar usuários: ${error.message}</p>`;
    }
}

// Função para obter IP atual
function getCurrentIP() {
    // Tentar pegar do elemento ou retornar o IP do servidor
    const ipDisplay = document.getElementById('currentIpDisplay');
    if (ipDisplay) {
        return ipDisplay.textContent;
    }
    return '<?= $current_ip ?>';
}

// Abrir editor de usuário
function openUserEditor(ip = null) {
    editingUserIp = ip;
    const modal = document.getElementById('userEditorModal');
    const modalTitle = document.getElementById('modalTitle');
    const responseDiv = document.getElementById('userResponse');
    
    responseDiv.innerHTML = '';
    document.getElementById('userForm').reset();
    document.getElementById('editUserIp').value = '';
    document.getElementById('userIp').disabled = false;
    document.getElementById('userRole').disabled = false;
    
    if (ip) {
        modalTitle.textContent = '$> Editar Usuário';
        loadUserData(ip);
    } else {
        modalTitle.textContent = '$> Novo Usuário';
        // Pré-preencher IP atual se disponível
        const currentIp = getCurrentIP();
        if (currentIp && currentIp !== '0.0.0.0') {
            document.getElementById('userIp').value = currentIp;
        }
    }
    
    if (!userEditorModal) {
        userEditorModal = new bootstrap.Modal(modal);
    }
    userEditorModal.show();
}

// Carregar dados do usuário para edição
async function loadUserData(ip) {
    try {
        const response = await fetch('list-users.php');
        const data = await response.json();
        
        if (data.success) {
            const user = data.users.find(u => u.ip === ip);
            if (user) {
                document.getElementById('editUserIp').value = ip;
                document.getElementById('userName').value = user.name || '';
                document.getElementById('userEmail').value = user.email || '';
                document.getElementById('userIp').value = user.ip || '';
                document.getElementById('userIp').disabled = true; // IP não pode ser alterado
                document.getElementById('userDepartment').value = user.department || '';
                document.getElementById('userBio').value = user.bio || '';
                if (document.getElementById('userRole')) {
                    document.getElementById('userRole').value = user.role || 'user';
                }
            }
        }
    } catch (error) {
        console.error('Erro ao carregar usuário:', error);
        alert('Erro ao carregar dados do usuário');
    }
}

// Salvar usuário
async function saveUser() {
    const name = document.getElementById('userName').value.trim();
    const email = document.getElementById('userEmail').value.trim();
    const ip = document.getElementById('userIp').value.trim();
    const department = document.getElementById('userDepartment').value.trim();
    const bio = document.getElementById('userBio').value.trim();
    const role = document.getElementById('userRole') ? document.getElementById('userRole').value : 'user';
    const editIp = document.getElementById('editUserIp').value;
    
    const responseDiv = document.getElementById('userResponse');
    
    if (!name) {
        responseDiv.innerHTML = `<div class="alert alert-danger">Nome é obrigatório</div>`;
        document.getElementById('userName').focus();
        return;
    }
    
    if (!email) {
        responseDiv.innerHTML = `<div class="alert alert-danger">Email é obrigatório</div>`;
        document.getElementById('userEmail').focus();
        return;
    }
    
    if (!ip) {
        responseDiv.innerHTML = `<div class="alert alert-danger">IP é obrigatório</div>`;
        document.getElementById('userIp').focus();
        return;
    }
    
    // Validar formato do IP
    const ipPattern = /^(\d{1,3}\.){3}\d{1,3}$/;
    if (!ipPattern.test(ip)) {
        responseDiv.innerHTML = `<div class="alert alert-danger">IP inválido. Use o formato: 192.168.100.xxx</div>`;
        document.getElementById('userIp').focus();
        return;
    }
    
    // Validar email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        responseDiv.innerHTML = `<div class="alert alert-danger">Email inválido</div>`;
        document.getElementById('userEmail').focus();
        return;
    }
    
    // Mostrar loading
    responseDiv.innerHTML = `<div class="alert alert-info">Salvando usuário...</div>`;
    
    try {
        const endpoint = editIp ? 'edit-user.php' : 'save-user.php';
        const payload = editIp ? {
            ip: editIp,
            name: name,
            email: email,
            department: department,
            bio: bio,
            role: role
        } : {
            ip: ip,
            name: name,
            email: email,
            department: department,
            bio: bio,
            role: role
        };
        
        const response = await fetch(endpoint, {
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
                if (userEditorModal) userEditorModal.hide();
                loadUsers();
                // Recarregar a página se for o primeiro usuário
                if (result.data && result.data.is_first_user) {
                    setTimeout(() => location.reload(), 500);
                }
            }, 1500);
        } else {
            responseDiv.innerHTML = `<div class="alert alert-danger">❌ ${result.error || 'Erro ao salvar'}</div>`;
        }
    } catch (error) {
        console.error('Erro:', error);
        responseDiv.innerHTML = `<div class="alert alert-danger">❌ Erro ao salvar usuário: ${error.message}</div>`;
    }
}

// Editar usuário
function editUser(ip) {
    openUserEditor(ip);
}

// Deletar usuário
async function deleteUser(ip) {
    if (!confirm(`Tem certeza que deseja excluir o usuário com IP ${ip}?`)) {
        return;
    }
    
    try {
        const response = await fetch('delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ip: ip })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Usuário excluído com sucesso!');
            loadUsers();
        } else {
            alert('Erro: ' + (result.error || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao excluir usuário:', error);
        alert('Erro ao excluir usuário: ' + error.message);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Carregar usuários quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});