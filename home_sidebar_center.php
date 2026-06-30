<!-- CONTEÚDO PRINCIPAL - COLUNA DO MEIO -->
<div class="col-lg-6 col-md-8">
    <!-- Botão Novo Post -->
    <div class="card">
        <div class="card-body text-center">
            <button class="btn btn-primary btn-lg w-100" onclick="openEditor()" style="padding: 20px;">
                <i class="bi bi-pencil-square me-2"></i>
                <span class="mono">$> Criar Novo Post</span>
            </button>
        </div>
    </div>

    <!-- BARRA DE BUSCA -->
    <div class="card">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" id="searchPosts" 
                       placeholder="Buscar posts por título, conteúdo ou autor..."
                       style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                <button class="btn btn-outline-primary" onclick="clearSearch()" id="clearSearchBtn" style="display: none;">
                    <i class="bi bi-x-circle"></i>
                </button>
                <span class="input-group-text" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-secondary);">
                    <span id="searchCount" style="font-size: 0.8rem;">0</span>
                </span>
            </div>
            <div class="mt-2 d-flex justify-content-between align-items-center" id="searchInfo" style="display: none;">
                <small class="text-secondary">
                    <i class="bi bi-info-circle me-1"></i>
                    Digite para filtrar os posts
                </small>
                <small class="search-hint">
                    <kbd>Ctrl</kbd> + <kbd>F</kbd>
                </small>
            </div>
        </div>
    </div>

    <!-- Lista de Posts Recentes -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-archive me-2"></i>Posts Recentes
            <span class="ms-auto">
                <span class="badge badge-info" id="postCount">0</span>
                <span class="text-secondary" style="font-size: 0.7rem; margin-left: 8px;" id="totalPostsLabel">Total: 0</span>
            </span>
        </div>
        <div class="card-body" id="postsList">
            <p class="text-secondary text-center py-3">Carregando posts...</p>
        </div>
        
        <!-- PAGINAÇÃO -->
        <div class="card-footer" id="paginationContainer" style="border-top: 1px solid var(--border-color); display: none;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary" style="font-size: 0.8rem; font-family: var(--font-mono);">
                        <span id="pageInfo">Página 1 de 1</span>
                    </span>
                    <select id="postsPerPage" class="form-select form-select-sm" style="width: auto; background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary); font-family: var(--font-mono); font-size: 0.75rem;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-primary btn-sm" id="firstPage" title="Primeira página">
                        <i class="bi bi-chevron-double-left"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm" id="prevPage" title="Página anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm" id="nextPage" title="Próxima página">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm" id="lastPage" title="Última página">
                        <i class="bi bi-chevron-double-right"></i>
                    </button>
                </div>
            </div>
            <div class="mt-2 text-center">
                <div id="pageNumbers" class="d-flex justify-content-center gap-1 flex-wrap"></div>
            </div>
        </div>
    </div>
</div>