<?php
/**
 * API RESTful para gestão de províncias
 * Suporta operações: GET, POST, PUT, DELETE
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Responder a requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexão com banco de dados
require_once('../config/conexao.php');

// Função para enviar resposta JSON
function sendResponse($status, $data = [], $message = '') {
    http_response_code($status);
    echo json_encode([
        'success' => ($status >= 200 && $status < 300),
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// Função para validar coordenadas
function validarCoordenadas($latitude, $longitude) {
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        return false;
    }
    
    // Moçambique está aproximadamente entre:
    // Latitude: -26.87° a -10.47°
    // Longitude: 30.22° a 40.84°
    if ($latitude < -27 || $latitude > -10 || $longitude < 30 || $longitude > 41) {
        return false;
    }
    
    return true;
}

// Obter método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// ===== GET: Listar todas as províncias =====
if ($method === 'GET') {
    // Verificar se há um ID específico na URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id > 0) {
        // Buscar província específica
        $stmt = $conn->prepare("SELECT * FROM provincias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $provincia = $result->fetch_assoc();
            sendResponse(200, $provincia, 'Província encontrada');
        } else {
            sendResponse(404, [], 'Província não encontrada');
        }
    } else {
        // Listar todas as províncias
        $sql = "SELECT * FROM provincias ORDER BY nome ASC";
        $result = $conn->query($sql);
        
        $provincias = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $provincias[] = $row;
            }
        }
        
        sendResponse(200, $provincias, count($provincias) . ' províncias encontradas');
    }
}

// ===== POST: Adicionar nova província =====
elseif ($method === 'POST') {
    // Obter dados JSON do corpo da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validar dados obrigatórios
    if (empty($input['nome'])) {
        sendResponse(400, [], 'Nome da província é obrigatório');
    }
    
    if (empty($input['latitude']) || empty($input['longitude'])) {
        sendResponse(400, [], 'Latitude e longitude são obrigatórios');
    }
    
    $nome = trim($input['nome']);
    $latitude = floatval($input['latitude']);
    $longitude = floatval($input['longitude']);
    
    // Validar coordenadas
    if (!validarCoordenadas($latitude, $longitude)) {
        sendResponse(400, [], 'Coordenadas inválidas para Moçambique');
    }
    
    // Verificar se a província já existe
    $stmt = $conn->prepare("SELECT id FROM provincias WHERE nome = ?");
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        sendResponse(409, [], 'Província já existe no sistema');
    }
    
    // Inserir nova província
    $stmt = $conn->prepare("INSERT INTO provincias (nome, latitude, longitude) VALUES (?, ?, ?)");
    $stmt->bind_param("sdd", $nome, $latitude, $longitude);
    
    if ($stmt->execute()) {
        $nova_provincia = [
            'id' => $conn->insert_id,
            'nome' => $nome,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        sendResponse(201, $nova_provincia, 'Província adicionada com sucesso');
    } else {
        sendResponse(500, [], 'Erro ao adicionar província: ' . $conn->error);
    }
}

// ===== PUT: Atualizar província existente =====
elseif ($method === 'PUT') {
    // Obter dados JSON do corpo da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validar ID
    if (empty($input['id'])) {
        sendResponse(400, [], 'ID da província é obrigatório');
    }
    
    $id = intval($input['id']);
    
    // Verificar se a província existe
    $stmt = $conn->prepare("SELECT * FROM provincias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(404, [], 'Província não encontrada');
    }
    
    $provincia_atual = $result->fetch_assoc();
    
    // Preparar dados para atualização (manter valores atuais se não fornecidos)
    $nome = isset($input['nome']) ? trim($input['nome']) : $provincia_atual['nome'];
    $latitude = isset($input['latitude']) ? floatval($input['latitude']) : $provincia_atual['latitude'];
    $longitude = isset($input['longitude']) ? floatval($input['longitude']) : $provincia_atual['longitude'];
    
    // Validar coordenadas
    if (!validarCoordenadas($latitude, $longitude)) {
        sendResponse(400, [], 'Coordenadas inválidas para Moçambique');
    }
    
    // Verificar se outro registro já usa esse nome
    if ($nome !== $provincia_atual['nome']) {
        $stmt = $conn->prepare("SELECT id FROM provincias WHERE nome = ? AND id != ?");
        $stmt->bind_param("si", $nome, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            sendResponse(409, [], 'Já existe outra província com este nome');
        }
    }
    
    // Atualizar província
    $stmt = $conn->prepare("UPDATE provincias SET nome = ?, latitude = ?, longitude = ? WHERE id = ?");
    $stmt->bind_param("sddi", $nome, $latitude, $longitude, $id);
    
    if ($stmt->execute()) {
        $provincia_atualizada = [
            'id' => $id,
            'nome' => $nome,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        sendResponse(200, $provincia_atualizada, 'Província atualizada com sucesso');
    } else {
        sendResponse(500, [], 'Erro ao atualizar província: ' . $conn->error);
    }
}

// ===== DELETE: Remover província =====
elseif ($method === 'DELETE') {
    // Obter ID da URL ou do corpo da requisição
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id === 0) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? intval($input['id']) : 0;
    }
    
    if ($id === 0) {
        sendResponse(400, [], 'ID da província é obrigatório');
    }
    
    // Verificar se a província existe
    $stmt = $conn->prepare("SELECT nome FROM provincias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(404, [], 'Província não encontrada');
    }
    
    $provincia = $result->fetch_assoc();
    
    // Remover província
    $stmt = $conn->prepare("DELETE FROM provincias WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendResponse(200, ['id' => $id, 'nome' => $provincia['nome']], 'Província removida com sucesso');
    } else {
        sendResponse(500, [], 'Erro ao remover província: ' . $conn->error);
    }
}

// Método não suportado
else {
    sendResponse(405, [], 'Método não permitido');
}

$conn->close();
?>
