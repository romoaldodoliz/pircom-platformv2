<?php
// Arquivo: admin/actions/notificacoesAction.php
// API para gerenciar notificações via AJAX

header('Content-Type: application/json');

require_once(__DIR__ . '/../helpers/auth.php');
require_once(__DIR__ . '/../helpers/notifications.php');

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {
    case 'listar':
        $notificacoes = obterNotificacoesNaoLidas($user_id);
        echo json_encode([
            'sucesso' => true,
            'total' => count($notificacoes),
            'notificacoes' => $notificacoes
        ]);
        break;
    
    case 'contar':
        $total = contarNotificacoesNaoLidas($user_id);
        echo json_encode([
            'sucesso' => true,
            'total' => $total
        ]);
        break;
    
    case 'marcar-lida':
        $notification_id = intval($_POST['id'] ?? 0);
        if ($notification_id > 0) {
            $resultado = marcarComoLida($notification_id);
            echo json_encode([
                'sucesso' => $resultado,
                'mensagem' => $resultado ? 'Notificação marcada como lida' : 'Erro ao marcar'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
        }
        break;
    
    case 'marcar-todas':
        $resultado = marcarTodasComoLidas($user_id);
        echo json_encode([
            'sucesso' => $resultado,
            'mensagem' => $resultado ? 'Todas as notificações marcadas como lidas' : 'Erro'
        ]);
        break;
    
    case 'deletar':
        $notification_id = intval($_POST['id'] ?? 0);
        if ($notification_id > 0) {
            $resultado = deletarNotificacao($notification_id);
            echo json_encode([
                'sucesso' => $resultado,
                'mensagem' => $resultado ? 'Notificação deletada' : 'Erro ao deletar'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido']);
        }
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['erro' => 'Ação inválida']);
}
?>
