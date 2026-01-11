<?php
session_start();
include('config/conexao.php');

// Verificar se é para deletar usuário
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    
    // Não permitir deletar a si mesmo
    if ($user_id == $_SESSION['user_id']) {
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
$sql = "SELECT * FROM users ORDER BY id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Utilizadores</title>
    <?php include('header.php'); ?>
    <style>
        .user-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .user-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-color: #86b7fe;
        }
        .user-card.current-user {
            border-left: 4px solid #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }
        .user-card.other-user {
            border-left: 4px solid #6c757d;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .user-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .user-actions {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .user-card:hover .user-actions {
            opacity: 1;
        }
        .password-toggle {
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
        }
        .password-toggle:hover {
            color: #212529;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #6c757d;
        }
        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-you {
            background-color: #28a745;
            color: white;
        }
        .badge-admin {
            background-color: #6f42c1;
            color: white;
        }
        .modal-lg-custom {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Cabeçalho -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold py-3 mb-0">
                        <span class="text-muted fw-light">Configurações / </span>Gerenciar Utilizadores
                    </h4>
                    <p class="text-muted mb-0">Gerencie os usuários com acesso ao sistema</p>
                </div>
                
                <div>
                    <a class="btn btn-primary" href="utilizadoresform.php">
                        <i class="bx bx-user-plus me-1"></i> Adicionar Utilizador
                    </a>
                </div>
            </div>
            
            <!-- Mensagens -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle me-2 fs-4"></i>
                        <div><?php echo $_SESSION['success_message']; ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-error-circle me-2 fs-4"></i>
                        <div><?php echo $_SESSION['error_message']; ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <!-- Lista de Usuários -->
            <div class="row">
                <div class="col-md-12">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $is_current_user = isset($_SESSION['user_id']) && $row['id'] == $_SESSION['user_id'];
                            $first_letter = strtoupper(substr($row['nome'], 0, 1));
                        ?>
                            <div class="card mb-3 user-card <?php echo $is_current_user ? 'current-user' : 'other-user'; ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- Avatar e Informações -->
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar">
                                                    <?php echo $first_letter; ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($row['nome']); ?></h6>
                                                    <small class="text-muted">ID: <?php echo $row['id']; ?></small>
                                                    <div class="mt-1">
                                                        <?php if ($is_current_user): ?>
                                                            <span class="user-badge badge-you">
                                                                <i class="bx bx-user-check"></i> Você
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Detalhes do Usuário -->
                                        <div class="col-md-5">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Email</small>
                                                <div><?php echo htmlspecialchars($row['email']); ?></div>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Tipo de Conta</small>
                                                <div>
                                                    <span class="user-status status-active">Ativo</span>
                                                    <span class="user-badge badge-admin ms-2">
                                                        <i class="bx bx-crown"></i> Administrador
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Ações -->
                                        <div class="col-md-4">
                                            <div class="user-actions">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- Botão Editar -->
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editUserModal"
                                                            data-user-id="<?php echo $row['id']; ?>"
                                                            data-user-name="<?php echo htmlspecialchars($row['nome']); ?>"
                                                            data-user-email="<?php echo htmlspecialchars($row['email']); ?>">
                                                        <i class="bx bx-edit me-1"></i> Editar
                                                    </button>
                                                    
                                                    <!-- Botão Alterar Senha -->
                                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#changePasswordModal"
                                                            data-user-id="<?php echo $row['id']; ?>"
                                                            data-user-name="<?php echo htmlspecialchars($row['nome']); ?>">
                                                        <i class="bx bx-key me-1"></i> Senha
                                                    </button>
                                                    
                                                    <!-- Botão Remover (não mostrar para usuário atual) -->
                                                    <?php if (!$is_current_user): ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteUserModal"
                                                                data-user-id="<?php echo $row['id']; ?>"
                                                                data-user-name="<?php echo htmlspecialchars($row['nome']); ?>">
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
                    <?php else: ?>
                        <!-- Estado vazio -->
                        <div class="empty-state">
                            <i class="bx bx-user-x"></i>
                            <h4 class="mb-3">Nenhum Utilizador Encontrado</h4>
                            <p class="text-muted mb-4">
                                Não existem utilizadores cadastrados no sistema.<br>
                                Clique no botão "Adicionar Utilizador" para criar o primeiro.
                            </p>
                            <a class="btn btn-primary" href="utilizadoresform.php">
                                <i class="bx bx-user-plus me-1"></i> Adicionar Primeiro Utilizador
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Estatísticas -->
            <?php if ($result->num_rows > 0): 
                $total_users = $result->num_rows;
                mysqli_data_seek($result, 0);
            ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Estatísticas</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 fw-bold"><?php echo $total_users; ?></div>
                                    <div class="text-muted">Total de Utilizadores</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 fw-bold"><?php echo $total_users - 1; ?></div>
                                    <div class="text-muted">Outros Utilizadores</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 fw-bold">1</div>
                                    <div class="text-muted">Você</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 fw-bold"><?php echo $total_users; ?></div>
                                    <div class="text-muted">Ativos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- / Content -->

        <!-- Modal para Editar Usuário -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-edit me-2"></i>Editar Utilizador
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editUserForm" method="POST" action="atualizar_utilizador.php">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="edit_user_id">
                            
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Informação:</strong> Altere apenas os campos que deseja atualizar.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_nome" class="form-label">
                                        <i class="bx bx-user me-1"></i>Nome Completo
                                    </label>
                                    <input type="text" class="form-control" id="edit_nome" name="nome" 
                                           placeholder="Digite o nome completo" required>
                                    <div class="form-text">O nome será exibido em todo o sistema</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="edit_email" class="form-label">
                                        <i class="bx bx-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" class="form-control" id="edit_email" name="email" 
                                           placeholder="exemplo@dominio.com" required>
                                    <div class="form-text">Será usado para login no sistema</div>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="bx bx-shield me-2"></i>
                                <small>Para alterar a senha, utilize a opção específica "Alterar Senha"</small>
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

        <!-- Modal para Alterar Senha -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bx bx-key me-2"></i>Alterar Senha
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="changePasswordForm" method="POST" action="alterar_senha.php">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="pass_user_id">
                            
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Alterando senha para: <strong id="pass_user_name"></strong>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nova_senha" class="form-label">
                                        <i class="bx bx-lock me-1"></i>Nova Senha
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" 
                                               placeholder="Digite a nova senha" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button" 
                                                data-target="nova_senha">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Mínimo 8 caracteres</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="confirmar_senha" class="form-label">
                                        <i class="bx bx-lock-alt me-1"></i>Confirmar Nova Senha
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                                               placeholder="Digite novamente a nova senha" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button" 
                                                data-target="confirmar_senha">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">As senhas devem coincidir</div>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="bx bx-error-circle me-2"></i>
                                <small>A nova senha será aplicada imediatamente após a confirmação.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-check me-1"></i> Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Deletar Usuário -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-trash me-2"></i>Remover Acesso
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="">
                        <div class="modal-body text-center">
                            <input type="hidden" name="user_id" id="delete_user_id">
                            
                            <div class="mb-4">
                                <i class="bx bx-user-x text-danger display-4"></i>
                            </div>
                            <h6 class="mb-3">Remover acesso de <span id="delete_user_name" class="text-danger"></span>?</h6>
                            <p class="text-muted small mb-0">
                                O usuário perderá acesso imediatamente ao sistema.
                            </p>
                            <div class="alert alert-danger mt-3">
                                <i class="bx bx-error me-2"></i>
                                <small>Esta ação não pode ser desfeita!</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> Manter Acesso
                            </button>
                            <button type="submit" name="delete_user" class="btn btn-danger">
                                <i class="bx bx-trash me-1"></i> Remover Acesso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <?php include('footerprincipal.php'); ?>
        <!-- / Footer -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->

    <?php include('footer.php'); ?>
    
    <script>
        // Modal de Editar Usuário
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const userEmail = button.getAttribute('data-user-email');
                
                document.getElementById('edit_user_id').value = userId;
                document.getElementById('edit_nome').value = userName;
                document.getElementById('edit_email').value = userEmail;
                
                // Atualizar título do modal
                editUserModal.querySelector('.modal-title').innerHTML = 
                    '<i class="bx bx-edit me-2"></i>Editar: ' + userName;
            });
        }
        
        // Modal de Alterar Senha
        const changePasswordModal = document.getElementById('changePasswordModal');
        if (changePasswordModal) {
            changePasswordModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                
                document.getElementById('pass_user_id').value = userId;
                document.getElementById('pass_user_name').textContent = userName;
                
                // Atualizar título do modal
                changePasswordModal.querySelector('.modal-title').innerHTML = 
                    '<i class="bx bx-key me-2"></i>Alterar Senha: ' + userName;
            });
        }
        
        // Modal de Deletar Usuário
        const deleteUserModal = document.getElementById('deleteUserModal');
        if (deleteUserModal) {
            deleteUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                
                document.getElementById('delete_user_id').value = userId;
                document.getElementById('delete_user_name').textContent = userName;
            });
        }
        
        // Alternar visibilidade da senha
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'bx bx-hide';
                } else {
                    input.type = 'password';
                    icon.className = 'bx bx-show';
                }
            });
        });
        
        // Validação do formulário de edição
        const editUserForm = document.getElementById('editUserForm');
        if (editUserForm) {
            editUserForm.addEventListener('submit', function(e) {
                const nome = document.getElementById('edit_nome').value.trim();
                const email = document.getElementById('edit_email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (!nome || nome.length < 3) {
                    e.preventDefault();
                    alert('O nome deve ter pelo menos 3 caracteres.');
                    return false;
                }
                
                if (!email || !emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Por favor, insira um email válido.');
                    return false;
                }
                
                if (!confirm('Deseja atualizar os dados deste usuário?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Validação do formulário de senha
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
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
                
                // Verificar força da senha
                const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                if (!strongRegex.test(novaSenha)) {
                    if (!confirm('A senha não é muito forte. Recomendamos usar letras maiúsculas, minúsculas, números e símbolos.\nDeseja usar esta senha mesmo assim?')) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                if (!confirm('Deseja alterar a senha deste usuário?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        // Adicionar efeito hover nas cards
        document.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>