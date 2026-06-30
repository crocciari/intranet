// ============================================
// EDITOR.JS - COMPLETO E REESCRITO
// Gerenciamento de Posts com Markdown e Paginação
// ============================================

// ============================================
// VARIÁVEIS GLOBAIS
// ============================================
let editingFile = null;
let editorModal = null;
let currentUser = null;

// Variáveis de Paginação
let currentPage = 1;
let postsPerPage = 10;
let allPostsData = [];
let filteredPostsData = [];
let searchTimer = null;
let isSearching = false;

// ============================================
// FUNÇÕES DE UTILIDADE (DEFINIDAS PRIMEIRO)
// ============================================

// Função para obter a base path
function getBasePath() {
    return window.location.pathname.includes('/intranet/') ? '/intranet/' : '/';
}

// Função para escapar HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Função para formatar data
function formatDate(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateStr;
    }
}

// Função para renderizar Markdown
function renderMarkdown(text) {
    if (!text) return '';
    
    // Se marked.js estiver disponível, usar ele
    if (typeof marked !== 'undefined') {
        try {
            marked.setOptions({
                breaks: true,
                gfm: true,
                headerIds: false,
                mangle: false
            });
            return marked.parse(text);
        } catch (e) {
            console.warn('Erro ao renderizar Markdown:', e);
            return text.replace(/\n/g, '<br>');
        }
    }
    
    // Fallback: converter quebras de linha
    return text.replace(/\n/g, '<br>');
}

// Função para mostrar/ocultar loading
function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = show ? 'flex' : 'none';
    }
}

// ============================================
// FUNÇÕES AUXILIARES DO EDITOR
// ============================================

// Inserir texto com wrappers
function insertText(before, after = '') {
    const textarea = document.getElementById('postContent');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const textBefore = textarea.value.substring(0, start);
    const textAfter = textarea.value.substring(end);
    
    // Se não houver seleção, inserir apenas o before
    if (!selectedText) {
        textarea.value = textBefore + before + after + textAfter;
        const newPos = start + before.length;
        textarea.selectionStart = newPos;
        textarea.selectionEnd = newPos;
    } else {
        // Se houver seleção, aplicar o wrapper
        textarea.value = textBefore + before + selectedText + after + textAfter;
        const newStart = start + before.length;
        const newEnd = newStart + selectedText.length;
        textarea.selectionStart = newStart;
        textarea.selectionEnd = newEnd;
    }
    
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
    
    // Atualizar preview se estiver visível
    if (document.getElementById('previewArea')?.style.display !== 'none') {
        updatePreview();
    }
}

// Inserir bloco de código com linguagem específica
function insertCodeBlock(language = '') {
    const textarea = document.getElementById('postContent');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const textBefore = textarea.value.substring(0, start);
    const textAfter = textarea.value.substring(end);
    
    const langTag = language ? language : '';
    const code = selectedText || 'digite seu código aqui';
    
    // Construir o bloco de código
    const codeBlock = '```' + langTag + '\n' + code + '\n```';
    
    // Se houver seleção, substituir; senão, inserir no cursor
    if (selectedText) {
        textarea.value = textBefore + codeBlock + textAfter;
        const newStart = start + langTag.length + 4; // ``` + lang + \n
        const newEnd = newStart + code.length;
        textarea.selectionStart = newStart;
        textarea.selectionEnd = newEnd;
    } else {
        // Inserir com o cursor posicionado dentro do bloco
        textarea.value = textBefore + codeBlock + textAfter;
        const newPos = start + langTag.length + 4; // ``` + lang + \n
        textarea.selectionStart = newPos;
        textarea.selectionEnd = newPos;
    }
    
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
    
    // Atualizar preview se estiver visível
    if (document.getElementById('previewArea')?.style.display !== 'none') {
        updatePreview();
    }
}

