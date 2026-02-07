<?php
session_start();
ob_start(); // Iniciar buffer de output
include('config/conexao.php');

// Função para verificar se já existe configuração
function hasConfig($conn) {
    $sql = "SELECT COUNT(*) as count FROM config";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'] > 0;
    }
    return false;
}

// Verificar modo de edição
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 'true';
$view_mode = !$edit_mode;

// Verificar se é para deletar
if (isset($_POST['delete'])) {
    if (hasConfig($conn)) {
        $sql = "DELETE FROM config";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "Configuração removida com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao remover configuração!";
        }
        header("Location: configuracoes.php");
        exit();
    }
}

// Verificar se é para atualizar
if (isset($_POST['update'])) {
    $missao = mysqli_real_escape_string($conn, $_POST["missao"]);
    $visao = mysqli_real_escape_string($conn, $_POST["visao"]);
    $valores = mysqli_real_escape_string($conn, $_POST["valores"]);
    
    if (hasConfig($conn)) {
        // Atualizar
        $sql = "UPDATE config SET missao = '$missao', valores = '$valores', visao = '$visao', updated_at = NOW() ORDER BY id LIMIT 1";
    } else {
        // Inserir (primeira vez)
        $sql = "INSERT INTO config (missao, valores, visao, created_at, updated_at) VALUES ('$missao', '$valores', '$visao', NOW(), NOW())";
    }
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Configuração " . (hasConfig($conn) ? "atualizada" : "criada") . " com sucesso!";
        header("Location: configuracoes.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao salvar configuração!";
    }
}

// Buscar configuração existente
$config = null;
$has_config = false;
$sql = "SELECT * FROM config LIMIT 1";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $config = mysqli_fetch_assoc($result);
    $has_config = true;
}

// Calcular estatísticas
if ($has_config) {
    $total_chars = strlen($config['missao']) + strlen($config['valores']) + strlen($config['visao']);
    $last_update = isset($config['updated_at']) ? $config['updated_at'] : $config['created_at'];
    $created_at = $config['created_at'];
}

mysqli_close($conn);
ob_end_flush(); // Limpar buffer e enviar output
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Missão, Visão e Valores</title>
    <?php include('header.php'); ?>
