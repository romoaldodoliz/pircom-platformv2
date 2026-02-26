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

// Verificar se usuário é manager e tenta remover - bloquear
if (isset($_GET['delete']) && isManager()) {
    $_SESSION['flash'] = ['type' => 'danger', 'text' => 'Gerenciadores não podem remover notícias. Apenas administradores podem executar esta ação.'];
    header('Location: noticias.php');
    exit;
}

// Filtros
$search     = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$dateFilter = isset($_GET['date'])   ? $conn->real_escape_string($_GET['date'])   : '';

$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " WHERE (descricao LIKE '%$search%' OR titulo LIKE '%$search%')";
}
if (!empty($dateFilter)) {
    $searchCondition .= empty($searchCondition) ? " WHERE " : " AND ";
    $searchCondition .= "DATE(data) = '$dateFilter'";
}

// Paginação
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$count_result = $conn->query("SELECT COUNT(*) as total FROM noticias" . $searchCondition);
$total_rows   = $count_result->fetch_assoc()['total'];
$total_pages  = ceil($total_rows / $limit);

$today     = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

$result = $conn->query("SELECT id, titulo, descricao, data FROM noticias $searchCondition ORDER BY data DESC LIMIT $limit OFFSET $offset");

$stats = $conn->query("SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN DATE(data) = CURDATE() THEN 1 END) as today,
    COUNT(CASE WHEN DATE(data) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 END) as yesterday
    FROM noticias")->fetch_assoc();
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
}

body, .content-wrapper * { font-family: 'Plus Jakarta Sans', sans-serif; }

.not-wrapper {
    padding: 1.5rem;
    background: var(--bg);
    min-height: 100vh;
}

/* ── HEADER ── */
.not-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.75rem;
}

.not-header-left { display: flex; align-items: center; gap: 0.75rem; }

.not-header-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, var(--primary), #1e40af);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.25rem; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(37,99,235,0.35);
}

.not-header h1 {
    font-size: 1.5rem; font-weight: 800;
    color: var(--text-primary); margin: 0; letter-spacing: -0.02em;
}

.not-count-pill {
    display: inline-flex; align-items: center;
    background: var(--primary-light); color: var(--primary);
    font-size: 0.8125rem; font-weight: 700;
    padding: 0.25rem 0.75rem; border-radius: 999px;
    border: 1px solid var(--primary-mid);
}

/* ── ALERT ── */
.not-alert {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 1.25rem; border-radius: var(--radius-md);
    margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9375rem;
    animation: fadeSlideDown 0.3s ease;
}
.not-alert.success { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.not-alert.danger  { background: var(--danger-light);  color: var(--danger);  border: 1px solid rgba(220,38,38,0.2); }

@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── STATS ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem; margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--surface); border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    display: flex; align-items: center; gap: 1rem;
    transition: box-shadow 0.2s, transform 0.2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.stat-card a { text-decoration: none; display: contents; }