// Inserir tabela
function insertTable() {
    const textarea = document.getElementById('postContent');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, start);
    const textAfter = textarea.value.substring(start);
    
    const table = `
| Cabeçalho 1 | Cabeçalho 2 | Cabeçalho 3 |
|-------------|-------------|-------------|
| Linha 1, Col 1 | Linha 1, Col 2 | Linha 1, Col 3 |
| Linha 2, Col 1 | Linha 2, Col 2 | Linha 2, Col 3 |
| Linha 3, Col 1 | Linha 3, Col 2 | Linha 3, Col 3 |
`;
    
    textarea.value = textBefore + table + textAfter;
    const newPos = start + table.length;
    textarea.selectionStart = newPos;
    textarea.selectionEnd = newPos;
    
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
    
    if (document.getElementById('previewArea')?.style.display !== 'none') {
        updatePreview();
    }
}

// Inserir link
function insertLink() {
    const textarea = document.getElementById('postContent');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const textBefore = textarea.value.substring(0, start);
    const textAfter = textarea.value.substring(end);
    
    const linkText = selectedText || 'texto do link';
    const link = `[${linkText}](url-do-link)`;
    
    textarea.value = textBefore + link + textAfter;
    const newStart = start + linkText.length + 3; // [texto]( -> posição após o texto
    const newEnd = newStart + 9; // url-do-link
    textarea.selectionStart = newStart;
    textarea.selectionEnd = newEnd;
    
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
    
    if (document.getElementById('previewArea')?.style.display !== 'none') {
        updatePreview();
    }
}

// Inserir imagem
function insertImage() {
    const textarea = document.getElementById('postContent');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, start);
    const textAfter = textarea.value.substring(start);
    
    const image = `![descrição da imagem](url-da-imagem)`;
    
    textarea.value = textBefore + image + textAfter;
    const newPos = start + 3; // ![
    textarea.selectionStart = newPos;
    textarea.selectionEnd = newPos + 9; // descrição
    
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
    
    if (document.getElementById('previewArea')?.style.display !== 'none') {
        updatePreview();
    }
}

// Alternar preview
function togglePreview() {
    const previewArea = document.getElementById('previewArea');
    if (!previewArea) return;
    
    if (previewArea.style.display === 'none') {
        previewArea.style.display = 'block';
        updatePreview();
    } else {
        previewArea.style.display = 'none';
    }
}

// Atualizar preview
function updatePreview() {
    const content = document.getElementById('postContent').value;
    const previewContent = document.getElementById('previewContent');
    
    if (!previewContent) return;
    
    if (content.trim()) {
        const rendered = renderMarkdown(content);
        previewContent.innerHTML = rendered || '<p class="text-secondary text-center">Conteúdo vazio</p>';
    } else {
        previewContent.innerHTML = '<p class="text-secondary text-center">O conteúdo aparecerá aqui</p>';
    }
}

// Limpar editor
function clearEditor() {
    if (confirm('Tem certeza que deseja limpar o conteúdo do editor?')) {
        document.getElementById('postContent').value = '';
        document.getElementById('postTitle').value = '';
        document.getElementById('postResponse').innerHTML = '';
        
        if (document.getElementById('previewArea')?.style.display !== 'none') {
            updatePreview();
        }
    }
}

// ============================================
// ADICIONAR ATALHOS DE TECLADO NO EDITOR
// ============================================

function setupEditorShortcuts() {
    const textarea = document.getElementById('postContent');
    if (!textarea) return;
    
    // Ctrl+B - Negrito
    textarea.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            insertText('**', '**');
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'i') {
            e.preventDefault();
            insertText('*', '*');
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            insertLink();
        }
        
        // Tab - indentar
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const text = this.value;
            
            if (start === end) {
                // Inserir tab no cursor
                this.value = text.substring(0, start) + '  ' + text.substring(end);
                this.selectionStart = this.selectionEnd = start + 2;
            } else {
                // Indentar linhas selecionadas
                const lines = text.split('\n');
                let lineStart = text.lastIndexOf('\n', start - 1) + 1;
                let lineEnd = text.indexOf('\n', end);
                if (lineEnd === -1) lineEnd = text.length;
                
                const before = text.substring(0, lineStart);
                const selected = text.substring(lineStart, lineEnd);
                const after = text.substring(lineEnd);
                
                const indented = selected.split('\n').map(line => '  ' + line).join('\n');
                this.value = before + indented + after;
                
                // Ajustar seleção
                const newStart = lineStart + 2;
                const newEnd = lineEnd + (lineEnd - lineStart > 0 ? (indented.length - selected.length) : 0);
                this.selectionStart = newStart;
                this.selectionEnd = newEnd;
            }
            
            this.dispatchEvent(new Event('input'));
            if (document.getElementById('previewArea')?.style.display !== 'none') {
                updatePreview();
            }
        }
    });
}

