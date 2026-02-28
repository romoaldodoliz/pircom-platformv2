<?php
$page_title = "Documentos - Pircom";
include 'config/conexao.php';

// Incrementar download se houver
if (isset($_GET['download']) && isset($_GET['id'])) {
    $doc_id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE documentos SET downloads = downloads + 1 WHERE id = ?");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $stmt->close();
}

// Buscar documentos publicados
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

if ($categoria_filtro) {
    $stmt = $conn->prepare("SELECT * FROM documentos WHERE status = 'publicado' AND categoria = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $categoria_filtro);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query("SELECT * FROM documentos WHERE status = 'publicado' ORDER BY created_at DESC");
}

// Buscar categorias disponíveis
$categorias = $conn->query("SELECT DISTINCT categoria FROM documentos WHERE status = 'publicado' ORDER BY categoria");

include 'includes/navbar.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --p-orange:      #FF6F0F;
    --p-orange-dark: #D95A00;
    --p-orange-glow: rgba(255,111,15,0.18);
    --p-black:       #111111;
    --p-white:       #FFFFFF;
    --p-offwhite:    #F5F5F5;
    --p-grey:        #5A5A5A;
    --p-light:       rgba(0,0,0,0.06);
}

/* ── PAGE SHELL ── */
.docs-page {
    background: var(--p-offwhite);
    min-height: 100vh;
    padding: 110px 0 80px;
    font-family: 'Source Sans 3', sans-serif;
}

/* ── HERO HEADER ── */
.docs-hero {
    background: var(--p-black);
    padding: 70px 0 60px;
    position: relative;
    overflow: hidden;
    margin-bottom: 0;
}

.docs-hero::before {
    content: '';
    position: absolute;
    top: -100px; right: -100px;
    width: 500px; height: 500px;
    border: 90px solid var(--p-orange);
    border-radius: 50%;
    opacity: 0.06;
    pointer-events: none;
}

.docs-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -60px;
    width: 300px; height: 300px;
    border: 60px solid var(--p-orange);
    border-radius: 50%;
    opacity: 0.04;
    pointer-events: none;
}

.docs-hero-inner {
    position: relative;
    z-index: 2;
    text-align: center;
}

.docs-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--p-orange);
    color: var(--p-white);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    padding: 6px 18px;
    border-radius: 50px;
    margin-bottom: 22px;
}

.docs-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 5vw, 3.6rem);
    font-weight: 900;
    color: var(--p-white);
    margin-bottom: 16px;
    line-height: 1.1;
}

.docs-hero h1 span {
    color: var(--p-orange);
}

.docs-hero p {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.55);
    max-width: 560px;
    margin: 0 auto 36px;
    line-height: 1.7;
}

/* stats strip */
.docs-stats {
    display: flex;
    justify-content: center;
    gap: 0;
    flex-wrap: wrap;
    border-top: 1px solid rgba(255,255,255,0.08);
    padding-top: 36px;
    margin-top: 10px;
}

.docs-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 36px;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.docs-stat:last-child { border-right: none; }

.docs-stat-icon {
    width: 44px; height: 44px;
    background: var(--p-orange-glow);
    border: 1.5px solid var(--p-orange);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    color: var(--p-orange);
    margin-bottom: 10px;
}

.docs-stat-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-weight: 600;
}

/* ── FILTER BAR ── */
.docs-filter-wrap {
    background: var(--p-white);
    border-bottom: 1px solid rgba(0,0,0,0.07);
    padding: 20px 0;
    position: sticky;
    top: 70px;
    z-index: 100;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.docs-filter {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 22px;
    border: 1.5px solid rgba(0,0,0,0.15);
    background: transparent;
    border-radius: 50px;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--p-grey);
    text-decoration: none;
    transition: all 0.25s ease;
    cursor: pointer;
    white-space: nowrap;
}

.filter-btn:hover {
    border-color: var(--p-orange);
    color: var(--p-orange);
    background: rgba(255,111,15,0.05);
}

.filter-btn.active {
    background: var(--p-orange);
    border-color: var(--p-orange);
    color: var(--p-white);
    box-shadow: 0 4px 14px var(--p-orange-glow);
}

/* ── CONTENT SECTION ── */
.docs-content {
    padding: 56px 0 80px;
}

/* ── DOCUMENT CARDS GRID ── */
.docs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 28px;
}

.doc-card {
    background: var(--p-white);
    border-radius: 16px;
    overflow: hidden;
    border: 1.5px solid rgba(0,0,0,0.07);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.32s cubic-bezier(.34,1.56,.64,1),
                box-shadow 0.3s ease,
                border-color 0.25s ease;
    display: flex;
    flex-direction: column;
    animation: fadeUp 0.55s ease both;
}

