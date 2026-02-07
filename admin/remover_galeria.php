<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

include('config/conexao.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['galeria_id'])) {
    $galeria_id = intval($_POST['galeria_id']);

    // Execute a consulta DELETE para remover o registro da base de dados
    $sql = "DELETE FROM galeria WHERE id = $galeria_id";

    if ($conn->query($sql) === TRUE) {
        logAdminActivity('DELETE_GALERIA', 'Galeria ID: ' . $galeria_id);
        echo json_encode(['success' => true, 'message' => 'Imagem removida com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao remover: ' . $conn->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
}

$conn->close();
?>
