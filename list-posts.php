<?php
// ============================================
// LIST POSTS - API Endpoint
// ============================================

// Desabilitar exibição de erros para não quebrar o JSON
error_reporting(0);
ini_set('display_errors', 0);

// Configurações
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
date_default_timezone_set('Asia/Tokyo');

// Função para retornar erro em JSON
function returnJsonError($message) {
    echo json_encode([
        'success' => false,
        'error' => $message,
        'posts' => [],
        'total' => 0
    ]);
    exit;
}

// Função para obter IP do usuário
function getClientIP() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    if (strpos($ip, ',') !== false) {
        $ip = explode(',', $ip)[0];
    }
    
    return trim($ip);
}

// Função para obter nome do usuário pelo IP
function getUserName($ip) {
    $users_dir = __DIR__ . '/_intra_users_json';
    $filename = 'user_' . md5($ip) . '.json';
    $caminho = $users_dir . '/' . $filename;
    
    if (file_exists($caminho)) {
        $user = json_decode(file_get_contents($caminho), true);
        return $user['name'] ?? $ip;
    }
    return $ip;
}

// ============================================
// MAIN
// ============================================

$posts_dir = __DIR__ . '/_intra_posts_json';

// Verificar se o diretório existe, se não, criar
if (!is_dir($posts_dir)) {
    if (!mkdir($posts_dir, 0755, true)) {
        returnJsonError('Erro ao criar diretório de posts');
    }
}

$posts = [];
$current_ip = getClientIP();

// Buscar arquivos de posts
try {
    $files = glob($posts_dir . '/post_*.json');
    
    if ($files === false) {
        returnJsonError('Erro ao listar arquivos de posts');
    }
    
    // Ordenar por data (mais recentes primeiro)
    rsort($files);
    
    foreach ($files as $file) {
        $content = json_decode(file_get_contents($file), true);
        if ($content && is_array($content)) {
            // Adicionar flag de proprietário
            $content['is_owner'] = ($content['user_ip'] ?? '') === $current_ip;
            // Adicionar nome do usuário
            $content['user_name'] = getUserName($content['user_ip'] ?? '');
            $posts[] = $content;
        }
    }
    
    // Limitar a 10 posts
    $posts = array_slice($posts, 0, 10);
    
    // Retornar sucesso
    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'total' => count($posts),
        'current_ip' => $current_ip
    ]);
    
} catch (Exception $e) {
    returnJsonError('Erro ao processar posts: ' . $e->getMessage());
}
?>