.stat-icon {
    width: 52px; height: 52px; border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.stat-icon.primary { background: linear-gradient(135deg,#eff6ff,#dbeafe); color: var(--primary); }
.stat-icon.info    { background: linear-gradient(135deg,#ecfeff,#cffafe); color: var(--info); }
.stat-icon.warning { background: linear-gradient(135deg,#fffbeb,#fef3c7); color: var(--warning); }
.stat-icon.success { background: linear-gradient(135deg,#ecfdf5,#d1fae5); color: var(--success); }

.stat-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem; }
.stat-value { font-size: 1.625rem; font-weight: 800; color: var(--text-primary); line-height: 1.1; letter-spacing: -0.02em; }
.stat-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; font-weight: 500; }

/* ── FILTER ── */
.filter-card {
    background: var(--surface); border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm);
    border: 1px solid var(--border); margin-bottom: 1.25rem;
}
.filter-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.filter-title i { color: var(--primary); }
.filter-row { display: grid; grid-template-columns: 1fr 200px auto; gap: 0.75rem; align-items: end; }
.form-group  { display: flex; flex-direction: column; gap: 0.35rem; }
.form-label  { font-size: 0.8125rem; font-weight: 600; color: var(--text-secondary); }

.form-control, .form-select {
    height: 42px; padding: 0 0.875rem;
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 0.9rem; font-family: inherit; color: var(--text-primary);
    background: #fafafa; transition: border-color 0.2s, box-shadow 0.2s; width: 100%;
}
.form-control:focus, .form-select:focus {
    outline: none; border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12); background: white;
}
.filter-actions { display: flex; gap: 0.5rem; }

/* ── BUTTONS ── */
.btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 0.4rem; padding: 0 1.125rem; height: 42px;
    border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 600;
    font-family: inherit; cursor: pointer; border: none;
    transition: all 0.18s ease; white-space: nowrap; text-decoration: none;
}
.btn-primary {
    background: linear-gradient(135deg, var(--primary), #1e40af);
    color: white; box-shadow: 0 2px 8px rgba(37,99,235,0.28);
}
.btn-primary:hover { box-shadow: 0 4px 14px rgba(37,99,235,0.38); transform: translateY(-1px); color: white; }
.btn-ghost  { background: #f3f4f6; color: var(--text-secondary); border: 1.5px solid var(--border); }
.btn-ghost:hover { background: #e5e7eb; color: var(--text-primary); }
.btn-success-outline { background: var(--success-light); color: var(--success); border: 1.5px solid rgba(22,163,74,0.3); }
.btn-success-outline:hover { background: var(--success); color: white; }

.btn-icon-sm {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: var(--radius-sm);
    font-size: 0.9rem; cursor: pointer; border: none; transition: all 0.15s;
    text-decoration: none;
}
.btn-edit   { background: var(--primary-light); color: var(--primary); border: 1.5px solid rgba(37,99,235,0.2); }
.btn-edit:hover { background: var(--primary); color: white; }
.btn-delete { background: transparent; color: var(--text-muted); border: 1.5px solid var(--border); }
.btn-delete:hover { background: var(--danger-light); color: var(--danger); border-color: rgba(220,38,38,0.3); }
.btn-view   { background: var(--info-light); color: var(--info); border: 1.5px solid rgba(8,145,178,0.2); }
.btn-view:hover { background: var(--info); color: white; }

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
.table-card-title {
    font-size: 1rem; font-weight: 700; color: var(--text-primary);
    display: flex; align-items: center; gap: 0.5rem; margin: 0;
}
.table-card-title i { color: var(--primary); }
.table-meta { font-size: 0.8125rem; color: var(--text-muted); font-weight: 500; }

/* ── NEWS LIST ── */
.news-list { padding: 0.5rem; }

.news-row {
    display: grid;
    grid-template-columns: 48px 72px 1fr 130px auto;
    align-items: center; gap: 0.75rem;
    padding: 0.875rem 1rem; border-radius: var(--radius-md);
    transition: background 0.15s; border-bottom: 1px solid #f3f4f6;
}
.news-row:last-child { border-bottom: none; }
.news-row:hover { background: #fafafa; }

.news-row.header-row {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--text-muted);
    padding: 0.625rem 1rem; border-bottom: 1px solid var(--border);
    background: #f9fafb; border-radius: 0;
}

.news-id { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-align: center; }

.news-thumb {
    width: 60px; height: 60px; border-radius: var(--radius-sm);
    object-fit: cover; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid var(--border);
}
.news-thumb:hover { transform: scale(1.06); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

.news-thumb-placeholder {
    width: 60px; height: 60px; border-radius: var(--radius-sm);
    background: #f3f4f6; display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 1.5rem; border: 1px solid var(--border);
}

.news-info { min-width: 0; }
.news-title {
    font-size: 0.9375rem; font-weight: 700; color: var(--text-primary);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.25rem;
}
.news-desc {
    font-size: 0.8rem; color: var(--text-muted);
    display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
    overflow: hidden; max-width: 500px;
}

.news-date { text-align: center; }
.date-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.3rem 0.75rem; border-radius: 999px;
    font-size: 0.75rem; font-weight: 700; white-space: nowrap;
}
.date-badge.today     { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.date-badge.yesterday { background: var(--warning-light); color: var(--warning); border: 1px solid rgba(217,119,6,0.2); }
.date-badge.old       { background: #f3f4f6; color: var(--text-secondary); border: 1px solid var(--border); }

.news-time { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; }

.news-actions { display: flex; align-items: center; gap: 0.375rem; }

/* ── EMPTY STATE ── */
.empty-state { padding: 3.5rem 2rem; text-align: center; }
.empty-state-icon {
    width: 72px; height: 72px; background: #f3f4f6; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem; font-size: 2rem; color: var(--text-muted);
}
.empty-state h6 { font-size: 1rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.375rem; }
.empty-state p  { font-size: 0.875rem; color: var(--text-muted); margin: 0; }

/* ── PAGINATION ── */
.pagination-wrap {
    padding: 1rem 1.5rem; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    gap: 0.375rem; flex-wrap: wrap;
}
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

/* ── MODAL ── */
.img-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.7); z-index: 9999;
    align-items: center; justify-content: center; padding: 1rem;
}
.img-modal-overlay.open { display: flex; }
.img-modal-box {
    background: white; border-radius: var(--radius-lg);
    max-width: 640px; width: 100%; overflow: hidden; position: relative;
}
.img-modal-header {
    padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.img-modal-header h6 { margin: 0; font-weight: 700; }
.img-modal-body { padding: 1.25rem; text-align: center; }
.img-modal-body img { max-width: 100%; border-radius: var(--radius-md); }
.img-modal-close {
    background: none; border: none; cursor: pointer; font-size: 1.25rem;
    color: var(--text-muted); transition: color 0.15s; line-height: 1;
}
.img-modal-close:hover { color: var(--danger); }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 1200px) {
    .news-row { grid-template-columns: 44px 64px 1fr 120px auto; gap: 0.5rem; }
}
@media (max-width: 991px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .filter-row { grid-template-columns: 1fr 1fr; }
    .filter-actions { grid-column: 1 / -1; }
    .news-row.header-row { display: none; }
    .news-row {
        grid-template-columns: 64px 1fr;
        grid-template-rows: auto auto auto;
        gap: 0.5rem; padding: 1rem;
        border-bottom: none; border-radius: var(--radius-md);
        background: white; box-shadow: var(--shadow-sm);
        border: 1px solid var(--border); margin-bottom: 0.75rem;
    }
    .news-row:hover { background: white; box-shadow: var(--shadow-md); }
    .news-list { padding: 1rem; display: flex; flex-direction: column; }
    .news-id   { display: none; }
    .news-thumb, .news-thumb-placeholder { grid-row: 1 / 3; }
    .news-info { grid-column: 2; }
    .news-date { grid-column: 2; text-align: left; }
    .news-actions { grid-column: 1 / -1; border-top: 1px solid var(--border); padding-top: 0.75rem; margin-top: 0.25rem; }
}
@media (max-width: 767px) {
    .not-wrapper { padding: 1rem; }
    .not-header h1 { font-size: 1.25rem; }
    .stats-grid { gap: 0.75rem; }
    .stat-card { padding: 1rem; }
    .stat-value { font-size: 1.25rem; }
    .filter-row { grid-template-columns: 1fr; }
}
@media (max-width: 575px) {
    .not-wrapper { padding: 0.75rem; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
    .stat-icon { width: 42px; height: 42px; font-size: 1.25rem; }
}
</style>

<div class="content-wrapper">
<div class="not-wrapper">

    <!-- Header -->
    <div class="not-header">
        <div class="not-header-left">
            <div class="not-header-icon"><i class="bx bx-news"></i></div>
            <div><h1>Gestão de Notícias</h1></div>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <span class="not-count-pill"><?php echo number_format($stats['total'], 0); ?> notícias</span>
            <a href="noticiasform.php" class="btn btn-primary" style="height:38px;">
                <i class="bx bx-plus"></i> Nova Notícia
            </a>
        </div>
    </div>

    <!-- Alert -->
    <?php if ($message): ?>
    <div class="not-alert <?php echo $message['type']; ?>">
        <i class="bx <?php echo $message['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($message['text']); ?></span>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bx bx-news"></i></div>
            <div>
                <div class="stat-label">Total</div>
                <div class="stat-value"><?php echo number_format($stats['total'], 0); ?></div>
                <div class="stat-sub">notícias</div>
            </div>
        </div>
        <div class="stat-card" style="cursor:pointer;" onclick="window.location='?date=<?php echo $today; ?>'">
            <div class="stat-icon info"><i class="bx bx-calendar-check"></i></div>
            <div>
                <div class="stat-label">Hoje</div>
                <div class="stat-value"><?php echo $stats['today']; ?></div>
                <div class="stat-sub">publicadas</div>
            </div>
        </div>
        <div class="stat-card" style="cursor:pointer;" onclick="window.location='?date=<?php echo $yesterday; ?>'">
            <div class="stat-icon warning"><i class="bx bx-time-five"></i></div>
            <div>
                <div class="stat-label">Ontem</div>
                <div class="stat-value"><?php echo $stats['yesterday']; ?></div>
                <div class="stat-sub">publicadas</div>
            </div>
        </div>
        <div class="stat-card" style="cursor:pointer;" onclick="window.location='noticiasform.php'">
            <div class="stat-icon success"><i class="bx bx-plus-circle"></i></div>
            <div>
                <div class="stat-label">Adicionar</div>
                <div class="stat-value" style="font-size:1rem;margin-top:0.15rem;">Nova</div>
                <div class="stat-sub">criar notícia</div>
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
                    <input type="text" name="search" class="form-control" placeholder="Título ou descrição..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Filtrar por data</label>
                    <input type="date" name="date" class="form-control"
                           value="<?php echo htmlspecialchars($dateFilter); ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search-alt"></i> Filtrar</button>
                    <a href="noticias.php" class="btn btn-ghost"><i class="bx bx-reset"></i> Limpar</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5 class="table-card-title">
                <i class="bx bx-list-ul"></i> Notícias Registradas
                <?php if (!empty($search) || !empty($dateFilter)): ?>
                <span style="font-size:0.8rem;font-weight:500;color:var(--text-muted);">
                    — filtradas
                </span>
                <?php endif; ?>
            </h5>
            <span class="table-meta">
                Mostrando <?php echo min($offset + 1, $total_rows); ?>–<?php echo min($offset + $limit, $total_rows); ?> de <?php echo $total_rows; ?>
            </span>
        </div>

        <div class="news-list">
            <!-- Desktop header -->
            <div class="news-row header-row">
                <div>#</div>
                <div>Imagem</div>
                <div>Notícia</div>
                <div style="text-align:center;">Data</div>
                <div>Ações</div>
            </div>

            <?php if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $fDate = date('d/m/Y', strtotime($row['data']));
                    $fTime = date('H:i',   strtotime($row['data']));
                    $rDate = date('Y-m-d', strtotime($row['data']));
                    $isToday     = $rDate == $today;
                    $isYesterday = $rDate == $yesterday;
                    $dateClass   = $isToday ? 'today' : ($isYesterday ? 'yesterday' : 'old');
                    $dateLabel   = $isToday ? 'Hoje' : ($isYesterday ? 'Ontem' : $fDate);
            ?>
            <div class="news-row">
                <div class="news-id">#<?php echo $row['id']; ?></div>

                <!-- Thumb: carregada via rota separada para não pesar a lista -->
                <div>
                    <img class="news-thumb"
                         src="get_news_image.php?id=<?php echo $row['id']; ?>"
                         alt="<?php echo htmlspecialchars($row['titulo']); ?>"
                         loading="lazy"
                         onclick="openImgModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['titulo'])); ?>')"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="news-thumb-placeholder" style="display:none;"><i class="bx bx-image"></i></div>
                </div>

                <div class="news-info">
                    <div class="news-title"><?php echo htmlspecialchars($row['titulo']); ?></div>
                    <div class="news-desc"><?php echo htmlspecialchars($row['descricao']); ?></div>
                </div>

                <div class="news-date">
                    <span class="date-badge <?php echo $dateClass; ?>"><?php echo $dateLabel; ?></span>
                    <div class="news-time"><?php echo $fTime; ?></div>
                </div>

                <div class="news-actions">
                    <a href="noticiasform.php?edit=<?php echo $row['id']; ?>"
                       class="btn-icon-sm btn-edit" title="Editar">
                        <i class="bx bx-edit"></i>
                    </a>

                    <button type="button"
                            class="btn-icon-sm btn-view"
                            title="Pré-visualizar"
                            onclick="openImgModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['titulo'])); ?>')">
                        <i class="bx bx-show"></i>
                    </button>

                    <?php if (isAdmin()): ?>
                    <form method="POST" action="remover_noticia.php" class="delete-form" style="display:contents;"
                          data-item-name="<?php echo htmlspecialchars(addslashes($row['titulo'])); ?>">
                        <input type="hidden" name="noticia_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn-icon-sm btn-delete" title="Remover">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                    <?php else: ?>
                    <button type="button" class="btn-icon-sm btn-delete" disabled title="Apenas admins podem remover" style="opacity:0.4;cursor:not-allowed;">
                        <i class="bx bx-trash"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile;
            else: ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bx bx-news"></i></div>
                <h6><?php echo (!empty($search) || !empty($dateFilter)) ? 'Nenhuma notícia encontrada' : 'Nenhuma notícia cadastrada'; ?></h6>
                <p><?php echo (!empty($search) || !empty($dateFilter)) ? 'Tente ajustar os filtros.' : 'Comece criando sua primeira notícia.'; ?></p>
                <?php if (empty($search) && empty($dateFilter)): ?>
                <a href="noticiasform.php" class="btn btn-primary" style="height:38px;margin-top:1rem;">
                    <i class="bx bx-plus"></i> Adicionar notícia
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-wrap">
            <?php
            $qs = '';
            if (!empty($search))     $qs .= '&search=' . urlencode($search);
            if (!empty($dateFilter)) $qs .= '&date='   . urlencode($dateFilter);

            echo $page > 1
                ? '<a href="?page=' . ($page-1) . $qs . '" class="page-btn"><i class="bx bx-chevron-left"></i></a>'
                : '<span class="page-btn disabled"><i class="bx bx-chevron-left"></i></span>';

            $start = max(1, $page - 2);
            $end   = min($total_pages, $page + 2);

            if ($start > 1) {
                echo '<a href="?page=1' . $qs . '" class="page-btn">1</a>';
                if ($start > 2) echo '<span class="page-btn ellipsis">…</span>';
            }
            for ($i = $start; $i <= $end; $i++) {
                $active = $i == $page ? ' active' : '';
                echo '<a href="?page=' . $i . $qs . '" class="page-btn' . $active . '">' . $i . '</a>';
            }
            if ($end < $total_pages) {
                if ($end < $total_pages - 1) echo '<span class="page-btn ellipsis">…</span>';
                echo '<a href="?page=' . $total_pages . $qs . '" class="page-btn">' . $total_pages . '</a>';
            }

            echo $page < $total_pages
                ? '<a href="?page=' . ($page+1) . $qs . '" class="page-btn"><i class="bx bx-chevron-right"></i></a>'
                : '<span class="page-btn disabled"><i class="bx bx-chevron-right"></i></span>';
            ?>
        </div>
        <?php endif; ?>
    </div>

</div>
</div>

<!-- Image preview modal -->
<div class="img-modal-overlay" id="imgModal" onclick="closeImgModal(event)">
    <div class="img-modal-box">
        <div class="img-modal-header">
            <h6 id="imgModalTitle">Visualização</h6>
            <button class="img-modal-close" onclick="closeImgModalDirect()"><i class="bx bx-x"></i></button>
        </div>
        <div class="img-modal-body">
            <img id="imgModalSrc" src="" alt="Preview">
        </div>
    </div>
</div>

<?php include('footerprincipal.php'); ?>
<div class="content-backdrop fade"></div>

<script>
// Confirm delete
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = this.getAttribute('data-item-name');
        const short = name.length > 60 ? name.substring(0, 60) + '...' : name;
        if (confirm(`Tem a certeza que deseja remover:\n"${short}"?\n\nEsta ação não pode ser desfeita.`)) {
            this.submit();
        }
    });
});

// Image modal
function openImgModal(id, title) {
    document.getElementById('imgModalTitle').textContent = title;
    document.getElementById('imgModalSrc').src = 'get_news_image.php?id=' + id;
    document.getElementById('imgModal').classList.add('open');
}
function closeImgModal(e) {
    if (e.target === document.getElementById('imgModal')) {
        document.getElementById('imgModal').classList.remove('open');
    }
}
function closeImgModalDirect() {
    document.getElementById('imgModal').classList.remove('open');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('imgModal').classList.remove('open');
});

// Auto-submit date
document.querySelector('input[name="date"]')?.addEventListener('change', function() {
    if (this.value) this.form.submit();
});
</script>

<?php
if (isset($conn) && $conn->ping()) $conn->close();
include('footer.php');
?>