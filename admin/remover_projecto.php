<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

header('Content-Type: application/json; charset=utf-8');

include('config/conexao.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['projecto_id'])) {
    $projecto_id = intval($_POST['projecto_id']);

    $sql = "DELETE FROM projectos WHERE id = $projecto_id";

    if ($conn->query($sql) === TRUE) {
        logAdminActivity('DELETE_PROJECTO', 'Projecto ID: ' . $projecto_id);
        echo json_encode(['success' => true, 'message' => 'Projecto removido com sucesso.']);
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
