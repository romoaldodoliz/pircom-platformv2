<?php
$page_title = "Pircom - Galeria";
include 'includes/navbar.php';

include('config/conexao.php');

$tipos_sql    = "SELECT DISTINCT tipo FROM galeria WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo";
$tipos_result = @$conn->query($tipos_sql);
$tipos        = [];
if ($tipos_result && $tipos_result->num_rows > 0) {
    while ($tr = $tipos_result->fetch_assoc()) $tipos[] = $tr['tipo'];
}

$sql    = "SELECT * FROM galeria ORDER BY created_date DESC";
$result = @$conn->query($sql);
$items  = [];
if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) $items[] = $r;
}

// Contadores para stats
$total_fotos  = count(array_filter($items, fn($i) => $i['tipo'] === 'imagem'));
$total_videos = count(array_filter($items, fn($i) => $i['tipo'] === 'video'));

function extractYouTubeId($url) {
    $p = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($p, $url, $m);
    return $m[1] ?? null;
}

$conn->close();
?>

<style>
/* ═══════════════════════════════════════════════════
   PIRCOM GALLERY — cores corporativas
   Primário:   #D0021B  (vermelho PIRCOM)
   Escuro:     #1A1A2E  (azul-noite)
   Texto:      #2D2D2D
   Superficie: #FFFFFF
   Bg:         #F7F7F7
═══════════════════════════════════════════════════ */

@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --pir-red:        #D0021B;
    --pir-red-dark:   #a80116;
    --pir-red-light:  rgba(208, 2, 27, 0.08);
    --pir-red-mid:    rgba(208, 2, 27, 0.18);
    --pir-dark:       #1A1A2E;
    --pir-dark-75:    rgba(26, 26, 46, 0.75);
    --text:           #2D2D2D;
    --text-secondary: #5A5A6E;
    --text-muted:     #9096A2;
    --surface:        #FFFFFF;
    --bg:             #F7F7F9;
    --border:         #E4E4ED;
    --radius-sm:      8px;
    --radius-md:      14px;
    --radius-lg:      20px;
    --shadow-sm:      0 2px 8px rgba(26,26,46,0.07);
    --shadow-md:      0 6px 24px rgba(26,26,46,0.11);
    --shadow-lg:      0 16px 48px rgba(26,26,46,0.16);
}

.gal-section {
    background: var(--bg);
    padding: 4rem 0 5rem;
    min-height: 70vh;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* ── SECTION TITLE ── */
.gal-title-block {
    text-align: center;
    margin-bottom: 3rem;
}
.gal-title-block h2 {
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 800;
    color: var(--pir-dark);
    letter-spacing: -0.02em;
    margin-bottom: 0.5rem;
}
.gal-title-block h2 span { color: var(--pir-red); }
.gal-title-block p {
    font-size: 1.05rem;
    color: var(--text-secondary);
    margin: 0;
}
.gal-title-bar {
    width: 60px; height: 4px;
    background: linear-gradient(90deg, var(--pir-red), var(--pir-red-dark));
    border-radius: 2px; margin: 1rem auto 0;
}

/* ── INTRO CARD ── */
.gal-intro {
    background: var(--surface);
    border-radius: var(--radius-lg);
    padding: 2.5rem 2rem;
    margin-bottom: 3rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
}
.gal-intro-inner {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.75rem;
}
.gal-intro-icon {
    width: 56px; height: 56px; flex-shrink: 0;
    background: var(--pir-red-light);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: var(--pir-red);
}
.gal-intro-text h3 {
    font-size: 1.2rem; font-weight: 800; color: var(--pir-dark); margin-bottom: 0.25rem;
}
.gal-intro-text p {
    font-size: 0.9375rem; color: var(--text-secondary); margin: 0; line-height: 1.7;
}

.gal-stats {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    border-top: 1px solid var(--border);
    padding-top: 1.5rem;
}
.gal-stat {
    text-align: center;
    padding: 0 1rem;
    border-right: 1px solid var(--border);
}
.gal-stat:last-child { border-right: none; }
.gal-stat-num {
    font-size: 2rem; font-weight: 800; color: var(--pir-red);
    line-height: 1; display: block; margin-bottom: 0.25rem;
}
.gal-stat-lbl {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--text-muted);
}

