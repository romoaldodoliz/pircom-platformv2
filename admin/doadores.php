<?php
session_start();
include('header.php');
include('config/conexao.php');

// Flash message vinda do remover_doador.php
$message = null;
if (!empty($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

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
            
            if ($success) {
                $message = ['type' => 'success', 'text' => 'Ação realizada com sucesso!'];
            }
        }
    } catch (Exception $e) {
        $message = ['type' => 'danger', 'text' => 'Erro ao processar ação: ' . $e->getMessage()];
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

$items_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

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

$stats_sql = "SELECT 
    COUNT(*) as total,
    COALESCE(SUM(CASE WHEN status = 'confirmado' THEN valor ELSE 0 END), 0) as total_confirmado,
    COALESCE(SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END), 0) as pendentes,
    COALESCE(SUM(CASE WHEN status = 'rejeitado' THEN 1 ELSE 0 END), 0) as rejeitados
    FROM doadores";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$stats['total'] = intval($stats['total'] ?? 0);
$stats['total_confirmado'] = floatval($stats['total_confirmado'] ?? 0);
$stats['pendentes'] = intval($stats['pendentes'] ?? 0);
$stats['rejeitados'] = intval($stats['rejeitados'] ?? 0);
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --primary: #FF6F0F;
        --primary-light: rgba(255, 111, 15, 0.08);
        --primary-mid: rgba(255, 111, 15, 0.15);
        --success: #16a34a;
        --success-light: rgba(22, 163, 74, 0.08);
        --warning: #d97706;
        --warning-light: rgba(217, 119, 6, 0.08);
        --danger: #dc2626;
        --danger-light: rgba(220, 38, 38, 0.08);
        --info: #0891b2;
        --info-light: rgba(8, 145, 178, 0.08);
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e5e7eb;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.07), 0 2px 6px rgba(0,0,0,0.04);
        --shadow-lg: 0 10px 30px rgba(0,0,0,0.10), 0 4px 10px rgba(0,0,0,0.05);
    }

    body, .content-wrapper * {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .doa-wrapper {
        padding: 1.5rem;
        background: var(--bg);
        min-height: 100vh;
    }

    /* ── PAGE HEADER ── */
    .doa-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .doa-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .doa-header-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--primary), #ff8c34);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(255,111,15,0.35);
    }

    .doa-header h1 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .doa-count-pill {
        display: inline-flex;
        align-items: center;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 0.8125rem;
        font-weight: 700;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        border: 1px solid var(--primary-mid);
    }

    /* ── ALERT ── */
    .doa-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        font-weight: 500;
        font-size: 0.9375rem;
        animation: fadeSlideDown 0.3s ease;
    }

    .doa-alert.success { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
    .doa-alert.danger  { background: var(--danger-light);  color: var(--danger);  border: 1px solid rgba(220,38,38,0.2); }

    @keyframes fadeSlideDown {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── STATS GRID ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-icon.primary { background: linear-gradient(135deg,#fff0e6,#ffe0c8); color: var(--primary); }
    .stat-icon.success { background: linear-gradient(135deg,#ecfdf5,#d1fae5); color: var(--success); }
    .stat-icon.warning { background: linear-gradient(135deg,#fffbeb,#fef3c7); color: var(--warning); }
    .stat-icon.danger  { background: linear-gradient(135deg,#fef2f2,#fee2e2); color: var(--danger); }

    .stat-info { min-width: 0; }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.625rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
        letter-spacing: -0.02em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── FILTER CARD ── */
    .filter-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin-bottom: 1.25rem;
    }

    .filter-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-title i { color: var(--primary); }

    .filter-row {
        display: grid;
        grid-template-columns: 1fr 160px 160px auto;
        gap: 0.75rem;
        align-items: end;
    }

    .form-group { display: flex; flex-direction: column; gap: 0.35rem; }

    .form-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .form-control, .form-select {
        height: 42px;
        padding: 0 0.875rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        font-family: inherit;
        color: var(--text-primary);
        background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(255,111,15,0.12);
        background: white;
    }

    .filter-actions { display: flex; gap: 0.5rem; padding-bottom: 0; }

    /* ── BUTTONS ── */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0 1.125rem;
        height: 42px;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        border: none;
        transition: all 0.18s ease;
        white-space: nowrap;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), #ff8c34);
        color: white;
        box-shadow: 0 2px 8px rgba(255,111,15,0.28);
    }
    .btn-primary:hover { box-shadow: 0 4px 14px rgba(255,111,15,0.38); transform: translateY(-1px); color: white; }

    .btn-ghost {
        background: #f3f4f6;
        color: var(--text-secondary);
        border: 1.5px solid var(--border);
    }
    .btn-ghost:hover { background: #e5e7eb; color: var(--text-primary); }

    .btn-approve {
        background: var(--success-light);
        color: var(--success);
        border: 1.5px solid rgba(22,163,74,0.3);
        padding: 0 0.75rem;
        height: 32px;
        font-size: 0.8rem;
    }
    .btn-approve:hover { background: var(--success); color: white; border-color: var(--success); }

    .btn-reject {
        background: var(--danger-light);
        color: var(--danger);
        border: 1.5px solid rgba(220,38,38,0.3);
        padding: 0 0.75rem;
        height: 32px;
        font-size: 0.8rem;
    }
    .btn-reject:hover { background: var(--danger); color: white; border-color: var(--danger); }

    .btn-file {
        background: var(--info-light);
        color: var(--info);
        border: 1.5px solid rgba(8,145,178,0.3);
        padding: 0 0.625rem;
        height: 32px;
        font-size: 0.8rem;
    }
    .btn-file:hover { background: var(--info); color: white; border-color: var(--info); }

    .btn-delete {
        background: transparent;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        padding: 0 0.625rem;
        height: 32px;
        font-size: 0.875rem;
    }
    .btn-delete:hover { background: var(--danger-light); color: var(--danger); border-color: rgba(220,38,38,0.3); }

    /* ── MAIN TABLE CARD ── */
    .table-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .table-card-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        border-bottom: 1px solid var(--border);
    }

    .table-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .table-card-title i { color: var(--primary); }

    .table-meta {
        font-size: 0.8125rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* ── DONOR LIST ── */
    .donor-list { padding: 0.5rem; }

    .donor-row {
        display: grid;
        grid-template-columns: 48px 1fr 130px 110px 120px 140px auto;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: var(--radius-md);
        transition: background 0.15s;
        border-bottom: 1px solid #f3f4f6;
    }

    .donor-row:last-child { border-bottom: none; }
    .donor-row:hover { background: #fafafa; }

    .donor-row.header-row {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        padding: 0.625rem 1rem;
        border-bottom: 1px solid var(--border);
        background: #f9fafb;
        border-radius: 0;
    }

    .donor-id {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-muted);
        text-align: center;
    }

    .donor-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-light), var(--primary-mid));
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .donor-info-cell { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }

    .donor-details { min-width: 0; }

    .donor-name {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.125rem;
    }

    .donor-contact {
        font-size: 0.75rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .donor-amount {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--success);
        white-space: nowrap;
    }

    .donor-method .badge-method {
        background: var(--info-light);
        color: var(--info);
        border: 1px solid rgba(8,145,178,0.2);
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        display: inline-block;
    }

    .donor-date {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    /* ── STATUS BADGES ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-badge.pendente   { background: var(--warning-light); color: var(--warning); border: 1px solid rgba(217,119,6,0.2); }
    .status-badge.confirmado { background: var(--success-light);  color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
    .status-badge.rejeitado  { background: var(--danger-light);   color: var(--danger);  border: 1px solid rgba(220,38,38,0.2); }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-badge.pendente   .status-dot { background: var(--warning); }
    .status-badge.confirmado .status-dot { background: var(--success); }
    .status-badge.rejeitado  .status-dot { background: var(--danger); }

    /* ── ACTION BUTTONS IN ROW ── */
    .donor-actions {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-wrap: nowrap;
        min-width: 0;
    }

    .btn-approve .btn-label,
    .btn-reject .btn-label,
    .btn-file .btn-label { display: inline; }

    /* ── EMPTY STATE ── */
    .empty-state {
        padding: 3.5rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        width: 72px;
        height: 72px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: var(--text-muted);
    }

    .empty-state h6 { font-size: 1rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.375rem; }
    .empty-state p  { font-size: 0.875rem; color: var(--text-muted); margin: 0; }

    /* ── PAGINATION ── */
    .pagination-wrap {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 0.625rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-secondary);
        border: 1.5px solid var(--border);
        background: white;
        text-decoration: none;
        transition: all 0.15s;
    }

    .page-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .page-btn.active { background: var(--primary); border-color: var(--primary); color: white; box-shadow: 0 2px 8px rgba(255,111,15,0.3); }
    .page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .page-btn.ellipsis { border-color: transparent; background: none; cursor: default; }
    .page-btn.ellipsis:hover { border-color: transparent; background: none; color: var(--text-muted); }

    /* ═══════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════ */

    @media (max-width: 1200px) {
        .donor-row {
            grid-template-columns: 44px 1fr 110px 100px 110px 130px auto;
            gap: 0.5rem;
        }
        .stat-value { font-size: 1.375rem; }
        .btn-approve .btn-label,
        .btn-reject .btn-label,
        .btn-file .btn-label { display: none; }
    }

    @media (max-width: 991px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .filter-row { grid-template-columns: 1fr 1fr; }
        .filter-actions { grid-column: 1 / -1; }

        .donor-row.header-row { display: none; }

        .donor-row {
            grid-template-columns: 1fr;
            gap: 0;
            padding: 1rem;
            border-bottom: none;
            border-radius: var(--radius-md);
            background: white;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            margin-bottom: 0.75rem;
        }

        .donor-row:hover { background: white; transform: none; box-shadow: var(--shadow-md); }

        .donor-list { padding: 1rem; display: flex; flex-direction: column; }

        .donor-id { display: none; }

        .donor-info-cell { margin-bottom: 0.875rem; }

        .donor-meta-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1rem;
            margin-bottom: 0.75rem;
        }

        .donor-meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .donor-meta-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .donor-actions {
            border-top: 1px solid var(--border);
            padding-top: 0.75rem;
            margin-top: 0.25rem;
            flex-wrap: wrap;
        }

        .donor-actions .btn { flex: 1; min-width: 90px; }
    }

    @media (max-width: 767px) {
        .doa-wrapper { padding: 1rem; }
        .doa-header h1 { font-size: 1.25rem; }
        .stats-grid { gap: 0.75rem; }
        .stat-card { padding: 1rem; }
        .stat-value { font-size: 1.25rem; }
        .filter-row { grid-template-columns: 1fr; }
    }

    @media (max-width: 575px) {
        .doa-wrapper { padding: 0.75rem; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        .stat-icon { width: 42px; height: 42px; font-size: 1.25rem; }
        .stat-value { font-size: 1.125rem; }
        .stat-label { font-size: 0.7rem; }
        .filter-card, .table-card { border-radius: var(--radius-md); }
    }
</style>

<div class="content-wrapper">
<div class="doa-wrapper">

    <!-- Header -->
    <div class="doa-header">
        <div class="doa-header-left">
            <div class="doa-header-icon"><i class="bx bx-donate-heart"></i></div>
            <div>
                <h1>Lista de Doadores</h1>
            </div>
        </div>
        <span class="doa-count-pill"><?php echo number_format($stats['total'], 0); ?> registros</span>
    </div>

    <!-- Alert (flash de remoção + feedback de aprovar/rejeitar) -->
    <?php if (isset($message)): ?>
    <div class="doa-alert <?php echo $message['type']; ?>">
        <i class="bx <?php echo $message['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($message['text']); ?></span>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bx bx-donate-heart"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total de Doações</div>
                <div class="stat-value"><?php echo number_format($stats['total'], 0); ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="bx bx-money"></i></div>
            <div class="stat-info">
                <div class="stat-label">Valor Confirmado</div>
                <div class="stat-value" style="font-size:1.25rem;"><?php echo number_format($stats['total_confirmado'], 2, ',', '.'); ?> MT</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bx bx-time"></i></div>
            <div class="stat-info">
                <div class="stat-label">Pendentes</div>
                <div class="stat-value"><?php echo number_format($stats['pendentes'], 0); ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger"><i class="bx bx-x-circle"></i></div>
            <div class="stat-info">
                <div class="stat-label">Rejeitados</div>
                <div class="stat-value"><?php echo number_format($stats['rejeitados'], 0); ?></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <div class="filter-title"><i class="bx bx-filter-alt"></i> Filtros de Pesquisa</div>
        <form method="GET" action="">
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" name="search" class="form-control" placeholder="Nome, email ou telefone..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="pendente"   <?php echo (($_GET['status'] ?? '') == 'pendente')   ? 'selected' : ''; ?>>Pendente</option>
                        <option value="confirmado" <?php echo (($_GET['status'] ?? '') == 'confirmado') ? 'selected' : ''; ?>>Confirmado</option>
                        <option value="rejeitado"  <?php echo (($_GET['status'] ?? '') == 'rejeitado')  ? 'selected' : ''; ?>>Rejeitado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Método</label>
                    <select name="metodo" class="form-select">
                        <option value="">Todos os métodos</option>
                        <option value="banco"  <?php echo (($_GET['metodo'] ?? '') == 'banco')  ? 'selected' : ''; ?>>Banco</option>
                        <option value="mpesa"  <?php echo (($_GET['metodo'] ?? '') == 'mpesa')  ? 'selected' : ''; ?>>M-Pesa</option>
                        <option value="emola"  <?php echo (($_GET['metodo'] ?? '') == 'emola')  ? 'selected' : ''; ?>>e-Mola</option>
                        <option value="paypal" <?php echo (($_GET['metodo'] ?? '') == 'paypal') ? 'selected' : ''; ?>>PayPal</option>
                        <option value="outro"  <?php echo (($_GET['metodo'] ?? '') == 'outro')  ? 'selected' : ''; ?>>Outro</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search-alt"></i> Filtrar</button>
                    <a href="doadores.php" class="btn btn-ghost"><i class="bx bx-reset"></i> Limpar</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5 class="table-card-title"><i class="bx bx-list-ul"></i> Doadores Registrados</h5>
            <span class="table-meta">
                Mostrando <?php echo min($offset + 1, $total_items); ?>–<?php echo min($offset + $items_per_page, $total_items); ?> de <?php echo $total_items; ?>
            </span>
        </div>

        <div class="donor-list">
            <!-- Desktop Header Row -->
            <div class="donor-row header-row">
                <div>#</div>
                <div>Doador</div>
                <div>Valor</div>
                <div>Método</div>
                <div>Status</div>
                <div>Data</div>
                <div>Ações</div>
            </div>

            <?php
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $status_map = [
                        'pendente'   => ['text' => 'Pendente',   'class' => 'pendente'],
                        'confirmado' => ['text' => 'Confirmado', 'class' => 'confirmado'],
                        'rejeitado'  => ['text' => 'Rejeitado',  'class' => 'rejeitado'],
                    ];
                    $s = $status_map[$row['status']] ?? $status_map['pendente'];
                    $valor = floatval($row['valor'] ?? 0);
                    $initials = mb_strtoupper(mb_substr($row['nome'], 0, 1));
            ?>
            <div class="donor-row">
                <!-- ID -->
                <div class="donor-id">#<?php echo $row['id']; ?></div>

                <!-- Info -->
                <div class="donor-info-cell">
                    <div class="donor-avatar"><?php echo $initials; ?></div>
                    <div class="donor-details">
                        <div class="donor-name"><?php echo htmlspecialchars($row['nome']); ?></div>
                        <div class="donor-contact"><?php echo htmlspecialchars($row['email']); ?> · <?php echo htmlspecialchars($row['telefone']); ?></div>
                    </div>
                </div>

                <!-- Amount -->
                <div class="donor-amount"><?php echo number_format($valor, 2, ',', '.'); ?> MT</div>

                <!-- Method -->
                <div class="donor-method">
                    <span class="badge-method"><?php echo strtoupper($row['metodo_pagamento']); ?></span>
                </div>

                <!-- Status -->
                <div>
                    <span class="status-badge <?php echo $s['class']; ?>">
                        <span class="status-dot"></span>
                        <?php echo $s['text']; ?>
                    </span>
                </div>

                <!-- Date -->
                <div class="donor-date"><?php echo date('d/m/Y H:i', strtotime($row['data_doacao'])); ?></div>

                <!-- Actions -->
                <div class="donor-actions">
                    <?php if ($row['status'] == 'pendente'): ?>
                    <form method="POST" action="" style="display:contents;" onsubmit="return confirm('Confirmar aprovação?')">
                        <input type="hidden" name="doador_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="action" value="aprovar">
                        <button type="submit" class="btn btn-approve"><i class="bx bx-check"></i> <span class="btn-label">Aprovar</span></button>
                    </form>
                    <form method="POST" action="" style="display:contents;" onsubmit="return confirm('Confirmar rejeição?')">
                        <input type="hidden" name="doador_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="action" value="rejeitar">
                        <button type="submit" class="btn btn-reject"><i class="bx bx-x"></i> <span class="btn-label">Rejeitar</span></button>
                    </form>
                    <?php endif; ?>

                    <?php if (!empty($row['comprovativo'])): ?>
                    <a href="../<?php echo htmlspecialchars($row['comprovativo']); ?>" target="_blank" class="btn btn-file" title="Ver Comprovativo">
                        <i class="bx bx-file"></i>
                        <span class="btn-label">Ficheiro</span>
                    </a>
                    <?php endif; ?>

                    <form method="POST" action="remover_doador.php" style="display:contents;" onsubmit="return confirm('Remover este doador permanentemente?')">
                        <input type="hidden" name="doador_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-delete" title="Remover">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bx bx-search-alt"></i></div>
                <h6>Nenhum doador encontrado</h6>
                <p>Tente ajustar os filtros de pesquisa</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-wrap">
            <?php
            $qs = '';
            if (!empty($_GET['status'])) $qs .= '&status=' . urlencode($_GET['status']);
            if (!empty($_GET['metodo']))  $qs .= '&metodo='  . urlencode($_GET['metodo']);
            if (!empty($_GET['search']))  $qs .= '&search='  . urlencode($_GET['search']);

            if ($page > 1)
                echo '<a href="?page=' . ($page-1) . $qs . '" class="page-btn"><i class="bx bx-chevron-left"></i></a>';
            else
                echo '<span class="page-btn disabled"><i class="bx bx-chevron-left"></i></span>';

            $start = max(1, $page - 2);
            $end   = min($total_pages, $page + 2);

            if ($start > 1) {
                echo '<a href="?page=1' . $qs . '" class="page-btn">1</a>';
                if ($start > 2) echo '<span class="page-btn ellipsis">…</span>';
            }

            for ($i = $start; $i <= $end; $i++) {
                $active = ($i == $page) ? ' active' : '';
                echo '<a href="?page=' . $i . $qs . '" class="page-btn' . $active . '">' . $i . '</a>';
            }

            if ($end < $total_pages) {
                if ($end < $total_pages - 1) echo '<span class="page-btn ellipsis">…</span>';
                echo '<a href="?page=' . $total_pages . $qs . '" class="page-btn">' . $total_pages . '</a>';
            }

            if ($page < $total_pages)
                echo '<a href="?page=' . ($page+1) . $qs . '" class="page-btn"><i class="bx bx-chevron-right"></i></a>';
            else
                echo '<span class="page-btn disabled"><i class="bx bx-chevron-right"></i></span>';
            ?>
        </div>
        <?php endif; ?>
    </div>

</div>
</div>

<?php include('footerprincipal.php'); ?>

<div class="content-backdrop fade"></div>
<?php
include('footer.php');
$stmt->close();
$conn->close();
?>