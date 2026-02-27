<?php
session_start();
include('header.php');
include('config/conexao.php');

// Flash message
$message = null;
if (!empty($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// ── SORTING ──
$sort      = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order     = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'desc' : 'asc';
$validSort = ['id', 'descricao'];
$sort      = in_array($sort, $validSort) ? $sort : 'id';

// ── SEARCH ──
$search          = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
if (!empty($search)) {
    $s               = $conn->real_escape_string($search);
    $searchCondition = " WHERE descricao LIKE '%$s%'";
}

// ── COUNT ──
$countResult  = $conn->query("SELECT COUNT(*) as total FROM homepagehero" . $searchCondition);
$totalRecords = (int)$countResult->fetch_assoc()['total'];

// ── PAGINATION ──
$recordsPerPage = 10;
$totalPages     = max(1, ceil($totalRecords / $recordsPerPage));
$currentPage    = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage    = max(1, min($currentPage, $totalPages));
$offset         = ($currentPage - 1) * $recordsPerPage;

// ── QUERY ──
$sql    = "SELECT id, descricao, data FROM homepagehero $searchCondition ORDER BY $sort $order LIMIT $offset, $recordsPerPage";
$result = $conn->query($sql);
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

.hero-wrapper {
    padding: 1.5rem;
    background: var(--bg);
    min-height: 100vh;
}

/* ── PAGE HEADER ── */
.hero-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;
}
.hero-header-left { display: flex; align-items: center; gap: 0.75rem; }
.hero-header-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, var(--primary), #1e40af);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.25rem; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(37,99,235,0.35);
}
.hero-header h1 {
    font-size: 1.5rem; font-weight: 800;
    color: var(--text-primary); margin: 0; letter-spacing: -0.02em;
}
.hero-header p { font-size: 0.85rem; color: var(--text-muted); margin: 0; }
.hero-count-pill {
    display: inline-flex; align-items: center;
    background: var(--primary-light); color: var(--primary);
    font-size: 0.8125rem; font-weight: 700;
    padding: 0.25rem 0.75rem; border-radius: 999px;
    border: 1px solid var(--primary-mid);
}

