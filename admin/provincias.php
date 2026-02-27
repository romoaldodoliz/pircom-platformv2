<?php
session_start();
include('header.php');
include('../config/conexao.php');

// Flash message
$message = null;
if (!empty($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// ── SORTING ──
$sortBy    = isset($_GET['sort']) ? $_GET['sort'] : 'nome';
$sortOrder = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';
$validSort = ['id', 'nome', 'latitude', 'longitude', 'created_at'];
$sortBy    = in_array($sortBy, $validSort) ? $sortBy : 'nome';

// ── SEARCH ──
$search          = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
if (!empty($search)) {
    $s               = $conn->real_escape_string($search);
    $searchCondition = " WHERE nome LIKE '%$s%'";
}

// ── PAGINATION ──
$limit       = 10;
$page        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset      = ($page - 1) * $limit;

$count_result = $conn->query("SELECT COUNT(*) as total FROM provincias" . $searchCondition);
$total_rows   = (int)$count_result->fetch_assoc()['total'];
$total_pages  = max(1, ceil($total_rows / $limit));
$page         = min($page, $total_pages);
$offset       = ($page - 1) * $limit;

// ── QUERY ──
$result = $conn->query(
    "SELECT * FROM provincias $searchCondition ORDER BY $sortBy $sortOrder LIMIT $limit OFFSET $offset"
);

// ── STATS ──
$stats = $conn->query(
    "SELECT COUNT(*) as total, MIN(created_at) as oldest, MAX(created_at) as newest FROM provincias"
)->fetch_assoc();
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --primary: #2563eb;
    --primary-light: rgba(37,99,235,0.08);
    --primary-mid: rgba(37,99,235,0.15);
    --success: #16a34a;
    --success-light: rgba(22,163,74,0.08);
    --warning: #d97706;
    --warning-light: rgba(217,119,6,0.08);
    --danger: #dc2626;
    --danger-light: rgba(220,38,38,0.08);
    --info: #0891b2;
    --info-light: rgba(8,145,178,0.08);
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

body, .content-wrapper * { font-family: 'Plus Jakarta Sans', sans-serif; }

.prov-wrapper { padding: 1.5rem; background: var(--bg); min-height: 100vh; }

/* ── HEADER ── */
.prov-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;
}
.prov-header-left { display: flex; align-items: center; gap: 0.75rem; }
.prov-header-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, var(--primary), #1e40af);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.25rem; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(37,99,235,0.35);
}
.prov-header h1 { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: -0.02em; }
.prov-header p  { font-size: 0.85rem; color: var(--text-muted); margin: 0; }
.prov-count-pill {
    display: inline-flex; align-items: center;
    background: var(--primary-light); color: var(--primary);
    font-size: 0.8125rem; font-weight: 700;
    padding: 0.25rem 0.75rem; border-radius: 999px;
    border: 1px solid var(--primary-mid);
}

