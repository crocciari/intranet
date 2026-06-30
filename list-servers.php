<?php
// ============================================
// LIST SERVERS - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

$servers_file = __DIR__ . '/_intra_server_json/servers.json';

// Criar diretório se não existir
$dir = dirname($servers_file);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Criar arquivo padrão se não existir
if (!file_exists($servers_file)) {
    $default_servers = [
        [
            'id' => 'server-one',
            'name' => 'Server-One',
            'ip' => '1',
            'base_ip' => '127.0.0.',
            'services' => [
                ['name' => 'Localhost', 'url' => 'https://localhost', 'icon' => 'bi-box-arrow-up-right'],
            ]
        ]
    ];
    file_put_contents($servers_file, json_encode($default_servers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$servers = json_decode(file_get_contents($servers_file), true);

if (!is_array($servers)) {
    $servers = [];
}

echo json_encode([
    'success' => true,
    'servers' => $servers,
    'total' => count($servers)
]);
?>