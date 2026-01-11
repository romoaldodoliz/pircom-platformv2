<?php
session_start();
include('header.php');
include('config/conexao.php');

// Verificar se já existe configuração
$check_sql = "SELECT * FROM config LIMIT 1";
$check_result = mysqli_query($conn, $check_sql);
$has_config = mysqli_num_rows($check_result) > 0;

// Se já existe configuração, buscar os dados
if ($has_config) {
    $row = mysqli_fetch_assoc($check_result);
    $id = $row['id'];
}

// Se estiver tentando deletar
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM config WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Configuração removida com sucesso!";
        header('Location: configform.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao remover configuração!";
    }
    mysqli_stmt_close($stmt);
}

// Se estiver tentando atualizar
if (isset($_POST['update'])) {
    $missao = mysqli_real_escape_string($conn, $_POST["missao"]);
    $visao = mysqli_real_escape_string($conn, $_POST["visao"]);
    $valores = mysqli_real_escape_string($conn, $_POST["valores"]);
    
    if ($has_config) {
        // Atualizar
        $update_sql = "UPDATE config SET missao = ?, valores = ?, visao = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "sssi", $missao, $valores, $visao, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Configuração atualizada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar configuração!";
        }
        mysqli_stmt_close($stmt);
    }
}

// Mostrar mensagens de sucesso/erro
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['success_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . $_SESSION['error_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['error_message']);
}

mysqli_close($conn);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light"></span>Missão, Visão e Valores
            </h4>
            
            <?php if ($has_config): ?>
                <div class="badge bg-label-primary">
                    <i class="bx bx-check-circle me-1"></i>
                    Configuração existente
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <?php echo $has_config ? 'Editar Configuração' : 'Nova Configuração'; ?>
                            </h5>
                            
                            <?php if ($has_config): ?>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bx bx-trash me-1"></i> Remover
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($has_config): ?>
                            <!-- Formulário de edição -->
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="missao" class="form-label">Missão</label>
                                        <textarea class="form-control" id="missao" name="missao" rows="4" required><?php echo htmlspecialchars($row['missao']); ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="valores" class="form-label">Valores</label>
                                        <textarea class="form-control" id="valores" name="valores" rows="4" required><?php echo htmlspecialchars($row['valores']); ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="visao" class="form-label">Visão</label>
                                        <textarea class="form-control" id="visao" name="visao" rows="4" required><?php echo htmlspecialchars($row['visao']); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" name="update" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i> Atualizar Configuração
                                    </button>
                                    <a href="configuracoes.php" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                            
                        <?php else: ?>
                            <!-- Mensagem quando não há configuração -->
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bx bx-message-square-x display-1 text-muted"></i>
                                </div>
                                <h4 class="mb-3">Nenhuma configuração encontrada</h4>
                                <p class="text-muted mb-4">
                                    Não existe configuração de Missão, Visão e Valores no sistema.<br>
                                    Para adicionar, utilize o formulário de criação.
                                </p>
                                <a href="criar_configuracao.php" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i> Criar Nova Configuração
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Modal de confirmação para deletar -->
    <?php if ($has_config): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">Confirmar remoção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja remover a configuração?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <form method="POST" action="" style="display: inline;">
                        <button type="submit" name="delete" class="btn btn-danger">
                            Sim, remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <?php include('footerprincipal.php'); ?>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

<?php
include('footer.php');
?>