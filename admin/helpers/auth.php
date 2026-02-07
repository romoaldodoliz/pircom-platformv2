<?php
/**
 * Helper de Autenticação
 * Funções para gerenciar autenticação e segurança no painel admin
 */

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está autenticado
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_nome']);
}

/**
 * Requer autenticação - redireciona para login se não autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        // Salvar a URL que tentou acessar
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        header('Location: index.php');
        exit;
    }
}

/**
 * Fazer login do usuário
 * @param int $id
 * @param string $nome
 * @param string $email
 * @param string $role (admin ou manager)
 */
function login($id, $nome, $email, $role = 'manager') {
    $_SESSION['usuario_id'] = $id;
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['usuario_email'] = $email;
    $_SESSION['usuario_role'] = $role;
    $_SESSION['last_activity'] = time();
    
    // Regenerar ID de sessão para prevenir session fixation
    session_regenerate_id(true);
}

/**
 * Fazer logout do usuário
 */
function logout() {
    // Limpar todas as variáveis de sessão
    $_SESSION = array();
    
    // Destruir a sessão
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
    
    // Redirecionar para login
    header('Location: index.php');
    exit;
}

/**
 * Obter ID do usuário logado
 * @return int|null
 */
function getUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obter nome do usuário logado
 * @return string|null
 */
function getUserName() {
    return $_SESSION['usuario_nome'] ?? null;
}

/**
 * Obter email do usuário logado
 * @return string|null
 */
function getUserEmail() {
    return $_SESSION['usuario_email'] ?? null;
}

/**
 * Obter role do usuário logado
 * @return string|null (admin ou manager)
 */
function getUserRole() {
    return $_SESSION['usuario_role'] ?? null;
}

/**
 * Verificar se usuário é admin
 * @return bool
 */
function isAdmin() {
    return (getUserRole() === 'admin');
}

/**
 * Verificar se usuário é manager
 * @return bool
 */
function isManager() {
    return (getUserRole() === 'manager');
}

/**
 * Requer que o usuário seja admin
 * Redireciona para dashboard se não for admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error_message'] = 'Acesso negado. Esta funcionalidade está disponível apenas para administradores.';
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Verificar timeout de sessão (30 minutos de inatividade)
 * @return bool
 */
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutos
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        logout();
        return true;
    }
    
    $_SESSION['last_activity'] = time();
    return false;
}

/**
 * Gerar token anti-CSRF
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validar token anti-CSRF
 * @param string $token
 * @return bool
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Criar input hidden com token CSRF
 * @return string
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Proteger contra múltiplas tentativas de login (rate limiting básico)
 * @param string $identifier (IP ou email)
 * @return bool True se permitido, False se bloqueado
 */
function checkLoginRateLimit($identifier) {
    $key = 'login_attempts_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'first_attempt' => time()
        ];
    }
    
    $data = $_SESSION[$key];
    
    // Reset após 15 minutos
    if (time() - $data['first_attempt'] > 900) {
        $_SESSION[$key] = [
            'attempts' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    // Bloquear após 5 tentativas
    if ($data['attempts'] >= 5) {
        return false;
    }
    
    $_SESSION[$key]['attempts']++;
    return true;
}

/**
 * Limpar tentativas de login após sucesso
 * @param string $identifier
 */
function clearLoginAttempts($identifier) {
    $key = 'login_attempts_' . md5($identifier);
    unset($_SESSION[$key]);
}

/**
 * Requer permissão para deletar (apenas admin)
 * Retorna erro JSON se for manager
 */
function requireDeletePermission() {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Apenas administradores podem deletar conteúdo. Manager tem acesso limitado a edição e adição.'
        ]);
        exit;
    }
}

/**
 * Log de atividades do admin (opcional - salvar em arquivo)
 * @param string $action
 * @param string $details
 */
function logAdminActivity($action, $details = '') {
    $log_file = __DIR__ . '/../logs/admin_activity.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_entry = sprintf(
        "[%s] User: %s (ID: %s) | Action: %s | Details: %s | IP: %s\n",
        date('Y-m-d H:i:s'),
        getUserName(),
        getUserId(),
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    );
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}