/* ── FILTER TABS ── */
.gal-filter-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 2.5rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 0.375rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    box-shadow: var(--shadow-sm);
}

.filter-btn {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: transparent; border: none;
    color: var(--text-secondary);
    padding: 0.5rem 1.125rem;
    border-radius: 999px;
    font-size: 0.875rem; font-weight: 600;
    font-family: inherit; cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.filter-btn:hover {
    background: var(--pir-red-light);
    color: var(--pir-red);
}
.filter-btn.active {
    background: var(--pir-red);
    color: #fff;
    box-shadow: 0 2px 10px rgba(208,2,27,0.35);
}
.filter-btn i { font-size: 0.9rem; }

/* filtros extras (tipos customizados) que não cabem na pílula */
.gal-filter-extra {
    display: flex; align-items: center; justify-content: center;
    flex-wrap: wrap; gap: 0.5rem;
    margin-bottom: 2rem;
}
.filter-tag {
    display: inline-flex; align-items: center; gap: 0.35rem;
    background: var(--surface); border: 1.5px solid var(--border);
    color: var(--text-secondary);
    padding: 0.375rem 0.875rem; border-radius: 999px;
    font-size: 0.8125rem; font-weight: 600;
    font-family: inherit; cursor: pointer;
    transition: all 0.2s ease;
}
.filter-tag:hover  { border-color: var(--pir-red); color: var(--pir-red); background: var(--pir-red-light); }
.filter-tag.active { background: var(--pir-red); border-color: var(--pir-red); color: white; }

/* ── GALLERY GRID ── */
#gallery-grid { --gap: 1.5rem; }

.gallery-item-wrapper {
    margin-bottom: var(--gap);
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-md);
    cursor: pointer;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    background: var(--surface);
    transition: transform 0.35s ease, box-shadow 0.35s ease;
    height: 280px;
}
.gallery-item:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}
.gallery-item img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.45s ease;
    display: block;
}
.gallery-item:hover img { transform: scale(1.07); }

/* ── VIDEO CARD ── */
.video-card {
    border: 2px solid var(--pir-red-mid);
}
.video-thumbnail {
    position: relative; width: 100%; height: 100%; background: #000;
}
.video-thumbnail img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.45s ease; opacity: 0.85;
}
.video-card:hover .video-thumbnail img {
    transform: scale(1.07); opacity: 1;
}
.video-play-overlay {
    position: absolute; inset: 0;
    background: rgba(26,26,46,0.3);
    display: flex; align-items: center; justify-content: center;
    transition: background 0.3s;
}
.video-card:hover .video-play-overlay { background: rgba(208,2,27,0.2); }
.video-play-icon {
    width: 64px; height: 64px;
    background: rgba(255,255,255,0.92);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; color: var(--pir-red);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.video-card:hover .video-play-icon {
    transform: scale(1.15);
    box-shadow: 0 6px 28px rgba(208,2,27,0.4);
}

/* ── BADGES ── */
.item-badge {
    position: absolute; z-index: 3;
    top: 12px; left: 12px;
    background: var(--pir-red);
    color: #fff;
    padding: 0.25rem 0.65rem;
    border-radius: 999px;
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.06em;
    display: inline-flex; align-items: center; gap: 0.3rem;
    box-shadow: 0 2px 8px rgba(208,2,27,0.35);
}
.item-badge.type-badge {
    top: 12px; left: auto; right: 12px;
    background: rgba(26,26,46,0.7);
    backdrop-filter: blur(4px);
}

/* ── OVERLAY ── */
.gallery-overlay {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(26,26,46,0.92) 0%, rgba(26,26,46,0.4) 70%, transparent 100%);
    padding: 1.25rem 1rem 1rem;
    transform: translateY(101%);
    transition: transform 0.3s ease;
}
.gallery-item:hover .gallery-overlay { transform: translateY(0); }
.gallery-overlay h5 {
    color: #fff; font-weight: 700; margin-bottom: 0.25rem;
    font-size: 0.9375rem; line-height: 1.3;
}
.gallery-overlay p {
    color: rgba(255,255,255,0.8); font-size: 0.8125rem; margin: 0; line-height: 1.5;
}

