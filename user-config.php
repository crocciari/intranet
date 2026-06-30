<?php
// ============================================
// USER CONFIG - Configurações de usuário
// ============================================

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

function getCurrentUser() {
    $users_dir = __DIR__ . '/_intra_users_json';
    $ip = getClientIP();
    
    // Se não houver diretório, criar
    if (!is_dir($users_dir)) {
        mkdir($users_dir, 0755, true);
        return null;
    }
    
    // Procurar usuário pelo IP
    $files = glob($users_dir . '/*.json');
    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user && $user['ip'] === $ip) {
            return $user;
        }
    }
    
    return null;
}

function hasAdminUser() {
    $users_dir = __DIR__ . '/_intra_users_json';
    
    if (!is_dir($users_dir)) {
        return false;
    }
    
    $files = glob($users_dir . '/*.json');
    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user && isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
    }
    
    return false;
}

function hasUsers() {
    $users_dir = __DIR__ . '/_intra_users_json';
    
    if (!is_dir($users_dir)) {
        return false;
    }
    
    $files = glob($users_dir . '/*.json');
    return count($files) > 0;
}

function getUserByIP($ip) {
    $users_dir = __DIR__ . '/_intra_users_json';
    $filename = 'user_' . md5($ip) . '.json';
    $caminho = $users_dir . '/' . $filename;
    
    if (file_exists($caminho)) {
        return json_decode(file_get_contents($caminho), true);
    }
    
    return null;
}

function getUserName($ip) {
    $user = getUserByIP($ip);
    return $user ? $user['name'] : $ip;
}
?>