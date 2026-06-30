<?php
// ============================================
// DELETE SERVER - API Endpoint
// ============================================

header('Content-Type: application/json');
date_default_timezone_set('Asia/Tokyo');

$servers_file = __DIR__ . '/_intra_server_json/servers.json';

// Receber dados
$raw_input = file_get_contents('php://input');
if (empty($raw_input)) {
    echo json_encode(['success' => false, 'error' => 'Nenhum dado recebido']);
    exit;
}

$data = json_decode($raw_input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

if (!isset($data['id']) || empty(trim($data['id']))) {
    echo json_encode(['success' => false, 'error' => 'ID do servidor é obrigatório']);
    exit;
}

$server_id = trim($data['id']);

// Carregar servidores
if (!file_exists($servers_file)) {
    echo json_encode(['success' => false, 'error' => 'Arquivo de servidores não encontrado']);
    exit;
}

$servers = json_decode(file_get_contents($servers_file), true);
if (!is_array($servers)) {
    echo json_encode(['success' => false, 'error' => 'Erro ao ler servidores']);
    exit;
}

// Remover servidor
$found = false;
$servers = array_filter($servers, function($s) use ($server_id, &$found) {
    if ($s['id'] === $server_id) {
        $found = true;
        return false;
    }
    return true;
});
$servers = array_values($servers);

if (!$found) {
    echo json_encode(['success' => false, 'error' => 'Servidor não encontrado']);
    exit;
}

// Salvar arquivo
if (file_put_contents($servers_file, json_encode($servers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'message' => 'Servidor removido com sucesso!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao remover servidor'
    ]);
}
?>