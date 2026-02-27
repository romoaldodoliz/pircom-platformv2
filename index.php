<?php
$page_title = "PIRCOM - Início";
$include_swiper = true;
include 'includes/navbar.php';
include 'includes/swiper-styles.php';
?>

<!-- Hero Slider com Swiper -->
<div class="swiper-container main-slider">
    <div class="swiper-wrapper">
        <?php
        include('config/conexao.php');
        $sql = "SELECT * FROM homepagehero ORDER BY id ASC LIMIT 3";
        $resultado = @$conn->query($sql);

        if (!$resultado) {
            $resultado = new stdClass();
            $resultado->num_rows = 0;
        }

        if ($resultado->num_rows > 0) {
            $i = 0;
            while ($row = $resultado->fetch_assoc()) {
                $imagemBlob = $row['foto'];
                $imagemBase64 = base64_encode($imagemBlob);
                echo '<div class="swiper-slide" style="background:url(data:image/jpeg;base64,' . $imagemBase64 . ') center center; background-size: cover;">';
                if ($i > 0 && !empty($row['descricao'])) {
                    echo '<h2>' . htmlspecialchars($row['descricao']) . '</h2>';
                }
                echo '</div>';
                $i++;
            }
        } else {
            echo '<div class="swiper-slide" style="background:url(\'https://cdn.pixabay.com/photo/2017/08/07/14/02/people-2604149_960_720.jpg\') center center; background-size: cover;">';
            echo '<h2>Pircom</h2>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>

<!-- ===================== STYLES ===================== -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@300;400;600&display=swap');

:root {
    --p-orange:  #FF6F0F;         /* primária */
    --p-orange-dark: #D95A00;     /* hover primária */
    --p-black:   #111111;         /* secundária escura */
    --p-white:   #FFFFFF;         /* secundária clara */
    --p-offwhite: #F7F7F7;        /* fundo alternativo neutro */
    --p-grey:    #5A5A5A;         /* texto muted */
    --p-light-border: rgba(0,0,0,0.08);
}

/* ── Shared Section Header ── */
.pircom-section-header {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 48px;
}

.pircom-section-header .pill-label {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    padding: 5px 16px;
    border-radius: 50px;
    background: var(--p-orange);
    color: var(--p-white);
    white-space: nowrap;
}

.pircom-section-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 900;
    margin: 0;
    line-height: 1.1;
}

.pircom-section-header .divider-line {
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, var(--p-orange) 0%, transparent 100%);
    opacity: 0.25;
}

/* ─────────────────────────────────────────
   NOTÍCIAS  —  fundo branco, cards com
   borda laranja no hover
───────────────────────────────────────── */
#noticias {
    background: var(--p-white);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

/* watermark decorativo */
#noticias::before {
    content: '✚';
    position: absolute;
    top: -20px;
    right: 4%;
    font-size: 300px;
    color: var(--p-orange);
    opacity: 0.04;
    line-height: 1;
    pointer-events: none;
    font-family: sans-serif;
}

#noticias .pircom-section-header h2 { color: var(--p-black); }

/* News Card */
.news-card {
    background: var(--p-white);
    border-radius: 14px;
    overflow: hidden;
    border: 1.5px solid var(--p-light-border);
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: transform 0.32s cubic-bezier(.34,1.56,.64,1),
                box-shadow 0.32s ease,
                border-color 0.25s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.news-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 44px rgba(255,111,15,0.14);
    border-color: var(--p-orange);
}

.news-card .card-img-wrapper {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.news-card .card-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.news-card:hover .card-img-wrapper img {
    transform: scale(1.06);
}

/* barra laranja inferior da imagem */
.news-card .card-img-wrapper::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 4px;
    background: var(--p-orange);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s ease;
}
.news-card:hover .card-img-wrapper::after { transform: scaleX(1); }

/* tag categoria */
.news-card .category-tag {
    position: absolute;
    bottom: 16px;
    left: 16px;
    background: var(--p-orange);
    color: var(--p-white);
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    padding: 4px 11px;
    border-radius: 4px;
}

.news-card .card-body {
    padding: 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
    border-top: none;
}

.news-card .card-body h5 {
    font-family: 'Playfair Display', serif;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--p-black);
    margin-bottom: 10px;
    line-height: 1.35;
}

.news-card .card-body p {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.92rem;
    color: var(--p-grey);
    line-height: 1.65;
    flex: 1;
}

.news-card .read-more {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--p-orange);
    text-decoration: none;
    margin-top: 16px;
    transition: gap 0.2s ease;
}

.news-card .read-more:hover { gap: 11px; color: var(--p-orange-dark); }

