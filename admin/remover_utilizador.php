<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

include('config/conexao.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    $sql = "DELETE FROM users WHERE id = $user_id";

    if ($conn->query($sql) === TRUE) {
        logAdminActivity('DELETE_USER', 'User ID: ' . $user_id);
        echo json_encode(['success' => true, 'message' => 'Utilizador removido com sucesso.']);
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