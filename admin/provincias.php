<?php
include('header.php');
include('../config/conexao.php');

// Add search and filter functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'nome';
$sortOrder = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';

// Valid sort columns
$validSortColumns = ['id', 'nome', 'latitude', 'longitude', 'created_at'];
$sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'nome';

// Build search condition
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " WHERE nome LIKE '%$search%'";
}

// Add pagination
$limit = 10; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM provincias" . $searchCondition;
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Get provinces with pagination
$sql = "SELECT * FROM provincias $searchCondition ORDER BY $sortBy $sortOrder LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get provinces statistics
$stats_sql = "SELECT 
    COUNT(*) as total,
    MIN(created_at) as oldest,
    MAX(created_at) as newest
    FROM provincias";
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
                <h4 class="fw-bold py-3 mb-2">Províncias - Cobertura Geográfica</h4>
                <p class="text-muted mb-0">Gerencie as províncias para o mapa interativo do site</p>
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
                                    placeholder="Pesquisar províncias..." 
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    aria-label="Pesquisar"
                                >
                                <?php if (!empty($search)): ?>
                                <a href="?" class="btn btn-outline-secondary" type="button">
                                    <i class="bx bx-x"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bx bx-filter"></i>
                                <span class="d-none d-md-inline">Filtrar</span>
                            </button>
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
                                <span class="avatar-initial rounded bg-label-primary" style="background-color: rgba(255, 111, 15, 0.1); color: #FF6F0F;">
                                    <i class="bx bx-map"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0"><?php echo $stats['total']; ?></h4>
                        </div>
                        <p class="mb-1">Total de Províncias</p>
                        <p class="mb-0">
                            <span class="badge" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2);">
                                <?php echo $total_rows; ?> encontradas
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info" style="background-color: rgba(23, 193, 232, 0.1); color: #17c1e8;">
                                    <i class="bx bx-calendar-plus"></i>
                                </span>
                            </div>
                            <div class="ms-1">
                                <h6 class="mb-0">Mais Recente</h6>
                                <p class="mb-0 text-muted">
                                    <?php echo $stats['newest'] ? date('d/m/Y', strtotime($stats['newest'])) : 'N/A'; ?>
                                </p>
                            </div>
                        </div>
                        <p class="mb-1">Adicionada</p>
                        <p class="mb-0">
                            <small class="text-muted">Última província cadastrada</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning" style="background-color: rgba(255, 171, 0, 0.1); color: #ffab00;">
                                    <i class="bx bx-calendar-minus"></i>
                                </span>
                            </div>
                            <div class="ms-1">
                                <h6 class="mb-0">Mais Antiga</h6>
                                <p class="mb-0 text-muted">
                                    <?php echo $stats['oldest'] ? date('d/m/Y', strtotime($stats['oldest'])) : 'N/A'; ?>
                                </p>
                            </div>
                        </div>
                        <p class="mb-1">Adicionada</p>
                        <p class="mb-0">
                            <small class="text-muted">Primeira província cadastrada</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success" style="background-color: rgba(113, 221, 55, 0.1); color: #71dd37;">
                                    <i class="bx bx-plus"></i>
                                </span>
                            </div>
                            <div class="ms-1">
                                <h4 class="mb-0">Nova</h4>
                            </div>
                        </div>
                        <p class="mb-1">Adicionar</p>
                        <p class="mb-0">
                            <a href="provinciasform.php" style="color: #71dd37;">Criar nova província</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="?" class="btn btn-outline-primary">
                        <i class="bx bx-list-ul"></i> Todas
                    </a>
                    <a href="?sort=nome&order=asc" class="btn btn-outline-info">
                        <i class="bx bx-sort-a-z"></i> Ordem A-Z
                    </a>
                    <a href="?sort=created_at&order=desc" class="btn btn-outline-warning">
                        <i class="bx bx-sort-down"></i> Mais Recentes
                    </a>
                    <a href="provinciasform.php" class="btn btn-primary ms-auto">
                        <i class='bx bx-plus'></i> Adicionar Província
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="card-title mb-0">
                    Lista de Províncias
                    <?php if (!empty($search)): ?>
                    <small class="text-muted">
                        Resultados para: "<?php echo htmlspecialchars($search); ?>"
                    </small>
                    <?php endif; ?>
                </h5>
                <div class="mt-2 mt-md-0">
                    <span class="badge" style="background-color: rgba(255, 111, 15, 0.1); color: #FF6F0F; border: 1px solid rgba(255, 111, 15, 0.2);">
                        <?php echo $total_rows; ?> província(s) encontrada(s)
                    </span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">
                                <a href="?sort=id&order=<?php echo $sortBy == 'id' && $sortOrder == 'ASC' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    ID
                                    <?php if ($sortBy == 'id'): ?>
                                    <i class="bx bx-chevron-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?sort=nome&order=<?php echo $sortBy == 'nome' && $sortOrder == 'ASC' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    Nome da Província
                                    <?php if ($sortBy == 'nome'): ?>
                                    <i class="bx bx-chevron-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="width: 120px;">
                                <a href="?sort=latitude&order=<?php echo $sortBy == 'latitude' && $sortOrder == 'ASC' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    Latitude
                                    <?php if ($sortBy == 'latitude'): ?>
                                    <i class="bx bx-chevron-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="width: 120px;">
                                <a href="?sort=longitude&order=<?php echo $sortBy == 'longitude' && $sortOrder == 'ASC' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    Longitude
                                    <?php if ($sortBy == 'longitude'): ?>
                                    <i class="bx bx-chevron-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="width: 140px;">
                                <a href="?sort=created_at&order=<?php echo $sortBy == 'created_at' && $sortOrder == 'ASC' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>"
                                   class="text-dark text-decoration-none d-flex align-items-center gap-1">
                                    Data de Criação
                                    <?php if ($sortBy == 'created_at'): ?>
                                    <i class="bx bx-chevron-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="width: 100px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): 
                                $createdDate = date('d/m/Y', strtotime($row["created_at"]));
                                $createdTime = date('H:i', strtotime($row["created_at"]));
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle" style="background-color: rgba(255, 111, 15, 0.1); color: #FF6F0F;">
                                                    <?php echo $row["id"]; ?>
                                                </span>
                                            </div>
                                            <strong><?php echo $row["id"]; ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-3">
                                                <span class="avatar-initial rounded" style="background-color: rgba(255, 111, 15, 0.1); color: #FF6F0F;">
                                                    <i class="bx bx-map-pin"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <strong class="d-block"><?php echo htmlspecialchars($row["nome"]); ?></strong>
                                                <small class="text-muted">Coordenadas: <?php echo $row["latitude"]; ?>, <?php echo $row["longitude"]; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="coordinate-value" 
                                             data-bs-toggle="tooltip" 
                                             title="Latitude: <?php echo $row["latitude"]; ?>">
                                            <code style="color: #FF6F0F;"><?php echo substr($row["latitude"], 0, 8); ?></code>
                                            <?php if (strlen($row["latitude"]) > 8): ?>
                                            <small class="text-muted">...</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="coordinate-value"
                                             data-bs-toggle="tooltip" 
                                             title="Longitude: <?php echo $row["longitude"]; ?>">
                                            <code style="color: #ffab00;"><?php echo substr($row["longitude"], 0, 8); ?></code>
                                            <?php if (strlen($row["longitude"]) > 8): ?>
                                            <small class="text-muted">...</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge" style="background-color: rgba(255, 111, 15, 0.1); color: #FF6F0F; border: 1px solid rgba(255, 111, 15, 0.2);" class="mb-1">
                                                <?php echo $createdDate; ?>
                                            </span>
                                            <small class="text-muted"><?php echo $createdTime; ?></small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="provinciasform.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-icon btn-sm"
                                               style="background-color: rgba(255, 171, 0, 0.1); color: #ffab00; border: 1px solid rgba(255, 171, 0, 0.2);"
                                               data-bs-toggle="tooltip"
                                               title="Editar">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-icon btn-sm"
                                                    style="background-color: rgba(23, 193, 232, 0.1); color: #17c1e8; border: 1px solid rgba(23, 193, 232, 0.2);"
                                                    data-bs-toggle="tooltip"
                                                    title="Ver no Mapa"
                                                    onclick="viewOnMap('<?php echo htmlspecialchars($row['nome']); ?>', <?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>)">
                                                <i class="bx bx-map"></i>
                                            </button>
                                            <form method="POST" 
                                                  action="remover_provincia.php" 
                                                  class="delete-form"
                                                  onsubmit="return confirmDelete(this, '<?php echo htmlspecialchars(addslashes($row['nome'])); ?>');">
                                                <input type="hidden" name="provincia_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" 
                                                        class="btn btn-icon btn-sm"
                                                        style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.2);"
                                                        data-bs-toggle="tooltip"
                                                        title="Remover"
                                                        name="remover">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bx bx-map" style="font-size: 3rem; color: #FF6F0F;"></i>
                                        </div>
                                        <h5 class="mt-3">
                                            <?php echo !empty($search) 
                                                ? 'Nenhuma província encontrada' 
                                                : 'Nenhuma província cadastrada'; ?>
                                        </h5>
                                        <p class="text-muted mb-3">
                                            <?php echo !empty($search) 
                                                ? 'Tente ajustar sua pesquisa.' 
                                                : 'Comece adicionando sua primeira província.'; ?>
                                        </p>
                                        <a href="provinciasform.php" class="btn btn-primary">
                                            <i class="bx bx-plus"></i> Adicionar primeira província
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6 text-muted mb-3 mb-lg-0">
                            Mostrando <strong><?php echo min($offset + 1, $total_rows); ?>-<?php echo min($offset + $limit, $total_rows); ?></strong> 
                            de <strong><?php echo $total_rows; ?></strong> províncias
                        </div>
                        <div class="col-lg-6">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center justify-content-lg-end mb-0">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo strtolower($sortOrder); ?>&search=<?php echo urlencode($search); ?>"
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
                                               href="?page=<?php echo $i; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo strtolower($sortOrder); ?>&search=<?php echo urlencode($search); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo strtolower($sortOrder); ?>&search=<?php echo urlencode($search); ?>"
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
        
        <!-- Map Preview Modal -->
        <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Visualização no Mapa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="mapPreview" style="height: 400px; border-radius: 8px; overflow: hidden;"></div>
                        <div class="mt-3">
                            <h6 id="provinceName" class="mb-2"></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Latitude:</strong> <code id="provinceLat"></code></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Longitude:</strong> <code id="provinceLng"></code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                        <a href="#" id="openGoogleMaps" class="btn btn-primary" target="_blank">
                            <i class="bx bx-map"></i> Abrir no Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded" style="background-color: rgba(23, 193, 232, 0.1); color: #17c1e8;">
                            <i class="bx bx-info-circle"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-1">Sobre a Cobertura Geográfica</h6>
                        <p class="mb-2 text-muted">
                            As províncias cadastradas aqui serão exibidas no mapa interativo do site.
                            As coordenadas (latitude e longitude) determinam a localização dos marcadores no mapa.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background-color: rgba(23, 193, 232, 0.1); color: #17c1e8; border: 1px solid rgba(23, 193, 232, 0.2);">
                                <i class="bx bx-map-pin"></i> Cada província tem coordenadas únicas
                            </span>
                            <span class="badge" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2);">
                                <i class="bx bx-globe"></i> Usado para geolocalização
                            </span>
                            <span class="badge" style="background-color: rgba(255, 171, 0, 0.1); color: #ffab00; border: 1px solid rgba(255, 171, 0, 0.2);">
                                <i class="bx bx-map"></i> Exibido no mapa principal
                            </span>
                        </div>
                    </div>
                </div>
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
        margin-bottom: 1rem;
    }
    
    .btn-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    .coordinate-value {
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 4px 8px;
        border-radius: 4px;
        background-color: rgba(255, 111, 15, 0.05);
    }
    
    .coordinate-value:hover {
        background-color: rgba(255, 111, 15, 0.1);
        transform: translateX(2px);
    }
    
    .card-border-shadow-primary {
        border: 1px solid rgba(255, 111, 15, 0.3);
    }
    
    .card-border-shadow-info {
        border: 1px solid rgba(23, 193, 232, 0.3);
    }
    
    .card-border-shadow-warning {
        border: 1px solid rgba(255, 171, 0, 0.3);
    }
    
    .card-border-shadow-success {
        border: 1px solid rgba(113, 221, 55, 0.3);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(255, 111, 15, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 111, 15, 0.4);
        background: linear-gradient(135deg, #E05A00 0%, #FF6F0F 100%);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header {
            padding: 1rem;
        }
        
        .table-responsive {
            border: none;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .coordinate-value {
            font-size: 0.875rem;
        }
        
        .badge {
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 576px) {
        .d-flex.gap-2 {
            gap: 0.5rem !important;
        }
        
        .table td {
            font-size: 0.875rem;
        }
        
        .empty-state {
            padding: 2rem 1rem;
        }
        
        .avatar {
            width: 32px;
            height: 32px;
        }
        
        .avatar-initial {
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
    function confirmDelete(form, provinceName) {
        return confirm(`Tem certeza que deseja remover a província "${provinceName}"?\n\nEsta ação irá remover permanentemente a província e pode afetar a exibição no mapa.`);
    }

    // View province on map
    function viewOnMap(name, lat, lng) {
        document.getElementById('provinceName').textContent = name;
        document.getElementById('provinceLat').textContent = lat;
        document.getElementById('provinceLng').textContent = lng;
        
        // Update Google Maps link
        const mapsLink = document.getElementById('openGoogleMaps');
        mapsLink.href = `https://www.google.com/maps?q=${lat},${lng}&z=10`;
        
        // Initialize map
        const mapElement = document.getElementById('mapPreview');
        
        // Clear previous map
        mapElement.innerHTML = '';
        
        // Create simple map visualization
        mapElement.innerHTML = `
            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
                    <i class="bx bx-map-pin" style="font-size: 3rem;"></i>
                    <p class="mt-2 fw-bold">${name}</p>
                    <p class="mb-0">Lat: ${lat}<br>Lng: ${lng}</p>
                </div>
                <div style="position: absolute; top: 50%; left: 50%; width: 20px; height: 20px; background: #FF6F0F; border-radius: 50%; transform: translate(-50%, -50%); border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.3);"></div>
            </div>
        `;
        
        // Show modal
        const mapModal = new bootstrap.Modal(document.getElementById('mapModal'));
        mapModal.show();
    }

    // Auto-focus search on Ctrl+F
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Ctrl+N to add new province
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = 'provinciasform.php';
        }
    });

    // Live search with debounce
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 800);
        });
    }

    // Hover effects for table rows
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(255, 111, 15, 0.04)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
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