/* ── ALERT ── */
.prov-alert {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 1.25rem; border-radius: var(--radius-md);
    margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9375rem;
    animation: fadeSlideDown 0.3s ease;
}
.prov-alert.success { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.prov-alert.danger  { background: var(--danger-light);  color: var(--danger);  border: 1px solid rgba(220,38,38,0.2); }
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── STATS ── */
.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card {
    background: var(--surface); border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    display: flex; align-items: center; gap: 1rem;
    transition: box-shadow 0.2s, transform 0.2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.stat-icon {
    width: 52px; height: 52px; border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.stat-icon.primary { background: linear-gradient(135deg,#eff6ff,#dbeafe); color: var(--primary); }
.stat-icon.info    { background: linear-gradient(135deg,#ecfeff,#cffafe); color: var(--info); }
.stat-icon.warning { background: linear-gradient(135deg,#fffbeb,#fef3c7); color: var(--warning); }
.stat-icon.success { background: linear-gradient(135deg,#ecfdf5,#d1fae5); color: var(--success); }
.stat-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.2rem; }
.stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); line-height: 1.1; letter-spacing: -0.02em; }
.stat-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; }

/* ── FILTER ── */
.filter-card {
    background: var(--surface); border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm);
    border: 1px solid var(--border); margin-bottom: 1.25rem;
}
.filter-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.filter-title i { color: var(--primary); }
.filter-row { display: grid; grid-template-columns: 1fr auto auto; gap: 0.75rem; align-items: end; }
.form-group { display: flex; flex-direction: column; gap: 0.35rem; }
.form-label { font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary); }
.form-control {
    height: 42px; padding: 0 0.875rem;
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 0.9rem; font-family: inherit; color: var(--text-primary);
    background: #fafafa; transition: border-color 0.2s, box-shadow 0.2s; width: 100%;
}
.form-control:focus {
    outline: none; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12); background: white;
}
.filter-actions { display: flex; gap: 0.5rem; }

/* ── QUICK SORT PILLS ── */
.quick-sort {
    display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem;
}
.sort-pill {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.35rem 0.875rem; border-radius: 999px;
    font-size: 0.8125rem; font-weight: 600;
    background: var(--surface); border: 1.5px solid var(--border);
    color: var(--text-secondary); text-decoration: none;
    transition: all 0.15s;
}
.sort-pill:hover  { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.sort-pill.active { background: var(--primary); border-color: var(--primary); color: white; }

/* ── BUTTONS ── */
.btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 0.4rem; padding: 0 1.125rem; height: 42px;
    border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 600;
    font-family: inherit; cursor: pointer; border: none;
    transition: all 0.18s ease; white-space: nowrap; text-decoration: none;
}
.btn-primary { background: linear-gradient(135deg, var(--primary), #1e40af); color: white; box-shadow: 0 2px 8px rgba(37,99,235,0.28); }
.btn-primary:hover { box-shadow: 0 4px 14px rgba(37,99,235,0.38); transform: translateY(-1px); color: white; }
.btn-ghost   { background: #f3f4f6; color: var(--text-secondary); border: 1.5px solid var(--border); }
.btn-ghost:hover { background: #e5e7eb; color: var(--text-primary); }

.btn-icon-sm {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: var(--radius-sm);
    font-size: 0.875rem; cursor: pointer; border: none; transition: all 0.15s;
    text-decoration: none;
}
.btn-edit   { background: var(--warning-light); color: var(--warning); border: 1.5px solid rgba(217,119,6,0.25); }
.btn-edit:hover { background: var(--warning); color: white; border-color: var(--warning); }
.btn-map    { background: var(--info-light); color: var(--info); border: 1.5px solid rgba(8,145,178,0.25); }
.btn-map:hover { background: var(--info); color: white; border-color: var(--info); }
.btn-delete { background: transparent; color: var(--text-muted); border: 1.5px solid var(--border); }
.btn-delete:hover { background: var(--danger-light); color: var(--danger); border-color: rgba(220,38,38,0.3); }

/* ── SORT LINK ── */
.sort-link {
    display: inline-flex; align-items: center; gap: 0.3rem;
    color: var(--text-muted); text-decoration: none;
    font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    transition: color 0.15s;
}
.sort-link:hover, .sort-link.active { color: var(--primary); }

/* ── TABLE CARD ── */
.table-card {
    background: var(--surface); border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm); border: 1px solid var(--border); overflow: hidden;
}
.table-card-header {
    padding: 1.25rem 1.5rem; display: flex; align-items: center;
    justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    border-bottom: 1px solid var(--border);
}
.table-card-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem; margin: 0; }
.table-card-title i { color: var(--primary); }
.table-meta { font-size: 0.8125rem; color: var(--text-muted); font-weight: 500; }

/* ── PROVINCE LIST ── */
.prov-list { padding: 0.5rem; }
.prov-row {
    display: grid;
    grid-template-columns: 44px 1fr 120px 120px 120px auto;
    align-items: center; gap: 0.75rem;
    padding: 0.875rem 1rem; border-radius: var(--radius-md);
    transition: background 0.15s; border-bottom: 1px solid #f3f4f6;
}
.prov-row:last-child { border-bottom: none; }
.prov-row:hover { background: #fafafa; }
.prov-row.header-row {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--text-muted);
    padding: 0.625rem 1rem; border-bottom: 1px solid var(--border);
    background: #f9fafb; border-radius: 0;
}

.prov-id { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-align: center; }

.prov-name-cell { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
.prov-name-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    background: var(--primary-light); border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); font-size: 0.9rem;
}
.prov-name { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prov-coords-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.1rem; }

.coord-chip {
    display: inline-flex; align-items: center;
    background: #f3f4f6; border: 1px solid var(--border);
    border-radius: 6px; padding: 0.25rem 0.5rem;
    font-size: 0.8rem; font-family: 'Courier New', monospace;
    font-weight: 600; color: var(--text-secondary);
    white-space: nowrap; cursor: default;
    transition: background 0.15s;
}
.coord-chip:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary-mid); }
.coord-chip.lat { color: var(--info); border-color: rgba(8,145,178,0.2); background: var(--info-light); }
.coord-chip.lng { color: var(--warning); border-color: rgba(217,119,6,0.2); background: var(--warning-light); }

.date-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: var(--primary-light); color: var(--primary);
    border: 1px solid rgba(37,99,235,0.15);
    padding: 0.25rem 0.65rem; border-radius: 6px;
    font-size: 0.75rem; font-weight: 700; white-space: nowrap;
}

.prov-actions { display: flex; align-items: center; gap: 0.375rem; }

/* ── EMPTY ── */
.empty-state { padding: 3.5rem 2rem; text-align: center; }
.empty-state-icon {
    width: 72px; height: 72px; background: #f3f4f6; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem; font-size: 2rem; color: var(--text-muted);
}
.empty-state h6 { font-size: 1rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.375rem; }
.empty-state p  { font-size: 0.875rem; color: var(--text-muted); margin: 0 0 1rem; }

/* ── PAGINATION ── */
.pagination-wrap {
    padding: 1rem 1.5rem; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
}
.pagination-info { font-size: 0.8125rem; color: var(--text-muted); font-weight: 500; }
.pagination-btns { display: flex; gap: 0.375rem; }
.page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 0.625rem;
    border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 600;
    color: var(--text-secondary); border: 1.5px solid var(--border);
    background: white; text-decoration: none; transition: all 0.15s;
}
.page-btn:hover  { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.page-btn.active { background: var(--primary); border-color: var(--primary); color: white; box-shadow: 0 2px 8px rgba(37,99,235,0.3); }
.page-btn.disabled { opacity: 0.4; pointer-events: none; }
.page-btn.ellipsis { border-color: transparent; background: none; cursor: default; }
.page-btn.ellipsis:hover { border-color: transparent; background: none; color: var(--text-muted); }

/* ── INFO CARD ── */
.info-card {
    background: var(--surface); border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm);
    border: 1px solid var(--border); margin-top: 1.25rem;
    display: flex; align-items: flex-start; gap: 1rem;
}
.info-card-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    background: var(--info-light); border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: var(--info); font-size: 1.25rem;
}
.info-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
.info-tag {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.25rem 0.65rem; border-radius: 6px;
    font-size: 0.75rem; font-weight: 600;
}
.info-tag.info    { background: var(--info-light);    color: var(--info);    border: 1px solid rgba(8,145,178,0.2); }
.info-tag.success { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.info-tag.warning { background: var(--warning-light); color: var(--warning); border: 1px solid rgba(217,119,6,0.2); }

/* ── MAP MODAL ── */
.map-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.7); z-index: 9999;
    align-items: center; justify-content: center; padding: 1rem;
    backdrop-filter: blur(4px);
}
.map-modal-overlay.open { display: flex; }
.map-modal-box {
    background: white; border-radius: var(--radius-lg);
    max-width: 600px; width: 100%; overflow: hidden; box-shadow: var(--shadow-lg);
}
.map-modal-header {
    padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.map-modal-header h6 { margin: 0; font-weight: 700; font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem; }
.map-modal-header h6 i { color: var(--primary); }
.map-modal-close {
    background: none; border: none; cursor: pointer; font-size: 1.125rem;
    color: var(--text-muted); transition: color 0.15s; padding: 0; line-height: 1;
}
.map-modal-close:hover { color: var(--danger); }
.map-modal-preview {
    height: 280px; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
    position: relative; display: flex; align-items: center; justify-content: center;
}
.map-modal-pin {
    text-align: center; color: white;
}
.map-modal-pin i { font-size: 3rem; display: block; margin-bottom: 0.5rem; text-shadow: 0 2px 8px rgba(0,0,0,0.4); }
.map-modal-pin strong { font-size: 1.1rem; display: block; margin-bottom: 0.25rem; }
.map-modal-pin span { font-size: 0.8rem; opacity: 0.8; }
.map-dot {
    position: absolute; top: 50%; left: 50%;
    width: 18px; height: 18px; border-radius: 50%;
    background: #fbbf24; border: 3px solid white;
    box-shadow: 0 0 0 4px rgba(251,191,36,0.4);
    transform: translate(-50%, -70px);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(251,191,36,0.4); }
    50%       { box-shadow: 0 0 0 8px rgba(251,191,36,0.15); }
}
.map-modal-body { padding: 1.25rem; }
.coord-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.coord-block { background: #f9fafb; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0.75rem; }
.coord-block label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 0.25rem; }
.coord-block code { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); }
.map-modal-footer {
    padding: 0.875rem 1.25rem; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 1100px) {
    .prov-row { grid-template-columns: 40px 1fr 100px 100px 100px auto; gap: 0.5rem; }
}
@media (max-width: 991px) {
    .stats-grid { grid-template-columns: repeat(2,1fr); }
    .prov-row.header-row { display: none; }
    .prov-row {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto auto;
        gap: 0.5rem 0.75rem; padding: 1rem;
        border-bottom: none; border-radius: var(--radius-md);
        background: white; box-shadow: var(--shadow-sm);
        border: 1px solid var(--border); margin-bottom: 0.75rem;
    }
    .prov-row:hover { background: white; box-shadow: var(--shadow-md); }
    .prov-list { padding: 1rem; display: flex; flex-direction: column; }
    .prov-id { display: none; }
    .prov-name-cell { grid-column: 1 / -1; }
    .prov-actions { grid-column: 1 / -1; border-top: 1px solid var(--border); padding-top: 0.75rem; }
    .filter-row { grid-template-columns: 1fr; }
    .filter-actions { flex-direction: row; }
    .quick-sort { display: none; }
}
@media (max-width: 767px) {
    .prov-wrapper { padding: 1rem; }
    .prov-header h1 { font-size: 1.25rem; }
    .stats-grid { gap: 0.75rem; }
    .stat-card { padding: 1rem; }
    .stat-value { font-size: 1.25rem; }
    .pagination-wrap { justify-content: center; }
    .pagination-info { width: 100%; text-align: center; }
}
@media (max-width: 575px) {
    .prov-wrapper { padding: 0.75rem; }
    .stats-grid { grid-template-columns: repeat(2,1fr); gap: 0.5rem; }
    .stat-icon { width: 42px; height: 42px; font-size: 1.25rem; }
}
</style>

