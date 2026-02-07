<?php
include('header.php');
include('config/conexao.php');

// Add sorting functionality
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'desc' : 'asc';
$validSortColumns = ['id', 'descricao'];
$sort = in_array($sort, $validSortColumns) ? $sort : 'id';

// Add search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $searchCondition = " WHERE descricao LIKE '%$search%'";
}

$sql = "SELECT * FROM homepagehero $searchCondition ORDER BY $sort $order";
$result = $conn->query($sql);
$totalRecords = $result->num_rows;

// Add pagination
$recordsPerPage = 10;
$totalPages = ceil($totalRecords / $recordsPerPage);
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));
$offset = ($currentPage - 1) * $recordsPerPage;

$sql .= " LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header with Stats -->
        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <h4 class="fw-bold py-3 mb-2">Gerenciar Hero da Página Inicial</h4>
                <p class="text-muted mb-0">Gerencie as imagens e descrições da seção principal da página inicial</p>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                    <div class="position-relative">
                        <form method="GET" action="" class="d-flex">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="search" 
                                    placeholder="Pesquisar descrição..." 
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    aria-label="Pesquisar"
                                >
                                <?php if (!empty($search)): ?>
                                <a href="?" class="btn btn-outline-secondary" type="button">
                                    <i class="bx bx-x"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <a class="btn btn-dark d-flex align-items-center gap-2" href="homepageheroform.php">
                        <i class="bx bx-plus"></i>
                        <span>Adicionar Novo</span>
                    </a>
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
                                    <i class="bx bx-images"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0"><?php echo $totalRecords; ?></h4>
                        </div>
                        <p class="mb-1">Total de Itens</p>
                        <p class="mb-0 text-muted">
                            <span class="fw-medium">Hero sections</span> cadastradas
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
                                    <i class="bx bx-star"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0"><?php echo $totalRecords; ?></h4>
                        </div>
                        <p class="mb-1">Ativos</p>
                        <p class="mb-0 text-muted">
                            Todos os itens estão <span class="fw-medium">ativos</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="card-title mb-0">Lista de Hero Sections</h5>
                
                <?php if (!empty($search)): ?>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-label-info">
                        Resultados para: "<?php echo htmlspecialchars($search); ?>"
                    </span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">
                                <a href="?sort=id&order=<?php echo $sort == 'id' && $order == 'asc' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    #ID
                                    <?php if ($sort == 'id'): ?>
                                    <i class="bx bx-chevron-<?php echo $order == 'asc' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="width: 120px;">Foto</th>
                            <th>
                                <a href="?sort=descricao&order=<?php echo $sort == 'descricao' && $order == 'asc' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    Descrição
                                    <?php if ($sort == 'descricao'): ?>
                                    <i class="bx bx-chevron-<?php echo $order == 'asc' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="width: 150px;">Data de Criação</th>
                            <th style="width: 120px;" class="text-end">Acção</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $truncatedDesc = strlen($row["descricao"]) > 100 
                                    ? substr($row["descricao"], 0, 100) . '...' 
                                    : $row["descricao"];
                                
                                $createdDate = isset($row['created_at']) 
                                    ? date('d/m/Y H:i', strtotime($row['created_at'])) 
                                    : '--/--/----';
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <?php echo $row["id"]; ?>
                                                </span>
                                            </div>
                                            <strong><?php echo $row["id"]; ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="image-preview-container">
                                            <img 
                                                src='data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>' 
                                                class="rounded border" 
                                                width="80" 
                                                height="80"
                                                style="object-fit: cover;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal<?php echo $row['id']; ?>"
                                                role="button"
                                                alt="Hero image"
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
                                                            alt="Hero image full size"
                                                        />
                                                        <p class="mt-3 text-muted">ID: <?php echo $row["id"]; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium mb-1">Descrição:</span>
                                            <span class="text-muted" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($row["descricao"]); ?>">
                                                <?php echo htmlspecialchars($truncatedDesc); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary"><?php echo $createdDate; ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="homepageheroform.php?edit=<?php echo $row['id']; ?>" 
                                               class="btn btn-icon btn-outline-primary btn-sm"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form method='POST' action='remover_homepagehero.php' 
                                                  class="delete-form"
                                                  onsubmit="return confirmDelete(this);">
                                                <input type='hidden' name='homepagehero_id' value='<?php echo $row['id']; ?>'>
                                                <button type='submit' 
                                                        class='btn btn-icon btn-outline-danger btn-sm'
                                                        data-bs-toggle="tooltip"
                                                        title="Remover"
                                                        name='remover'>
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bx bx-images" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="mt-3">Nenhum resultado encontrado</h5>
                                        <p class="text-muted">
                                            <?php echo !empty($search) 
                                                ? 'Tente ajustar sua pesquisa ou ' 
                                                : ''; ?>
                                            <a href="homepageheroform.php" class="btn btn-sm btn-dark mt-2">
                                                <i class="bx bx-plus"></i> Adicionar seu primeiro item
                                            </a>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination & Footer -->
            <?php if ($totalPages > 1): ?>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-muted mb-3 mb-lg-0">
                        Mostrando <strong><?php echo ($offset + 1); ?>-<?php echo min($offset + $recordsPerPage, $totalRecords); ?></strong> 
                        de <strong><?php echo $totalRecords; ?></strong> itens
                    </div>
                    <div class="col-lg-6">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center justify-content-lg-end mb-0">
                                <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $currentPage - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="bx bx-chevron-left"></i>
                                    </a>
                                </li>
                                
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $currentPage + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="bx bx-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
    }
    
    .image-preview-container img:hover {
        transform: scale(1.05);
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
    
    .card-border-shadow-primary {
        border: 1px solid;
        border-color: #696cff;
    }
    
    .card-border-shadow-warning {
        border: 1px solid;
        border-color: #ffab00;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            border: none;
        }
        
        .card-header {
            padding: 1rem;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
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

    // Confirm delete function
    function confirmDelete(form) {
        return confirm('Tem certeza que deseja remover este item? Esta ação não pode ser desfeita.');
    }

    // Live search debounce
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    // Sort indicator
    document.addEventListener('DOMContentLoaded', function() {
        const sortLinks = document.querySelectorAll('th a[href*="sort="]');
        sortLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const currentUrl = new URL(window.location.href);
                const newUrl = new URL(this.href);
                
                if (currentUrl.searchParams.get('sort') === newUrl.searchParams.get('sort') &&
                    currentUrl.searchParams.get('order') === newUrl.searchParams.get('order')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

<?php
$conn->close();
include('footer.php');
?>