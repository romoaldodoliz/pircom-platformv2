<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');

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
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Doador removido com sucesso.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Erro ao remover doador: ' . $conn->error];
    }
    
    $stmt->close();
} else {
    $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Parâmetros inválidos.'];
}

$conn->close();
header('Location: doadores.php');
exit;
?>