</head>
<body>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <h4 class="fw-bold py-3 mb-2">Configurações Institucionais</h4>
                    <p class="text-muted mb-0">Gerencie os textos de Missão, Visão e Valores da organização</p>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                        <?php if ($has_config): ?>
                            <?php if (!$edit_mode): ?>
                                <!-- Botão para entrar no modo edição -->
                                <a href="?edit=true" class="btn btn-warning">
                                    <i class="bx bx-edit me-1"></i> Editar Configuração
                                </a>
                                <!-- Botão remover -->
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bx bx-trash me-1"></i> Remover
                                </button>
                            <?php else: ?>
                                <!-- Botão para sair do modo edição -->
                                <a href="configuracoes.php" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i> Cancelar Edição
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Botão criar configuração -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bx bx-plus me-1"></i> Criar Configuração
                            </button>
                        <?php endif; ?>
                    </div>
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
            
            <!-- Status Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card status-card <?php echo $has_config ? ($edit_mode ? 'border-warning' : 'border-success') : 'border-secondary'; ?>">
                        <div class="card-body py-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="status-indicator <?php echo $has_config ? ($edit_mode ? 'bg-warning' : 'bg-success') : 'bg-secondary'; ?>">
                                        <i class="bx <?php echo $has_config ? ($edit_mode ? 'bx-edit' : 'bx-check-circle') : 'bx-info-circle'; ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">
                                            <?php echo $has_config ? 'Configuração Ativa' : 'Configuração Não Definida'; ?>
                                        </h6>
                                        <p class="text-muted mb-0 small">
                                            <?php if ($has_config && $edit_mode): ?>
                                                <span class="text-warning">Modo de edição ativo - Faça suas alterações</span>
                                            <?php elseif ($has_config): ?>
                                                Configuração publicada e visível no site
                                            <?php else: ?>
                                                Nenhuma configuração definida. Clique em "Criar Configuração" para começar.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-md-end">
                                    <?php if ($has_config && !$edit_mode): ?>
                                        <small class="text-muted">
                                            <i class="bx bx-calendar me-1"></i>
                                            Última atualização: <?php echo date('d/m/Y H:i', strtotime($last_update)); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($has_config): ?>
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-bullseye"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="ms-1 mb-0">Missão</h4>
                                    <p class="mb-0 text-muted small">Texto definido</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-label-primary">
                                    <?php echo strlen($config['missao']); ?> caracteres
                                </span>
                                <i class="bx bx-check text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="bx bx-heart"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="ms-1 mb-0">Valores</h4>
                                    <p class="mb-0 text-muted small">Texto definido</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-label-info">
                                    <?php echo strlen($config['valores']); ?> caracteres
                                </span>
                                <i class="bx bx-check text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-show-alt"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="ms-1 mb-0">Visão</h4>
                                    <p class="mb-0 text-muted small">Texto definido</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-label-warning">
                                    <?php echo strlen($config['visao']); ?> caracteres
                                </span>
                                <i class="bx bx-check text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-stats"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="ms-1 mb-0">Total</h4>
                                    <p class="mb-0 text-muted small">Todos os textos</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-label-success">
                                    <?php echo $total_chars; ?> caracteres
                                </span>
                                <span class="badge bg-label-primary">3 seções</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Main Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-file-text me-2"></i>
                                <?php echo $has_config ? ($edit_mode ? 'Editando Configuração' : 'Visualização da Configuração') : 'Configuração Institucional'; ?>
                            </h5>
                            <?php if ($has_config && $edit_mode): ?>
                                <div class="mt-2 mt-md-0">
                                    <span class="badge bg-warning">
                                        <i class="bx bx-edit"></i> Modo de Edição Ativo
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <?php if ($has_config): ?>
                                <?php if (!$edit_mode): ?>
                                    <!-- MODE VIEW -->
                                    <div class="config-view-mode">
                                        <div class="row">
                                            <div class="col-lg-4 mb-4">
                                                <div class="card config-section h-100">
                                                    <div class="card-header bg-primary text-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="section-icon me-2">
                                                                <i class="bx bx-bullseye"></i>
                                                            </div>
                                                            <h6 class="mb-0">Missão</h6>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="config-content">
                                                            <?php echo nl2br(htmlspecialchars($config['missao'])); ?>
                                                        </div>
                                                        <div class="mt-3 pt-3 border-top">
                                                            <small class="text-muted">
                                                                <i class="bx bx-text"></i> 
                                                                <?php echo strlen($config['missao']); ?> caracteres
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 mb-4">
                                                <div class="card config-section h-100">
                                                    <div class="card-header bg-info text-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="section-icon me-2">
                                                                <i class="bx bx-heart"></i>
                                                            </div>
                                                            <h6 class="mb-0">Valores</h6>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="config-content">
                                                            <?php echo nl2br(htmlspecialchars($config['valores'])); ?>
                                                        </div>
                                                        <div class="mt-3 pt-3 border-top">
                                                            <small class="text-muted">
                                                                <i class="bx bx-text"></i> 
                                                                <?php echo strlen($config['valores']); ?> caracteres
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 mb-4">
                                                <div class="card config-section h-100">
                                                    <div class="card-header bg-warning">
                                                        <div class="d-flex align-items-center">
                                                            <div class="section-icon me-2">
                                                                <i class="bx bx-show-alt"></i>
                                                            </div>
                                                            <h6 class="mb-0">Visão</h6>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="config-content">
                                                            <?php echo nl2br(htmlspecialchars($config['visao'])); ?>
                                                        </div>
                                                        <div class="mt-3 pt-3 border-top">
                                                            <small class="text-muted">
                                                                <i class="bx bx-text"></i> 
                                                                <?php echo strlen($config['visao']); ?> caracteres
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bx bx-info-circle me-3 fs-4"></i>
                                                        <div>
                                                            <strong>Informação:</strong> Para editar os textos, clique no botão "Editar Configuração" no topo da página.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- MODE EDIT -->
                                    <form method="POST" action="" id="configForm" class="edit-mode-form">
                                        <div class="row">
                                            <div class="col-lg-4 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-header bg-primary text-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="section-icon me-2">
                                                                <i class="bx bx-bullseye"></i>
                                                            </div>
                                                            <h6 class="mb-0">Missão</h6>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Descrição da Missão:</label>
                                                            <textarea class="form-control" 
                                                                      name="missao" 
                                                                      rows="8"
                                                                      required
                                                                      placeholder="Descreva a missão principal da organização..."
                                                                      oninput="updateCharCount(this, 'missao-count')"><?php echo htmlspecialchars($config['missao']); ?></textarea>
                                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                                <small class="text-muted">Campo obrigatório</small>
                                                                <small class="char-counter">
                                                                    <span id="missao-count"><?php echo strlen($config['missao']); ?></span> caracteres
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-header bg-info text-white">
                                                        <div class="d-flex align-items-center">
                                                            <div class="section-icon me-2">
                                                                <i class="bx bx-heart"></i>
                                                            </div>
                                                            <h6 class="mb-0">Valores</h6>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Lista de Valores:</label>
                                                            <textarea class="form-control" 
                                                                      name="valores" 
                                                                      rows="8"
                                                                      required
                                                                      placeholder="Liste os valores fundamentais da organização..."
                                                                      oninput="updateCharCount(this, 'valores-count')"><?php echo htmlspecialchars($config['valores']); ?></textarea>
                                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                                <small class="text-muted">Campo obrigatório</small>
                                                                <small class="char-counter">
                                                                    <span id="valores-count"><?php echo strlen($config['valores']); ?></span> caracteres
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-header bg-warning">
                                                        <div class="d-flex align-items-center">
                                                            <div class="section-icon me-2">
                                                                <i class="bx bx-show-alt"></i>
                                                            </div>
                                                            <h6 class="mb-0">Visão</h6>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Descrição da Visão:</label>
                                                            <textarea class="form-control" 
                                                                      name="visao" 
                                                                      rows="8"
                                                                      required
                                                                      placeholder="Descreva a visão futura da organização..."
                                                                      oninput="updateCharCount(this, 'visao-count')"><?php echo htmlspecialchars($config['visao']); ?></textarea>
                                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                                <small class="text-muted">Campo obrigatório</small>
                                                                <small class="char-counter">
                                                                    <span id="visao-count"><?php echo strlen($config['visao']); ?></span> caracteres
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="action-buttons">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="form-text">
                                                                <i class="bx bx-info-circle me-1"></i>
                                                                Todos os campos são obrigatórios. Mínimo recomendado: 50 caracteres por seção.
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="configuracoes.php" class="btn btn-outline-secondary">
                                                                <i class="bx bx-x me-1"></i> Cancelar
                                                            </a>
                                                            <button type="submit" name="update" class="btn btn-primary">
                                                                <i class="bx bx-save me-1"></i> Salvar Alterações
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- NO CONFIGURATION -->
                                <div class="empty-state text-center py-5">
                                    <div class="empty-state-icon mb-4">
                                        <i class="bx bx-file-text display-1 text-muted"></i>
                                    </div>
                                    <h4 class="mb-3">Nenhuma Configuração Encontrada</h4>
                                    <p class="text-muted mb-4">
                                        Você ainda não configurou os textos institucionais da organização.<br>
                                        Estes textos serão exibidos publicamente no site.
                                    </p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                                        <i class="bx bx-plus me-1"></i> Criar Primeira Configuração
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <?php if ($has_config && !$edit_mode): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="bx bx-info-circle"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1">Sobre as Configurações Institucionais</h6>
                                    <p class="mb-2 text-muted">
                                        Os textos de Missão, Visão e Valores são fundamentais para comunicar a identidade e propósito da organização.
                                        Eles são exibidos publicamente no site e devem refletir os princípios e objetivos da empresa.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-label-primary">
                                            <i class="bx bx-globe"></i> Visível publicamente
                                        </span>
                                        <span class="badge bg-label-success">
                                            <i class="bx bx-time"></i> Última atualização: <?php echo date('d/m/Y', strtotime($last_update)); ?>
                                        </span>
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-calendar-plus"></i> Criado em: <?php echo date('d/m/Y', strtotime($created_at)); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <!-- / Content -->

        <!-- Modal para criar configuração (primeira vez) -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-plus-circle me-2"></i>Criar Nova Configuração
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="" id="createForm">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Atenção:</strong> Esta configuração define a identidade institucional da organização e será exibida publicamente.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Missão</label>
                                    <textarea class="form-control" name="missao" rows="4" required 
                                              placeholder="Descreva a missão principal da organização..."></textarea>
                                    <div class="form-text">Qual é o propósito principal da organização?</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Valores</label>
                                    <textarea class="form-control" name="valores" rows="4" required 
                                              placeholder="Liste os valores fundamentais da organização..."></textarea>
                                    <div class="form-text">Quais são os princípios e valores que guiam a organização?</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Visão</label>
                                    <textarea class="form-control" name="visao" rows="4" required 
                                              placeholder="Descreva a visão futura da organização..."></textarea>
                                    <div class="form-text">Qual é o objetivo de longo prazo da organização?</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Criar Configuração
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de confirmação para deletar -->
        <?php if ($has_config && !$edit_mode): ?>
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-trash me-2"></i>Confirmar Remoção
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-4">
                            <div class="avatar avatar-xl bg-label-danger mb-3">
                                <i class="bx bx-error-circle fs-1"></i>
                            </div>
                            <h6 class="mb-3">Remover Configuração?</h6>
                            <p class="text-muted small mb-0">
                                Esta ação irá remover permanentemente todos os textos de Missão, Visão e Valores.
                            </p>
                            <div class="alert alert-warning mt-3">
                                <i class="bx bx-error me-2"></i>
                                <small>Esta ação não pode ser desfeita!</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Cancelar
                        </button>
                        <form method="POST" action="" style="display: inline;">
                            <button type="submit" name="delete" class="btn btn-danger">
                                <i class="bx bx-trash me-1"></i> Confirmar Remoção
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

    <?php include('footer.php'); ?>
    
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            border-radius: 10px;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 1.5rem;
        }
        
        .status-card {
            border-width: 2px !important;
        }
        
        .status-indicator {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .bg-warning {
            background-color: #ffab00 !important;
        }
        
        .bg-success {
            background-color: #71dd37 !important;
        }
        
        .bg-secondary {
            background-color: #8592a3 !important;
        }
        
        .config-section .card-header {
            color: white;
            border-radius: 8px 8px 0 0 !important;
        }
        
        .config-section .section-icon {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .config-content {
            line-height: 1.8;
            color: #566a7f;
            white-space: pre-wrap;
            font-size: 1rem;
        }
        
        .empty-state {
            padding: 3rem 1rem;
        }
        
        .empty-state-icon {
            color: #b4bdc6;
        }
        
        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .card-border-shadow-primary {
            border: 1px solid;
            border-color: #696cff;
        }
        
        .card-border-shadow-info {
            border: 1px solid;
            border-color: #17c1e8;
        }
        
        .card-border-shadow-warning {
            border: 1px solid;
            border-color: #ffab00;
        }
        
        .card-border-shadow-success {
            border: 1px solid;
            border-color: #71dd37;
        }
        
        .action-buttons {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .char-counter {
            font-size: 0.875rem;
            color: #8592a3;
        }
        
        textarea.form-control {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        textarea.form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        .edit-mode-form .card {
            box-shadow: 0 4px 20px rgba(255, 193, 7, 0.1);
        }
        
        @media (max-width: 768px) {
            .status-indicator {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }
            
            .card-header {
                padding: 1rem;
            }
            
            .config-content {
                font-size: 0.875rem;
            }
            
            textarea.form-control {
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 576px) {
            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
            
            .empty-state {
                padding: 2rem 1rem;
            }
            
            .action-buttons {
                padding: 1rem;
            }
        }
    </style>
    
    <script>
        // Atualizar contador de caracteres
        function updateCharCount(textarea, counterId) {
            const counter = document.getElementById(counterId);
            if (counter) {
                const charCount = textarea.value.length;
                counter.textContent = charCount;
                
                // Mudar cor conforme tamanho
                if (charCount < 20) {
                    counter.style.color = '#dc3545';
                } else if (charCount < 50) {
                    counter.style.color = '#ffab00';
                } else {
                    counter.style.color = '#71dd37';
                }
            }
        }
        
        // Inicializar contadores
        document.addEventListener('DOMContentLoaded', function() {
            // Atualizar contadores se estiver em modo edição
            const missaoField = document.querySelector('textarea[name="missao"]');
            const valoresField = document.querySelector('textarea[name="valores"]');
            const visaoField = document.querySelector('textarea[name="visao"]');
            
            if (missaoField) {
                updateCharCount(missaoField, 'missao-count');
                missaoField.addEventListener('input', function() {
                    updateCharCount(this, 'missao-count');
                });
            }
            
            if (valoresField) {
                updateCharCount(valoresField, 'valores-count');
                valoresField.addEventListener('input', function() {
                    updateCharCount(this, 'valores-count');
                });
            }
            
            if (visaoField) {
                updateCharCount(visaoField, 'visao-count');
                visaoField.addEventListener('input', function() {
                    updateCharCount(this, 'visao-count');
                });
            }
            
            // Validação do formulário de edição
            const editForm = document.getElementById('configForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    let errors = [];
                    const submitBtn = this.querySelector('button[type="submit"]');
                    
                    // Validar cada campo
                    const fields = [
                        { name: 'missao', label: 'Missão', min: 20 },
                        { name: 'valores', label: 'Valores', min: 20 },
                        { name: 'visao', label: 'Visão', min: 20 }
                    ];
                    
                    fields.forEach(field => {
                        const textarea = this.querySelector(`textarea[name="${field.name}"]`);
                        const value = textarea.value.trim();
                        
                        if (!value) {
                            errors.push(`O campo "${field.label}" é obrigatório`);
                            textarea.classList.add('is-invalid');
                        } else if (value.length < field.min) {
                            errors.push(`O campo "${field.label}" deve ter pelo menos ${field.min} caracteres`);
                            textarea.classList.add('is-invalid');
                        } else {
                            textarea.classList.remove('is-invalid');
                        }
                    });
                    
                    if (errors.length > 0) {
                        showFormErrors(errors);
                        return false;
                    }
                    
                    // Confirmar antes de salvar
                    if (confirm('Deseja salvar as alterações na configuração?')) {
                        // Mostrar indicador de carregamento
                        submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Salvando...';
                        submitBtn.disabled = true;
                        
                        // Enviar formulário
                        this.submit();
                    }
                });
            }
            
            // Validação do formulário de criação
            const createForm = document.getElementById('createForm');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    let errors = [];
                    const fields = [
                        { name: 'missao', label: 'Missão', min: 20 },
                        { name: 'valores', label: 'Valores', min: 20 },
                        { name: 'visao', label: 'Visão', min: 20 }
                    ];
                    
                    fields.forEach(field => {
                        const textarea = this.querySelector(`textarea[name="${field.name}"]`);
                        const value = textarea.value.trim();
                        
                        if (!value || value.length < field.min) {
                            errors.push(`O campo "${field.label}" deve ter pelo menos ${field.min} caracteres`);
                            textarea.classList.add('is-invalid');
                        } else {
                            textarea.classList.remove('is-invalid');
                        }
                    });
                    
                    if (errors.length > 0) {
                        showFormErrors(errors);
                        return false;
                    }
                    
                    // Confirmar criação
                    if (confirm('Deseja criar a configuração institucional?')) {
                        this.submit();
                    }
                });
            }
            
            // Remover classes de erro ao digitar
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
        
        function showFormErrors(errors) {
            // Remover erros anteriores
            const existingErrors = document.querySelectorAll('.form-errors');
            existingErrors.forEach(error => error.remove());
            
            // Criar container de erros
            const errorContainer = document.createElement('div');
            errorContainer.className = 'form-errors';
            
            // Criar alerta de erros
            let errorHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-error-circle me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Erros de Validação</h6>
                            <ul class="mb-0 ps-3">
            `;
            
            errors.forEach(error => {
                errorHtml += `<li>${error}</li>`;
            });
            
            errorHtml += `
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            errorContainer.innerHTML = errorHtml;
            
            // Inserir no topo do conteúdo
            const content = document.querySelector('.container-p-y');
            if (content) {
                content.insertBefore(errorContainer, content.firstChild);
                
                // Rolar para o topo
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        // Efeito de destaque para modo de edição
        if (window.location.search.includes('edit=true')) {
            document.addEventListener('DOMContentLoaded', function() {
                const editCards = document.querySelectorAll('.edit-mode-form .card');
                editCards.forEach(card => {
                    card.style.animation = 'pulse 2s infinite';
                });
                
                // Adicionar estilo de animação
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes pulse {
                        0% { box-shadow: 0 4px 20px rgba(255, 193, 7, 0.1); }
                        50% { box-shadow: 0 4px 30px rgba(255, 193, 7, 0.2); }
                        100% { box-shadow: 0 4px 20px rgba(255, 193, 7, 0.1); }
                    }
                `;
                document.head.appendChild(style);
                
                // Focar no primeiro campo
                setTimeout(() => {
                    const firstField = document.querySelector('textarea[name="missao"]');
                    if (firstField) {
                        firstField.focus();
                        firstField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 500);
            });
        }
    </script>
</body>
</html>