/* Placeholder (sem foto) */
.item-placeholder {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, #f0f0f5 0%, #e4e4ed 100%);
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 0.5rem; color: var(--text-muted);
}
.item-placeholder i { font-size: 3rem; }
.item-placeholder span { font-size: 0.8rem; font-weight: 600; }

/* ── LIGHTBOX ── */
.lightbox {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(10,10,20,0.96);
    backdrop-filter: blur(6px);
    align-items: center; justify-content: center;
    padding: 1rem;
}
.lightbox.active { display: flex; }

.lightbox-inner {
    position: relative;
    max-width: 900px; width: 100%;
    display: flex; flex-direction: column; align-items: center; gap: 1rem;
}

.lightbox-media {
    position: relative; width: 100%;
}
.lightbox-media img {
    max-width: 100%; max-height: 70vh;
    border-radius: var(--radius-md);
    display: block; margin: 0 auto;
    box-shadow: 0 8px 48px rgba(0,0,0,0.6);
}
.video-container {
    width: 100%; aspect-ratio: 16/9;
    border-radius: var(--radius-md); overflow: hidden;
    box-shadow: 0 8px 48px rgba(0,0,0,0.6);
}
.video-container iframe { width: 100%; height: 100%; border: none; display: block; }

.lightbox-info {
    background: var(--surface);
    border-radius: var(--radius-md);
    padding: 1rem 1.25rem;
    width: 100%; text-align: center;
    box-shadow: var(--shadow-md);
}
.lightbox-info h4 {
    font-size: 1rem; font-weight: 700; color: var(--pir-dark); margin-bottom: 0.25rem;
}
.lightbox-info p   { font-size: 0.875rem; color: var(--text-secondary); margin: 0; }
.lightbox-info .lb-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: var(--pir-red); color: white;
    padding: 0.2rem 0.65rem; border-radius: 999px;
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    margin-top: 0.5rem;
}

.lightbox-close {
    position: fixed; top: 1.25rem; right: 1.5rem;
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.2);
    color: white; font-size: 1.25rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s, transform 0.2s; z-index: 10001;
}
.lightbox-close:hover { background: var(--pir-red); transform: rotate(90deg); border-color: var(--pir-red); }

.lightbox-nav {
    position: fixed; top: 50%; transform: translateY(-50%);
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.2);
    color: white; font-size: 1.25rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s; z-index: 10001;
}
.lightbox-nav:hover { background: var(--pir-red); border-color: var(--pir-red); }
.lightbox-prev { left: 1rem; }
.lightbox-next { right: 1rem; }
.video-mode .lightbox-nav { display: none; }

/* ── EMPTY STATE ── */
.gal-empty {
    text-align: center; padding: 4rem 1rem;
    background: var(--surface); border-radius: var(--radius-lg);
    border: 1px dashed var(--border);
}
.gal-empty i { font-size: 4rem; color: var(--pir-red); opacity: 0.3; display: block; margin-bottom: 1rem; }
.gal-empty h4 { font-weight: 800; color: var(--pir-dark); margin-bottom: 0.5rem; }
.gal-empty p  { color: var(--text-muted); margin: 0; }

/* ── CTA ── */
.gal-cta {
    background: linear-gradient(135deg, var(--pir-red) 0%, var(--pir-red-dark) 100%);
    border-radius: var(--radius-lg);
    padding: 2.5rem 2rem;
    text-align: center;
    margin-top: 3rem;
    box-shadow: 0 8px 32px rgba(208,2,27,0.3);
}
.gal-cta h5 { font-size: 1.25rem; font-weight: 800; color: white; margin-bottom: 0.75rem; }
.gal-cta p  { color: rgba(255,255,255,0.88); margin-bottom: 1.5rem; font-size: 0.9375rem; }
.btn-gal-cta {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: white; color: var(--pir-red); font-weight: 700;
    padding: 0.75rem 2rem; border-radius: 999px;
    text-decoration: none; font-size: 0.9375rem;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn-gal-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.25); color: var(--pir-red-dark); }

