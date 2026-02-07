<?php
include('header.php');
include('config/conexao.php');

// Processar ações (aprovar/rejeitar)
if (isset($_POST['action']) && isset($_POST['doador_id'])) {
    $doador_id = intval($_POST['doador_id']);
    $action = $_POST['action'];
    
    try {
        if ($action == 'aprovar') {
            $stmt = $conn->prepare("UPDATE doadores SET status = 'confirmado' WHERE id = ?");
        } else if ($action == 'rejeitar') {
            $stmt = $conn->prepare("UPDATE doadores SET status = 'rejeitado' WHERE id = ?");
        }
        
        if (isset($stmt)) {
            $stmt->bind_param("i", $doador_id);
            $success = $stmt->execute();
            $stmt->close();
            
            // Feedback ao usuário
            if ($success) {
                $message = [
                    'type' => 'success',
                    'text' => 'Ação realizada com sucesso!'
                ];
            }
        }
    } catch (Exception $e) {
        $message = [
            'type' => 'danger',
            'text' => 'Erro ao processar ação: ' . $e->getMessage()
        ];
    }
}

// Filtros
$where = [];
$params = [];
$types = '';

if (isset($_GET['status']) && $_GET['status'] != '') {
    $where[] = "status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

if (isset($_GET['metodo']) && $_GET['metodo'] != '') {
    $where[] = "metodo_pagamento = ?";
    $params[] = $_GET['metodo'];
    $types .= 's';
}

if (isset($_GET['search']) && $_GET['search'] != '') {
    $where[] = "(nome LIKE ? OR email LIKE ? OR telefone LIKE ?)";
    $search_param = '%' . $_GET['search'] . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Paginação
$items_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Contar total de registros
$count_sql = "SELECT COUNT(*) as total FROM doadores $where_clause";
$count_stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);
$count_stmt->close();

// Consultar doadores com paginação
$sql = "SELECT * FROM doadores $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$params[] = $items_per_page;
$params[] = $offset;
$types .= 'ii';

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Estatísticas - FIX: Tratar valores nulos
$stats_sql = "SELECT 
    COUNT(*) as total,
    COALESCE(SUM(CASE WHEN status = 'confirmado' THEN valor ELSE 0 END), 0) as total_confirmado,
    COALESCE(SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END), 0) as pendentes,
    COALESCE(SUM(CASE WHEN status = 'rejeitado' THEN 1 ELSE 0 END), 0) as rejeitados
    FROM doadores";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Garantir que os valores nunca sejam null
$stats['total'] = intval($stats['total'] ?? 0);
$stats['total_confirmado'] = floatval($stats['total_confirmado'] ?? 0);
$stats['pendentes'] = intval($stats['pendentes'] ?? 0);
$stats['rejeitados'] = intval($stats['rejeitados'] ?? 0);
?>

