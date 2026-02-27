<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

include('config/conexao.php');

requireAuth();
requireDeletePermission();

if (isset($_POST['homepagehero_id'])) {
    $homepagehero_id = intval($_POST['homepagehero_id']);

    $stmt = $conn->prepare("DELETE FROM homepagehero WHERE id = ?");
    $stmt->bind_param("i", $homepagehero_id);

    if ($stmt->execute()) {
        logAdminActivity('DELETE_HOMEPAGEHERO', 'HomePage Hero ID: ' . $homepagehero_id);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Banner removido com sucesso.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Erro ao remover: ' . $conn->error];
    }

    $stmt->close();
} else {
    $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Parâmetros inválidos.'];
}

$conn->close();
header('Location: homepagehero.php');
exit;
?>