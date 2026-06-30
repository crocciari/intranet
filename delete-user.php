<?php
// ============================================
// DELETE USER - API Endpoint
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
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ip']) || empty(trim($data['ip']))) {
    echo json_encode(['success' => false, 'error' => 'IP não especificado']);
    exit;
}

$ip = trim($data['ip']);
$current_ip = getClientIP();

// Verificar se o usuário é o atual ou tem permissão (admin)
$is_admin = false;
// Verificar se o usuário atual é admin
$current_user_file = $users_dir . '/user_' . md5($current_ip) . '.json';
if (file_exists($current_user_file)) {
    $current_user = json_decode(file_get_contents($current_user_file), true);
    $is_admin = ($current_user['role'] ?? 'user') === 'admin';
}

// Permitir deletar se for o próprio usuário ou admin
if ($ip !== $current_ip && !$is_admin) {
    echo json_encode(['success' => false, 'error' => 'Você não tem permissão para deletar este usuário']);
    exit;
}

$caminho_completo = $users_dir . '/user_' . md5($ip) . '.json';

if (!file_exists($caminho_completo)) {
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado']);
    exit;
}

if (unlink($caminho_completo)) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuário deletado com sucesso!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao deletar o usuário'
    ]);
}
?>