// ============================================
// FUNÇÕES DE PAGINAÇÃO
// ============================================

function getPaginatedPosts() {
    const data = isSearching ? filteredPostsData : allPostsData;
    const totalPosts = data.length;
    const totalPages = Math.ceil(totalPosts / postsPerPage) || 1;
    
    // Garantir que a página atual é válida
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    if (currentPage < 1) {
        currentPage = 1;
    }
    
    const start = (currentPage - 1) * postsPerPage;
    const end = Math.min(start + postsPerPage, totalPosts);
    const pagePosts = data.slice(start, end);
    
    return {
        posts: pagePosts,
        total: totalPosts,
        totalPages: totalPages,
        currentPage: currentPage,
        start: start + 1,
        end: end,
        perPage: postsPerPage
    };
}

function renderPosts(postsData) {
    const postsList = document.getElementById('postsList');
    const postCount = document.getElementById('postCount');
    const searchCount = document.getElementById('searchCount');
    const totalPostsLabel = document.getElementById('totalPostsLabel');
    
    if (!postsList) {
        console.warn('Elemento postsList não encontrado');
        return;
    }
    
    // Limpar resultados de busca anteriores
    const noResults = postsList.querySelector('.no-search-results');
    if (noResults) noResults.remove();
    
    if (!postsData || postsData.length === 0) {
        postsList.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: var(--text-secondary); opacity: 0.3;"></i>
                <h5 class="mt-3 text-secondary">Nenhum post encontrado</h5>
                <p class="text-secondary">${isSearching ? 'Tente outra busca' : 'Seja o primeiro a postar!'}</p>
                ${!isSearching ? `
                    <button class="btn btn-primary mt-2" onclick="openEditor()">
                        <i class="bi bi-pencil-square me-2"></i>Criar Primeiro Post
                    </button>
                ` : `
                    <button class="btn btn-outline-primary mt-2" onclick="clearSearch()">
                        <i class="bi bi-arrow-left me-1"></i>Limpar busca
                    </button>
                `}
            </div>
        `;
        postCount.textContent = '0';
        if (searchCount) searchCount.textContent = '0';
        if (totalPostsLabel) totalPostsLabel.textContent = 'Total: 0';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    // Renderizar os posts da página atual
    postsList.innerHTML = postsData.map(post => {
        const renderedContent = post.texto_html || renderMarkdown(post.texto);
        
        return `
        <div class="post-card card" data-post="${post.arquivo || ''}">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="card-title mb-1">
                            <i class="bi bi-file-text me-2" style="color: var(--accent-primary);"></i>
                            ${escapeHtml(post.titulo || 'Sem título')}
                        </h5>
                        <div class="post-meta">
                            <small class="text-secondary">
                                <i class="bi bi-person-circle me-1"></i>
                                ${post.user_name ? escapeHtml(post.user_name) : 'Anônimo'}
                            </small>
                            <small class="text-secondary ms-3">
                                <i class="bi bi-clock me-1"></i>
                                ${formatDate(post.criado_em)}
                            </small>
                            ${post.editado ? ' <span class="badge badge-info ms-2">Editado</span>' : ''}
                        </div>
                    </div>
                    ${post.is_owner ? `
                        <div class="btn-group ms-2" style="flex-shrink: 0;">
                            <button class="btn btn-outline-primary btn-sm" onclick="editPost('${post.arquivo || ''}')" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deletePost('${post.arquivo || ''}')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
            <div class="card-body">
                <div class="post-content-full markdown-content">
                    ${renderedContent}
                </div>
            </div>
            ${post.editado && post.editado_em ? `
                <div class="card-footer text-muted" style="font-size: 0.75rem; border-top: 1px solid var(--border-color);">
                    <i class="bi bi-pencil me-1"></i>
                    Editado em ${formatDate(post.editado_em)}
                </div>
            ` : ''}
        </div>
    `}).join('');
    
    // Atualizar contadores
    const total = isSearching ? filteredPostsData.length : allPostsData.length;
    postCount.textContent = postsData.length;
    if (searchCount) searchCount.textContent = total;
    if (totalPostsLabel) totalPostsLabel.textContent = `Total: ${total}`;
    
    // Atualizar paginação
    updatePagination(total);
}

