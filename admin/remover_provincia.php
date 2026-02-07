<?php
/**
 * Script para remover província
 */

// Incluir helper de autenticação
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

// Verificar se usuário está autenticado
requireAuth();

// Validar permissão DELETE (apenas admin)
requireDeletePermission();

include('../config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover']) && isset($_POST['provincia_id'])) {
    $provincia_id = intval($_POST['provincia_id']);
    
    // Buscar nome da província antes de remover
    $stmt = $conn->prepare("SELECT nome FROM provincias WHERE id = ?");
    $stmt->bind_param("i", $provincia_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $provincia = $result->fetch_assoc();
        $nome_provincia = $provincia['nome'];
        
        // Remover província
        $stmt = $conn->prepare("DELETE FROM provincias WHERE id = ?");
        $stmt->bind_param("i", $provincia_id);
        
        if ($stmt->execute()) {
            logAdminActivity('DELETE_PROVINCIA', 'Província: ' . $nome_provincia);
            echo json_encode(['success' => true, 'message' => 'Província removida com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao remover província.']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Província não encontrada.']);
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
}

$conn->close();
?>
