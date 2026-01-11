<?php
/**
 * Script para remover província
 */

// Incluir helper de autenticação
require_once(__DIR__ . '/helpers/auth.php');

// Verificar se usuário está autenticado
requireAuth();

include('../config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover']) && isset($_POST['provincia_id'])) {
    $provincia_id = intval($_POST['provincia_id']);
    
    // Buscar nome da província antes de remover
    $stmt = $conn->prepare("SELECT nome FROM provincias WHERE id = ?");
    $stmt->bind_param("i", $provincia_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $provincia = $result->fetch_assoc();
        $nome_provincia = $provincia['nome'];
        
        // Remover província
        $stmt = $conn->prepare("DELETE FROM provincias WHERE id = ?");
        $stmt->bind_param("i", $provincia_id);
        
        if ($stmt->execute()) {
            // Redirecionar com mensagem de sucesso
            header('Location: provincias.php?msg=success&action=removed&nome=' . urlencode($nome_provincia));
        } else {
            // Redirecionar com mensagem de erro
            header('Location: provincias.php?msg=error&action=removed');
        }
    } else {
        // Província não encontrada
        header('Location: provincias.php?msg=notfound');
    }
    
    $stmt->close();
} else {
    // Requisição inválida
    header('Location: provincias.php?msg=invalid');
}

$conn->close();
exit();
?>
