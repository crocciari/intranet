<?php
// ============================================
// EDIT POST - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

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

$posts_dir = __DIR__ . '/_intra_posts_json';
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['arquivo']) || empty(trim($data['arquivo']))) {
    echo json_encode(['success' => false, 'error' => 'Arquivo não especificado']);
    exit;
}

if (!isset($data['titulo']) || empty(trim($data['titulo']))) {
    echo json_encode(['success' => false, 'error' => 'Título é obrigatório']);
    exit;
}

if (!isset($data['texto']) || empty(trim($data['texto']))) {
    echo json_encode(['success' => false, 'error' => 'Conteúdo é obrigatório']);
    exit;
}

$arquivo = trim($data['arquivo']);
$caminho_completo = $posts_dir . '/' . $arquivo;

// Verificar se o arquivo existe
if (!file_exists($caminho_completo)) {
    echo json_encode(['success' => false, 'error' => 'Post não encontrado']);
    exit;
}

// Ler o conteúdo do post para verificar o IP
$content = json_decode(file_get_contents($caminho_completo), true);
if (!$content) {
    echo json_encode(['success' => false, 'error' => 'Erro ao ler o post']);
    exit;
}

// Verificar se o usuário é o dono do post
$user_ip = getClientIP();
if ($content['user_ip'] !== $user_ip) {
    echo json_encode(['success' => false, 'error' => 'Você não tem permissão para editar este post']);
    exit;
}

// Atualizar dados
$content['titulo'] = trim($data['titulo']);
$content['texto'] = trim($data['texto']);
$content['editado'] = true;
$content['editado_em'] = date('Y-m-d H:i:s');

// Salvar arquivo
if (file_put_contents($caminho_completo, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Post atualizado com sucesso!',
        'data' => $content
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao salvar as alterações'
    ]);
}
?>