<style>
    /* ========== VARIÁVEIS E RESET ========== */
    :root {
        --primary-color: #FF6F0F;
        --success-color: #28c76f;
        --warning-color: #ff9f43;
        --danger-color: #ea5455;
        --info-color: #00cfe8;
        --card-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        --card-shadow-hover: 0 8px 32px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ========== LAYOUT RESPONSIVO ========== */
    .content-wrapper {
        padding: 0;
    }

    .container-xxl {
        padding: 1.5rem;
    }

    /* ========== HEADER SECTION ========== */
    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .page-header h4 {
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .page-header .badge-count {
        background: linear-gradient(135deg, var(--primary-color), #ff8534);
        color: white;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* ========== ALERT MESSAGES ========== */
    .alert-modern {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--card-shadow);
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-modern i {
        font-size: 1.5rem;
    }

    /* ========== CARDS DE ESTATÍSTICAS ========== */
    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        border: none;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), transparent);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
    }

    .stats-card:hover::before {
        transform: scaleX(1);
    }

    .stats-card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .stats-info h6 {
        color: #666;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-info h3 {
        color: #1a1a1a;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
        transition: var(--transition);
    }

    .stats-card:hover .stats-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stats-icon.primary {
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.1), rgba(255, 111, 15, 0.2));
        color: var(--primary-color);
    }

    .stats-icon.success {
        background: linear-gradient(135deg, rgba(40, 199, 111, 0.1), rgba(40, 199, 111, 0.2));
        color: var(--success-color);
    }

    .stats-icon.warning {
        background: linear-gradient(135deg, rgba(255, 159, 67, 0.1), rgba(255, 159, 67, 0.2));
        color: var(--warning-color);
    }

    .stats-icon.danger {
        background: linear-gradient(135deg, rgba(234, 84, 85, 0.1), rgba(234, 84, 85, 0.2));
        color: var(--danger-color);
    }

    /* ========== FILTROS MODERNOS ========== */
    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        border: none;
    }

    .filter-card h5 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-card h5 i {
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 600;
        color: #333;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control,
    .form-select {
        border: 2px solid #e8e8e8;
        border-radius: 10px;
        padding: 0.625rem 1rem;
        font-size: 0.9375rem;
        transition: var(--transition);
        height: auto;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 111, 15, 0.1);
    }

    .btn-modern {
        padding: 0.625rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: var(--transition);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), #ff8534);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 111, 15, 0.3);
    }

    .btn-secondary {
        background: #f5f5f5;
        color: #666;
    }

    .btn-secondary:hover {
        background: #e8e8e8;
        color: #333;
    }

    /* ========== TABELA RESPONSIVA ========== */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        border: none;
    }

    .table-card-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-card-header h5 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-card-header h5 i {
        color: var(--primary-color);
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        margin-bottom: 0;
        white-space: nowrap;
    }

    .table thead th {
        background: #f8f9fa;
        color: #333;
        font-weight: 700;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9375rem;
    }

    .table tbody tr {
        transition: var(--transition);
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* ========== BADGES MODERNOS ========== */
    .badge-modern {
        padding: 0.4rem 0.85rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .badge-modern i {
        font-size: 0.875rem;
    }

    .badge-warning {
        background: linear-gradient(135deg, rgba(255, 159, 67, 0.15), rgba(255, 159, 67, 0.25));
        color: #d97706;
    }

    .badge-success {
        background: linear-gradient(135deg, rgba(40, 199, 111, 0.15), rgba(40, 199, 111, 0.25));
        color: #059669;
    }

    .badge-danger {
        background: linear-gradient(135deg, rgba(234, 84, 85, 0.15), rgba(234, 84, 85, 0.25));
        color: #dc2626;
    }

    .badge-info {
        background: linear-gradient(135deg, rgba(0, 207, 232, 0.15), rgba(0, 207, 232, 0.25));
        color: #0891b2;
    }

    /* ========== DROPDOWN DE AÇÕES ========== */
    .action-dropdown .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 2px solid #e8e8e8;
        background: white;
        color: #333;
        transition: var(--transition);
    }

    .action-dropdown .btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        background: rgba(255, 111, 15, 0.05);
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border-radius: 12px;
        padding: 0.5rem;
        min-width: 200px;
    }

    .dropdown-item {
        padding: 0.625rem 1rem;
        border-radius: 8px;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
    }

    .dropdown-item i {
        font-size: 1.125rem;
    }

    .dropdown-item.text-danger:hover {
        background: rgba(234, 84, 85, 0.1);
        color: var(--danger-color) !important;
    }

    /* ========== PAGINAÇÃO MODERNA ========== */
    .pagination-modern {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination-modern .page-link {
        border: 2px solid #e8e8e8;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        color: #333;
        font-weight: 600;
        transition: var(--transition);
        min-width: 40px;
        text-align: center;
    }

    .pagination-modern .page-link:hover {
        border-color: var(--primary-color);
        background: rgba(255, 111, 15, 0.05);
        color: var(--primary-color);
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-color), #ff8534);
        border-color: var(--primary-color);
        color: white;
    }

    .pagination-modern .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ========== EMPTY STATE ========== */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h6 {
        color: #666;
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #999;
        font-size: 0.9375rem;
    }

    /* ========== MOBILE CARDS (alternativa à tabela em mobile) ========== */
    .mobile-card {
        display: none;
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: var(--card-shadow);
        border-left: 4px solid var(--primary-color);
    }

    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .mobile-card-info h6 {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.25rem;
    }

    .mobile-card-info p {
        font-size: 0.875rem;
        color: #666;
        margin: 0;
    }

    .mobile-card-body {
        display: grid;
        gap: 0.75rem;
    }

    .mobile-card-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.875rem;
    }

    .mobile-card-item .label {
        color: #666;
        font-weight: 600;
    }

    .mobile-card-item .value {
        color: #1a1a1a;
        font-weight: 500;
        text-align: right;
    }

    .mobile-card-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .mobile-card-actions .btn {
        flex: 1;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    /* ========== RESPONSIVE BREAKPOINTS ========== */
    
    /* Large Desktop (>1400px) */
    @media (min-width: 1400px) {
        .container-xxl {
            max-width: 1400px;
        }
    }

    /* Desktop (992px - 1399px) */
    @media (max-width: 1399.98px) {
        .stats-info h3 {
            font-size: 1.75rem;
        }
    }

    /* Tablet (768px - 991px) */
    @media (max-width: 991.98px) {
        .container-xxl {
            padding: 1.25rem;
        }

        .stats-card {
            margin-bottom: 1rem;
        }

        .filter-card {
            padding: 1.25rem;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }

        .table-card-header {
            flex-direction: column;
            align-items: stretch;
        }
    }

    /* Mobile (576px - 767px) */
    @media (max-width: 767.98px) {
        .container-xxl {
            padding: 1rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h4 {
            font-size: 1.5rem;
        }

        .stats-card {
            padding: 1.25rem;
        }

        .stats-card-body {
            flex-direction: row;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }

        .filter-card {
            padding: 1rem;
        }

        .filter-card h5 {
            font-size: 1rem;
        }

        /* Mostrar cards em vez de tabela em mobile */
        .table-responsive.desktop-only {
            display: none;
        }

        .mobile-card {
            display: block;
        }

        .pagination-modern {
            gap: 0.25rem;
        }

        .pagination-modern .page-link {
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
            min-width: 36px;
        }
    }

    /* Small Mobile (<576px) */
    @media (max-width: 575.98px) {
        .container-xxl {
            padding: 0.75rem;
        }

        .page-header h4 {
            font-size: 1.25rem;
        }

        .stats-card {
            padding: 1rem;
        }

        .stats-info h6 {
            font-size: 0.75rem;
        }

        .stats-info h3 {
            font-size: 1.5rem;
        }

        .stats-icon {
            width: 45px;
            height: 45px;
            font-size: 1.25rem;
        }

        .filter-card {
            padding: 0.875rem;
        }

        .form-control,
        .form-select {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .btn-modern {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .mobile-card {
            padding: 1rem;
        }

        .mobile-card-header h6 {
            font-size: 0.9375rem;
        }

        .mobile-card-actions .btn {
            font-size: 0.8125rem;
            padding: 0.4rem 0.75rem;
        }
    }

    /* Landscape Mobile */
    @media (max-height: 500px) and (orientation: landscape) {
        .stats-card {
            margin-bottom: 0.75rem;
        }

        .filter-card {
            margin-bottom: 1rem;
        }
    }

    /* Print Styles */
    @media print {
        .filter-card,
        .pagination-modern,
        .action-dropdown {
            display: none !important;
        }

        .table-card {
            box-shadow: none;
        }
    }
</style>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="page-header">
            <h4>
                <i class="bx bx-donate-heart"></i>
                <span>Lista de Doadores</span>
                <span class="badge-count"><?php echo number_format($stats['total'], 0); ?></span>
            </h4>
        </div>

        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $message['type']; ?> alert-modern alert-dismissible fade show" role="alert">
            <i class="bx <?php echo $message['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>"></i>
            <span><?php echo htmlspecialchars($message['text']); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-card-body">
                        <div class="stats-info">
                            <h6>Total de Doações</h6>
                            <h3><?php echo number_format($stats['total'], 0); ?></h3>
                        </div>
                        <div class="stats-icon primary">
                            <i class="bx bx-donate-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-card-body">
                        <div class="stats-info">
                            <h6>Valor Confirmado</h6>
                            <h3><?php echo number_format($stats['total_confirmado'], 2, ',', '.'); ?> MT</h3>
                        </div>
                        <div class="stats-icon success">
                            <i class="bx bx-money"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-card-body">
                        <div class="stats-info">
                            <h6>Pendentes</h6>
                            <h3><?php echo number_format($stats['pendentes'], 0); ?></h3>
                        </div>
                        <div class="stats-icon warning">
                            <i class="bx bx-time"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-card-body">
                        <div class="stats-info">
                            <h6>Rejeitados</h6>
                            <h3><?php echo number_format($stats['rejeitados'], 0); ?></h3>
                        </div>
                        <div class="stats-icon danger">
                            <i class="bx bx-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <h5><i class="bx bx-filter-alt"></i> Filtros de Pesquisa</h5>
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Pesquisar</label>
                        <input type="text" name="search" class="form-control" placeholder="Nome, email ou telefone..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendente" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                            <option value="confirmado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmado') ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="rejeitado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'rejeitado') ? 'selected' : ''; ?>>Rejeitado</option>
                        </select>
                    </div>
                    
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label">Método</label>
                        <select name="metodo" class="form-select">
                            <option value="">Todos</option>
                            <option value="banco" <?php echo (isset($_GET['metodo']) && $_GET['metodo'] == 'banco') ? 'selected' : ''; ?>>Banco</option>
                            <option value="mpesa" <?php echo (isset($_GET['metodo']) && $_GET['metodo'] == 'mpesa') ? 'selected' : ''; ?>>M-Pesa</option>
                            <option value="emola" <?php echo (isset($_GET['metodo']) && $_GET['metodo'] == 'emola') ? 'selected' : ''; ?>>e-Mola</option>
                            <option value="paypal" <?php echo (isset($_GET['metodo']) && $_GET['metodo'] == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                            <option value="outro" <?php echo (isset($_GET['metodo']) && $_GET['metodo'] == 'outro') ? 'selected' : ''; ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div class="col-lg-5 col-md-6">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-modern flex-fill">
                                <i class="bx bx-search-alt"></i> Filtrar
                            </button>
                            <a href="doadores.php" class="btn btn-secondary btn-modern flex-fill">
                                <i class="bx bx-reset"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabela Desktop -->
        <div class="table-card">
            <div class="table-card-header">
                <h5><i class="bx bx-list-ul"></i> Doadores Registrados</h5>
                <span class="text-muted" style="font-size: 0.875rem;">
                    Mostrando <?php echo min($offset + 1, $total_items); ?> - <?php echo min($offset + $items_per_page, $total_items); ?> de <?php echo $total_items; ?> registros
                </span>
            </div>
            
            <div class="table-responsive desktop-only">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Nome</th>
                            <th>Contato</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_config = [
                                    'pendente' => ['class' => 'warning', 'icon' => 'bx-time', 'text' => 'Pendente'],
                                    'confirmado' => ['class' => 'success', 'icon' => 'bx-check-circle', 'text' => 'Confirmado'],
                                    'rejeitado' => ['class' => 'danger', 'icon' => 'bx-x-circle', 'text' => 'Rejeitado']
                                ];
                                
                                $status = $status_config[$row['status']] ?? $status_config['pendente'];
                                $valor = floatval($row['valor'] ?? 0);
                                
                                echo "<tr>";
                                echo "<td><strong>#" . $row['id'] . "</strong></td>";
                                echo "<td><strong>" . htmlspecialchars($row['nome']) . "</strong></td>";
                                echo "<td>";
                                echo "<div style='font-size: 0.875rem;'>";
                                echo "<div><i class='bx bx-envelope'></i> " . htmlspecialchars($row['email']) . "</div>";
                                echo "<div><i class='bx bx-phone'></i> " . htmlspecialchars($row['telefone']) . "</div>";
                                echo "</div>";
                                echo "</td>";
                                echo "<td><strong style='color: var(--success-color);'>" . number_format($valor, 2, ',', '.') . " MT</strong></td>";
                                echo "<td><span class='badge-modern badge-info'><i class='bx bx-credit-card'></i>" . strtoupper($row['metodo_pagamento']) . "</span></td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row['data_doacao'])) . "</td>";
                                echo "<td><span class='badge-modern badge-{$status['class']}'><i class='bx {$status['icon']}'></i>{$status['text']}</span></td>";
                                echo "<td>";
                                echo "<div class='dropdown action-dropdown'>";
                                echo "<button type='button' class='btn dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
                                echo "<i class='bx bx-dots-vertical-rounded'></i>";
                                echo "</button>";
                                echo "<div class='dropdown-menu dropdown-menu-end'>";
                                
                                if ($row['status'] == 'pendente') {
                                    echo "<form method='POST' action='' class='d-inline' onsubmit='return confirm(\"Confirmar aprovação?\")'>";
                                    echo "<input type='hidden' name='doador_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='aprovar'>";
                                    echo "<button type='submit' class='dropdown-item'><i class='bx bx-check'></i> Aprovar</button>";
                                    echo "</form>";
                                    
                                    echo "<form method='POST' action='' class='d-inline' onsubmit='return confirm(\"Confirmar rejeição?\")'>";
                                    echo "<input type='hidden' name='doador_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='rejeitar'>";
                                    echo "<button type='submit' class='dropdown-item'><i class='bx bx-x'></i> Rejeitar</button>";
                                    echo "</form>";
                                    echo "<div class='dropdown-divider'></div>";
                                }
                                
                                if (!empty($row['comprovativo'])) {
                                    echo "<a href='../" . htmlspecialchars($row['comprovativo']) . "' target='_blank' class='dropdown-item'><i class='bx bx-file'></i> Ver Comprovativo</a>";
                                }
                                
                                echo "<form method='POST' action='remover_doador.php' class='d-inline' onsubmit='return confirm(\"Tem certeza que deseja remover este doador?\")'>";
                                echo "<input type='hidden' name='doador_id' value='" . $row['id'] . "'>";
                                echo "<button type='submit' class='dropdown-item text-danger'><i class='bx bx-trash'></i> Remover</button>";
                                echo "</form>";
                                
                                echo "</div>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>";
                            echo "<div class='empty-state'>";
                            echo "<i class='bx bx-search-alt'></i>";
                            echo "<h6>Nenhum doador encontrado</h6>";
                            echo "<p>Tente ajustar os filtros de pesquisa</p>";
                            echo "</div>";
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Cards Mobile -->
            <div class="mobile-cards-container">
                <?php
                // Reset result pointer para mobile
                $result->data_seek(0);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_config = [
                            'pendente' => ['class' => 'warning', 'icon' => 'bx-time', 'text' => 'Pendente'],
                            'confirmado' => ['class' => 'success', 'icon' => 'bx-check-circle', 'text' => 'Confirmado'],
                            'rejeitado' => ['class' => 'danger', 'icon' => 'bx-x-circle', 'text' => 'Rejeitado']
                        ];
                        
                        $status = $status_config[$row['status']] ?? $status_config['pendente'];
                        $valor = floatval($row['valor'] ?? 0);
                        ?>
                        
                        <div class="mobile-card">
                            <div class="mobile-card-header">
                                <div class="mobile-card-info">
                                    <h6><?php echo htmlspecialchars($row['nome']); ?></h6>
                                    <p><i class="bx bx-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></p>
                                </div>
                                <span class="badge-modern badge-<?php echo $status['class']; ?>">
                                    <i class="bx <?php echo $status['icon']; ?>"></i>
                                    <?php echo $status['text']; ?>
                                </span>
                            </div>
                            
                            <div class="mobile-card-body">
                                <div class="mobile-card-item">
                                    <span class="label">ID:</span>
                                    <span class="value">#<?php echo $row['id']; ?></span>
                                </div>
                                <div class="mobile-card-item">
                                    <span class="label">Telefone:</span>
                                    <span class="value"><?php echo htmlspecialchars($row['telefone']); ?></span>
                                </div>
                                <div class="mobile-card-item">
                                    <span class="label">Valor:</span>
                                    <span class="value" style="color: var(--success-color); font-weight: 700;">
                                        <?php echo number_format($valor, 2, ',', '.'); ?> MT
                                    </span>
                                </div>
                                <div class="mobile-card-item">
                                    <span class="label">Método:</span>
                                    <span class="value">
                                        <span class="badge-modern badge-info">
                                            <?php echo strtoupper($row['metodo_pagamento']); ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="mobile-card-item">
                                    <span class="label">Data:</span>
                                    <span class="value"><?php echo date('d/m/Y H:i', strtotime($row['data_doacao'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="mobile-card-actions">
                                <?php if ($row['status'] == 'pendente'): ?>
                                <form method="POST" action="" class="d-inline flex-fill" onsubmit="return confirm('Confirmar aprovação?')">
                                    <input type="hidden" name="doador_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="aprovar">
                                    <button type="submit" class="btn btn-success btn-modern w-100">
                                        <i class="bx bx-check"></i> Aprovar
                                    </button>
                                </form>
                                
                                <form method="POST" action="" class="d-inline flex-fill" onsubmit="return confirm('Confirmar rejeição?')">
                                    <input type="hidden" name="doador_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="rejeitar">
                                    <button type="submit" class="btn btn-danger btn-modern w-100">
                                        <i class="bx bx-x"></i> Rejeitar
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if (!empty($row['comprovativo'])): ?>
                                <a href="../<?php echo htmlspecialchars($row['comprovativo']); ?>" target="_blank" class="btn btn-info btn-modern flex-fill">
                                    <i class="bx bx-file"></i> Comprovativo
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php
                    }
                } else {
                    ?>
                    <div class="empty-state">
                        <i class="bx bx-search-alt"></i>
                        <h6>Nenhum doador encontrado</h6>
                        <p>Tente ajustar os filtros de pesquisa</p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-modern">
                <?php
                // Botão Anterior
                if ($page > 1) {
                    echo '<a href="?page=' . ($page - 1);
                    if (isset($_GET['status'])) echo '&status=' . urlencode($_GET['status']);
                    if (isset($_GET['metodo'])) echo '&metodo=' . urlencode($_GET['metodo']);
                    if (isset($_GET['search'])) echo '&search=' . urlencode($_GET['search']);
                    echo '" class="page-link"><i class="bx bx-chevron-left"></i></a>';
                } else {
                    echo '<span class="page-link disabled"><i class="bx bx-chevron-left"></i></span>';
                }
                
                // Números das páginas
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1) {
                    echo '<a href="?page=1';
                    if (isset($_GET['status'])) echo '&status=' . urlencode($_GET['status']);
                    if (isset($_GET['metodo'])) echo '&metodo=' . urlencode($_GET['metodo']);
                    if (isset($_GET['search'])) echo '&search=' . urlencode($_GET['search']);
                    echo '" class="page-link">1</a>';
                    if ($start > 2) echo '<span class="page-link disabled">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo '<a href="?page=' . $i;
                    if (isset($_GET['status'])) echo '&status=' . urlencode($_GET['status']);
                    if (isset($_GET['metodo'])) echo '&metodo=' . urlencode($_GET['metodo']);
                    if (isset($_GET['search'])) echo '&search=' . urlencode($_GET['search']);
                    echo '" class="page-link ' . $active . '">' . $i . '</a>';
                }
                
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<span class="page-link disabled">...</span>';
                    echo '<a href="?page=' . $total_pages;
                    if (isset($_GET['status'])) echo '&status=' . urlencode($_GET['status']);
                    if (isset($_GET['metodo'])) echo '&metodo=' . urlencode($_GET['metodo']);
                    if (isset($_GET['search'])) echo '&search=' . urlencode($_GET['search']);
                    echo '" class="page-link">' . $total_pages . '</a>';
                }
                
                // Botão Próximo
                if ($page < $total_pages) {
                    echo '<a href="?page=' . ($page + 1);
                    if (isset($_GET['status'])) echo '&status=' . urlencode($_GET['status']);
                    if (isset($_GET['metodo'])) echo '&metodo=' . urlencode($_GET['metodo']);
                    if (isset($_GET['search'])) echo '&search=' . urlencode($_GET['search']);
                    echo '" class="page-link"><i class="bx bx-chevron-right"></i></a>';
                } else {
                    echo '<span class="page-link disabled"><i class="bx bx-chevron-right"></i></span>';
                }
                ?>
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

<?php
include('footer.php');
$stmt->close();
$conn->close();
?>