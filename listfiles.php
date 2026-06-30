<?php

// ============================================
// FUNÇÕES DE ARQUIVOS - LISTA A PARTIR DA RAIZ
// ============================================

function listarArquivos($diretorio, $all = false) {
    // Verifica se o diretório existe
    if (!is_dir($diretorio)) {
        return [];
    }
    
    $arquivos = scandir($diretorio);
    $items = [];
    
    // Ordenar: pastas primeiro, depois arquivos
    usort($arquivos, function($a, $b) use ($diretorio) {
        $a_is_dir = is_dir($diretorio . '/' . $a);
        $b_is_dir = is_dir($diretorio . '/' . $b);
        
        if ($a_is_dir && !$b_is_dir) return -1;
        if (!$a_is_dir && $b_is_dir) return 1;
        return strcasecmp($a, $b);
    });
    
    foreach ($arquivos as $arquivo) {
        if ($arquivo === "." || $arquivo === "..") continue;
        
        $caminho = $diretorio . "/" . $arquivo;
        
        if ($all) {
            if (is_dir($caminho)) {
                // Recursivamente lista arquivos em subdiretórios
                $items = array_merge($items, listarArquivos($caminho, true));
            } else {
                $items[] = $caminho;
            }
        } else {
            // Para exibição na interface
            $icone = is_dir($caminho) ? 'bi-folder-fill' : 'bi-file-earmark';
            $cor = is_dir($caminho) ? 'text-warning' : 'text-primary';
            
            // Se for pasta, adicionar uma classe extra
            $classe_extra = is_dir($caminho) ? 'folder-item' : 'file-item';
            
            // Se for um arquivo .php, adicionar ícone especial
            if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'php') {
                $icone = 'bi-file-code';
                $cor = 'text-info';
            }
            
            // Se for um arquivo .js
            if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'js') {
                $icone = 'bi-file-code';
                $cor = 'text-warning';
            }
            
            // Se for um arquivo .css
            if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'css') {
                $icone = 'bi-file-css';
                $cor = 'text-primary';
            }
            
            // Se for um arquivo .json
            if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'json') {
                $icone = 'bi-file-json';
                $cor = 'text-success';
            }
            
            // Se for um arquivo .md
            if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'md') {
                $icone = 'bi-file-text';
                $cor = 'text-secondary';
            }
            
            // Contar arquivos dentro da pasta (se for diretório)
            $badge = '';
            if (is_dir($caminho)) {
                $sub_files = scandir($caminho);
                $count = count(array_filter($sub_files, function($f) {
                    return $f !== '.' && $f !== '..';
                }));
                if ($count > 0) {
                    $badge = " <span class='badge badge-secondary' style='background: var(--border-color); color: var(--text-secondary); font-size: 0.6rem;'>$count</span>";
                }
            }
            
            echo "<li class='list-group-item {$classe_extra}'>
                    <a href='/{$arquivo}' class='text-decoration-none d-flex align-items-center gap-2' target='_blank'>
                        <i class='bi {$icone} me-1 {$cor}'></i>
                        <span style='flex: 1;'>{$arquivo}</span>
                        {$badge}
                        <i class='bi bi-box-arrow-up-right' style='font-size: 0.7rem; opacity: 0.3;'></i>
                    </a>
                  </li>";
        }
    }
    
    return $items;
}

// Função para listar a partir da raiz do projeto
function listarRaiz() {
    // Obter o diretório raiz (pai da pasta intranet)
    $root_dir = dirname(__DIR__);
    return listarArquivos($root_dir);
}

// Função para listar com caminho relativo
function listarArquivosComCaminho($diretorio_base = null) {
    if ($diretorio_base === null) {
        $diretorio_base = dirname(__DIR__);
    }
    return listarArquivos($diretorio_base);
}
?>