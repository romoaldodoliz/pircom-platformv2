<?php
session_start();
include('config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $nova_senha = $_POST['nova_senha'];
    
    // Validar senha
    if (strlen($nova_senha) < 8) {
        $_SESSION['error_message'] = "A senha deve ter pelo menos 8 caracteres!";
    } else {
        // Hash da nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Atualizar senha
        $sql = "UPDATE users SET senha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $senha_hash, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Senha alterada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao alterar senha!";
        }
        $stmt->close();
    }
}

$conn->close();
header("Location: utilizadores.php");
exit();
?>