function updatePagination(totalPosts) {
    const container = document.getElementById('paginationContainer');
    if (!container) return;
    
    const totalPages = Math.ceil(totalPosts / postsPerPage) || 1;
    
    // Mostrar/ocultar paginador
    if (totalPages <= 1 && totalPosts <= postsPerPage) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'block';
    
    // Atualizar informações da página
    const start = (currentPage - 1) * postsPerPage + 1;
    const end = Math.min(currentPage * postsPerPage, totalPosts);
    document.getElementById('pageInfo').textContent = 
        totalPosts > 0 ? `Página ${currentPage} de ${totalPages} (${start}-${end} de ${totalPosts})` : 'Nenhum post';
    
    // Atualizar botões de navegação
    const firstBtn = document.getElementById('firstPage');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const lastBtn = document.getElementById('lastPage');
    
    if (firstBtn) {
        firstBtn.disabled = currentPage <= 1;
        firstBtn.className = `btn btn-outline-primary btn-sm ${currentPage <= 1 ? 'disabled' : ''}`;
    }
    if (prevBtn) {
        prevBtn.disabled = currentPage <= 1;
        prevBtn.className = `btn btn-outline-primary btn-sm ${currentPage <= 1 ? 'disabled' : ''}`;
    }
    if (nextBtn) {
        nextBtn.disabled = currentPage >= totalPages;
        nextBtn.className = `btn btn-outline-primary btn-sm ${currentPage >= totalPages ? 'disabled' : ''}`;
    }
    if (lastBtn) {
        lastBtn.disabled = currentPage >= totalPages;
        lastBtn.className = `btn btn-outline-primary btn-sm ${currentPage >= totalPages ? 'disabled' : ''}`;
    }
    
    // Gerar números das páginas
    const pageNumbers = document.getElementById('pageNumbers');
    if (!pageNumbers) return;
    
    pageNumbers.innerHTML = '';
    
    // Determinar quais páginas mostrar
    let pages = [];
    const maxVisible = 7;
    
    if (totalPages <= maxVisible) {
        for (let i = 1; i <= totalPages; i++) {
            pages.push(i);
        }
    } else {
        // Sempre mostrar primeira e última página
        pages.push(1);
        
        let startPage = Math.max(2, currentPage - 2);
        let endPage = Math.min(totalPages - 1, currentPage + 2);
        
        // Ajustar se estiver perto do início
        if (currentPage <= 3) {
            endPage = 5;
        }
        // Ajustar se estiver perto do fim
        if (currentPage >= totalPages - 2) {
            startPage = totalPages - 4;
        }
        
        // Adicionar ellipsis se necessário
        if (startPage > 2) {
            pages.push('...');
        }
        
        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }
        
        if (endPage < totalPages - 1) {
            pages.push('...');
        }
        
        pages.push(totalPages);
    }
    
    pages.forEach(page => {
        const btn = document.createElement('button');
        if (page === '...') {
            btn.className = 'pagination-btn disabled';
            btn.textContent = '…';
            btn.disabled = true;
        } else {
            btn.className = `pagination-btn ${page === currentPage ? 'active' : ''}`;
            btn.textContent = page;
            btn.onclick = () => goToPage(page);
        }
        pageNumbers.appendChild(btn);
    });
}

