<?php
// ============================================
// LIST USERS - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

$users_dir = __DIR__ . '/_intra_users_json';
$users = [];

if (is_dir($users_dir)) {
    $files = glob($users_dir . '/*.json');
    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user) {
            $users[] = $user;
        }
    }
}

// Ordenar por nome
usort($users, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

echo json_encode([
    'success' => true,
    'users' => $users,
    'total' => count($users)
]);
?>