<div class="content-wrapper">
<div class="prov-wrapper">

    <!-- Header -->
    <div class="prov-header">
        <div class="prov-header-left">
            <div class="prov-header-icon"><i class="bx bx-map"></i></div>
            <div>
                <h1>Províncias</h1>
                <p>Cobertura geográfica para o mapa interativo</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <span class="prov-count-pill"><?php echo number_format($stats['total']); ?> províncias</span>
            <a href="provinciasform.php" class="btn btn-primary" style="height:38px;">
                <i class="bx bx-plus"></i> Adicionar Província
            </a>
        </div>
    </div>

    <!-- Flash Alert -->
    <?php if ($message): ?>
    <div class="prov-alert <?php echo $message['type']; ?>">
        <i class="bx <?php echo $message['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($message['text']); ?></span>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bx bx-map"></i></div>
            <div>
                <div class="stat-label">Total</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                <div class="stat-sub">províncias</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info"><i class="bx bx-search-alt"></i></div>
            <div>
                <div class="stat-label">Encontradas</div>
                <div class="stat-value"><?php echo number_format($total_rows); ?></div>
                <div class="stat-sub"><?php echo !empty($search) ? 'na pesquisa' : 'no total'; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bx bx-calendar-check"></i></div>
            <div>
                <div class="stat-label">Mais Recente</div>
                <div class="stat-value" style="font-size:1rem;margin-top:0.1rem;">
                    <?php echo $stats['newest'] ? date('d/m/Y', strtotime($stats['newest'])) : 'N/A'; ?>
                </div>
                <div class="stat-sub">última adicionada</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="bx bx-calendar"></i></div>
            <div>
                <div class="stat-label">Mais Antiga</div>
                <div class="stat-value" style="font-size:1rem;margin-top:0.1rem;">
                    <?php echo $stats['oldest'] ? date('d/m/Y', strtotime($stats['oldest'])) : 'N/A'; ?>
                </div>
                <div class="stat-sub">primeira adicionada</div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <div class="filter-title"><i class="bx bx-filter-alt"></i> Pesquisa e Ordenação</div>
        <form method="GET" action="">
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label">Pesquisar por nome</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nome da província..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div style="display:flex;flex-direction:column;gap:0.35rem;">
                    <label class="form-label">Ordenar por</label>
                    <select name="sort" class="form-control" style="height:42px;">
                        <option value="nome"       <?php echo $sortBy == 'nome'       ? 'selected' : ''; ?>>Nome</option>
                        <option value="id"         <?php echo $sortBy == 'id'         ? 'selected' : ''; ?>>ID</option>
                        <option value="created_at" <?php echo $sortBy == 'created_at' ? 'selected' : ''; ?>>Data</option>
                        <option value="latitude"   <?php echo $sortBy == 'latitude'   ? 'selected' : ''; ?>>Latitude</option>
                        <option value="longitude"  <?php echo $sortBy == 'longitude'  ? 'selected' : ''; ?>>Longitude</option>
                    </select>
                </div>
                <div class="filter-actions" style="padding-bottom:0;">
                    <button type="submit" name="order" value="asc"  class="btn btn-primary"><i class="bx bx-sort-a-z"></i> A→Z</button>
                    <button type="submit" name="order" value="desc" class="btn btn-ghost"  ><i class="bx bx-sort-z-a"></i> Z→A</button>
                    <?php if (!empty($search)): ?>
                    <a href="provincias.php" class="btn btn-ghost"><i class="bx bx-reset"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick sort pills -->
    <div class="quick-sort">
        <?php
        $qs_search = !empty($search) ? '&search=' . urlencode($search) : '';
        $pills = [
            ['label' => 'Todas',       'sort' => 'id',         'order' => 'asc',  'icon' => 'bx-list-ul'],
            ['label' => 'A → Z',       'sort' => 'nome',       'order' => 'asc',  'icon' => 'bx-sort-a-z'],
            ['label' => 'Z → A',       'sort' => 'nome',       'order' => 'desc', 'icon' => 'bx-sort-z-a'],
            ['label' => 'Mais Recentes','sort' => 'created_at','order' => 'desc', 'icon' => 'bx-sort-down'],
        ];
        foreach ($pills as $pill):
            $isActive = ($sortBy === $pill['sort'] && strtolower($sortOrder) === $pill['order']);
        ?>
        <a href="?sort=<?php echo $pill['sort']; ?>&order=<?php echo $pill['order']; ?><?php echo $qs_search; ?>"
           class="sort-pill <?php echo $isActive ? 'active' : ''; ?>">
            <i class="bx <?php echo $pill['icon']; ?>"></i>
            <?php echo $pill['label']; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5 class="table-card-title">
                <i class="bx bx-list-ul"></i> Lista de Províncias
                <?php if (!empty($search)): ?>
                <span style="font-size:0.8rem;font-weight:500;color:var(--text-muted);">
                    — "<?php echo htmlspecialchars($search); ?>"
                </span>
                <?php endif; ?>
            </h5>
            <span class="table-meta">
                <?php if ($total_rows > 0): ?>
                Mostrando <?php echo $offset + 1; ?>–<?php echo min($offset + $limit, $total_rows); ?> de <?php echo $total_rows; ?>
                <?php else: ?>
                Nenhum registo
                <?php endif; ?>
            </span>
        </div>

        <div class="prov-list">
            <!-- Desktop header -->
            <div class="prov-row header-row">
                <div>
                    <a class="sort-link <?php echo $sortBy=='id' ? 'active' : ''; ?>"
                       href="?sort=id&order=<?php echo ($sortBy=='id'&&$sortOrder=='ASC') ? 'desc':'asc'; ?><?php echo $qs_search; ?>">
                        # <i class="bx bx-chevron-<?php echo ($sortBy=='id'&&$sortOrder=='ASC') ? 'up':'down'; ?>"></i>
                    </a>
                </div>
                <div>
                    <a class="sort-link <?php echo $sortBy=='nome' ? 'active' : ''; ?>"
                       href="?sort=nome&order=<?php echo ($sortBy=='nome'&&$sortOrder=='ASC') ? 'desc':'asc'; ?><?php echo $qs_search; ?>">
                        Província <i class="bx bx-chevron-<?php echo ($sortBy=='nome'&&$sortOrder=='ASC') ? 'up':'down'; ?>"></i>
                    </a>
                </div>
                <div>
                    <a class="sort-link <?php echo $sortBy=='latitude' ? 'active' : ''; ?>"
                       href="?sort=latitude&order=<?php echo ($sortBy=='latitude'&&$sortOrder=='ASC') ? 'desc':'asc'; ?><?php echo $qs_search; ?>">
                        Latitude <i class="bx bx-chevron-<?php echo ($sortBy=='latitude'&&$sortOrder=='ASC') ? 'up':'down'; ?>"></i>
                    </a>
                </div>
                <div>
                    <a class="sort-link <?php echo $sortBy=='longitude' ? 'active' : ''; ?>"
                       href="?sort=longitude&order=<?php echo ($sortBy=='longitude'&&$sortOrder=='ASC') ? 'desc':'asc'; ?><?php echo $qs_search; ?>">
                        Longitude <i class="bx bx-chevron-<?php echo ($sortBy=='longitude'&&$sortOrder=='ASC') ? 'up':'down'; ?>"></i>
                    </a>
                </div>
                <div>
                    <a class="sort-link <?php echo $sortBy=='created_at' ? 'active' : ''; ?>"
                       href="?sort=created_at&order=<?php echo ($sortBy=='created_at'&&$sortOrder=='ASC') ? 'desc':'asc'; ?><?php echo $qs_search; ?>">
                        Data <i class="bx bx-chevron-<?php echo ($sortBy=='created_at'&&$sortOrder=='ASC') ? 'up':'down'; ?>"></i>
                    </a>
                </div>
                <div>Ações</div>
            </div>

            <?php if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $nome     = htmlspecialchars($row['nome']);
                    $lat      = htmlspecialchars($row['latitude']);
                    $lng      = htmlspecialchars($row['longitude']);
                    $latShort = strlen($lat) > 9 ? substr($lat, 0, 9) . '…' : $lat;
                    $lngShort = strlen($lng) > 9 ? substr($lng, 0, 9) . '…' : $lng;
                    $dataCriacao = !empty($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : '—';
            ?>
            <div class="prov-row">
                <!-- ID -->
                <div class="prov-id">#<?php echo $row['id']; ?></div>

                <!-- Nome -->
                <div class="prov-name-cell">
                    <div class="prov-name-icon"><i class="bx bx-map-pin"></i></div>
                    <div>
                        <div class="prov-name"><?php echo $nome; ?></div>
                        <div class="prov-coords-sub"><?php echo $lat; ?>, <?php echo $lng; ?></div>
                    </div>
                </div>

                <!-- Latitude -->
                <div>
                    <span class="coord-chip lat" title="Latitude: <?php echo $lat; ?>">
                        <?php echo $latShort; ?>
                    </span>
                </div>

                <!-- Longitude -->
                <div>
                    <span class="coord-chip lng" title="Longitude: <?php echo $lng; ?>">
                        <?php echo $lngShort; ?>
                    </span>
                </div>

                <!-- Data -->
                <div>
                    <span class="date-badge">
                        <i class="bx bx-calendar" style="font-size:0.75rem;"></i>
                        <?php echo $dataCriacao; ?>
                    </span>
                </div>

                <!-- Ações -->
                <div class="prov-actions">
                    <a href="provinciasform.php?id=<?php echo $row['id']; ?>"
                       class="btn-icon-sm btn-edit" title="Editar">
                        <i class="bx bx-edit"></i>
                    </a>
                    <button type="button"
                            class="btn-icon-sm btn-map" title="Ver no mapa"
                            onclick="viewOnMap('<?php echo addslashes($nome); ?>', <?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>)">
                        <i class="bx bx-map"></i>
                    </button>
                    <form method="POST" action="remover_provincia.php"
                          class="delete-form" style="display:contents;"
                          data-nome="<?php echo addslashes($nome); ?>">
                        <input type="hidden" name="provincia_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="remover"
                                class="btn-icon-sm btn-delete" title="Remover">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile;
            else: ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bx bx-map"></i></div>
                <h6><?php echo !empty($search) ? 'Nenhuma província encontrada' : 'Nenhuma província cadastrada'; ?></h6>
                <p><?php echo !empty($search) ? 'Tente ajustar a pesquisa.' : 'Adicione a primeira província ao mapa.'; ?></p>
                <?php if (empty($search)): ?>
                <a href="provinciasform.php" class="btn btn-primary" style="height:38px;">
                    <i class="bx bx-plus"></i> Adicionar província
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-wrap">
            <span class="pagination-info">
                Mostrando <?php echo $offset + 1; ?>–<?php echo min($offset + $limit, $total_rows); ?> de <?php echo $total_rows; ?> províncias
            </span>
            <div class="pagination-btns">
                <?php
                $qs_page = '&sort=' . $sortBy . '&order=' . strtolower($sortOrder) . $qs_search;

                echo $page > 1
                    ? '<a href="?page=' . ($page-1) . $qs_page . '" class="page-btn"><i class="bx bx-chevron-left"></i></a>'
                    : '<span class="page-btn disabled"><i class="bx bx-chevron-left"></i></span>';

                $start = max(1, $page - 2);
                $end   = min($total_pages, $page + 2);

                if ($start > 1) {
                    echo '<a href="?page=1' . $qs_page . '" class="page-btn">1</a>';
                    if ($start > 2) echo '<span class="page-btn ellipsis">…</span>';
                }
                for ($i = $start; $i <= $end; $i++) {
                    $active = $i == $page ? ' active' : '';
                    echo '<a href="?page=' . $i . $qs_page . '" class="page-btn' . $active . '">' . $i . '</a>';
                }
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<span class="page-btn ellipsis">…</span>';
                    echo '<a href="?page=' . $total_pages . $qs_page . '" class="page-btn">' . $total_pages . '</a>';
                }

                echo $page < $total_pages
                    ? '<a href="?page=' . ($page+1) . $qs_page . '" class="page-btn"><i class="bx bx-chevron-right"></i></a>'
                    : '<span class="page-btn disabled"><i class="bx bx-chevron-right"></i></span>';
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Info card -->
    <div class="info-card">
        <div class="info-card-icon"><i class="bx bx-info-circle"></i></div>
        <div>
            <strong style="color:var(--text-primary);font-size:0.9375rem;">Sobre a Cobertura Geográfica</strong>
            <p style="color:var(--text-secondary);font-size:0.875rem;margin:0.35rem 0 0.75rem;line-height:1.6;">
                As províncias cadastradas aqui serão exibidas no mapa interativo do site.
                As coordenadas (latitude e longitude) determinam a localização dos marcadores no mapa.
            </p>
            <div class="info-tags">
                <span class="info-tag info"><i class="bx bx-map-pin"></i> Coordenadas únicas por província</span>
                <span class="info-tag success"><i class="bx bx-globe"></i> Usado para geolocalização</span>
                <span class="info-tag warning"><i class="bx bx-map"></i> Exibido no mapa principal</span>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Map Modal -->
