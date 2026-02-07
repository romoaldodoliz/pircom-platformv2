<?php
    session_start();
    require_once(__DIR__ . '/helpers/auth.php');
    
    // Definir header JSON ANTES de qualquer output
    header('Content-Type: application/json; charset=utf-8');
    
    include('config/conexao.php');

    // Validar autenticação
    requireAuth();
    
    // Validar permissão DELETE (apenas admin)
    requireDeletePermission();

    if (isset($_POST['noticia_id'])) {
        $noticia_id = intval($_POST['noticia_id']);

        // Execute a consulta DELETE para remover o registro da base de dados
        $sql = "DELETE FROM noticias WHERE id = $noticia_id";

        if ($conn->query($sql) === TRUE) {
            logAdminActivity('DELETE_NOTICIA', 'Notícia ID: ' . $noticia_id);
            // Redirecionar com sucesso
            header('Location: noticias.php?delete=success');
            exit;
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
