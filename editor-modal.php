<!-- Modal do Editor -->
<div class="modal fade" id="editorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title mono">
                    <i class="bi bi-pencil-square me-2" style="color: var(--accent-primary);"></i>
                    $> Novo Post
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: var(--btn-close-filter);"></button>
            </div>
            <div class="modal-body">
                <form id="postForm">
                    <div class="mb-3">
                        <label class="form-label mono" style="font-size: 0.9rem;">
                            <i class="bi bi-tag me-1"></i>Título
                        </label>
                        <input type="text" class="form-control" id="postTitle" 
                               placeholder="Digite o título do post..." 
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    
                    <!-- BARRA DE FERRAMENTAS DO EDITOR -->
                    <div class="mb-2">
                        <label class="form-label mono" style="font-size: 0.9rem;">
                            <i class="bi bi-text-paragraph me-1"></i>Conteúdo
                        </label>
                        <div class="editor-toolbar">
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Formatação básica -->
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('**', '**')" title="Negrito">
                                    <i class="bi bi-type-bold"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('*', '*')" title="Itálico">
                                    <i class="bi bi-type-italic"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('~~', '~~')" title="Riscado">
                                    <i class="bi bi-type-strikethrough"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('`', '`')" title="Código inline">
                                    <i class="bi bi-code"></i>
                                </button>
                            </div>
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Cabeçalhos -->
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('# ', '')" title="Título 1">
                                    H1
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('## ', '')" title="Título 2">
                                    H2
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('### ', '')" title="Título 3">
                                    H3
                                </button>
                            </div>
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Listas -->
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('- ', '')" title="Lista não ordenada">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('1. ', '')" title="Lista ordenada">
                                    <i class="bi bi-list-ol"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('- [ ] ', '')" title="Checklist">
                                    <i class="bi bi-check-square"></i>
                                </button>
                            </div>
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Citações e separadores -->
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('> ', '')" title="Citação">
                                    <i class="bi bi-quote"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertText('---\n', '')" title="Linha horizontal">
                                    <i class="bi bi-hr"></i>
                                </button>
                            </div>
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Blocos de código com linguagem -->
                                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" title="Bloco de código">
                                    <i class="bi bi-file-code"></i> Código
                                </button>
                                <ul class="dropdown-menu" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('bash')">Bash</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('javascript')">JavaScript</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('php')">PHP</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('python')">Python</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('html')">HTML</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('css')">CSS</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('sql')">SQL</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('json')">JSON</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('yaml')">YAML</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('dockerfile')">Dockerfile</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('nginx')">Nginx</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="insertCodeBlock('')">Sem linguagem</a></li>
                                </ul>
                            </div>
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Tabela -->
                                <button type="button" class="btn btn-outline-primary" onclick="insertTable()" title="Inserir tabela">
                                    <i class="bi bi-table"></i>
                                </button>
                            </div>
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Links e imagens -->
                                <button type="button" class="btn btn-outline-primary" onclick="insertLink()" title="Inserir link">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertImage()" title="Inserir imagem">
                                    <i class="bi bi-image"></i>
                                </button>
                            </div>
                            
                            <div class="ms-auto">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="togglePreview()" title="Pré-visualizar">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearEditor()" title="Limpar">
                                    <i class="bi bi-eraser"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Área de preview (inicialmente oculta) -->
                        <div id="previewArea" style="display: none;" class="mt-2">
                            <div class="alert alert-info mb-2">
                                <i class="bi bi-eye me-2"></i>Pré-visualização do conteúdo
                                <button type="button" class="btn-close float-end" onclick="togglePreview()" style="filter: var(--btn-close-filter);"></button>
                            </div>
                            <div id="previewContent" class="markdown-content" style="background: var(--bg-primary); padding: 16px; border-radius: 8px; border: 1px solid var(--border-color); min-height: 100px;">
                                <p class="text-secondary text-center">O conteúdo aparecerá aqui</p>
                            </div>
                        </div>
                        
                        <textarea class="form-control" id="postContent" rows="12" 
                                  placeholder="Escreva seu post aqui... Use Markdown para formatação."
                                  style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); resize: vertical; font-family: var(--font-mono); font-size: 0.9rem; tab-size: 4;"></textarea>
                    </div>
                    <div id="postResponse" class="mt-2"></div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="savePost()">
                    <i class="bi bi-check-circle me-1"></i>Salvar Post
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner (escondido por padrão) -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Salvando...</span>
        </div>
        <p class="mt-3 mono" style="color: var(--text-primary);">Salvando post...</p>
    </div>
</div>
