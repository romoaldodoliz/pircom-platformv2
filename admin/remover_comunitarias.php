<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

include('config/conexao.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['comunitarias_id'])) {
    $comunitarias_id = intval($_POST['comunitarias_id']);

    $sql = "DELETE FROM comunitarias WHERE id = $comunitarias_id";

    if ($conn->query($sql) === TRUE) {
        logAdminActivity('DELETE_COMUNITARIA', 'Comunitária ID: ' . $comunitarias_id);
        echo json_encode(['success' => true, 'message' => 'Item removido com sucesso.']);
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
