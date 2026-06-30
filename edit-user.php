<?php
// ============================================
// EDIT USER - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

$users_dir = __DIR__ . '/_intra_users_json';

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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ip']) || empty(trim($data['ip']))) {
    echo json_encode(['success' => false, 'error' => 'IP não especificado']);
    exit;
}

$ip = trim($data['ip']);
$current_ip = getClientIP();

// Verificar se o usuário é o atual ou admin
$is_admin = false;
$current_user_file = $users_dir . '/user_' . md5($current_ip) . '.json';
if (file_exists($current_user_file)) {
    $current_user = json_decode(file_get_contents($current_user_file), true);
    $is_admin = ($current_user['role'] ?? 'user') === 'admin';
}

if ($ip !== $current_ip && !$is_admin) {
    echo json_encode(['success' => false, 'error' => 'Você não tem permissão para editar este usuário']);
    exit;
}

$caminho_completo = $users_dir . '/user_' . md5($ip) . '.json';

if (!file_exists($caminho_completo)) {
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado']);
    exit;
}

$user = json_decode(file_get_contents($caminho_completo), true);

// Atualizar dados (somente campos permitidos)
if (isset($data['name'])) $user['name'] = trim($data['name']);
if (isset($data['email'])) $user['email'] = trim($data['email']);
if (isset($data['avatar'])) $user['avatar'] = $data['avatar'];
if (isset($data['bio'])) $user['bio'] = $data['bio'];
if (isset($data['department'])) $user['department'] = $data['department'];
if (isset($data['role']) && $is_admin) $user['role'] = $data['role'];

$user['updated_at'] = date('Y-m-d H:i:s');

if (file_put_contents($caminho_completo, json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuário atualizado com sucesso!',
        'data' => $user
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao atualizar o usuário'
    ]);
}
?>