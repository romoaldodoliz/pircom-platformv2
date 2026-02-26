<?php
include('header.php');
include('config/conexao.php');

// ── DEBUG: verificar se a tabela de fotos existe ──────────────────────────
$fotos_table_exists = false;
$check_table = $conn->query("SHOW TABLES LIKE 'movimentos_fotos'");
if ($check_table && $check_table->num_rows > 0) {
    $fotos_table_exists = true;
}

// Alternativa: verificar tabela com nome diferente (ex: movimento_fotos)
if (!$fotos_table_exists) {
    $check_table2 = $conn->query("SHOW TABLES LIKE 'movimento_fotos'");
    if ($check_table2 && $check_table2->num_rows > 0) {
        $fotos_table_exists = true;
        $fotos_table_name = 'movimento_fotos';
    } else {
        $fotos_table_name = 'movimentos_fotos'; // fallback padrão
    }
} else {
    $fotos_table_name = 'movimentos_fotos';
}

// ── ESTATÍSTICAS ──────────────────────────────────────────────────────────
$stats_sql = "SELECT 
    COUNT(*) as total,
    COALESCE(SUM(CASE WHEN status = 'publicado' THEN 1 ELSE 0 END), 0) as publicados,
    COALESCE(SUM(CASE WHEN status = 'rascunho' THEN 1 ELSE 0 END), 0) as rascunhos,
    COALESCE(SUM(CASE WHEN status = 'arquivado' THEN 1 ELSE 0 END), 0) as arquivados,
    COALESCE(SUM(visualizacoes), 0) as total_views
    FROM movimentos";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
$stats['total']       = intval($stats['total'] ?? 0);
$stats['publicados']  = intval($stats['publicados'] ?? 0);
$stats['rascunhos']   = intval($stats['rascunhos'] ?? 0);
$stats['arquivados']  = intval($stats['arquivados'] ?? 0);
$stats['total_views'] = intval($stats['total_views'] ?? 0);

// ── BUSCAR MOVIMENTOS COM CONTAGEM DE FOTOS ───────────────────────────────
// Usa LEFT JOIN para não perder registos sem fotos e para funcionar
// mesmo que a tabela de fotos não exista ou esteja vazia
if ($fotos_table_exists) {
    $sql = "SELECT m.*, 
            COUNT(f.id) as total_fotos 
            FROM movimentos m
            LEFT JOIN {$fotos_table_name} f ON f.movimento_id = m.id
            GROUP BY m.id
            ORDER BY m.created_at DESC";
} else {
    // Tabela de fotos não existe — retornar 0 fotos
    $sql = "SELECT m.*, 0 as total_fotos 
            FROM movimentos m
            ORDER BY m.created_at DESC";
}

