<?php
// ============================================
// SAVE USER - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

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

$users_dir = __DIR__ . '/_intra_users_json';

// Criar pasta se não existir
if (!is_dir($users_dir)) {
    mkdir($users_dir, 0755, true);
}

$data = json_decode(file_get_contents('php://input'), true);

// Validar dados
if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode(['success' => false, 'error' => 'Nome é obrigatório']);
    exit;
}

if (!isset($data['ip']) || empty(trim($data['ip']))) {
    echo json_encode(['success' => false, 'error' => 'IP é obrigatório']);
    exit;
}

if (!isset($data['email']) || empty(trim($data['email']))) {
    echo json_encode(['success' => false, 'error' => 'Email é obrigatório']);
    exit;
}

// Verificar se email já existe
$files = glob($users_dir . '/*.json');
foreach ($files as $file) {
    $user = json_decode(file_get_contents($file), true);
    if ($user && $user['email'] === $data['email'] && $user['ip'] !== $data['ip']) {
        echo json_encode(['success' => false, 'error' => 'Email já está em uso']);
        exit;
    }
}

// Verificar se IP já existe (exceto se for o mesmo usuário)
$current_ip = getClientIP();
if ($data['ip'] !== $current_ip) {
    // Se não for o IP atual, verificar se já existe
    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user && $user['ip'] === $data['ip']) {
            echo json_encode(['success' => false, 'error' => 'IP já está em uso por outro usuário']);
            exit;
        }
    }
}

// Gerar nome do arquivo
$filename = 'user_' . md5($data['ip']) . '.json';
$caminho_completo = $users_dir . '/' . $filename;

// Dados do usuário
$user_data = [
    'ip' => trim($data['ip']),
    'name' => trim($data['name']),
    'email' => trim($data['email']),
    'avatar' => $data['avatar'] ?? '',
    'bio' => $data['bio'] ?? '',
    'department' => $data['department'] ?? '',
    'role' => $data['role'] ?? 'user',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

// Salvar arquivo
if (file_put_contents($caminho_completo, json_encode($user_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuário salvo com sucesso!',
        'data' => $user_data
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao salvar o usuário'
    ]);
}
?>