.doc-card:nth-child(1) { animation-delay: 0.06s; }
.doc-card:nth-child(2) { animation-delay: 0.14s; }
.doc-card:nth-child(3) { animation-delay: 0.22s; }
.doc-card:nth-child(4) { animation-delay: 0.30s; }
.doc-card:nth-child(5) { animation-delay: 0.38s; }
.doc-card:nth-child(6) { animation-delay: 0.46s; }

.doc-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 48px rgba(255,111,15,0.13);
    border-color: var(--p-orange);
}

/* card top — icon area */
.doc-card-top {
    background: var(--p-black);
    padding: 36px 28px 28px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

.doc-card-top::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: var(--p-orange);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s ease;
}

.doc-card:hover .doc-card-top::after { transform: scaleX(1); }

.doc-pdf-icon {
    width: 54px; height: 60px;
    background: var(--p-orange);
    border-radius: 8px 8px 8px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    flex-shrink: 0;
}

.doc-pdf-icon::before {
    content: '';
    position: absolute;
    top: 0; right: -12px;
    width: 0; height: 0;
    border-left: 12px solid var(--p-orange-dark);
    border-top: 12px solid transparent;
}

.doc-pdf-icon i {
    font-size: 1.5rem;
    color: var(--p-white);
}

.doc-cat-badge {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 50px;
    border: 1px solid rgba(255,255,255,0.12);
}

/* card body */
.doc-card-body {
    padding: 22px 24px 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.doc-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--p-black);
    margin-bottom: 10px;
    line-height: 1.4;
    min-height: 52px;
}

.doc-desc {
    font-size: 0.88rem;
    color: var(--p-grey);
    line-height: 1.65;
    flex: 1;
    margin-bottom: 18px;
}

/* meta row */
.doc-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.78rem;
    color: #999;
    padding: 14px 0 0;
    border-top: 1px solid rgba(0,0,0,0.07);
    margin-bottom: 16px;
}

.doc-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* action buttons */
.doc-actions {
    display: flex;
    gap: 10px;
}

.btn-doc-view,
.btn-doc-dl {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 16px;
    border-radius: 10px;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s ease;
    letter-spacing: 0.02em;
}

.btn-doc-view {
    background: var(--p-orange);
    color: var(--p-white);
    border: 1.5px solid var(--p-orange);
}

.btn-doc-view:hover {
    background: var(--p-orange-dark);
    border-color: var(--p-orange-dark);
    color: var(--p-white);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px var(--p-orange-glow);
}

.btn-doc-dl {
    background: transparent;
    color: var(--p-black);
    border: 1.5px solid rgba(0,0,0,0.18);
}

.btn-doc-dl:hover {
    background: var(--p-black);
    color: var(--p-white);
    border-color: var(--p-black);
    transform: translateY(-1px);
}

/* ── EMPTY STATE ── */
.docs-empty {
    text-align: center;
    padding: 80px 30px;
    background: var(--p-white);
    border-radius: 20px;
    border: 1.5px dashed rgba(0,0,0,0.12);
}

.docs-empty i {
    font-size: 64px;
    color: var(--p-orange);
    opacity: 0.4;
    display: block;
    margin-bottom: 18px;
}

.docs-empty h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    color: var(--p-black);
    margin-bottom: 8px;
}

.docs-empty p { color: var(--p-grey); font-size: 0.95rem; }

/* ── INFO BOX ── */
.docs-info {
    margin-top: 60px;
    background: var(--p-black);
    border-radius: 20px;
    padding: 44px 48px;
    color: var(--p-white);
    position: relative;
    overflow: hidden;
}

.docs-info::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    border: 40px solid var(--p-orange);
    border-radius: 50%;
    opacity: 0.06;
}

.docs-info h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--p-white);
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.docs-info h4 i { color: var(--p-orange); }

.docs-info h6 {
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--p-orange);
    margin-bottom: 14px;
    margin-top: 0;
}

.docs-info ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.docs-info ul li {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.6);
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: flex-start;
    gap: 10px;
    line-height: 1.5;
}

.docs-info ul li::before {
    content: '';
    width: 6px; height: 6px;
    background: var(--p-orange);
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}

.docs-info ul li strong { color: rgba(255,255,255,0.9); }

.docs-info-note {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
    font-size: 0.82rem;
    color: rgba(255,255,255,0.35);
    display: flex;
    align-items: center;
    gap: 8px;
}

.docs-info-note i { color: var(--p-orange); opacity: 0.7; }