function goToPage(page) {
    const total = isSearching ? filteredPostsData.length : allPostsData.length;
    const totalPages = Math.ceil(total / postsPerPage) || 1;
    
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    const paginated = getPaginatedPosts();
    renderPosts(paginated.posts);
    
    // Scroll para o topo da lista de posts
    const postsList = document.getElementById('postsList');
    if (postsList) {
        postsList.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ============================================
// FUNÇÕES DE BUSCA
// ============================================

function searchPosts(query) {
    const searchCount = document.getElementById('searchCount');
    const clearBtn = document.getElementById('clearSearchBtn');
    const searchInfo = document.getElementById('searchInfo');
    
    // Mostrar informação de busca
    if (searchInfo) {
        searchInfo.style.display = query.trim() ? 'block' : 'none';
    }
    
    // Mostrar/ocultar botão de limpar
    if (clearBtn) {
        clearBtn.style.display = query.trim() ? 'inline-block' : 'none';
    }
    
    if (!query.trim()) {
        // Se a busca estiver vazia, mostrar todos os posts
        isSearching = false;
        filteredPostsData = [];
        currentPage = 1;
        const paginated = getPaginatedPosts();
        renderPosts(paginated.posts);
        return;
    }
    
    const searchTerm = query.toLowerCase().trim();
    isSearching = true;
    
    // Filtrar posts
    filteredPostsData = allPostsData.filter(post => {
        const title = (post.titulo || '').toLowerCase();
        const content = (post.texto || '').toLowerCase();
        const author = (post.user_name || '').toLowerCase();
        const fullText = title + ' ' + content + ' ' + author;
        return fullText.includes(searchTerm);
    });
    
    // Resetar para primeira página da busca
    currentPage = 1;
    const paginated = getPaginatedPosts();
    renderPosts(paginated.posts);
    
    // Atualizar contador de busca
    if (searchCount) {
        searchCount.textContent = filteredPostsData.length;
    }
}

function clearSearch() {
    const searchInput = document.getElementById('searchPosts');
    if (searchInput) {
        searchInput.value = '';
        isSearching = false;
        filteredPostsData = [];
        currentPage = 1;
        const paginated = getPaginatedPosts();
        renderPosts(paginated.posts);
        searchInput.focus();
    }
}

function initSearch() {
    const searchInput = document.getElementById('searchPosts');
    if (!searchInput) return;
    
    // Debounce para não executar a busca a cada tecla
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            searchPosts(this.value);
        }, 300);
    });
    
    // Buscar ao pressionar Enter
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimer);
            searchPosts(this.value);
            e.preventDefault();
        }
    });
    
    // Suporte para Ctrl+F / Cmd+F para focar na busca
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('searchPosts');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });
}

// ============================================
// FUNÇÕES DE POSTS
// ============================================

