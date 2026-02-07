<?php
include('header.php');
include('../config/conexao.php');

// Verificar se usuário é manager e tenta remover - bloquear
if (isset($_GET['delete']) && isManager()) {
    $_SESSION['error_message'] = 'Gerenciadores não podem remover notícias. Apenas administradores podem executar esta ação.';
    header('Location: noticias.php');
    exit;
}

// Add search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

// Build search conditions
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " WHERE descricao LIKE '%$search%'";
}

if (!empty($dateFilter)) {
    $searchCondition .= empty($searchCondition) ? " WHERE " : " AND ";
    $searchCondition .= "DATE(data) = '$dateFilter'";
}

// Adicionar paginação
$limit = 10; // Itens por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Contar total de registros
$count_sql = "SELECT COUNT(*) as total FROM noticias" . $searchCondition;
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Get today's and yesterday's date for quick filters
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Buscar notícias com paginação
$sql = "SELECT * FROM noticias $searchCondition ORDER BY data DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get news stats
$stats_sql = "SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN DATE(data) = CURDATE() THEN 1 END) as today,
    COUNT(CASE WHEN DATE(data) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 END) as yesterday
    FROM noticias";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <h4 class="fw-bold py-3 mb-2">Gestão de Notícias</h4>
                <p class="text-muted mb-0">Gerencie todas as notícias publicadas no sistema</p>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                    <div class="position-relative">
                        <form method="GET" action="" class="d-flex flex-column flex-md-row gap-2">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="search" 
                                    placeholder="Pesquisar notícias..." 
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    aria-label="Pesquisar"
                                >
                            </div>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    name="date" 
                                    value="<?php echo htmlspecialchars($dateFilter); ?>"
                                    aria-label="Filtrar por data"
                                >
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter"></i>
                                <span class="d-none d-md-inline">Filtrar</span>
                            </button>
                            <?php if (!empty($search) || !empty($dateFilter)): ?>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="bx bx-x"></i>
                                <span class="d-none d-md-inline">Limpar</span>
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-news"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0"><?php echo $stats['total']; ?></h4>
                        </div>
                        <p class="mb-1">Total de Notícias</p>
                        <p class="mb-0">
                            <span class="badge bg-label-success"><?php echo $total_rows; ?> encontradas</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-calendar-check"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0"><?php echo $stats['today']; ?></h4>
                        </div>
                        <p class="mb-1">Hoje</p>
                        <p class="mb-0">
                            <a href="?date=<?php echo $today; ?>" class="text-info">Ver notícias de hoje</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time-five"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0"><?php echo $stats['yesterday']; ?></h4>
                        </div>
                        <p class="mb-1">Ontem</p>
                        <p class="mb-0">
                            <a href="?date=<?php echo $yesterday; ?>" class="text-warning">Ver notícias de ontem</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-plus"></i>
                                </span>
                            </div>
                            <div class="ms-1">
                                <h4 class="mb-0">Nova</h4>
                            </div>
                        </div>
                        <p class="mb-1">Adicionar</p>
                        <p class="mb-0">
                            <a href="noticiasform.php" class="text-success">Criar nova notícia</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="?" class="btn btn-outline-primary">
                        <i class="bx bx-list-ul"></i> Todas
                    </a>
                    <a href="?date=<?php echo $today; ?>" class="btn btn-outline-info">
                        <i class="bx bx-calendar"></i> Hoje
                    </a>
                    <a href="?date=<?php echo $yesterday; ?>" class="btn btn-outline-warning">
                        <i class="bx bx-time"></i> Ontem
                    </a>
                    <a href="noticiasform.php" class="btn btn-dark ms-auto">
                        <i class="bx bx-plus me-1"></i>Adicionar notícia
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="card-title mb-0">
                    Lista de Notícias
                    <?php if (!empty($search) || !empty($dateFilter)): ?>
                    <small class="text-muted">
                        <?php 
                        if (!empty($search)) echo "Resultados para: \"" . htmlspecialchars($search) . "\" ";
                        if (!empty($dateFilter)) echo "Data: " . date('d/m/Y', strtotime($dateFilter));
                        ?>
                    </small>
                    <?php endif; ?>
                </h5>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-label-primary">
                        <?php echo $total_rows; ?> notícia(s) encontrada(s)
                    </span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">
                                <div class="d-flex align-items-center gap-1">
                                    <span>#ID</span>
                                </div>
                            </th>
                            <th width="120">Imagem</th>
                            <th>Descrição</th>
                            <th width="140" class="text-center">Data</th>
                            <th width="120" class="text-center">Acções</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): 
                                $formattedDate = date('d/m/Y', strtotime($row["data"]));
                                $formattedTime = date('H:i', strtotime($row["data"]));
                                $isToday = date('Y-m-d', strtotime($row["data"])) == $today;
                                $isYesterday = date('Y-m-d', strtotime($row["data"])) == $yesterday;
                            ?>
                                <tr class="<?php echo $isToday ? 'table-active' : ''; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <?php echo $row["id"]; ?>
                                                </span>
                                            </div>
                                            <strong>#<?php echo htmlspecialchars($row["id"]); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="image-preview-container">
                                            <img 
                                                src='data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>' 
                                                alt='Notícia <?php echo htmlspecialchars($row["id"]); ?>'
                                                class="rounded border"
                                                loading="lazy"
                                                width="80"
                                                height="80"
                                                style="object-fit: cover;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal<?php echo $row['id']; ?>"
                                                role="button"
                                            />
                                        </div>
                                        
                                        <!-- Image Modal -->
                                        <div class="modal fade" id="imageModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Visualização da Imagem</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img 
                                                            src='data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>' 
                                                            class="img-fluid rounded"
                                                            alt="Notícia <?php echo htmlspecialchars($row["id"]); ?>"
                                                        />
                                                        <div class="mt-3">
                                                            <p class="mb-1"><strong>ID:</strong> <?php echo $row["id"]; ?></p>
                                                            <p class="mb-0 text-muted"><small>Publicado em: <?php echo $formattedDate; ?> às <?php echo $formattedTime; ?></small></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="news-description">
                                            <h6 class="mb-1">
                                                <span class="badge bg-label-<?php echo $isToday ? 'success' : ($isYesterday ? 'warning' : 'secondary'); ?> me-2">
                                                    <?php echo $isToday ? 'Hoje' : ($isYesterday ? 'Ontem' : $formattedDate); ?>
                                                </span>
                                            </h6>
                                            <p class="mb-0 text-truncate-2" 
                                               data-bs-toggle="tooltip" 
                                               title="<?php echo htmlspecialchars($row["descricao"]); ?>"
                                               style="max-width: 400px;">
                                                <?php echo htmlspecialchars($row["descricao"]); ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-label-primary mb-1">
                                                <?php echo $formattedDate; ?>
                                            </span>
                                            <small class="text-muted"><?php echo $formattedTime; ?></small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="noticiasform.php?edit=<?php echo $row['id']; ?>" 
                                               class="btn btn-icon btn-outline-primary btn-sm"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-icon btn-outline-info btn-sm preview-btn"
                                                    data-bs-toggle="tooltip"
                                                    title="Pré-visualizar"
                                                    onclick="previewNews(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row["descricao"])); ?>', '<?php echo $formattedDate; ?>')">
                                                <i class="bx bx-show"></i>
                                            </button>
                                            <?php if (isAdmin()): ?>
                                            <form method='POST' 
                                                  action='remover_noticia.php' 
                                                  class="delete-form"
                                                  data-item-name="<?php echo htmlspecialchars(addslashes($row["descricao"])); ?>">
                                                <input type='hidden' name='noticia_id' value='<?php echo $row['id']; ?>'>
                                                <button type='submit' 
                                                        class='btn btn-icon btn-outline-danger btn-sm'
                                                        data-bs-toggle="tooltip"
                                                        title="Remover">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <button type="button" 
                                                    class="btn btn-icon btn-outline-secondary btn-sm" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Apenas admins podem remover"
                                                    disabled>
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bx bx-news" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="mt-3">
                                            <?php echo !empty($search) || !empty($dateFilter) 
                                                ? 'Nenhuma notícia encontrada' 
                                                : 'Nenhuma notícia cadastrada'; ?>
                                        </h5>
                                        <p class="text-muted mb-3">
                                            <?php echo !empty($search) || !empty($dateFilter) 
                                                ? 'Tente ajustar seus filtros de pesquisa.' 
                                                : 'Comece adicionando sua primeira notícia.'; ?>
                                        </p>
                                        <a href="noticiasform.php" class="btn btn-dark">
                                            <i class="bx bx-plus"></i> Adicionar primeira notícia
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6 text-muted mb-3 mb-lg-0">
                            Mostrando <strong><?php echo min($offset + 1, $total_rows); ?>-<?php echo min($offset + $limit, $total_rows); ?></strong> 
                            de <strong><?php echo $total_rows; ?></strong> notícias
                        </div>
                        <div class="col-lg-6">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center justify-content-lg-end mb-0">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>"
                                               aria-label="Previous">
                                                <i class="bx bx-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($total_pages, $page + 2);
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++):
                                    ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" 
                                               href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>"
                                               aria-label="Next">
                                                <i class="bx bx-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Footer -->
    <?php include('footerprincipal.php'); ?>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

