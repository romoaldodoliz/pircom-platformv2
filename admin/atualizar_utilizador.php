<?php
session_start();
include('config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Verificar se email já existe (exceto para o próprio usuário)
    $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Este email já está em uso por outro usuário!";
    } else {
        // Atualizar usuário
        $update_sql = "UPDATE users SET nome = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $nome, $email, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Dados do usuário atualizados com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar dados do usuário!";
        }
    }
    $stmt->close();
}

$conn->close();
header("Location: utilizadores.php");
exit();
?>