/* ── RESPONSIVE ── */
@media (max-width: 576px) {
    .gal-filter-wrap { padding: 0.25rem; gap: 0.25rem; }
    .filter-btn { padding: 0.4rem 0.75rem; font-size: 0.8125rem; }
    .gallery-item { height: 220px; }
    .gal-stat { padding: 0 0.5rem; }
    .gal-stat-num { font-size: 1.5rem; }
    .lightbox-nav { display: none; }
}
</style>

<section class="gal-section">
    <div class="container">

        <!-- Título -->
        <div class="gal-title-block">
            <h2>GALERIA <span>PIRCOM</span></h2>
            <p>Registos do nosso trabalho pela saúde e paz</p>
            <div class="gal-title-bar"></div>
        </div>

        <!-- Intro -->
        <div class="gal-intro">
            <div class="gal-intro-inner">
                <div class="gal-intro-icon"><i class="bi bi-camera-fill"></i></div>
                <div class="gal-intro-text">
                    <h3>Nossa História em Imagens</h3>
                    <p>Desde 2006, a PIRCOM tem trabalhado em comunidades moçambicanas unindo cristãos, muçulmanos, hindus e bahai na promoção da saúde. Aqui partilhamos momentos das nossas intervenções em prevenção da malária, combate ao HIV, saúde materno-infantil e construção da paz.</p>
                </div>
            </div>
            <div class="gal-stats">
                <div class="gal-stat">
                    <span class="gal-stat-num">18+</span>
                    <span class="gal-stat-lbl">Anos de Impacto</span>
                </div>
                <div class="gal-stat">
                    <span class="gal-stat-num">4</span>
                    <span class="gal-stat-lbl">Comunidades Religiosas</span>
                </div>
                <div class="gal-stat">
                    <span class="gal-stat-num"><?php echo count($items); ?></span>
                    <span class="gal-stat-lbl">Itens na Galeria</span>
                </div>
                <div class="gal-stat">
                    <span class="gal-stat-num"><?php echo $total_videos; ?></span>
                    <span class="gal-stat-lbl">Vídeos</span>
                </div>
            </div>
        </div>

        <!-- Filtros principais -->
        <?php if (count($items) > 0): ?>
        <div class="gal-filter-wrap">
            <button class="filter-btn active" data-filter="all">
                <i class="bi bi-grid-3x3-gap-fill"></i> Todos
            </button>
            <button class="filter-btn" data-filter="imagem">
                <i class="bi bi-image-fill"></i> Fotos
            </button>
            <button class="filter-btn" data-filter="video">
                <i class="bi bi-play-circle-fill"></i> Vídeos
            </button>
        </div>

        <!-- Filtros de categorias customizadas -->
        <?php
        $extra_tipos = array_filter($tipos, fn($t) => $t !== 'imagem' && $t !== 'video');
        if (count($extra_tipos) > 0):
        ?>
        <div class="gal-filter-extra">
            <?php foreach ($extra_tipos as $tipo): ?>
            <button class="filter-tag" data-filter="<?php echo htmlspecialchars($tipo); ?>">
                <i class="bi bi-tag-fill"></i>
                <?php echo htmlspecialchars(ucfirst($tipo)); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Grid -->
        <div class="row g-4" id="gallery-grid">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $row):
                    $tipo      = !empty($row['tipo']) ? htmlspecialchars($row['tipo']) : 'outros';
                    $isVideo   = ($tipo === 'video');
                    $isImage   = ($tipo === 'imagem');
                    $titulo    = htmlspecialchars($row['titulo'] ?? '');
                    $descricao = htmlspecialchars($row['descricao'] ?? '');
                    $desc_curta = mb_strlen($descricao) > 90 ? mb_substr($descricao, 0, 90) . '...' : $descricao;
                ?>
                <div class="col-lg-4 col-md-6 gallery-item-wrapper" data-category="<?php echo $tipo; ?>">
                    <div class="gallery-item <?php echo $isVideo ? 'video-card' : 'image-card'; ?>"
                         data-id="<?php echo (int)$row['id']; ?>"
                         data-titulo="<?php echo $titulo; ?>"
                         data-descricao="<?php echo $descricao; ?>"
                         data-tipo="<?php echo $tipo; ?>"
                         <?php if ($isVideo): ?>data-video-url="<?php echo htmlspecialchars($row['link'] ?? ''); ?>"<?php endif; ?>>

                        <?php if ($isVideo): ?>
                            <?php
                            $videoId  = extractYouTubeId($row['link'] ?? '');
                            $thumbUrl = $videoId ? "https://img.youtube.com/vi/$videoId/hqdefault.jpg" : '';
                            ?>
                            <span class="item-badge"><i class="bi bi-play-fill"></i> Vídeo</span>
                            <div class="video-thumbnail">
                                <?php if ($thumbUrl): ?>
                                <img src="<?php echo $thumbUrl; ?>"
                                     alt="<?php echo $titulo; ?>"
                                     onerror="this.parentElement.innerHTML='<div class=\'item-placeholder\'><i class=\'bi bi-play-btn-fill\'></i><span>Vídeo</span></div>'">
                                <?php else: ?>
                                <div class="item-placeholder">
                                    <i class="bi bi-play-btn-fill"></i><span>Vídeo</span>
                                </div>
                                <?php endif; ?>
                                <div class="video-play-overlay">
                                    <div class="video-play-icon"><i class="bi bi-play-fill"></i></div>
                                </div>
                            </div>

                        <?php elseif ($isImage && !empty($row['foto'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>"
                                 alt="<?php echo $titulo; ?>" loading="lazy">

                        <?php else: ?>
                            <?php if (!empty($tipo) && $tipo !== 'imagem' && $tipo !== 'video'): ?>
                                <span class="item-badge type-badge">
                                    <i class="bi bi-tag-fill"></i> <?php echo $tipo; ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($row['foto'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>"
                                     alt="<?php echo $titulo; ?>" loading="lazy">
                            <?php else: ?>
                                <div class="item-placeholder">
                                    <i class="bi bi-image"></i>
                                    <span>Sem imagem</span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Overlay -->
                        <div class="gallery-overlay">
                            <h5><?php echo $titulo; ?></h5>
                            <?php if ($desc_curta): ?>
                            <p><?php echo $desc_curta; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
            <div class="col-12">
                <div class="gal-empty">
                    <i class="bi bi-images"></i>
                    <h4>Galeria em Construção</h4>
                    <p>Em breve, partilharemos momentos do nosso trabalho nas comunidades moçambicanas.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- CTA -->
        <div class="gal-cta">
            <h5><i class="bi bi-megaphone-fill me-2"></i>Acompanhe Nosso Trabalho</h5>
            <p>Siga-nos nas redes sociais para mais atualizações sobre as nossas atividades e impacto nas comunidades.</p>
            <a href="contacto.php" class="btn-gal-cta">
                <i class="bi bi-telephone-fill"></i> Entre em Contacto
            </a>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose" aria-label="Fechar">
        <i class="bi bi-x-lg"></i>
    </button>
    <button class="lightbox-nav lightbox-prev" id="lbPrev" aria-label="Anterior">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button class="lightbox-nav lightbox-next" id="lbNext" aria-label="Próximo">
        <i class="bi bi-chevron-right"></i>
    </button>

    <div class="lightbox-inner">
        <div class="lightbox-media">
            <img id="lbImg" src="" alt="" style="display:none;">
            <div class="video-container" id="lbVideoWrap" style="display:none;">
                <iframe id="lbVideo" src=""
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="lightbox-info">
            <h4 id="lbTitle"></h4>
            <p id="lbDesc"></p>
            <span id="lbBadge" class="lb-badge"></span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
(function () {
    // ── FILTER ──
    let activeFilter = 'all';

    function applyFilter(filter) {
        activeFilter = filter;

        document.querySelectorAll('.filter-btn, .filter-tag').forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-filter') === filter);
        });

        document.querySelectorAll('.gallery-item-wrapper').forEach(wrapper => {
            const cat = wrapper.getAttribute('data-category');
            let show = false;
            if      (filter === 'all')    show = true;
            else if (filter === 'imagem') show = cat === 'imagem';
            else if (filter === 'video')  show = cat === 'video';
            else                          show = cat === filter;
            wrapper.style.display = show ? '' : 'none';
        });

        buildGalleryIndex();
    }

    document.querySelectorAll('.filter-btn, .filter-tag').forEach(btn => {
        btn.addEventListener('click', () => applyFilter(btn.getAttribute('data-filter')));
    });

    // ── GALLERY INDEX (itens visíveis) ──
    let gallery = [];

    function buildGalleryIndex() {
        gallery = [];
        document.querySelectorAll('.gallery-item-wrapper:not([style*="display: none"]) .gallery-item').forEach(el => {
            const imgEl    = el.querySelector('img');
            const isVideo  = !!el.getAttribute('data-video-url');
            gallery.push({
                el,
                src      : imgEl ? imgEl.src : null,
                videoUrl : el.getAttribute('data-video-url'),
                isVideo,
                titulo   : el.getAttribute('data-titulo')   || '',
                descricao: el.getAttribute('data-descricao') || '',
                tipo     : el.getAttribute('data-tipo')      || '',
            });
        });
    }

    buildGalleryIndex();

    // Click em cada item
    document.querySelectorAll('.gallery-item').forEach(el => {
        el.addEventListener('click', () => {
            buildGalleryIndex();
            const idx = gallery.findIndex(g => g.el === el);
            if (idx !== -1) openLightbox(idx);
        });
    });

    // ── LIGHTBOX ──
    const lightbox    = document.getElementById('lightbox');
    const lbImg       = document.getElementById('lbImg');
    const lbVideoWrap = document.getElementById('lbVideoWrap');
    const lbVideo     = document.getElementById('lbVideo');
    const lbTitle     = document.getElementById('lbTitle');
    const lbDesc      = document.getElementById('lbDesc');
    const lbBadge     = document.getElementById('lbBadge');
    let currentIdx    = 0;

    function openLightbox(idx) {
        currentIdx = idx;
        const item = gallery[idx];
        if (!item) return;

        if (item.isVideo) {
            lightbox.classList.add('video-mode');
            lbImg.style.display       = 'none';
            lbVideoWrap.style.display = 'block';
            const vid = extractYTId(item.videoUrl);
            lbVideo.src = vid
                ? `https://www.youtube.com/embed/${vid}?autoplay=1&rel=0`
                : '';
        } else {
            lightbox.classList.remove('video-mode');
            lbVideoWrap.style.display = 'none';
            lbImg.style.display       = 'block';
            lbImg.src                 = item.src || '';
            lbImg.alt                 = item.titulo;
        }

        lbTitle.textContent = item.titulo;
        lbDesc.textContent  = item.descricao;
        lbBadge.innerHTML   = `<i class="bi bi-${item.isVideo ? 'play-circle-fill' : 'image-fill'}"></i> ${item.tipo}`;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lbVideo.src = '';
        lightbox.classList.remove('active', 'video-mode');
        document.body.style.overflow = '';
    }

    function navigate(dir) {
        if (gallery.length === 0) return;
        lbVideo.src = '';
        currentIdx  = (currentIdx + dir + gallery.length) % gallery.length;
        openLightbox(currentIdx);
    }

    document.getElementById('lightboxClose').addEventListener('click', closeLightbox);
    document.getElementById('lbNext').addEventListener('click', () => navigate(1));
    document.getElementById('lbPrev').addEventListener('click', () => navigate(-1));
    lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', e => {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape')      closeLightbox();
        if (!lightbox.classList.contains('video-mode')) {
            if (e.key === 'ArrowRight') navigate(1);
            if (e.key === 'ArrowLeft')  navigate(-1);
        }
    });

    function extractYTId(url) {
        if (!url) return null;
        const m = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
        return m ? m[1] : null;
    }
})();
</script>