<?php
// ============================================
// SAVE POST - API Endpoint
// ============================================

// ATIVAR DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 0); // Mudar para 0 para não exibir erros na resposta
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Limpar qualquer saída anterior
ob_clean();

// Configurações
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
date_default_timezone_set('Asia/Tokyo');

// Função para log
function logError($msg) {
    error_log("[SAVE-POST] " . $msg);
}

logError("=== NOVA REQUISIÇÃO ===");
logError("Method: " . $_SERVER['REQUEST_METHOD']);
logError("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A'));

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

// Função para retornar erro em JSON
function returnError($message, $debug = null) {
    $response = ['success' => false, 'error' => $message];
    if ($debug !== null) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    logError("Erro retornado: " . $message);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    returnError('Método não permitido. Use POST.');
}

// Criar pasta se não existir
$posts_dir = __DIR__ . '/_intra_posts_json';
logError("Diretório de posts: " . $posts_dir);

if (!is_dir($posts_dir)) {
    logError("Criando diretório: " . $posts_dir);
    if (!mkdir($posts_dir, 0755, true)) {
        returnError('Erro ao criar diretório de posts');
    }
}

// Verificar permissões do diretório
if (!is_writable($posts_dir)) {
    returnError('Diretório de posts não tem permissão de escrita');
}

// Receber dados
$raw_input = file_get_contents('php://input');
logError("Raw input: " . substr($raw_input, 0, 500));

if (empty($raw_input)) {
    returnError('Nenhum dado recebido');
}

$data = json_decode($raw_input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    logError("Erro JSON: " . json_last_error_msg());
    logError("Raw input: " . $raw_input);
    returnError('JSON inválido: ' . json_last_error_msg());
}

logError("Dados decodificados: " . print_r($data, true));

// Validar dados
if (!isset($data['titulo']) || empty(trim($data['titulo']))) {
    returnError('Título é obrigatório');
}

if (!isset($data['texto']) || empty(trim($data['texto']))) {
    returnError('Conteúdo é obrigatório');
}

// Gerar nome do arquivo
$timestamp = date('Ymd_His');
$filename = 'post_' . $timestamp . '.json';
$user_ip = getClientIP();

// Dados do post
$post_data = [
    'arquivo' => $filename,
    'titulo' => trim($data['titulo']),
    'texto' => trim($data['texto']),
    'user_ip' => $user_ip,
    'criado_em' => date('Y-m-d H:i:s'),
    'editado' => false
];

// Salvar arquivo
$caminho_completo = $posts_dir . '/' . $filename;
logError("Salvando em: " . $caminho_completo);

$json_content = json_encode($post_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($json_content === false) {
    returnError('Erro ao codificar JSON');
}

if (file_put_contents($caminho_completo, $json_content) === false) {
    logError("Erro ao salvar arquivo: " . $caminho_completo);
    logError("Permissões: " . substr(sprintf('%o', fileperms($posts_dir)), -4));
    returnError('Erro ao salvar o post');
}

// Sucesso
logError("Post salvo com sucesso: " . $filename);
echo json_encode([
    'success' => true,
    'message' => 'Post criado com sucesso!',
    'data' => $post_data
]);
?>