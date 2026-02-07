<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

include('config/conexao.php');
require_once('helpers/upload.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['doador_id'])) {
    $doador_id = intval($_POST['doador_id']);
    
    // Buscar comprovativo para deletar arquivo
    $stmt = $conn->prepare("SELECT comprovativo FROM doadores WHERE id = ?");
    $stmt->bind_param("i", $doador_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doador = $result->fetch_assoc();
    $stmt->close();
    
    // Deletar arquivo de comprovativo se existir
    if ($doador && $doador['comprovativo']) {
        $uploader = new ImageUploader();
        $uploader->deleteImage($doador['comprovativo']);
    }
    
    // Deletar doador do banco
    $stmt = $conn->prepare("DELETE FROM doadores WHERE id = ?");
    $stmt->bind_param("i", $doador_id);
    
    if ($stmt->execute()) {
        logAdminActivity('DELETE_DOADOR', 'Doador ID: ' . $doador_id);
        echo json_encode(['success' => true, 'message' => 'Doador removido com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao remover doador: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
}

$conn->close();
?>
