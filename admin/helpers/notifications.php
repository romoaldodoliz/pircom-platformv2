<?php
// Arquivo: admin/helpers/notifications.php
// Sistema de notificações para o painel administrativo

require_once(__DIR__ . '/../config/conexao.php');

/**
 * Criar notificação
 * @param int $user_id ID do utilizador
 * @param string $titulo Título da notificação
 * @param string $mensagem Mensagem detalhada
 * @param string $tipo Tipo: 'info', 'success', 'warning', 'error'
 * @param string $referencia_tipo Tipo de recurso (ex: 'noticia', 'evento')
 * @param int $referencia_id ID do recurso
 */
function criarNotificacao($user_id, $titulo, $mensagem, $tipo = 'info', $referencia_tipo = null, $referencia_id = null) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, titulo, mensagem, tipo, referencia_tipo, referencia_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt === false) {
        error_log("Erro ao preparar notificação: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param(
        "issssi",
        $user_id,
        $titulo,
        $mensagem,
        $tipo,
        $referencia_tipo,
        $referencia_id
    );
    
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Obter notificações não lidas do utilizador
 * @param int $user_id ID do utilizador
 * @return array Array de notificações
 */
function obterNotificacoesNaoLidas($user_id) {
    global $conn;
    
    // Limpar notificações expiradas
    $conn->query("DELETE FROM notifications WHERE expira_em < NOW() AND lida = 0");
    
    $stmt = $conn->prepare("
        SELECT id, titulo, mensagem, tipo, referencia_tipo, referencia_id, criada_em
        FROM notifications
        WHERE user_id = ? AND lida = 0 AND expira_em > NOW()
        ORDER BY criada_em DESC
        LIMIT 20
    ");
    
    if ($stmt === false) {
        error_log("Erro ao buscar notificações: " . $conn->error);
        return [];
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notificacoes = [];
    while ($row = $result->fetch_assoc()) {
        $notificacoes[] = $row;
    }
    
    $stmt->close();
    return $notificacoes;
}

/**
 * Contar notificações não lidas
 * @param int $user_id ID do utilizador
 * @return int Quantidade de notificações não lidas
 */
function contarNotificacoesNaoLidas($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM notifications
        WHERE user_id = ? AND lida = 0 AND expira_em > NOW()
    ");
    
    if ($stmt === false) {
        return 0;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['total'] ?? 0;
}

/**
 * Marcar notificação como lida
 * @param int $notification_id ID da notificação
 */
function marcarComoLida($notification_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE notifications
        SET lida = 1, vista_em = NOW()
        WHERE id = ?
    ");
    
    if ($stmt === false) {
        error_log("Erro ao marcar notificação: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $notification_id);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Marcar todas as notificações como lidas
 * @param int $user_id ID do utilizador
 */
function marcarTodasComoLidas($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE notifications
        SET lida = 1, vista_em = NOW()
        WHERE user_id = ? AND lida = 0
    ");
    
    if ($stmt === false) {
        error_log("Erro ao marcar notificações: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Notificar todos os admins sobre uma ação
 * @param string $titulo Título
 * @param string $mensagem Mensagem
 * @param string $tipo Tipo de notificação
 * @param string $referencia_tipo Tipo de recurso
 * @param int $referencia_id ID do recurso
 */
function notificarAdmins($titulo, $mensagem, $tipo = 'info', $referencia_tipo = null, $referencia_id = null) {
    global $conn;
    
    // Buscar todos os admins
    $result = $conn->query("SELECT id FROM users WHERE role = 'admin'");
    
    if ($result === false) {
        error_log("Erro ao buscar admins: " . $conn->error);
        return false;
    }
    
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row['id'];
    }
    
    // Criar notificação para cada admin
    foreach ($admins as $admin_id) {
        criarNotificacao($admin_id, $titulo, $mensagem, $tipo, $referencia_tipo, $referencia_id);
    }
    
    return true;
}

/**
 * Obter notificação por ID
 * @param int $notification_id ID da notificação
 * @return array|null Dados da notificação
 */
function obterNotificacao($notification_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT * FROM notifications WHERE id = ?
    ");
    
    if ($stmt === false) {
        return null;
    }
    
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notificacao = $result->fetch_assoc();
    $stmt->close();
    
    return $notificacao;
}

/**
 * Deletar notificação expirada manualmente
 * @param int $notification_id ID da notificação
 */
function deletarNotificacao($notification_id) {
    global $conn;
    
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
    
    if ($stmt === false) {
        return false;
    }
    
    $stmt->bind_param("i", $notification_id);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}
?>