<!-- Custom Styles -->
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
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #566a7f;
        padding: 1rem 0.75rem;
        background-color: #f8f9fa;
    }
    
    .table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(67, 89, 113, 0.04);
    }
    
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }
    
    .empty-state-icon {
        color: #b4bdc6;
        margin-bottom: 1rem;
    }
    
    .image-preview-container img {
        transition: transform 0.3s ease;
        cursor: pointer;
        border-radius: 8px;
    }
    
    .image-preview-container img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
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
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header {
            padding: 1rem;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .news-description {
            max-width: 100% !important;
        }
        
        .table-responsive {
            border: none;
        }
        
        .table td {
            padding: 0.75rem;
        }
        
        .image-preview-container img {
            width: 60px;
            height: 60px;
        }
    }
    
    @media (max-width: 576px) {
        .d-flex.gap-2 {
            gap: 0.5rem !important;
        }
        
        .table td {
            font-size: 0.875rem;
        }
    }
</style>

<!-- JavaScript Enhancements -->
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Enhanced confirm delete function
    function confirmDelete(form, newsTitle) {
        const title = newsTitle.length > 50 ? newsTitle.substring(0, 50) + '...' : newsTitle;
        return confirm(`Tem certeza que deseja remover a notícia "${title}"?\n\nEsta ação não pode ser desfeita.`);
    }

    // News preview function
    function previewNews(id, title, date) {
        const modalContent = `
            <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pré-visualização da Notícia</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <span class="badge bg-primary mb-2">ID: #${id}</span>
                                <h6 class="mb-2">${title}</h6>
                                <p class="text-muted mb-0"><small>Publicado em: ${date}</small></p>
                            </div>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Esta é apenas uma pré-visualização. A visualização completa incluirá a imagem e conteúdo completo.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="noticiasform.php?edit=${id}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i>Editar Notícia
                            </a>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('previewModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add new modal to body
        document.body.insertAdjacentHTML('beforeend', modalContent);
        
        // Show modal
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        previewModal.show();
    }

    // Auto-submit search on date change
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput) {
            dateInput.addEventListener('change', function() {
                if (this.value) {
                    this.form.submit();
                }
            });
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Ctrl/Cmd + N to add new news
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = 'noticiasform.php';
        }
    });
    
    // Interceptar delete forms - simples validação
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const itemName = this.getAttribute('data-item-name');
            
            // Validação: pergunta se tem certeza
            if (confirm(`Tem a certeza que deseja remover: "${itemName}"?`)) {
                this.submit();
            }
        });
    });
</script>

<?php
// Fechar a conexão apenas se ainda estiver aberta
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
include('footer.php');
?>