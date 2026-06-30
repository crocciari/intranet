<?php
// ============================================
// SAVE SERVER - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

$servers_file = __DIR__ . '/_intra_server_json/servers.json';

// Criar diretório se não existir
$dir = dirname($servers_file);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Função para log
function logError($msg) {
    error_log("[SAVE-SERVER] " . $msg);
}

// Receber dados
$raw_input = file_get_contents('php://input');
if (empty($raw_input)) {
    echo json_encode(['success' => false, 'error' => 'Nenhum dado recebido']);
    exit;
}

$data = json_decode($raw_input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'JSON inválido: ' . json_last_error_msg()]);
    exit;
}

// Validar dados
if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode(['success' => false, 'error' => 'Nome do servidor é obrigatório']);
    exit;
}

if (!isset($data['ip']) || empty(trim($data['ip']))) {
    echo json_encode(['success' => false, 'error' => 'IP é obrigatório']);
    exit;
}

if (!isset($data['services']) || !is_array($data['services'])) {
    echo json_encode(['success' => false, 'error' => 'Serviços devem ser uma lista']);
    exit;
}

// Carregar servidores existentes
$servers = [];
if (file_exists($servers_file)) {
    $servers = json_decode(file_get_contents($servers_file), true);
    if (!is_array($servers)) {
        $servers = [];
    }
}

// Gerar ID ou usar existente
$server_id = $data['id'] ?? null;
if (!$server_id) {
    $server_id = 'server-' . uniqid();
}

// Criar objeto do servidor
$server = [
    'id' => $server_id,
    'name' => trim($data['name']),
    'ip' => trim($data['ip']),
    'base_ip' => $data['base_ip'] ?? '192.168.100.',
    'services' => array_map(function($service) {
        return [
            'name' => trim($service['name'] ?? ''),
            'url' => trim($service['url'] ?? ''),
            'icon' => $service['icon'] ?? 'bi-box-arrow-up-right'
        ];
    }, $data['services'])
];

// Filtrar serviços vazios
$server['services'] = array_filter($server['services'], function($s) {
    return !empty($s['name']) && !empty($s['url']);
});
$server['services'] = array_values($server['services']);

// Atualizar ou adicionar
$found = false;
foreach ($servers as $key => $s) {
    if ($s['id'] === $server_id) {
        $servers[$key] = $server;
        $found = true;
        break;
    }
}

if (!$found) {
    $servers[] = $server;
}

// Salvar arquivo
if (file_put_contents($servers_file, json_encode($servers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Servidor salvo com sucesso!',
        'data' => $server
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao salvar o servidor'
    ]);
}
?>