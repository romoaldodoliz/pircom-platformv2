<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');
require_once(__DIR__ . '/../config/conexao.php');

// Verificar autenticação e se é admin
requireAuth();
requireAdmin(); // Apenas admins podem gerenciar utilizadores

// Verificar se é para deletar usuário
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    
    // Não permitir deletar a si mesmo
    if ($user_id == getUserId()) {
        $_SESSION['error_message'] = "Você não pode remover seu próprio acesso!";
    } else {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Usuário removido com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao remover usuário!";
        }
        $stmt->close();
    }
    
    header("Location: utilizadores.php");
    exit();
}

// Buscar usuários
$sql = "SELECT id, nome, email, role, created_at FROM users ORDER BY nome ASC";
$result = $conn->query($sql);
?>
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Utilizadores</title>
    <?php include('header.php'); ?>
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --border-radius: 12px;
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .user-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .user-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .user-card.current-user {
            border-left: 4px solid var(--success-color);
            background: linear-gradient(to right, rgba(16, 185, 129, 0.03), transparent);
        }
        
        .user-card.other-user {
            border-left: 4px solid #e5e7eb;
        }
        
        .user-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }
        
        .user-id {
            color: #9ca3af;
            font-size: 0.8rem;
        }
        
        .user-email {
            color: #4b5563;
            font-size: 0.95rem;
        }
        
        .badge-custom {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-you {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }
        
        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .user-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .user-card:hover .user-actions {
            opacity: 1;
        }
        
        .btn-edit {
            background: white;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }
        
        .btn-edit:hover {
            background: #f9fafb;
            border-color: #3b82f6;
            color: #3b82f6;
        }
        
        .btn-password {
            background: white;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }
        
        .btn-password:hover {
            background: #fffbeb;
            border-color: var(--warning-color);
            color: var(--warning-color);
        }
        
        .btn-delete {
            background: white;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }
        
        .btn-delete:hover {
            background: #fef2f2;
            border-color: var(--danger-color);
            color: var(--danger-color);
        }

        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-admin {
            background: linear-gradient(135deg, rgba(255, 111, 15, 0.2), rgba(255, 111, 15, 0.1));
            color: #FF6F0F;
            border: 1px solid rgba(255, 111, 15, 0.3);
        }

        .badge-manager {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(102, 126, 234, 0.1));
            color: #667eea;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        .badge-active {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-you {
            background: linear-gradient(135deg, rgba(100, 200, 255, 0.2), rgba(100, 200, 255, 0.1));
            color: #0284c7;
            border: 1px solid rgba(100, 200, 255, 0.3);
        }
        
        .stats-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: var(--border-radius);
            padding: 1.5rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #f9fafb;
            border-radius: var(--border-radius);
            border: 2px dashed #e5e7eb;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #9ca3af;
            margin-bottom: 1rem;
        }
        
        .password-toggle {
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .user-actions {
                opacity: 1;
            }
            
            .stat-value {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Cabeçalho -->
            <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="page-title">
                        <i class="bx bx-user-circle me-2"></i>Gerenciar Utilizadores
                    </h1>
                    <p class="page-subtitle mb-0">Controle total sobre os utilizadores do sistema</p>
                </div>
                <div>
                    <a class="btn btn-primary" href="utilizadoresform.php">
                        <i class="bx bx-plus-circle me-1"></i> Adicionar Utilizador
                    </a>
                </div>
            </div>
            
            <!-- Mensagens -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-2"></i>
                    <?php echo $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-2"></i>
                    <?php echo $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <!-- Lista de Usuários -->
            <div class="row">
                <div class="col-12">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($user = $result->fetch_assoc()): 
                            $isCurrentUser = isset($_SESSION['user_id']) && $user['id'] == $_SESSION['user_id'];
                            $initials = strtoupper(substr($user['nome'], 0, 2));
                            $userClass = $isCurrentUser ? 'current-user' : 'other-user';
                        ?>
                            <div class="card user-card <?php echo $userClass; ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-lg-4 col-md-5">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar">
                                                    <?php echo $initials; ?>
                                                </div>
                                                <div>
                                                    <h5 class="user-name mb-1">
                                                        <?php echo htmlspecialchars($user['nome']); ?>
                                                    </h5>
                                                    <div class="user-id">ID: #<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></div>
                                                    <?php if ($isCurrentUser): ?>
                                                        <div class="mt-2">
                                                            <span class="badge-custom badge-you">
                                                                <i class="bx bx-user-check"></i> Você
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4 col-md-4">
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1">Email</small>
                                                <div class="user-email">
                                                    <i class="bx bx-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($user['email']); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block mb-1">Tipo de Conta</small>
                                                <?php 
                                                    $role = $user['role'] ?? 'manager';
                                                    if ($role === 'admin') {
                                                        echo '<span class="badge-custom badge-admin"><i class="bx bx-crown"></i> Administrador</span>';
                                                    } else {
                                                        echo '<span class="badge-custom badge-manager"><i class="bx bx-user"></i> Gerenciador</span>';
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-4 col-md-3">
                                            <div class="user-actions">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-edit" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editUserModal"
                                                            data-user-id="<?php echo $user['id']; ?>"
                                                            data-user-name="<?php echo htmlspecialchars($user['nome']); ?>"
                                                            data-user-email="<?php echo htmlspecialchars($user['email']); ?>">
                                                        <i class="bx bx-edit-alt me-1"></i> Editar
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-sm btn-password"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#changePasswordModal"
                                                            data-user-id="<?php echo $user['id']; ?>"
                                                            data-user-name="<?php echo htmlspecialchars($user['nome']); ?>">
                                                        <i class="bx bx-key me-1"></i> Senha
                                                    </button>
                                                    
                                                    <?php if (!$isCurrentUser): ?>
                                                        <button type="button" class="btn btn-sm btn-delete"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteUserModal"
                                                                data-user-id="<?php echo $user['id']; ?>"
                                                                data-user-name="<?php echo htmlspecialchars($user['nome']); ?>">
                                                            <i class="bx bx-trash me-1"></i> Remover
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        
                        <!-- Estatísticas -->
                        <div class="stats-card mt-4">
                            <h6 class="mb-4">
                                <i class="bx bx-bar-chart-alt-2 text-primary me-2"></i>
                                Estatísticas do Sistema
                            </h6>
                            <div class="row text-center">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <div class="stat-value"><?php echo $result->num_rows; ?></div>
                                    <div class="stat-label">Total de Utilizadores</div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <div class="stat-value"><?php echo $result->num_rows; ?></div>
                                    <div class="stat-label">Utilizadores Ativos</div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <div class="stat-value"><?php echo max(0, $result->num_rows - 1); ?></div>
                                    <div class="stat-label">Outros Utilizadores</div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <div class="stat-value">100%</div>
                                    <div class="stat-label">Taxa de Ativação</div>
                                </div>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bx bx-user-x"></i>
                            </div>
                            <h3 class="mb-3">Nenhum Utilizador Encontrado</h3>
                            <p class="text-muted mb-4">
                                Ainda não existem utilizadores cadastrados no sistema.<br>
                                Comece adicionando o primeiro utilizador agora.
                            </p>
                            <a class="btn btn-primary" href="utilizadoresform.php">
                                <i class="bx bx-plus-circle me-1"></i> Adicionar Primeiro Utilizador
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Utilizador -->
        <div class="modal fade" id="editUserModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-edit-alt me-2"></i>Editar Utilizador
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editUserForm" method="POST" action="atualizar_utilizador.php">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="edit_user_id">
                            
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Informação:</strong> Altere apenas os campos necessários.
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_nome" class="form-label">
                                    <i class="bx bx-user me-1"></i>Nome Completo
                                </label>
                                <input type="text" class="form-control" id="edit_nome" name="nome" 
                                       placeholder="Digite o nome completo" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">
                                    <i class="bx bx-envelope me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" id="edit_email" name="email" 
                                       placeholder="exemplo@dominio.com" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Atualizar Dados
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Alterar Senha -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                        <h5 class="modal-title">
                            <i class="bx bx-key me-2"></i>Alterar Senha
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="changePasswordForm" method="POST" action="alterar_senha.php">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="pass_user_id">
                            
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Alterando senha para: <strong id="pass_user_name"></strong>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nova_senha" class="form-label">
                                    <i class="bx bx-lock me-1"></i>Nova Senha
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" 
                                           placeholder="Digite a nova senha" required>
                                    <button class="btn btn-outline-secondary password-toggle" type="button" 
                                            onclick="togglePassword('nova_senha', this)">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Mínimo 8 caracteres</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">
                                    <i class="bx bx-lock-alt me-1"></i>Confirmar Nova Senha
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                                           placeholder="Digite novamente a nova senha" required>
                                    <button class="btn btn-outline-secondary password-toggle" type="button" 
                                            onclick="togglePassword('confirmar_senha', this)">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                                <small class="text-muted">As senhas devem coincidir</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn" style="background: #f59e0b; color: white;">
                                <i class="bx bx-check me-1"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal: Remover Utilizador -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                        <h5 class="modal-title">
                            <i class="bx bx-trash me-2"></i>Remover Acesso
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body text-center py-4">
                            <input type="hidden" name="user_id" id="delete_user_id">
                            
                            <div class="mb-4">
                                <i class="bx bx-user-x text-danger" style="font-size: 4rem;"></i>
                            </div>
                            
                            <h6 class="mb-3">
                                Remover acesso de<br>
                                <span id="delete_user_name" class="text-danger fw-bold"></span>?
                            </h6>
                            
                            <p class="text-muted small mb-0">
                                O utilizador perderá acesso imediatamente ao sistema.
                            </p>
                            
                            <div class="alert alert-danger mt-3">
                                <i class="bx bx-error-circle me-2"></i>
                                <small><strong>Atenção:</strong> Esta ação não pode ser desfeita!</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> Cancelar
                            </button>
                            <button type="submit" name="delete_user" class="btn btn-danger">
                                <i class="bx bx-trash me-1"></i> Confirmar Remoção
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <?php include('footerprincipal.php'); ?>
        <div class="content-backdrop fade"></div>
    </div>

    <?php include('footer.php'); ?>
    
    <script>
        // Modal de Editar
        document.getElementById('editUserModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('edit_user_id').value = button.getAttribute('data-user-id');
            document.getElementById('edit_nome').value = button.getAttribute('data-user-name');
            document.getElementById('edit_email').value = button.getAttribute('data-user-email');
        });
        
        // Modal de Senha
        document.getElementById('changePasswordModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('pass_user_id').value = button.getAttribute('data-user-id');
            document.getElementById('pass_user_name').textContent = button.getAttribute('data-user-name');
            document.getElementById('nova_senha').value = '';
            document.getElementById('confirmar_senha').value = '';
        });
        
        // Modal de Deletar
        document.getElementById('deleteUserModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('delete_user_id').value = button.getAttribute('data-user-id');
            document.getElementById('delete_user_name').textContent = button.getAttribute('data-user-name');
        });
        
        // Toggle Password
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        }
        
        // Validação do formulário de edição
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const nome = document.getElementById('edit_nome').value.trim();
            const email = document.getElementById('edit_email').value.trim();
            
            if (nome.length < 3) {
                e.preventDefault();
                alert('O nome deve ter pelo menos 3 caracteres.');
                return false;
            }
            
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                e.preventDefault();
                alert('Por favor, insira um email válido.');
                return false;
            }
            
            return confirm('Deseja atualizar os dados deste usuário?');
        });
        
        // Validação do formulário de senha
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (novaSenha.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres.');
                return false;
            }
            
            if (novaSenha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique.');
                return false;
            }
            
            return confirm('Deseja alterar a senha deste usuário?');
        });
    </script>
</body>
</html>