/* ── ALERT ── */
.hero-alert {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 1.25rem; border-radius: var(--radius-md);
    margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9375rem;
    animation: fadeSlideDown 0.3s ease;
}
.hero-alert.success { background: var(--success-light); color: var(--success); border: 1px solid rgba(22,163,74,0.2); }
.hero-alert.danger  { background: var(--danger-light);  color: var(--danger);  border: 1px solid rgba(220,38,38,0.2); }
@keyframes fadeSlideDown {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── STATS ── */
.stats-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
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
.stat-icon {
    width: 52px; height: 52px; border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; flex-shrink: 0;
}
.stat-icon.primary { background: linear-gradient(135deg,#eff6ff,#dbeafe); color: var(--primary); }
.stat-icon.success { background: linear-gradient(135deg,#ecfdf5,#d1fae5); color: var(--success); }
.stat-icon.warning { background: linear-gradient(135deg,#fffbeb,#fef3c7); color: var(--warning); }
.stat-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem; }
.stat-value { font-size: 1.625rem; font-weight: 800; color: var(--text-primary); line-height: 1.1; letter-spacing: -0.02em; }
.stat-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; }

/* ── FILTER CARD ── */
.filter-card {
    background: var(--surface); border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm);
    border: 1px solid var(--border); margin-bottom: 1.25rem;
}
.filter-title { font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.filter-title i { color: var(--primary); }
.filter-row { display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; align-items: end; }
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
.btn-ghost { background: #f3f4f6; color: var(--text-secondary); border: 1.5px solid var(--border); }
.btn-ghost:hover { background: #e5e7eb; color: var(--text-primary); }

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

/* ── SORT LINK ── */
.sort-link {
    display: inline-flex; align-items: center; gap: 0.3rem;
    color: var(--text-muted); text-decoration: none;
    font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    transition: color 0.15s;
}
.sort-link:hover { color: var(--primary); }
.sort-link.active { color: var(--primary); }

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

/* ── HERO LIST ── */
.hero-list { padding: 0.5rem; }
.hero-row {
    display: grid;
    grid-template-columns: 48px 90px 1fr 130px auto;
    align-items: center; gap: 0.75rem;
    padding: 0.875rem 1rem; border-radius: var(--radius-md);
    transition: background 0.15s; border-bottom: 1px solid #f3f4f6;
}
.hero-row:last-child { border-bottom: none; }
.hero-row:hover { background: #fafafa; }
.hero-row.header-row {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--text-muted);
    padding: 0.625rem 1rem; border-bottom: 1px solid var(--border);
    background: #f9fafb; border-radius: 0;
}

.item-id { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-align: center; }

.item-thumb {
    width: 72px; height: 56px; border-radius: var(--radius-sm);
    object-fit: cover; cursor: pointer; display: block;
    border: 1px solid var(--border);
    transition: transform 0.2s, box-shadow 0.2s;
}
.item-thumb:hover { transform: scale(1.05); box-shadow: var(--shadow-md); }
.item-thumb-placeholder {
    width: 72px; height: 56px; border-radius: var(--radius-sm);
    background: #f3f4f6; display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 1.25rem; border: 1px solid var(--border);
}

.item-info { min-width: 0; }
.item-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.2rem; }
.item-desc {
    font-size: 0.875rem; color: var(--text-secondary);
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.item-desc.empty { color: var(--text-muted); font-style: italic; }

.item-date {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: var(--primary-light); color: var(--primary);
    border: 1px solid rgba(37,99,235,0.15);
    padding: 0.25rem 0.65rem; border-radius: 6px;
    font-size: 0.75rem; font-weight: 700; white-space: nowrap;
}

.item-actions { display: flex; align-items: center; gap: 0.375rem; }

/* ── EMPTY STATE ── */
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
.pagination-btns { display: flex; align-items: center; gap: 0.375rem; }
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

/* ── IMG MODAL ── */
.img-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.75); z-index: 9999;
    align-items: center; justify-content: center; padding: 1rem;
    backdrop-filter: blur(4px);
}
.img-modal-overlay.open { display: flex; }
.img-modal-box {
    background: white; border-radius: var(--radius-lg);
    max-width: 700px; width: 100%; overflow: hidden;
}
.img-modal-header {
    padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.img-modal-header h6 { margin: 0; font-weight: 700; font-size: 0.9375rem; }
.img-modal-body { padding: 1.25rem; text-align: center; background: #f9fafb; }
.img-modal-body img { max-width: 100%; border-radius: var(--radius-md); box-shadow: var(--shadow-md); }
.img-modal-footer { padding: 0.875rem 1.25rem; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.img-modal-footer small { color: var(--text-muted); font-weight: 500; }
.img-modal-close {
    background: none; border: none; cursor: pointer; font-size: 1.125rem;
    color: var(--text-muted); transition: color 0.15s; line-height: 1; padding: 0;
}
.img-modal-close:hover { color: var(--danger); }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 991px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .hero-row.header-row { display: none; }
    .hero-row {
        grid-template-columns: 90px 1fr;
        grid-template-rows: auto auto auto;
        gap: 0.5rem 0.75rem;
        padding: 1rem;
        border-bottom: none; border-radius: var(--radius-md);
        background: white; box-shadow: var(--shadow-sm);
        border: 1px solid var(--border); margin-bottom: 0.75rem;
    }
    .hero-row:hover { background: white; box-shadow: var(--shadow-md); }
    .hero-list { padding: 1rem; display: flex; flex-direction: column; }
    .item-id   { display: none; }
    .item-thumb, .item-thumb-placeholder { grid-row: 1 / 3; width: 80px; height: 64px; }
    .item-info { grid-column: 2; }
    .item-date { grid-column: 2; }
    .item-actions { grid-column: 1 / -1; border-top: 1px solid var(--border); padding-top: 0.75rem; }
    .filter-row { grid-template-columns: 1fr; }
}
@media (max-width: 767px) {
    .hero-wrapper { padding: 1rem; }
    .hero-header h1 { font-size: 1.25rem; }
    .stats-grid { gap: 0.75rem; }
    .stat-card { padding: 1rem; }
    .stat-value { font-size: 1.25rem; }
    .pagination-wrap { justify-content: center; }
    .pagination-info { text-align: center; width: 100%; }
}
@media (max-width: 575px) {
    .hero-wrapper { padding: 0.75rem; }
    .stats-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
}
</style>

<div class="content-wrapper">
<div class="hero-wrapper">

    <!-- Header -->
    <div class="hero-header">
        <div class="hero-header-left">
            <div class="hero-header-icon"><i class="bx bx-images"></i></div>
            <div>
                <h1>Hero da Página Inicial</h1>
                <p>Gerencie os slides e imagens principais</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <span class="hero-count-pill"><?php echo number_format($totalRecords); ?> registos</span>
            <a href="homepageheroform.php" class="btn btn-primary" style="height:38px;">
                <i class="bx bx-plus"></i> Adicionar Novo
            </a>
        </div>
    </div>

    <!-- Flash Alert -->
    <?php if ($message): ?>
    <div class="hero-alert <?php echo $message['type']; ?>">
        <i class="bx <?php echo $message['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>" style="font-size:1.25rem;flex-shrink:0;"></i>
        <span><?php echo htmlspecialchars($message['text']); ?></span>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bx bx-images"></i></div>
            <div>
                <div class="stat-label">Total de Slides</div>
                <div class="stat-value"><?php echo number_format($totalRecords); ?></div>
                <div class="stat-sub">hero sections</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="bx bx-check-shield"></i></div>
            <div>
                <div class="stat-label">Ativos</div>
                <div class="stat-value"><?php echo number_format($totalRecords); ?></div>
                <div class="stat-sub">todos ativos</div>
            </div>
        </div>
        <div class="stat-card" style="cursor:pointer;" onclick="window.location='homepageheroform.php'">
            <div class="stat-icon warning"><i class="bx bx-plus-circle"></i></div>
            <div>
                <div class="stat-label">Adicionar</div>
                <div class="stat-value" style="font-size:1rem;margin-top:0.15rem;">Novo Slide</div>
                <div class="stat-sub">criar registo</div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <div class="filter-title"><i class="bx bx-filter-alt"></i> Pesquisa</div>
        <form method="GET" action="">
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label">Pesquisar por descrição</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Digite para pesquisar..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search-alt"></i> Pesquisar</button>
                    <?php if (!empty($search)): ?>
                    <a href="homepagehero.php" class="btn btn-ghost"><i class="bx bx-reset"></i> Limpar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-card-header">
            <h5 class="table-card-title">
                <i class="bx bx-list-ul"></i> Lista de Hero Sections
                <?php if (!empty($search)): ?>
                <span style="font-size:0.8rem;font-weight:500;color:var(--text-muted);">
                    — "<?php echo htmlspecialchars($search); ?>"
                </span>
                <?php endif; ?>
            </h5>
            <span class="table-meta">
                <?php if ($totalRecords > 0): ?>
                Mostrando <?php echo $offset + 1; ?>–<?php echo min($offset + $recordsPerPage, $totalRecords); ?> de <?php echo $totalRecords; ?>
                <?php else: ?>
                Nenhum registo
                <?php endif; ?>
            </span>
        </div>

        <div class="hero-list">
            <!-- Desktop header -->
            <div class="hero-row header-row">
                <div>
                    <a class="sort-link <?php echo $sort == 'id' ? 'active' : ''; ?>"
                       href="?sort=id&order=<?php echo ($sort == 'id' && $order == 'asc') ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $currentPage; ?>">
                        # <i class="bx bx-chevron-<?php echo ($sort == 'id' && $order == 'asc') ? 'up' : 'down'; ?>"></i>
                    </a>
                </div>
                <div>Imagem</div>
                <div>
                    <a class="sort-link <?php echo $sort == 'descricao' ? 'active' : ''; ?>"
                       href="?sort=descricao&order=<?php echo ($sort == 'descricao' && $order == 'asc') ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $currentPage; ?>">
                        Descrição <i class="bx bx-chevron-<?php echo ($sort == 'descricao' && $order == 'asc') ? 'up' : 'down'; ?>"></i>
                    </a>
                </div>
                <div>Data</div>
                <div>Ações</div>
            </div>

            <?php if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $desc        = htmlspecialchars($row['descricao'] ?? '');
                    $dataFormatada = !empty($row['data']) ? date('d/m/Y', strtotime($row['data'])) : '—';
            ?>
            <div class="hero-row">
                <!-- ID -->
                <div class="item-id">#<?php echo $row['id']; ?></div>

                <!-- Thumb -->
                <div>
                    <img class="item-thumb"
                         src="get_hero_image.php?id=<?php echo $row['id']; ?>"
                         alt="Slide <?php echo $row['id']; ?>"
                         loading="lazy"
                         onclick="openImgModal(<?php echo $row['id']; ?>, '<?php echo addslashes($desc ?: 'Slide #' . $row['id']); ?>')"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="item-thumb-placeholder" style="display:none;"><i class="bx bx-image"></i></div>
                </div>

                <!-- Descrição -->
                <div class="item-info">
                    <div class="item-label">Descrição</div>
                    <?php if (!empty($desc)): ?>
                    <div class="item-desc"><?php echo $desc; ?></div>
                    <?php else: ?>
                    <div class="item-desc empty">Sem descrição</div>
                    <?php endif; ?>
                </div>

                <!-- Data -->
                <div>
                    <span class="item-date">
                        <i class="bx bx-calendar" style="font-size:0.8rem;"></i>
                        <?php echo $dataFormatada; ?>
                    </span>
                </div>

                <!-- Ações -->
                <div class="item-actions">
                    <a href="homepageheroform.php?edit=<?php echo $row['id']; ?>"
                       class="btn-icon-sm btn-edit" title="Editar">
                        <i class="bx bx-edit"></i>
                    </a>
                    <form method="POST" action="remover_homepagehero.php"
                          class="delete-form" style="display:contents;"
                          data-item-id="<?php echo $row['id']; ?>">
                        <input type="hidden" name="homepagehero_id" value="<?php echo $row['id']; ?>">
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
                <div class="empty-state-icon"><i class="bx bx-images"></i></div>
                <h6><?php echo !empty($search) ? 'Nenhum resultado encontrado' : 'Nenhum slide cadastrado'; ?></h6>
                <p><?php echo !empty($search) ? 'Tente ajustar a pesquisa.' : 'Adicione o primeiro slide da página inicial.'; ?></p>
                <?php if (empty($search)): ?>
                <a href="homepageheroform.php" class="btn btn-primary" style="height:38px;">
                    <i class="bx bx-plus"></i> Adicionar primeiro slide
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-wrap">
            <span class="pagination-info">
                Mostrando <?php echo $offset + 1; ?>–<?php echo min($offset + $recordsPerPage, $totalRecords); ?> de <?php echo $totalRecords; ?> registos
            </span>
            <div class="pagination-btns">
                <?php
                $qs = '';
                if (!empty($search)) $qs .= '&search=' . urlencode($search);
                $qs .= '&sort=' . $sort . '&order=' . $order;

                echo $currentPage > 1
                    ? '<a href="?page=' . ($currentPage-1) . $qs . '" class="page-btn"><i class="bx bx-chevron-left"></i></a>'
                    : '<span class="page-btn disabled"><i class="bx bx-chevron-left"></i></span>';

                $start = max(1, $currentPage - 2);
                $end   = min($totalPages, $currentPage + 2);

                if ($start > 1) {
                    echo '<a href="?page=1' . $qs . '" class="page-btn">1</a>';
                    if ($start > 2) echo '<span class="page-btn ellipsis">…</span>';
                }
                for ($i = $start; $i <= $end; $i++) {
                    $active = $i == $currentPage ? ' active' : '';
                    echo '<a href="?page=' . $i . $qs . '" class="page-btn' . $active . '">' . $i . '</a>';
                }
                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) echo '<span class="page-btn ellipsis">…</span>';
                    echo '<a href="?page=' . $totalPages . $qs . '" class="page-btn">' . $totalPages . '</a>';
                }

                echo $currentPage < $totalPages
                    ? '<a href="?page=' . ($currentPage+1) . $qs . '" class="page-btn"><i class="bx bx-chevron-right"></i></a>'
                    : '<span class="page-btn disabled"><i class="bx bx-chevron-right"></i></span>';
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
</div>

<!-- Image preview modal -->
<div class="img-modal-overlay" id="imgModal" onclick="closeImgModalBg(event)">
    <div class="img-modal-box">
        <div class="img-modal-header">
            <h6 id="imgModalTitle">Visualização</h6>
            <button class="img-modal-close" onclick="closeImgModal()"><i class="bx bx-x"></i></button>
        </div>
        <div class="img-modal-body">
            <img id="imgModalSrc" src="" alt="Preview">
        </div>
        <div class="img-modal-footer">
            <small id="imgModalId"></small>
            <a id="imgModalEdit" href="#" class="btn btn-edit btn-icon-sm" title="Editar">
                <i class="bx bx-edit"></i>
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
        const id = this.getAttribute('data-item-id');
        if (confirm(`Remover o slide #${id}?\n\nEsta ação não pode ser desfeita.`)) {
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
        searchTimer = setTimeout(() => this.form.submit(), 500);
    });
}

// ── IMAGE MODAL ──
function openImgModal(id, title) {
    document.getElementById('imgModalTitle').textContent = title || 'Slide #' + id;
    document.getElementById('imgModalId').textContent    = 'ID: #' + id;
    document.getElementById('imgModalSrc').src           = 'get_hero_image.php?id=' + id;
    document.getElementById('imgModalEdit').href         = 'homepageheroform.php?edit=' + id;
    document.getElementById('imgModal').classList.add('open');
}
function closeImgModal() {
    document.getElementById('imgModal').classList.remove('open');
}
function closeImgModalBg(e) {
    if (e.target === document.getElementById('imgModal')) closeImgModal();
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeImgModal();
});
</script>

<?php
$conn->close();
include('footer.php');
?>