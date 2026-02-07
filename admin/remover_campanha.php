<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

include('config/conexao.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['campanha_id'])) {
    $campanha_id = intval($_POST['campanha_id']);

    $sql = "DELETE FROM campanhas WHERE id = $campanha_id";

    if ($conn->query($sql) === TRUE) {
        logAdminActivity('DELETE_CAMPANHA', 'Campanha ID: ' . $campanha_id);
        echo json_encode(['success' => true, 'message' => 'Campanha removida com sucesso.']);
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