// Função para salvar post
async function savePost() {
    const titulo = document.getElementById('postTitle').value.trim();
    const texto = document.getElementById('postContent').value.trim();
    const responseDiv = document.getElementById('postResponse');
    
    // Validar título
    if (!titulo) {
        responseDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Por favor, insira um título.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.getElementById('postTitle').focus();
        return;
    }
    
    // Validar conteúdo
    if (!texto) {
        responseDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Por favor, insira o conteúdo do post.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.getElementById('postContent').focus();
        return;
    }
    
    // Mostrar loading
    showLoading(true);
    responseDiv.innerHTML = '';
    
    try {
        const basePath = getBasePath();
        const endpoint = editingFile ? `${basePath}edit-post.php` : `${basePath}save-post.php`;
        const payload = editingFile ? {
            arquivo: editingFile,
            titulo: titulo,
            texto: texto
        } : {
            titulo: titulo,
            texto: texto
        };
        
        console.log('📤 Enviando para:', endpoint);
        console.log('📦 Payload:', payload);
        
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        });
        
        console.log('📥 Status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Tentar parsear a resposta
        let result;
        try {
            const text = await response.text();
            console.log('📄 Resposta bruta:', text.substring(0, 200));
            result = JSON.parse(text);
        } catch (e) {
            console.error('Erro ao parsear JSON:', e);
            throw new Error('Resposta do servidor não é um JSON válido');
        }
        
        console.log('✅ Resposta:', result);
        
        if (result.success) {
            responseDiv.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    ${result.message || 'Post salvo com sucesso!'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            setTimeout(() => {
                if (editorModal) {
                    editorModal.hide();
                }
                loadPosts();
                editingFile = null;
            }, 1500);
            
        } else {
            responseDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${result.error || 'Erro ao salvar o post.'}
                    ${result.debug ? `<br><small class="text-muted">Debug: ${result.debug}</small>` : ''}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    } catch (error) {
        console.error('❌ Erro detalhado:', error);
        console.error('Stack:', error.stack);
        
        let errorMsg = 'Erro ao salvar o post. Tente novamente.';
        if (error.message.includes('JSON')) {
            errorMsg = `Erro de comunicação com o servidor: ${error.message}`;
        } else if (error.message.includes('HTTP error')) {
            errorMsg = `Erro HTTP: ${error.message}`;
        } else if (error.message.includes('Failed to fetch')) {
            errorMsg = 'Não foi possível conectar ao servidor. Verifique se o servidor está rodando.';
        }
        
        responseDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${errorMsg}
                <br><small class="text-muted">Verifique o console (F12) para mais detalhes.</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    } finally {
        showLoading(false);
    }
}

// Função para carregar os posts
async function loadPosts() {
    const postsList = document.getElementById('postsList');
    const postCount = document.getElementById('postCount');
    
    if (!postsList) {
        console.warn('Elemento postsList não encontrado');
        return;
    }
    
    try {
        const basePath = getBasePath();
        const response = await fetch(`${basePath}list-posts.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📋 Posts carregados:', data.total || 0);
        
        // Armazenar dados para paginação e busca
        allPostsData = data.posts || [];
        filteredPostsData = [];
        isSearching = false;
        currentPage = 1;
        
        if (data.success && data.posts && data.posts.length > 0) {
            const paginated = getPaginatedPosts();
            renderPosts(paginated.posts);
            
            // Inicializar a busca após carregar os posts
            initSearch();
            
        } else {
            renderPosts([]);
        }
    } catch (error) {
        console.error('❌ Erro ao carregar posts:', error);
        postsList.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Erro ao carregar posts: ${escapeHtml(error.message)}
                <br><small>Verifique o console (F12) para mais detalhes.</small>
            </div>
        `;
    }
}

// ============================================
// FUNÇÕES DE AÇÕES DOS POSTS
// ============================================

// Função para editar post
function editPost(filename) {
    if (!filename) {
        console.warn('Filename não especificado para edição');
        return;
    }
    console.log('✏️ Editando:', filename);
    openEditor(filename);
}

// Função para deletar post
async function deletePost(filename) {
    if (!filename) {
        console.warn('Filename não especificado para exclusão');
        return;
    }
    
    if (!confirm('Tem certeza que deseja excluir este post?')) {
        return;
    }
    
    try {
        const basePath = getBasePath();
        const response = await fetch(`${basePath}delete-post.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ arquivo: filename })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Post excluído com sucesso!');
            loadPosts();
        } else {
            alert('❌ Erro: ' + (result.error || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('❌ Erro ao excluir post:', error);
        alert('Erro ao excluir o post: ' + error.message);
    }
}

// ============================================
// FUNÇÕES DO EDITOR
// ============================================

// Função para abrir o editor
function openEditor(filename = null) {
    editingFile = filename;
    const modal = document.getElementById('editorModal');
    
    if (!modal) {
        console.error('Modal do editor não encontrado');
        return;
    }
    
    const modalTitle = modal.querySelector('.modal-title');
    const postTitle = document.getElementById('postTitle');
    const postContent = document.getElementById('postContent');
    const responseDiv = document.getElementById('postResponse');
    
    // Limpar mensagens anteriores
    responseDiv.innerHTML = '';
    
    if (filename) {
        // Modo edição
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="bi bi-pencil-square me-2" style="color: var(--accent-primary);"></i>$> Editar Post`;
        }
        loadPostData(filename);
    } else {
        // Modo novo post
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="bi bi-pencil-square me-2" style="color: var(--accent-primary);"></i>$> Novo Post`;
        }
        if (postTitle) postTitle.value = '';
        if (postContent) postContent.value = '';
    }
    
    // Inicializar o modal se não existir
    if (!editorModal) {
        editorModal = new bootstrap.Modal(modal, {
            backdrop: 'static',
            keyboard: true
        });
    }
    editorModal.show();
}

// Função para carregar dados do post para edição
async function loadPostData(filename) {
    try {
        const basePath = getBasePath();
        const response = await fetch(`${basePath}list-posts.php`);
        const data = await response.json();
        
        if (data.success && data.posts) {
            const post = data.posts.find(p => p.arquivo === filename);
            if (post) {
                document.getElementById('postTitle').value = post.titulo || '';
                document.getElementById('postContent').value = post.texto || '';
            } else {
                console.error('Post não encontrado:', filename);
                alert('Post não encontrado para edição.');
            }
        }
    } catch (error) {
        console.error('❌ Erro ao carregar post para edição:', error);
        alert('Erro ao carregar dados do post para edição.');
    }
}

// ============================================
// CONFIGURAR EVENTOS DE PAGINAÇÃO
// ============================================

function setupPaginationEvents() {
    // Botões de navegação
    document.getElementById('firstPage')?.addEventListener('click', () => goToPage(1));
    document.getElementById('prevPage')?.addEventListener('click', () => goToPage(currentPage - 1));
    document.getElementById('nextPage')?.addEventListener('click', () => goToPage(currentPage + 1));
    document.getElementById('lastPage')?.addEventListener('click', () => {
        const total = isSearching ? filteredPostsData.length : allPostsData.length;
        const totalPages = Math.ceil(total / postsPerPage) || 1;
        goToPage(totalPages);
    });
    
    // Mudar quantidade de posts por página
    document.getElementById('postsPerPage')?.addEventListener('change', function() {
        postsPerPage = parseInt(this.value);
        currentPage = 1;
        const paginated = getPaginatedPosts();
        renderPosts(paginated.posts);
    });
}

// ============================================
// EVENTOS E INICIALIZAÇÃO
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando editor com paginação...');
    
    // Carregar posts
    loadPosts();
    
    // Configurar eventos de paginação
    setupPaginationEvents();
    
    // Configurar botão de salvar
    const saveButton = document.querySelector('#editorModal .btn-primary');
    if (saveButton) {
        saveButton.onclick = savePost;
        console.log('✅ Botão salvar configurado');
    } else {
        console.warn('⚠️ Botão salvar não encontrado');
    }
    
    // Configurar fechamento do modal
    const modal = document.getElementById('editorModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            console.log('🔒 Modal fechado');
            editingFile = null;
            const responseDiv = document.getElementById('postResponse');
            if (responseDiv) responseDiv.innerHTML = '';
        });
    }
    
    // Suporte a Ctrl+Enter para salvar
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const modal = document.getElementById('editorModal');
            if (modal && modal.classList.contains('show')) {
                e.preventDefault();
                savePost();
            }
        }
    });

    // Configurar atalhos do editor
    setupEditorShortcuts();

    // Atualizar preview ao digitar
    document.getElementById('postContent')?.addEventListener('input', function() {
        if (document.getElementById('previewArea')?.style.display !== 'none') {
            updatePreview();
        }
    });    
    
    console.log('✅ Editor inicializado com sucesso!');
});

// ============================================
// EXPORTAÇÕES PARA USO GLOBAL
// ============================================

// Garantir que as funções estejam disponíveis globalmente
window.savePost = savePost;
window.loadPosts = loadPosts;
window.editPost = editPost;
window.deletePost = deletePost;
window.openEditor = openEditor;
window.showLoading = showLoading;
window.renderMarkdown = renderMarkdown;
window.escapeHtml = escapeHtml;
window.formatDate = formatDate;
window.searchPosts = searchPosts;
window.clearSearch = clearSearch;
window.goToPage = goToPage;
window.insertText = insertText;
window.insertCodeBlock = insertCodeBlock;
window.insertTable = insertTable;
window.insertLink = insertLink;
window.insertImage = insertImage;
window.togglePreview = togglePreview;
window.updatePreview = updatePreview;
window.clearEditor = clearEditor;
window.setupEditorShortcuts = setupEditorShortcuts;

console.log('📦 Editor.js carregado e pronto para uso!');