<div class="map-modal-overlay" id="mapModal" onclick="closeMapModalBg(event)">
    <div class="map-modal-box">
        <div class="map-modal-header">
            <h6><i class="bx bx-map-pin"></i> <span id="mapModalTitle">—</span></h6>
            <button class="map-modal-close" onclick="closeMapModal()"><i class="bx bx-x"></i></button>
        </div>
        <div class="map-modal-preview">
            <div class="map-dot"></div>
            <div class="map-modal-pin">
                <i class="bx bx-map-pin"></i>
                <strong id="mapModalName"></strong>
                <span id="mapModalCoords"></span>
            </div>
        </div>
        <div class="map-modal-body">
            <div class="coord-row">
                <div class="coord-block">
                    <label>Latitude</label>
                    <code id="mapModalLat"></code>
                </div>
                <div class="coord-block">
                    <label>Longitude</label>
                    <code id="mapModalLng"></code>
                </div>
            </div>
        </div>
        <div class="map-modal-footer">
            <button class="btn btn-ghost" style="height:38px;" onclick="closeMapModal()">Fechar</button>
            <a id="mapGoogleLink" href="#" target="_blank" class="btn btn-primary" style="height:38px;">
                <i class="bx bx-map-alt"></i> Abrir no Google Maps
            </a>
        </div>
    </div>