.news-card .read-more svg {
    width: 14px; height: 14px; stroke: currentColor;
}

/* ─────────────────────────────────────────
   EVENTOS  —  fundo preto, cards escuros
   com acento laranja
───────────────────────────────────────── */
#eventos {
    background: var(--p-black);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

/* círculo decorativo */
#eventos::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 380px; height: 380px;
    border: 70px solid var(--p-orange);
    border-radius: 50%;
    opacity: 0.05;
    pointer-events: none;
}
#eventos::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -60px;
    width: 260px; height: 260px;
    border: 50px solid var(--p-orange);
    border-radius: 50%;
    opacity: 0.04;
    pointer-events: none;
}

#eventos .pircom-section-header h2      { color: var(--p-white); }
#eventos .pircom-section-header .pill-label { background: var(--p-orange); }
#eventos .divider-line { opacity: 0.3; }

/* Event Card */
.event-card {
    background: #1C1C1C;
    border: 1.5px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    transition: transform 0.32s cubic-bezier(.34,1.56,.64,1),
                border-color 0.3s ease,
                box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.event-card:hover {
    transform: translateY(-8px);
    border-color: var(--p-orange);
    box-shadow: 0 22px 54px rgba(255,111,15,0.2);
}

.event-card .img-wrapper {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.event-card .img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    filter: brightness(0.8);
}

.event-card:hover .img-wrapper img {
    transform: scale(1.07);
    filter: brightness(0.95);
}

/* barra laranja no fundo da imagem */
.event-card .accent-bar {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 4px;
    background: var(--p-orange);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.35s ease;
}
.event-card:hover .accent-bar { transform: scaleX(1); }

/* ícone flutuante */
.event-card .event-icon {
    position: absolute;
    top: 14px; right: 14px;
    width: 38px; height: 38px;
    background: rgba(17,17,17,0.82);
    border: 1.5px solid var(--p-orange);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    backdrop-filter: blur(4px);
}

.event-card .card-body {
    padding: 22px 22px 26px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.event-card .card-body h5 {
    font-family: 'Playfair Display', serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--p-white);
    margin-bottom: 10px;
    line-height: 1.35;
}

.event-card .card-body p {
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.88rem;
    color: rgba(255,255,255,0.50);
    line-height: 1.6;
}

/* badges de tema — variações de laranja/branco */
.event-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.11em;
    text-transform: uppercase;
    padding: 4px 11px;
    border-radius: 50px;
    margin-bottom: 12px;
    width: fit-content;
}

/* badge sólido laranja */
.badge-saude  {
    background: var(--p-orange);
    color: var(--p-white);
}
/* badge contorno laranja */
.badge-fe     {
    background: transparent;
    color: var(--p-orange);
    border: 1.5px solid var(--p-orange);
}
/* badge branco */
.badge-genero {
    background: rgba(255,255,255,0.12);
    color: var(--p-white);
    border: 1.5px solid rgba(255,255,255,0.25);
}

/* ─────────────────────────────────────────
   CTA
───────────────────────────────────────── */
.cta {
    background: var(--p-offwhite);
    padding: 80px 0;
    text-align: center;
}

.cta h3 {
    color: var(--p-black) !important;
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    margin-bottom: 20px;
    font-size: 2.5rem;
}

.cta p {
    color: var(--p-grey) !important;
    font-family: 'Source Sans 3', sans-serif;
    font-size: 1.1rem;
    max-width: 800px;
    margin: 0 auto 30px;
    line-height: 1.7;
}

.cta-btn {
    display: inline-block;
    padding: 14px 44px;
    background: var(--p-orange);
    color: var(--p-white) !important;
    text-decoration: none;
    font-family: 'Source Sans 3', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    border-radius: 50px;
    transition: all 0.3s ease;
    border: 2px solid var(--p-orange);
}

.cta-btn:hover {
    background: var(--p-orange-dark);
    border-color: var(--p-orange-dark);
    color: var(--p-white) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(255,111,15,0.35);
}

