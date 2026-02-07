<?php
session_start();
require_once(__DIR__ . '/helpers/auth.php');
require_once(__DIR__ . '/../config/conexao.php');

// Verificar autenticação
requireAuth();
checkSessionTimeout();

$usuario_id = getUserId();
$usuario_nome = getUserName();
$usuario_email = getUserEmail();
$usuario_role = getUserRole();

$success_message = '';
$error_message = '';

// Buscar dados do usuário
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Processar atualização de perfil
if (isset($_POST['update_profile'])) {
    $nome = trim($_POST['nome'] ?? '');
    
    if (empty($nome)) {
        $error_message = 'O nome não pode estar vazio.';
    } else {
        $sql = "UPDATE users SET nome = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome, $usuario_id);
        
        if ($stmt->execute()) {
            $_SESSION['usuario_nome'] = $nome;
            $success_message = 'Perfil atualizado com sucesso!';
            $usuario_nome = $nome;
        } else {
            $error_message = 'Erro ao atualizar perfil.';
        }
        $stmt->close();
    }
}

// Processar alteração de senha
if (isset($_POST['update_password'])) {
    $senha_atual = $_POST['senha_atual'] ?? '';
    $senha_nova = $_POST['senha_nova'] ?? '';
    $senha_confirmar = $_POST['senha_confirmar'] ?? '';
    
    if (empty($senha_atual) || empty($senha_nova) || empty($senha_confirmar)) {
        $error_message = 'Por favor, preencha todos os campos de senha.';
    } elseif (strlen($senha_nova) < 6) {
        $error_message = 'A nova senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha_nova !== $senha_confirmar) {
        $error_message = 'As senhas não correspondem.';
    } elseif (!password_verify($senha_atual, $user['senha'])) {
        $error_message = 'Senha atual incorreta.';
    } else {
        $hash_senha = password_hash($senha_nova, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET senha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hash_senha, $usuario_id);
        
        if ($stmt->execute()) {
            $success_message = 'Senha alterada com sucesso!';
        } else {
            $error_message = 'Erro ao alterar senha.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Editar Perfil - PIRCOM</title>
    <?php include('header.php'); ?>
    <style>
        .profile-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1.5rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF6F0F, #ff3333);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(255, 111, 15, 0.3);
        }

        .profile-info h5 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .profile-info p {
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #FF6F0F, #ff3333);
            color: white;
        }

        .role-badge.manager {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h6 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            border-bottom: 2px solid #FF6F0F;
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #374151;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #FF6F0F;
            box-shadow: 0 0 0 3px rgba(255, 111, 15, 0.1);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FF6F0F, #ff3333);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 111, 15, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .alert i {
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .password-field-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            transition: color 0.3s ease;
            border: none;
            background: none;
            padding: 0.5rem;
            margin-top: 1.5rem;
        }

        .toggle-password:hover {
            color: #FF6F0F;
        }

        .form-control + .toggle-password {
            margin-top: -2.3rem;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 1rem;
            cursor: pointer;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: #FF6F0F;
            border-bottom-color: #FF6F0F;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-section {
                padding: 1.5rem;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .tab-button {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Conteúdo -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"></nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Mensagens de Sucesso/Erro -->
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success">
                                <i class="bx bx-check-circle"></i>
                                <span><?php echo htmlspecialchars($success_message); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <i class="bx bx-error-circle"></i>
                                <span><?php echo htmlspecialchars($error_message); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Seção de Perfil -->
                        <div class="profile-section">
                            <!-- Cabeçalho do Perfil -->
                            <div class="profile-header">
                                <div class="profile-avatar">
                                    <?php echo strtoupper(substr($usuario_nome, 0, 1)); ?>
                                </div>
                                <div class="profile-info">
                                    <h5><?php echo htmlspecialchars($usuario_nome); ?></h5>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario_email); ?></p>
                                    <p><strong>ID do Utilizador:</strong> #<?php echo $usuario_id; ?></p>
                                    <span class="role-badge <?php echo $usuario_role; ?>">
                                        <?php echo ($usuario_role === 'admin') ? 'ADMINISTRADOR' : 'GERENCIADOR DE CONTEÚDO'; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Abas -->
                            <div class="tabs">
                                <button type="button" class="tab-button active" onclick="switchTab('perfil')">
                                    <i class="bx bx-user"></i> Dados Pessoais
                                </button>
                                <button type="button" class="tab-button" onclick="switchTab('senha')">
                                    <i class="bx bx-lock"></i> Alterar Senha
                                </button>
                            </div>

                            <!-- Tab: Dados Pessoais -->
                            <div id="perfil" class="tab-content active">
                                <form method="POST">
                                    <div class="form-group">
                                        <label class="form-label">Nome Completo</label>
                                        <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario_nome); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario_email); ?>" disabled>
                                        <small style="color: #6b7280; margin-top: 0.5rem; display: block;">O email não pode ser alterado.</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Tipo de Conta</label>
                                        <input type="text" class="form-control" value="<?php echo ($usuario_role === 'admin') ? 'Administrador' : 'Gerenciador de Conteúdo'; ?>" disabled>
                                        <small style="color: #6b7280; margin-top: 0.5rem; display: block;">Apenas administradores podem alterar este valor.</small>
                                    </div>

                                    <button type="submit" name="update_profile" class="btn-primary">
                                        <i class="bx bx-save"></i> Salvar Alterações
                                    </button>
                                </form>
                            </div>

                            <!-- Tab: Alterar Senha -->
                            <div id="senha" class="tab-content">
                                <form method="POST">
                                    <div class="form-group">
                                        <label class="form-label">Senha Atual</label>
                                        <div class="password-field-group">
                                            <input type="password" name="senha_atual" class="form-control" id="senha_atual" required>
                                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('senha_atual')">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Nova Senha</label>
                                        <div class="password-field-group">
                                            <input type="password" name="senha_nova" class="form-control" id="senha_nova" required>
                                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('senha_nova')">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        </div>
                                        <small style="color: #6b7280; margin-top: 0.5rem; display: block;">Mínimo 6 caracteres</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Confirmar Nova Senha</label>
                                        <div class="password-field-group">
                                            <input type="password" name="senha_confirmar" class="form-control" id="senha_confirmar" required>
                                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('senha_confirmar')">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" name="update_password" class="btn-primary">
                                        <i class="bx bx-save"></i> Alterar Senha
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content wrapper -->
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Esconder todas as abas
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(el => {
                el.classList.remove('active');
            });

            // Mostrar aba selecionada
            document.getElementById(tab).classList.add('active');
            event.target.closest('.tab-button').classList.add('active');
        }

        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const button = event.target.closest('.toggle-password');
            
            if (field.type === 'password') {
                field.type = 'text';
                button.innerHTML = '<i class="bx bx-hide"></i>';
            } else {
                field.type = 'password';
                button.innerHTML = '<i class="bx bx-show"></i>';
            }
        }

        // Detectar tab a partir do parâmetro URL
        const params = new URLSearchParams(window.location.search);
        if (params.has('tab')) {
            const tab = params.get('tab');
            if (tab === 'password') {
                switchTab('senha');
                // Simular clique no botão correto
                document.querySelectorAll('.tab-button')[1].classList.add('active');
            }
        }
    </script>
</body>
</html>
