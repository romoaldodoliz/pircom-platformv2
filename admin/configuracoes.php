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
        $sql = "UPDATE config SET missao = '$missao', valores = '$valores', visao = '$visao' ORDER BY id LIMIT 1";
    } else {
        // Inserir (primeira vez)
        $sql = "INSERT INTO config (missao, valores, visao) VALUES ('$missao', '$valores', '$visao')";
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
    <style>
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }
        .config-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        .config-card.view-mode {
            border-left-color: #28a745;
        }
        .config-card.edit-mode {
            border-left-color: #ffc107;
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.15);
        }
        .config-card.empty-mode {
            border-left-color: #6c757d;
        }
        .edit-indicator {
            position: absolute;
            top: -10px;
            right: 20px;
            background: #ffc107;
            color: #212529;
            padding: 5px 15px;
            border-radius: 0 0 10px 10px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .content-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            position: relative;
        }
        .content-box h6 {
            color: #495057;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #dee2e6;
        }
        .content-box.view-mode {
            background: white;
        }
        .content-box.edit-mode textarea {
            background: white;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s;
        }
        .content-box.edit-mode textarea:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .action-buttons {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 1rem;
            margin: 1rem -1rem -1rem -1rem;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .view-content {
            line-height: 1.8;
            color: #495057;
            white-space: pre-wrap;
            font-size: 1rem;
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
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-label i {
            color: #6c757d;
        }
        .textarea-counter {
            font-size: 0.75rem;
            color: #6c757d;
            text-align: right;
            margin-top: 0.25rem;
        }
        .mode-switch {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: #e9ecef;
            color: #495057;
        }
        .mode-switch i {
            font-size: 1.1rem;
        }
        .config-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .config-summary .stat {
            text-align: center;
            padding: 0.5rem;
        }
        .config-summary .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            line-height: 1;
        }
        .config-summary .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        .char-count {
            position: absolute;
            right: 15px;
            bottom: 10px;
            font-size: 0.75rem;
            color: #6c757d;
            background: rgba(255,255,255,0.9);
            padding: 2px 5px;
            border-radius: 3px;
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
                        <span class="text-muted fw-light">Configurações / </span>Missão, Visão e Valores
                    </h4>
                    <p class="text-muted mb-0">Gerencie os textos institucionais da organização</p>
                </div>
                
                <div>
                    <?php if ($has_config): ?>
                        <?php if ($edit_mode): ?>
                            <div class="badge bg-warning text-dark status-badge">
                                <i class="bx bx-edit me-1"></i>
                                Modo Edição
                            </div>
                        <?php else: ?>
                            <div class="badge bg-success status-badge">
                                <i class="bx bx-check-circle me-1"></i>
                                Configuração Ativa
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="badge bg-secondary status-badge">
                            <i class="bx bx-info-circle me-1"></i>
                            Não Configurado
                        </div>
                    <?php endif; ?>
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
            
            <!-- Resumo da Configuração -->
            <?php if ($has_config): ?>
            <div class="config-summary">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat">
                            <div class="stat-value">3</div>
                            <div class="stat-label">Seções Configuradas</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat">
                            <div class="stat-value">
                                <?php echo strlen($config['missao']) + strlen($config['valores']) + strlen($config['visao']); ?>
                            </div>
                            <div class="stat-label">Caracteres Totais</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat">
                            <div class="stat-value">
                                <?php 
                                $last_update = isset($config['updated_at']) ? $config['updated_at'] : date('Y-m-d H:i:s');
                                echo date('d/m/Y', strtotime($last_update)); 
                                ?>
                            </div>
                            <div class="stat-label">Última Atualização</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Card Principal -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4 config-card <?php echo $has_config ? ($edit_mode ? 'edit-mode' : 'view-mode') : 'empty-mode'; ?>">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <?php if ($has_config): ?>
                                            <?php if ($edit_mode): ?>
                                                <i class="bx bx-edit text-warning me-2"></i>Editando Configuração
                                            <?php else: ?>
                                                <i class="bx bx-show text-success me-2"></i>Visualizando Configuração
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <i class="bx bx-plus-circle text-secondary me-2"></i>Criar Nova Configuração
                                        <?php endif; ?>
                                    </h5>
                                    <?php if ($has_config && !$edit_mode): ?>
                                        <p class="text-muted mb-0 small">Clique no botão "Editar" para modificar os textos</p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($has_config): ?>
                                        <?php if (!$edit_mode): ?>
                                            <!-- Botão para entrar no modo edição -->
                                            <a href="?edit=true" class="btn btn-warning">
                                                <i class="bx bx-edit me-1"></i> Editar
                                            </a>
                                        <?php else: ?>
                                            <!-- Botão para sair do modo edição -->
                                            <a href="configuracoes.php" class="btn btn-outline-secondary">
                                                <i class="bx bx-x me-1"></i> Cancelar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!$edit_mode): ?>
                                            <!-- Botão remover (só no modo visualização) -->
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="bx bx-trash me-1"></i> Remover
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($edit_mode): ?>
                                <div class="edit-indicator">MODO EDIÇÃO ATIVO</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <?php if ($has_config): ?>
                                <!-- CONFIGURAÇÃO EXISTENTE -->
                                <?php if (!$edit_mode): ?>
                                    <!-- MODO VISUALIZAÇÃO -->
                                    <div class="content-box view-mode">
                                        <h6><i class="bx bx-bullseye me-2"></i>Missão</h6>
                                        <div class="view-content"><?php echo nl2br(htmlspecialchars($config['missao'])); ?></div>
                                    </div>
                                    
                                    <div class="content-box view-mode">
                                        <h6><i class="bx bx-heart me-2"></i>Valores</h6>
                                        <div class="view-content"><?php echo nl2br(htmlspecialchars($config['valores'])); ?></div>
                                    </div>
                                    
                                    <div class="content-box view-mode">
                                        <h6><i class="bx bx-show-alt me-2"></i>Visão</h6>
                                        <div class="view-content"><?php echo nl2br(htmlspecialchars($config['visao'])); ?></div>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <i class="bx bx-info-circle me-2"></i>
                                        <strong>Informação:</strong> Para editar os textos, clique no botão "Editar" acima.
                                    </div>
                                    
                                <?php else: ?>
                                    <!-- MODO EDIÇÃO -->
                                    <form method="POST" action="" id="configForm">
                                        <div class="content-box edit-mode">
                                            <label for="missao" class="form-label">
                                                <i class="bx bx-bullseye"></i>Missão
                                            </label>
                                            <textarea class="form-control" id="missao" name="missao" rows="6" required 
                                                      placeholder="Descreva a missão principal da organização..."
                                                      oninput="updateCharCount('missao')"><?php echo htmlspecialchars($config['missao']); ?></textarea>
                                            <div class="textarea-counter" id="missao-counter">
                                                Caracteres: <span id="missao-chars"><?php echo strlen($config['missao']); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="content-box edit-mode">
                                            <label for="valores" class="form-label">
                                                <i class="bx bx-heart"></i>Valores
                                            </label>
                                            <textarea class="form-control" id="valores" name="valores" rows="6" required 
                                                      placeholder="Liste os valores fundamentais da organização (separados por vírgula ou ponto)..."
                                                      oninput="updateCharCount('valores')"><?php echo htmlspecialchars($config['valores']); ?></textarea>
                                            <div class="textarea-counter" id="valores-counter">
                                                Caracteres: <span id="valores-chars"><?php echo strlen($config['valores']); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="content-box edit-mode">
                                            <label for="visao" class="form-label">
                                                <i class="bx bx-show-alt"></i>Visão
                                            </label>
                                            <textarea class="form-control" id="visao" name="visao" rows="6" required 
                                                      placeholder="Descreva a visão futura e objetivos da organização..."
                                                      oninput="updateCharCount('visao')"><?php echo htmlspecialchars($config['visao']); ?></textarea>
                                            <div class="textarea-counter" id="visao-counter">
                                                Caracteres: <span id="visao-chars"><?php echo strlen($config['visao']); ?></span>
                                            </div>
                                        </div>
                                        
                                        <!-- Botões de ação no modo edição -->
                                        <div class="action-buttons">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="text-muted small">
                                                        <i class="bx bx-info-circle me-1"></i>
                                                        Todos os campos são obrigatórios
                                                    </span>
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
                                    </form>
                                    
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <!-- SEM CONFIGURAÇÃO - CRIAR PELA PRIMEIRA VEZ -->
                                <div class="empty-state">
                                    <i class="bx bx-file-blank"></i>
                                    <h4 class="mb-3">Nenhuma Configuração Encontrada</h4>
                                    <p class="text-muted mb-4">
                                        Você ainda não configurou a Missão, Visão e Valores da organização.<br>
                                        Clique no botão abaixo para criar a primeira configuração.
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
                    <form method="POST" action="">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Atenção:</strong> Esta configuração só pode ser criada uma vez. Após criar, você poderá apenas editar ou remover.
                            </div>
                            
                            <div class="mb-3">
                                <label for="modal-missao" class="form-label">
                                    <i class="bx bx-bullseye me-1"></i>Missão
                                </label>
                                <textarea class="form-control" id="modal-missao" name="missao" rows="4" required 
                                          placeholder="Descreva a missão principal da organização..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modal-valores" class="form-label">
                                    <i class="bx bx-heart me-1"></i>Valores
                                </label>
                                <textarea class="form-control" id="modal-valores" name="valores" rows="4" required 
                                          placeholder="Liste os valores fundamentais da organização..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modal-visao" class="form-label">
                                    <i class="bx bx-show-alt me-1"></i>Visão
                                </label>
                                <textarea class="form-control" id="modal-visao" name="visao" rows="4" required 
                                          placeholder="Descreva a visão futura da organização..."></textarea>
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
                            <i class="bx bx-error-circle text-danger display-4"></i>
                        </div>
                        <h6 class="mb-3">Tem certeza que deseja remover esta configuração?</h6>
                        <p class="text-muted small mb-0">
                            Todos os textos de Missão, Visão e Valores serão permanentemente excluídos.
                        </p>
                        <div class="alert alert-warning mt-3">
                            <i class="bx bx-error me-2"></i>
                            <small>Esta ação não pode ser desfeita!</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Cancelar
                        </button>
                        <form method="POST" action="" style="display: inline;">
                            <button type="submit" name="delete" class="btn btn-danger">
                                <i class="bx bx-trash me-1"></i> Sim, Remover
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
    
    <script>
        // Atualizar contador de caracteres
        function updateCharCount(fieldId) {
            const textarea = document.getElementById(fieldId);
            const counter = document.getElementById(fieldId + '-chars');
            if (textarea && counter) {
                counter.textContent = textarea.value.length;
                
                // Mudar cor conforme tamanho
                const charCount = textarea.value.length;
                if (charCount < 50) {
                    counter.style.color = '#dc3545';
                    counter.parentElement.style.color = '#dc3545';
                } else if (charCount < 100) {
                    counter.style.color = '#ffc107';
                    counter.parentElement.style.color = '#ffc107';
                } else {
                    counter.style.color = '#28a745';
                    counter.parentElement.style.color = '#28a745';
                }
            }
        }
        
        // Inicializar contadores
        document.addEventListener('DOMContentLoaded', function() {
            // Atualizar contadores se estiver em modo edição
            if (document.getElementById('missao')) {
                updateCharCount('missao');
                updateCharCount('valores');
                updateCharCount('visao');
            }
            
            // Validação do formulário
            const form = document.getElementById('configForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const missao = document.getElementById('missao').value.trim();
                    const valores = document.getElementById('valores').value.trim();
                    const visao = document.getElementById('visao').value.trim();
                    
                    let errors = [];
                    
                    if (!missao) {
                        errors.push('O campo Missão é obrigatório');
                    } else if (missao.length < 20) {
                        errors.push('A Missão deve ter pelo menos 20 caracteres');
                    }
                    
                    if (!valores) {
                        errors.push('O campo Valores é obrigatório');
                    } else if (valores.length < 20) {
                        errors.push('Os Valores devem ter pelo menos 20 caracteres');
                    }
                    
                    if (!visao) {
                        errors.push('O campo Visão é obrigatório');
                    } else if (visao.length < 20) {
                        errors.push('A Visão deve ter pelo menos 20 caracteres');
                    }
                    
                    if (errors.length > 0) {
                        e.preventDefault();
                        showErrors(errors);
                        return false;
                    }
                    
                    // Confirmar antes de salvar
                    if (!confirm('Deseja salvar as alterações na configuração?')) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Mostrar indicador de carregamento
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Salvando...';
                    submitBtn.disabled = true;
                });
            }
            
            // Validação do modal de criação
            const createForm = document.querySelector('#createModal form');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    const missao = document.getElementById('modal-missao').value.trim();
                    const valores = document.getElementById('modal-valores').value.trim();
                    const visao = document.getElementById('modal-visao').value.trim();
                    
                    let errors = [];
                    
                    if (!missao || missao.length < 20) {
                        errors.push('A Missão deve ter pelo menos 20 caracteres');
                    }
                    
                    if (!valores || valores.length < 20) {
                        errors.push('Os Valores devem ter pelo menos 20 caracteres');
                    }
                    
                    if (!visao || visao.length < 20) {
                        errors.push('A Visão deve ter pelo menos 20 caracteres');
                    }
                    
                    if (errors.length > 0) {
                        e.preventDefault();
                        showErrors(errors);
                        return false;
                    }
                    
                    // Confirmar criação
                    if (!confirm('Deseja criar a configuração de Missão, Visão e Valores?')) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
        
        function showErrors(errors) {
            let errorHtml = '<div class="alert alert-danger"><h6 class="alert-heading">Erros de Validação:</h6><ul class="mb-0">';
            errors.forEach(error => {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul></div>';
            
            // Mostrar erro no topo da página
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = errorHtml;
            document.querySelector('.container-p-y').prepend(alertContainer);
            
            // Rolar para o topo
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Remover após 5 segundos
            setTimeout(() => {
                alertContainer.remove();
            }, 5000);
        }
        
        // Destacar modo edição
        if (window.location.search.includes('edit=true')) {
            // Adicionar efeito visual
            const editIndicator = document.querySelector('.edit-indicator');
            if (editIndicator) {
                setInterval(() => {
                    editIndicator.style.opacity = editIndicator.style.opacity === '0.7' ? '1' : '0.7';
                }, 1000);
            }
            
            // Focar no primeiro campo
            setTimeout(() => {
                const firstField = document.getElementById('missao');
                if (firstField) {
                    firstField.focus();
                }
            }, 300);
        }
    </script>
</body>
</html>