/* ── Entrance animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}

.animate-up {
    opacity: 0;
    animation: fadeUp 0.6s ease forwards;
}

.animate-up:nth-child(1) { animation-delay: 0.08s; }
.animate-up:nth-child(2) { animation-delay: 0.20s; }
.animate-up:nth-child(3) { animation-delay: 0.32s; }
.animate-up:nth-child(4) { animation-delay: 0.16s; }
.animate-up:nth-child(5) { animation-delay: 0.28s; }
.animate-up:nth-child(6) { animation-delay: 0.40s; }
</style>

<!-- ===================== NOTÍCIAS ===================== -->
<section id="noticias">
    <div class="container">

        <div class="pircom-section-header">
            <span class="pill-label">Últimas</span>
            <h2>Notícias</h2>
            <span class="divider-line"></span>
        </div>

        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM noticias ORDER BY id DESC LIMIT 3";
            $result = @$conn->query($sql);

            /* cycling icons / labels for community themes */
            $themes = [
                ['tag' => 'Saúde', 'icon' => '✚'],
                ['tag' => 'Comunidade', 'icon' => '🤝'],
                ['tag' => 'Género', 'icon' => '♀'],
            ];
            $ti = 0;

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagemBLOB = base64_encode($row["foto"]);
                    $theme = $themes[$ti % 3];
                    $ti++;
                    echo '<div class="col-lg-4 col-md-6 animate-up">';
                    echo '  <div class="news-card">';
                    echo '    <a href="noticias.php?id=' . $row["id"] . '" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;height:100%;">';
                    echo '      <div class="card-img-wrapper">';
                    echo '        <img src="data:image/jpeg;base64,' . $imagemBLOB . '" alt="Notícia">';
                    echo '        <span class="category-tag">' . $theme['tag'] . '</span>';
                    echo '      </div>';
                    echo '      <div class="card-body">';
                    echo '        <h5>' . htmlspecialchars($row["titulo"]) . '</h5>';
                    echo '        <p>' . htmlspecialchars(substr($row["descricao"], 0, 110)) . '...</p>';
                    echo '        <span class="read-more">';
                    echo '          Ler mais';
                    echo '          <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
                    echo '        </span>';
                    echo '      </div>';
                    echo '    </a>';
                    echo '  </div>';
                    echo '</div>';
                }
            }
            ?>
        </div>

    </div>
</section>

<!-- ===================== EVENTOS ===================== -->
<section id="eventos">
    <div class="container">

        <div class="pircom-section-header">
            <span class="pill-label">Agenda</span>
            <h2>Nossos Eventos</h2>
            <span class="divider-line"></span>
        </div>

        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM eventos ORDER BY id DESC LIMIT 6";
            $result = @$conn->query($sql);

            $event_themes = [
                ['badge_class' => 'badge-saude',  'label' => 'Saúde Materno-Infantil', 'icon' => '🏥'],
                ['badge_class' => 'badge-fe',      'label' => 'Fé & Acção',             'icon' => '✝'],
                ['badge_class' => 'badge-genero',  'label' => 'Igualdade de Género',    'icon' => '⚤'],
                ['badge_class' => 'badge-saude',   'label' => 'Malária & HIV',          'icon' => '💊'],
                ['badge_class' => 'badge-fe',      'label' => 'Comunidade',             'icon' => '🤝'],
                ['badge_class' => 'badge-genero',  'label' => 'Empoderamento',          'icon' => '✊'],
            ];
            $ei = 0;

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagemBLOB = base64_encode($row["foto"]);
                    $et = $event_themes[$ei % 6];
                    $ei++;
                    echo '<div class="col-lg-4 col-md-6 animate-up">';
                    echo '  <div class="event-card">';
                    echo '    <div class="img-wrapper">';
                    echo '      <img src="data:image/jpeg;base64,' . $imagemBLOB . '" alt="Evento">';
                    echo '      <div class="accent-bar"></div>';
                    echo '      <div class="event-icon">' . $et['icon'] . '</div>';
                    echo '    </div>';
                    echo '    <div class="card-body">';
                    echo '      <span class="event-badge ' . $et['badge_class'] . '">' . $et['label'] . '</span>';
                    echo '      <h5>' . htmlspecialchars($row["titulo"]) . '</h5>';
                    echo '      <p>' . htmlspecialchars(substr($row["descricao"], 0, 110)) . '...</p>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            }
            ?>
        </div>

    </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="cta">
    <div class="container text-center">
        <h3>SEJA PARTE DA SOLUÇÃO</h3>
        <p>"Desde 2006, unimos fé e ação para melhorar a saúde materno-infantil, combater a malária e o HIV. Ajude-nos a alcançar mais vidas!"</p>
        <a class="cta-btn" href="doacoes.php">Doar Agora</a>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>

<script>
var swiper = new Swiper('.swiper-container', {
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
});

/* Intersection Observer — trigger animate-up only when cards enter viewport */
(function () {
    var cards = document.querySelectorAll('.animate-up');
    if (!window.IntersectionObserver) {
        cards.forEach(function(c){ c.style.opacity = 1; });
        return;
    }
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if (e.isIntersecting) {
                e.target.style.animationPlayState = 'running';
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });

    cards.forEach(function(c){
        c.style.animationPlayState = 'paused';
        io.observe(c);
    });
})();
</script>