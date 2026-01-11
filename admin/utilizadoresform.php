<?php
session_start();
include('config/conexao.php');

if (isset($_POST['submit'])) {
    $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $senha = $_POST["senha"];
    
    // Verificar se email já existe
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Este email já está registrado!";
    } elseif (strlen($senha) < 8) {
        $_SESSION['error_message'] = "A senha deve ter pelo menos 8 caracteres!";
    } else {
        // Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Inserir novo usuário
        $insert_sql = "INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $nome, $email, $senha_hash);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Usuário criado com sucesso!";
            header("Location: utilizadores.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Erro ao criar usuário!";
        }
        $stmt->close();
    }
}

// Mostrar mensagens
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle me-2 fs-4"></i>
                <div>' . $_SESSION['error_message'] . '</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Utilizador</title>
    <?php include('header.php'); ?>
</head>
<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Utilizadores / </span>Adicionar Novo
            </h4>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" name="nome" required 
                                       placeholder="Digite o nome completo">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required 
                                       placeholder="exemplo@dominio.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" class="form-control" name="senha" required 
                                       placeholder="Mínimo 8 caracteres">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" name="confirmar_senha" required 
                                       placeholder="Digite a senha novamente">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="bx bx-user-plus me-1"></i> Criar Utilizador
                            </button>
                            <a href="utilizadores.php" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <?php include('footerprincipal.php'); ?>
    </div>
    
    <?php include('footer.php'); ?>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const senha = document.querySelector('input[name="senha"]').value;
            const confirmar = document.querySelector('input[name="confirmar_senha"]').value;
            
            if (senha.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres.');
                return false;
            }
            
            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique.');
                return false;
            }
            
            if (!confirm('Deseja criar este novo usuário?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>