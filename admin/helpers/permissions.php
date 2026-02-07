<?php
/**
 * Helper para Controle de Acesso por Role
 * Define as regras de acesso para managers vs admins
 */

/**
 * Verificar se usuário pode remover conteúdo
 * @return bool
 */
function canDeleteContent() {
    return isAdmin();
}

/**
 * Verificar se usuário pode criar utilizadores
 * @return bool
 */
function canCreateUsers() {
    return isAdmin();
}

/**
 * Verificar se usuário pode editar utilizadores
 * @return bool
 */
function canEditUsers() {
    return isAdmin();
}

/**
 * Verificar se usuário pode remover utilizadores
 * @return bool
 */
function canDeleteUsers() {
    return isAdmin();
}

/**
 * Verificar se usuário pode editar conteúdo
 * @return bool
 */
function canEditContent() {
    return isAuthenticated();
}

/**
 * Verificar se usuário pode criar conteúdo
 * @return bool
 */
function canCreateContent() {
    return isAuthenticated();
}

/**
 * Proteger operação de deleção
 * Redireciona manager se tentar deletar
 */
function protectDelete($redirect_page = 'dashboard.php') {
    if (isManager()) {
        $_SESSION['error_message'] = 'Você não tem permissão para remover conteúdo. Apenas administradores podem deletar.';
        header('Location: ' . $redirect_page);
        exit;
    }
}

/**
 * Proteger gerenciamento de utilizadores
 * Redireciona manager se tentar acessar
 */
function protectUserManagement($redirect_page = 'dashboard.php') {
    if (isManager()) {
        $_SESSION['error_message'] = 'Acesso negado. Gerenciadores não podem gerenciar utilizadores.';
        header('Location: ' . $redirect_page);
        exit;
    }
}
?>