$result = $conn->query($sql);
$total_movimentos = $result ? $result->num_rows : 0;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --primary: #FF6F0F;
        --primary-light: rgba(255,111,15,0.08);
        --primary-mid: rgba(255,111,15,0.15);
        --success: #16a34a;
        --success-light: rgba(22,163,74,0.08);
        --warning: #d97706;
        --warning-light: rgba(217,119,6,0.08);
        --danger: #dc2626;
        --danger-light: rgba(220,38,38,0.08);
        --info: #0891b2;
        --info-light: rgba(8,145,178,0.08);
        --secondary: #6b7280;
        --secondary-light: rgba(107,114,128,0.08);
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

    .mov-wrapper * { font-family: 'Plus Jakarta Sans', sans-serif; }

    .mov-wrapper {
        padding: 1.5rem;
        background: var(--bg);
        min-height: 100vh;
    }

    /* ── PAGE HEADER ── */
    .mov-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .mov-header-left { display: flex; align-items: center; gap: 0.75rem; }

    .mov-header-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, var(--primary), #ff8c34);
        border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.25rem; flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(255,111,15,0.35);
    }

    .mov-header h1 {
        font-size: 1.5rem; font-weight: 800;
        color: var(--text-primary); margin: 0; letter-spacing: -0.02em;
    }

    .mov-count-pill {
        display: inline-flex; align-items: center;
        background: var(--primary-light); color: var(--primary);
        font-size: 0.8125rem; font-weight: 700;
        padding: 0.25rem 0.75rem; border-radius: 999px;
        border: 1px solid var(--primary-mid);
    }

    /* ── ADD BUTTON ── */
    .btn-add {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0 1.25rem; height: 42px;
        background: linear-gradient(135deg, var(--primary), #ff8c34);
        color: white; border: none; border-radius: var(--radius-sm);
        font-size: 0.9rem; font-weight: 700; font-family: inherit;
        cursor: pointer; text-decoration: none;
        box-shadow: 0 2px 10px rgba(255,111,15,0.30);
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .btn-add:hover {
        color: white; transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(255,111,15,0.42);
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
        display: flex; align-items: center; gap: 1rem;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

    .stat-icon {
        width: 52px; height: 52px; border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
    }
    .stat-icon.primary  { background: linear-gradient(135deg,#fff0e6,#ffe0c8); color: var(--primary); }
    .stat-icon.success  { background: linear-gradient(135deg,#ecfdf5,#d1fae5); color: var(--success); }
    .stat-icon.warning  { background: linear-gradient(135deg,#fffbeb,#fef3c7); color: var(--warning); }
    .stat-icon.info     { background: linear-gradient(135deg,#ecfeff,#cffafe); color: var(--info); }

    .stat-info { min-width: 0; }
    .stat-label {
        font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.625rem; font-weight: 800; color: var(--text-primary);
        line-height: 1.1; letter-spacing: -0.02em;
    }

    /* ── MAIN CARD ── */
    .mov-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .mov-card-header {
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between;
        gap: 1rem; flex-wrap: wrap;
        border-bottom: 1px solid var(--border);
        background: #fafafa;
    }

    .mov-card-title {
        font-size: 1rem; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 0.5rem; margin: 0;
    }
    .mov-card-title i { color: var(--primary); }

    .mov-card-meta { font-size: 0.8125rem; color: var(--text-muted); font-weight: 500; }

    /* ── MOVEMENT ROW LIST ── */
    .mov-list { padding: 0.5rem; }

    .mov-row {
        display: grid;
        grid-template-columns: 90px 1fr 120px 130px 140px auto;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 0.75rem;
        border-radius: var(--radius-md);
        transition: background 0.15s;
        border-bottom: 1px solid #f3f4f6;
    }
    .mov-row:last-child { border-bottom: none; }
    .mov-row:hover { background: #fafafa; }

    .mov-row.header-row {
        font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--text-muted); padding: 0.625rem 0.75rem;
        border-bottom: 1px solid var(--border);
        background: #f9fafb; border-radius: 0;
    }

    /* ── IMAGE THUMBNAIL ── */
    .mov-thumb {
        width: 84px; height: 64px; border-radius: var(--radius-sm);
        object-fit: cover; display: block;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .mov-thumb-placeholder {
        width: 84px; height: 64px; border-radius: var(--radius-sm);
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); font-size: 1.5rem;
        border: 1px dashed var(--border);
    }

    /* ── MOVEMENT INFO ── */
    .mov-info { min-width: 0; }

    .mov-title {
        font-size: 0.9375rem; font-weight: 700;
        color: var(--text-primary); margin-bottom: 0.25rem;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .mov-meta-line {
        font-size: 0.775rem; color: var(--text-muted);
        display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
    }

    .mov-meta-line i { font-size: 0.875rem; }

    .mov-tag {
        display: inline-flex; align-items: center; gap: 0.2rem;
        background: var(--primary-light); color: var(--primary);
        padding: 0.15rem 0.5rem; border-radius: 999px;
        font-size: 0.7rem; font-weight: 700;
        border: 1px solid var(--primary-mid);
    }

    /* ── DATE / LOCAL ── */
    .mov-date, .mov-local {
        font-size: 0.8375rem; color: var(--text-secondary);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .mov-date-inner {
        display: flex; flex-direction: column; gap: 0.1rem;
    }
    .mov-date-main { font-weight: 600; color: var(--text-primary); font-size: 0.875rem; }
    .mov-date-sub  { font-size: 0.75rem; color: var(--text-muted); }

    /* ── STATUS BADGE ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        font-size: 0.75rem; font-weight: 700; white-space: nowrap;
    }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

    .status-badge.publicado  { background: var(--success-light);   color: var(--success);   border: 1px solid rgba(22,163,74,0.2); }
    .status-badge.rascunho   { background: var(--warning-light);   color: var(--warning);   border: 1px solid rgba(217,119,6,0.2); }
    .status-badge.arquivado  { background: var(--secondary-light); color: var(--secondary); border: 1px solid rgba(107,114,128,0.2); }

    .status-badge.publicado .status-dot  { background: var(--success); }
    .status-badge.rascunho .status-dot   { background: var(--warning); }
    .status-badge.arquivado .status-dot  { background: var(--secondary); }

    /* ── FOTOS BADGE ── */
    .fotos-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        background: var(--info-light); color: var(--info);
        border: 1px solid rgba(8,145,178,0.2);
        padding: 0.25rem 0.65rem; border-radius: 6px;
        font-size: 0.78rem; font-weight: 700;
    }
    .fotos-badge.zero {
        background: #f3f4f6; color: var(--text-muted);
        border-color: var(--border);
    }

    /* ── ACTIONS ── */
    .mov-actions {
        display: flex; align-items: center; gap: 0.375rem; flex-wrap: nowrap;
    }

    .btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 0.35rem; border: none; border-radius: var(--radius-sm);
        font-family: inherit; font-weight: 600; cursor: pointer;
        transition: all 0.18s ease; text-decoration: none; white-space: nowrap;
    }

    .btn-edit {
        background: var(--primary-light); color: var(--primary);
        border: 1.5px solid var(--primary-mid);
        padding: 0 0.75rem; height: 32px; font-size: 0.8rem;
    }
    .btn-edit:hover { background: var(--primary); color: white; border-color: var(--primary); }

    .btn-view {
        background: var(--success-light); color: var(--success);
        border: 1.5px solid rgba(22,163,74,0.25);
        padding: 0 0.75rem; height: 32px; font-size: 0.8rem;
    }
    .btn-view:hover { background: var(--success); color: white; border-color: var(--success); }

    .btn-delete {
        background: transparent; color: var(--text-muted);
        border: 1.5px solid var(--border);
        padding: 0 0.625rem; height: 32px; font-size: 0.875rem;
    }
    .btn-delete:hover { background: var(--danger-light); color: var(--danger); border-color: rgba(220,38,38,0.3); }

    /* ── EMPTY STATE ── */
    .empty-state {
        padding: 3.5rem 2rem; text-align: center;
    }
    .empty-state-icon {
        width: 72px; height: 72px; background: #f3f4f6; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem; font-size: 2rem; color: var(--text-muted);
    }
    .empty-state h6 { font-size: 1rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.375rem; }
    .empty-state p  { font-size: 0.875rem; color: var(--text-muted); margin: 0 0 1.5rem; }

    /* ── DEBUG NOTICE (remover em produção) ── */
    .debug-notice {
        background: #fffbeb; border: 1px solid rgba(217,119,6,0.3);
        border-radius: var(--radius-sm); padding: 0.75rem 1rem;
        font-size: 0.8125rem; color: var(--warning);
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 1rem;
    }

    /* ═══════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════ */
    @media (max-width: 1200px) {
        .mov-row {
            grid-template-columns: 80px 1fr 110px 120px 130px auto;
            gap: 0.75rem;
        }
        .mov-thumb, .mov-thumb-placeholder { width: 72px; height: 56px; }
    }

    @media (max-width: 991px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }

        /* Switch to cards */
        .mov-row.header-row { display: none; }

        .mov-row {
            grid-template-columns: 1fr;
            gap: 0;
            padding: 1rem;
            border-radius: var(--radius-md);
            background: white;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            margin-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }
        .mov-row:hover { background: white; box-shadow: var(--shadow-md); }
        .mov-list { padding: 1rem; }

        .mov-card-top {
            display: flex; gap: 0.875rem; margin-bottom: 0.875rem;
            align-items: flex-start;
        }
        .mov-thumb, .mov-thumb-placeholder { width: 80px; height: 64px; flex-shrink: 0; }

        .mov-meta-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1rem;
            margin-bottom: 0.875rem;
        }
        .mov-meta-item { display: flex; flex-direction: column; gap: 0.1rem; }
        .mov-meta-key  { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); }
        .mov-meta-val  { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }

        .mov-actions {
            border-top: 1px solid var(--border);
            padding-top: 0.875rem; flex-wrap: wrap;
        }
        .mov-actions .btn { flex: 1; min-width: 80px; height: 38px; font-size: 0.8375rem; }
    }

    @media (max-width: 767px) {
        .mov-wrapper { padding: 1rem; }
        .mov-header h1 { font-size: 1.25rem; }
        .stats-grid { gap: 0.75rem; }
        .stat-card { padding: 1rem; }
        .stat-value { font-size: 1.25rem; }
    }

    @media (max-width: 575px) {
        .mov-wrapper { padding: 0.75rem; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
        .stat-icon { width: 42px; height: 42px; font-size: 1.2rem; }
        .stat-value { font-size: 1.125rem; }
        .btn-add span { display: none; } /* mobile: só ícone no header */
    }
</style>

<div class="content-wrapper">
<div class="mov-wrapper">

    <!-- Header -->
    <div class="mov-header">
        <div class="mov-header-left">
            <div class="mov-header-icon"><i class="bx bx-news"></i></div>
            <div>
                <h1>Nossos Movimentos</h1>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <span class="mov-count-pill"><?php echo $stats['total']; ?> registros</span>
            <a href="movimentosform.php" class="btn-add">
                <i class="bx bx-plus"></i>
                <span>Adicionar Movimento</span>
            </a>
        </div>
    </div>

    <?php if (!$fotos_table_exists): ?>
    <div class="debug-notice">
        <i class="bx bx-info-circle"></i>
        <span><strong>Aviso:</strong> A tabela de fotos (<code>movimentos_fotos</code> / <code>movimento_fotos</code>) não foi encontrada. A coluna de fotos mostrará 0. Verifique o nome correto da tabela na base de dados.</span>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bx bx-news"></i></div>
            <div class="stat-info">
                <div class="stat-label">Total</div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="bx bx-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-label">Publicados</div>
                <div class="stat-value"><?php echo $stats['publicados']; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bx bx-edit"></i></div>
            <div class="stat-info">
                <div class="stat-label">Rascunhos</div>
                <div class="stat-value"><?php echo $stats['rascunhos']; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info"><i class="bx bx-show"></i></div>
            <div class="stat-info">
                <div class="stat-label">Visualizações</div>
                <div class="stat-value"><?php echo number_format($stats['total_views'], 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="mov-card">
        <div class="mov-card-header">
            <h5 class="mov-card-title"><i class="bx bx-list-ul"></i> Lista de Movimentos</h5>
            <span class="mov-card-meta"><?php echo $total_movimentos; ?> movimento<?php echo $total_movimentos != 1 ? 's' : ''; ?> encontrado<?php echo $total_movimentos != 1 ? 's' : ''; ?></span>
        </div>

        <div class="mov-list">

            <!-- Desktop header -->
            <div class="mov-row header-row">
                <div>Imagem</div>
                <div>Movimento</div>
                <div>Status / Fotos</div>
                <div>Data do Evento</div>
                <div>Local</div>
                <div>Ações</div>
            </div>

            <?php
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $status_classes = [
                        'publicado' => 'publicado',
                        'rascunho'  => 'rascunho',
                        'arquivado' => 'arquivado',
                    ];
                    $sc = $status_classes[$row['status']] ?? 'rascunho';
                    $total_fotos = intval($row['total_fotos'] ?? 0);
                    $data_evento = $row['data_evento'] ? date('d/m/Y', strtotime($row['data_evento'])) : null;
                    $local = htmlspecialchars($row['local'] ?? '');
                    $tema  = htmlspecialchars($row['tema'] ?? '');
                    $autor = htmlspecialchars($row['autor'] ?? '');
            ?>

            <div class="mov-row">

                <!-- Thumbnail -->
                <div>
                    <?php if (!empty($row['imagem_principal'])): ?>
                        <img src="../<?php echo htmlspecialchars($row['imagem_principal']); ?>"
                             class="mov-thumb"
                             alt="<?php echo htmlspecialchars($row['titulo']); ?>"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="mov-thumb-placeholder" style="display:none;"><i class="bx bx-image-alt"></i></div>
                    <?php else: ?>
                        <div class="mov-thumb-placeholder"><i class="bx bx-image-alt"></i></div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="mov-info">
                    <div class="mov-title"><?php echo htmlspecialchars($row['titulo']); ?></div>
                    <div class="mov-meta-line">
                        <?php if ($tema): ?>
                            <span class="mov-tag"><i class="bx bx-tag-alt"></i><?php echo $tema; ?></span>
                        <?php endif; ?>
                        <?php if ($autor): ?>
                            <span><i class="bx bx-user"></i> <?php echo $autor; ?></span>
                        <?php endif; ?>
                        <span style="color:#d1d5db;">·</span>
                        <span>#<?php echo $row['id']; ?></span>
                    </div>
                </div>

                <!-- Status + Fotos (combined for space) -->
                <div style="display:flex;flex-direction:column;gap:0.4rem;align-items:flex-start;">
                    <span class="status-badge <?php echo $sc; ?>">
                        <span class="status-dot"></span>
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                    <span class="fotos-badge <?php echo $total_fotos == 0 ? 'zero' : ''; ?>">
                        <i class="bx bx-photo-album"></i>
                        <?php echo $total_fotos; ?> foto<?php echo $total_fotos != 1 ? 's' : ''; ?>
                    </span>
                </div>

                <!-- Date -->
                <div class="mov-date-inner">
                    <?php if ($data_evento): ?>
                        <span class="mov-date-main"><?php echo $data_evento; ?></span>
                    <?php else: ?>
                        <span class="mov-date-sub">Sem data</span>
                    <?php endif; ?>
                    <span class="mov-date-sub">
                        Criado <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                    </span>
                </div>

                <!-- Local -->
                <div class="mov-local">
                    <?php if ($local): ?>
                        <span style="display:flex;align-items:center;gap:0.3rem;">
                            <i class="bx bx-map-pin" style="color:var(--primary);flex-shrink:0;"></i>
                            <?php echo $local; ?>
                        </span>
                    <?php else: ?>
                        <span style="color:var(--text-muted);">—</span>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="mov-actions">
                    <a href="movimentosform.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                        <i class="bx bx-edit"></i><span class="btn-label"> Editar</span>
                    </a>
                    <a href="../movimento-detalhes.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-view">
                        <i class="bx bx-show"></i><span class="btn-label"> Ver</span>
                    </a>
                    <form method="POST" action="remover_movimento.php" style="display:contents;"
                          onsubmit="return confirm('Remover este movimento e todas as suas fotos?')">
                        <input type="hidden" name="movimento_id" value="<?php echo $row['id']; ?>">
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
                <div class="empty-state-icon"><i class="bx bx-news"></i></div>
                <h6>Nenhum movimento encontrado</h6>
                <p>Comece por adicionar o primeiro movimento.</p>
                <a href="movimentosform.php" class="btn-add" style="margin:0 auto;">
                    <i class="bx bx-plus"></i> Adicionar Movimento
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>
</div>

<!-- Footer -->
<?php include('footerprincipal.php'); ?>
<div class="content-backdrop fade"></div>

<?php
include('footer.php');
$conn->close();
?>