/* ── ANIMATIONS ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .docs-page { padding-top: 80px; }
    .docs-stat  { padding: 16px 20px; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .docs-stat:last-child { border-bottom: none; }
    .docs-stats { flex-direction: column; }
    .docs-info  { padding: 30px 24px; }
    .doc-actions { flex-direction: column; }
    .docs-filter { justify-content: flex-start; overflow-x: auto; padding-bottom: 4px; flex-wrap: nowrap; }
}
</style>

<!-- HERO -->
<div class="docs-hero">
    <div class="container">
        <div class="docs-hero-inner">
            <div class="docs-hero-eyebrow">
                <i class="bi bi-file-earmark-text-fill"></i>
                Centro de Documentos
            </div>
            <h1>Transparência &amp;<br><span>Conhecimento</span></h1>
            <p>Relatórios, estudos e recursos sobre saúde comunitária — partilhados abertamente desde 2006.</p>

            <div class="docs-stats">
                <div class="docs-stat">
                    <div class="docs-stat-icon"><i class="bi bi-shield-check-fill"></i></div>
                    <span class="docs-stat-label">Transparência Total</span>
                </div>
                <div class="docs-stat">
                    <div class="docs-stat-icon"><i class="bi bi-book-fill"></i></div>
                    <span class="docs-stat-label">Recursos Educativos</span>
                </div>
                <div class="docs-stat">
                    <div class="docs-stat-icon"><i class="bi bi-people-fill"></i></div>
                    <span class="docs-stat-label">Acesso Público</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTER BAR -->
<div class="docs-filter-wrap">
    <div class="container">
        <div class="docs-filter">
            <a href="documentos.php" class="filter-btn <?php echo !$categoria_filtro ? 'active' : ''; ?>">
                <i class="bi bi-grid-3x3-gap-fill"></i> Todos
            </a>
            <?php while ($cat = $categorias->fetch_assoc()): ?>
                <a href="?categoria=<?php echo urlencode($cat['categoria']); ?>"
                   class="filter-btn <?php echo $categoria_filtro === $cat['categoria'] ? 'active' : ''; ?>">
                    <i class="bi bi-folder-fill"></i> <?php echo htmlspecialchars($cat['categoria']); ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- DOCUMENTS GRID -->
<section class="docs-content">
    <div class="container">

        <?php if ($result->num_rows > 0): ?>
        <div class="docs-grid">
            <?php while ($doc = $result->fetch_assoc()): ?>
                <div class="doc-card">
                    <div class="doc-card-top">
                        <div class="doc-pdf-icon">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>
                        <span class="doc-cat-badge"><?php echo htmlspecialchars($doc['categoria']); ?></span>
                    </div>
                    <div class="doc-card-body">
                        <h3 class="doc-title"><?php echo htmlspecialchars($doc['titulo']); ?></h3>
                        <p class="doc-desc">
                            <?php
                            echo $doc['descricao']
                                ? htmlspecialchars(substr($doc['descricao'], 0, 110)) . (strlen($doc['descricao']) > 110 ? '...' : '')
                                : 'Documento disponível para consulta e download.';
                            ?>
                        </p>
                        <div class="doc-meta">
                            <span><i class="bi bi-download"></i> <?php echo $doc['downloads']; ?> downloads</span>
                            <span><i class="bi bi-calendar3"></i> <?php echo date('d/m/Y', strtotime($doc['created_at'])); ?></span>
                        </div>
                        <div class="doc-actions">
                            <a href="<?php echo $doc['arquivo']; ?>" target="_blank" class="btn-doc-view">
                                <i class="bi bi-eye-fill"></i> Visualizar
                            </a>
                            <a href="<?php echo $doc['arquivo']; ?>?download=1&id=<?php echo $doc['id']; ?>" download class="btn-doc-dl">
                                <i class="bi bi-download"></i> Baixar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
        <div class="docs-empty">
            <i class="bi bi-inbox"></i>
            <h4>Nenhum Documento Encontrado</h4>
            <p>
                <?php if ($categoria_filtro): ?>
                    Não há documentos disponíveis na categoria "<?php echo htmlspecialchars($categoria_filtro); ?>".
                <?php else: ?>
                    Em breve disponibilizaremos documentos sobre o nosso trabalho nas comunidades.
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Info Box -->
        <div class="docs-info">
            <h4><i class="bi bi-info-circle-fill"></i>Sobre os Nossos Documentos</h4>
            <div class="row">
                <div class="col-md-6">
                    <h6>Áreas Documentadas</h6>
                    <ul>
                        <li><strong>Saúde Materno-Infantil</strong> — Relatórios e estudos sobre cuidados a grávidas e crianças</li>
                        <li><strong>Prevenção da Malária</strong> — Materiais educativos e resultados de campanhas</li>
                        <li><strong>HIV/SIDA</strong> — Estratégias de prevenção e sensibilização comunitária</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Também Disponíveis</h6>
                    <ul>
                        <li><strong>Nutrição</strong> — Guias e recursos sobre alimentação saudável</li>
                        <li><strong>Construção da Paz</strong> — Documentos sobre diálogo inter-religioso</li>
                        <li><strong>Relatórios Anuais</strong> — Prestação de contas e impacto das nossas ações</li>
                    </ul>
                </div>
            </div>
            <p class="docs-info-note">
                <i class="bi bi-shield-check-fill"></i>
                Todos os documentos são de acesso público e refletem o nosso compromisso com a transparência e partilha de conhecimento.
            </p>
        </div>

    </div>
</section>

<?php
include 'includes/footer.php';
$conn->close();
?>