</div>

<?php include('footerprincipal.php'); ?>
<div class="content-backdrop fade"></div>

<script>
// ── CONFIRM DELETE ──
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const nome = this.getAttribute('data-nome');
        if (confirm(`Remover a província "${nome}"?\n\nEsta ação não pode ser desfeita e pode afetar o mapa interativo.`)) {
            this.submit();
        }
    });
});

// ── LIVE SEARCH DEBOUNCE ──
let searchTimer;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) this.form.submit();
        }, 600);
    });
}

// ── MAP MODAL ──
function viewOnMap(name, lat, lng) {
    document.getElementById('mapModalTitle').textContent  = name;
    document.getElementById('mapModalName').textContent   = name;
    document.getElementById('mapModalCoords').textContent = lat + ', ' + lng;
    document.getElementById('mapModalLat').textContent    = lat;
    document.getElementById('mapModalLng').textContent    = lng;
    document.getElementById('mapGoogleLink').href = `https://www.google.com/maps?q=${lat},${lng}&z=10`;
    document.getElementById('mapModal').classList.add('open');
}
function closeMapModal() {
    document.getElementById('mapModal').classList.remove('open');
}
function closeMapModalBg(e) {
    if (e.target === document.getElementById('mapModal')) closeMapModal();
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeMapModal();
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        searchInput?.focus(); searchInput?.select();
    }
});
</script>

<?php
if (isset($conn) && $conn->ping()) $conn->close();
include('footer.php');
?>