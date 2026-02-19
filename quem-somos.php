<?php
$page_title = "PIRCOM - Quem Somos";
include 'config/conexao.php';

$config_query = $conn->query("SELECT * FROM config LIMIT 1");
$config = $config_query->fetch_assoc();

// Fetch only metadata — no foto blob
$members_query = $conn->query(
    "SELECT id, nome, cargo, descricao, email, linkedin, categoria, ordem,
            (foto IS NOT NULL AND foto != '') AS has_foto
     FROM team_members
     WHERE ativo = 1
     ORDER BY ordem ASC, id ASC"
);
$members_by_cat = [];
if ($members_query) {
    while ($m = $members_query->fetch_assoc()) {
        $members_by_cat[$m['categoria']][] = $m;
    }
}

$cat_labels = [
    'liderança'  => 'Conselho de Direcção',
    'conselho'   => 'Conselho Consultivo',
    'técnico'    => 'Equipa Técnica',
    'voluntário' => 'Voluntários',
];

function initials($nome) {
    $parts = explode(' ', trim($nome));
    return strtoupper(mb_substr($parts[0],0,1) . (isset($parts[1]) ? mb_substr($parts[1],0,1) : ''));
}

include 'includes/navbar.php';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* ═══════════════════════════════════════
   PIRCOM BRAND TOKENS
   Orange · Black · White
═══════════════════════════════════════ */
:root {
  --orange:     #FF6F0F;
  --orange-dk:  #D95A00;
  --orange-lt:  #FFF3EA;
  --orange-mid: #FF8C3A;
  --black:      #0D0D0D;
  --black-soft: #1A1A1A;
  --ink:        #222222;
  --ink-s:      #555555;
  --ink-m:      #999999;
  --white:      #FFFFFF;
  --off-white:  #FAF9F7;
  --border:     #EBEBEB;
  --border-dk:  #D0D0D0;

  --ff-head: 'Bebas Neue', Impact, sans-serif;
  --ff-body: 'Outfit', sans-serif;
  --ease:    .22s cubic-bezier(.4,0,.2,1);
  --sh:      0 2px 16px rgba(0,0,0,.08);
  --sh-hov:  0 12px 40px rgba(255,111,15,.18);
  --sh-card: 0 1px 4px rgba(0,0,0,.06), 0 4px 20px rgba(0,0,0,.06);
}

body { font-family: var(--ff-body); color: var(--ink); background: var(--white); }

/* ── Utilities ── */
.sec-label {
  display: inline-block;
  font-family: var(--ff-body); font-size: .72rem; font-weight: 700;
  letter-spacing: 1.8px; text-transform: uppercase;
  color: var(--orange); background: var(--orange-lt);
  padding: .3rem 1rem; border-radius: 999px; margin-bottom: .75rem;
}
.sec-title {
  font-family: var(--ff-head);
  font-size: clamp(2.2rem, 5vw, 3.2rem);
  letter-spacing: 1.5px; color: var(--black);
  line-height: 1.1; margin-bottom: .4rem;
}
.sec-title span { color: var(--orange); }
.sec-sub { font-size: 1rem; color: var(--ink-s); max-width: 560px; margin: 0 auto; }
.sec-head { text-align: center; margin-bottom: 3rem; }

/* Orange rule under section titles */
.sec-rule {
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  margin: .75rem auto 1.5rem;
}
.sec-rule span {
  display: block; height: 3px; border-radius: 999px; background: var(--orange);
}
.sec-rule span:nth-child(1) { width: 12px; opacity:.4; }
.sec-rule span:nth-child(2) { width: 40px; }
.sec-rule span:nth-child(3) { width: 12px; opacity:.4; }

/* ── About cards ── */
.about-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 2.5rem;
  box-shadow: var(--sh-card);
  margin-bottom: 1.75rem;
  position: relative;
  overflow: hidden;
}
.about-card::before {
  content: '';
  position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
  background: var(--orange);
  border-radius: 4px 0 0 4px;
}
.about-card h3 {
  font-family: var(--ff-head);
  letter-spacing: 1px; font-size: 1.4rem;
  color: var(--black); margin-bottom: 1rem;
  display: flex; align-items: center; gap: .55rem;
}
.about-card h3 i { color: var(--orange); }

/* ═══════════════════════════════════════
   TEAM SECTION
═══════════════════════════════════════ */
.team-section { background: var(--off-white); padding: 0 0 5rem; }

/* Black header band */
.team-band {
  background: var(--black);
  padding: 4rem 0 2.5rem;
  text-align: center;
  position: relative;
  overflow: hidden;
  margin-bottom: 3.5rem;
}
/* diagonal orange stripe decoration */
.team-band::after {
  content: '';
  position: absolute; bottom: -1px; left: 0; right: 0; height: 5px;
  background: repeating-linear-gradient(
    90deg,
    var(--orange) 0, var(--orange) 40px,
    transparent 40px, transparent 50px
  );
}
.team-band .sec-label { background: rgba(255,111,15,.15); color: var(--orange); }
.team-band .sec-title { color: var(--white); }
.team-band .sec-sub   { color: rgba(255,255,255,.65); }
.team-band .sec-rule span { background: var(--orange); }

/* ═══════════════════════════════════════
   CATEGORY GROUP
═══════════════════════════════════════ */
.cat-group { margin-bottom: 4rem; }

.cat-title {
  font-family: var(--ff-head);
  font-size: 1.5rem; letter-spacing: 1.5px;
  color: var(--black); margin-bottom: 2rem;
  display: flex; align-items: center; gap: .75rem;
}
.cat-title::before {
  content: '';
  display: inline-block; width: 24px; height: 4px;
  background: var(--orange); border-radius: 2px; flex-shrink: 0;
}

/* ── PRESIDENT ROW: always centred ── */
.grid-president {
  display: flex;
  justify-content: center;
  margin-bottom: 1.75rem;
}

/* ── MEMBERS GRID: fixed 3 columns, centred when fewer items ── */
.grid-members {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;   /* centres orphan items */
  gap: 1.25rem;
}
.grid-members .mc {
  /* fixed width so grid looks like a proper grid */
  width: 200px;
  flex: 0 0 200px;
}
@media (max-width: 700px) {
  .grid-members .mc { width: 160px; flex: 0 0 160px; }
}
@media (max-width: 480px) {
  .grid-members .mc { width: calc(50% - .65rem); flex: 0 0 calc(50% - .65rem); }
}

/* ── MEMBER CARD ── */
.mc {
  display: flex; flex-direction: column; align-items: center;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 1.5rem 1rem 1.25rem;
  cursor: pointer; text-align: center;
  box-shadow: var(--sh-card);
  transition: transform var(--ease), box-shadow var(--ease), border-color var(--ease);
  position: relative; overflow: hidden;
}
.mc::after {
  content: '';
  position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
  background: var(--orange);
  transform: scaleX(0); transform-origin: left;
  transition: transform var(--ease);
}
.mc:hover { transform: translateY(-5px); box-shadow: var(--sh-hov); border-color: #FFD4B0; }
.mc:hover::after { transform: scaleX(1); }

/* President card is wider */
.mc-president {
  width: 240px;
  padding: 2rem 1.75rem 1.5rem;
  border-color: var(--border-dk);
}

/* ── PHOTO ── */
.mc-photo-wrap { position: relative; display: inline-block; margin-bottom: 1rem; }

.mc-photo {
  width: 130px; height: 130px;
  border-radius: 10px; object-fit: cover;
  border: 3px solid var(--border);
  display: block; background: #e8e8e8;
  transition: border-color var(--ease);
}
.mc-president .mc-photo {
  width: 160px; height: 160px;
  border-radius: 12px;
}
.mc:hover .mc-photo { border-color: var(--orange); }

.mc-photo-ph {
  width: 130px; height: 130px; border-radius: 10px;
  background: var(--black); color: var(--orange);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--ff-head); font-size: 2.2rem; letter-spacing: 1px;
  border: 3px solid var(--border);
  transition: border-color var(--ease);
}
.mc-president .mc-photo-ph {
  width: 160px; height: 160px;
  border-radius: 12px; font-size: 2.8rem;
}
.mc:hover .mc-photo-ph { border-color: var(--orange); }

.mc-photo-overlay {
  position: absolute; inset: 0; border-radius: 10px;
  background: rgba(13,13,13,.6);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity var(--ease);
  color: var(--orange); font-size: 1.4rem;
}
.mc-president .mc-photo-overlay { border-radius: 12px; }
.mc:hover .mc-photo-overlay { opacity: 1; }

/* ── CARD TEXT ── */
.mc-name {
  font-family: var(--ff-head);
  font-size: .95rem; letter-spacing: .7px;
  color: var(--black); margin-bottom: .4rem; line-height: 1.2;
}
.mc-president .mc-name { font-size: 1.15rem; }

.mc-role-badge {
  display: inline-block;
  background: var(--orange-lt); color: var(--orange-dk);
  font-size: .65rem; font-weight: 700; letter-spacing: .5px; text-transform: uppercase;
  padding: .2rem .75rem; border-radius: 999px;
  line-height: 1.5;
}

.mc-socials {
  display: flex; gap: .4rem; justify-content: center;
  margin-top: .8rem; opacity: 0; transition: opacity var(--ease);
}
.mc:hover .mc-socials { opacity: 1; }
.mc-socials a {
  width: 26px; height: 26px; border-radius: 50%;
  background: var(--black); color: var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: .72rem; text-decoration: none;
  transition: background var(--ease);
}
.mc-socials a:hover { background: var(--orange); }

/* ═══════════════════════════════════════
   MODAL — flex, negative-margin photo
═══════════════════════════════════════ */
.modal-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.75); backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem;
  opacity: 0; pointer-events: none; transition: opacity .22s ease;
}
.modal-overlay.open { opacity: 1; pointer-events: all; }

.modal-box {
  background: var(--white);
  border-radius: 20px; max-width: 460px; width: 100%;
  box-shadow: 0 32px 80px rgba(0,0,0,.4);
  overflow: hidden;            /* clips band border-radius */
  transform: translateY(30px) scale(.95);
  transition: transform .3s cubic-bezier(.34,1.4,.64,1);
  display: flex; flex-direction: column;
}
.modal-overlay.open .modal-box { transform: none; }

/* Black band */
.modal-band {
  flex-shrink: 0; height: 80px; position: relative;
  background: var(--black);
  background-image: repeating-linear-gradient(
    -52deg, transparent 0, transparent 16px,
    rgba(255,111,15,.07) 16px, rgba(255,111,15,.07) 18px
  );
}
.modal-band::after {
  content: ''; position: absolute;
  bottom: 0; left: 0; right: 0; height: 3px;
  background: var(--orange);
}
.modal-close {
  position: absolute; top: .8rem; right: .8rem; z-index: 2;
  width: 32px; height: 32px; border-radius: 50%;
  background: rgba(255,255,255,.12);
  border: 1.5px solid rgba(255,255,255,.25);
  color: var(--white); cursor: pointer; font-size: .88rem;
  display: flex; align-items: center; justify-content: center;
  transition: background var(--ease), border-color var(--ease);
}
.modal-close:hover { background: var(--orange); border-color: var(--orange); }

/* Body — flex col, photo uses negative margin to overlap band */
.modal-body {
  display: flex; flex-direction: column; align-items: center;
  padding: 0 2rem 2rem; text-align: center;
}
.modal-photo-wrap {
  margin-top: -44px;   /* pull up over band bottom */
  margin-bottom: .9rem;
  flex-shrink: 0;
  line-height: 0; z-index: 1; position: relative;
}
.modal-photo {
  width: 88px; height: 88px; border-radius: 50%; object-fit: cover;
  border: 4px solid var(--white);
  box-shadow: 0 4px 20px rgba(0,0,0,.3);
  display: block; background: #333;
}
.modal-photo-ph {
  width: 88px; height: 88px; border-radius: 50%;
  border: 4px solid var(--white);
  box-shadow: 0 4px 20px rgba(0,0,0,.3);
  background: var(--black); color: var(--orange);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--ff-head); font-size: 2rem; letter-spacing: 1px;
}
.modal-photo-loading {
  width: 88px; height: 88px; border-radius: 50%;
  border: 4px solid var(--white); background: #1a1a1a;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 20px rgba(0,0,0,.3);
}
.modal-photo-loading::after {
  content: ''; width: 24px; height: 24px; border-radius: 50%;
  border: 3px solid var(--orange); border-top-color: transparent;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.modal-name {
  font-family: var(--ff-head);
  font-size: 1.5rem; letter-spacing: 1px;
  color: var(--black); line-height: 1.15; margin: 0 0 .5rem;
}
.modal-role {
  display: inline-block;
  background: var(--orange); color: var(--white);
  font-size: .7rem; font-weight: 700; letter-spacing: .7px;
  text-transform: uppercase; padding: .3rem 1rem;
  border-radius: 999px; margin-bottom: 1.1rem;
}
.modal-divider {
  width: 36px; height: 3px; border-radius: 999px;
  background: var(--orange); opacity: .3;
  margin: 0 auto 1.1rem;
}
.modal-desc {
  font-size: .92rem; color: var(--ink-s);
  line-height: 1.85; text-align: left;
  margin: 0 0 1.4rem; width: 100%;
}
.modal-desc:empty { display: none; }

.modal-contacts {
  display: flex; justify-content: center;
  gap: .6rem; flex-wrap: wrap; width: 100%;
}
.modal-contact-btn {
  display: inline-flex; align-items: center; gap: .4rem;
  padding: .48rem 1.1rem; border-radius: 999px;
  font-size: .82rem; font-weight: 600; text-decoration: none;
  transition: all var(--ease);
}
.mc-btn-email    { background: var(--orange-lt); color: var(--orange-dk); border: 1.5px solid #FFD4B0; }
.mc-btn-email:hover    { background: var(--orange); color: var(--white); border-color: var(--orange); }
.mc-btn-linkedin { background: #EBF2FF; color: #0A66C2; border: 1.5px solid #C5D9F5; }
.mc-btn-linkedin:hover { background: #0A66C2; color: var(--white); border-color: #0A66C2; }

/* ═══════════════════════════════════════
   INTER-RELIGIOUS BANNER
═══════════════════════════════════════ */
.ir-banner {
  background: var(--black);
  border-radius: 16px; padding: 2.75rem;
  color: var(--white); text-align: center;
  position: relative; overflow: hidden;
}
.ir-banner::before {
  content: '';
  position: absolute; inset: 0;
  background: repeating-linear-gradient(
    -45deg,
    transparent 0, transparent 18px,
    rgba(255,111,15,.06) 18px, rgba(255,111,15,.06) 20px
  );
}
.ir-banner::after {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 4px;
  background: var(--orange);
}
.ir-banner h3 {
  font-family: var(--ff-head); font-size: 1.6rem; letter-spacing: 1.5px;
  color: var(--white); margin-bottom: .85rem; position: relative;
}
.ir-banner h3 i { color: var(--orange); }
.ir-banner p { color: rgba(255,255,255,.75); font-size: 1rem; margin: 0; position: relative; }

/* ═══════════════════════════════════════
   VALUES
═══════════════════════════════════════ */
.value-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px; padding: 2rem 1.5rem; text-align: center;
  box-shadow: var(--sh-card);
  transition: transform var(--ease), box-shadow var(--ease), border-color var(--ease);
  position: relative; overflow: hidden;
}
.value-card::before {
  content: '';
  position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
  background: var(--orange); transform: scaleX(0);
  transform-origin: left; transition: transform var(--ease);
}
.value-card:hover { transform: translateY(-5px); box-shadow: var(--sh-hov); border-color: #FFD4B0; }
.value-card:hover::before { transform: scaleX(1); }
.value-icon {
  width: 60px; height: 60px; border-radius: 14px;
  background: var(--black); color: var(--orange);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.55rem; margin: 0 auto 1.1rem;
}
.value-card h4 { font-family: var(--ff-head); letter-spacing: .8px; color: var(--black); margin-bottom: .5rem; font-size: 1.05rem; }
.value-card p  { font-size: .875rem; color: var(--ink-s); margin: 0; }

/* Scroll reveal */
.reveal { opacity: 0; transform: translateY(22px); transition: opacity .5s ease, transform .5s ease; }
.reveal.visible { opacity: 1; transform: none; }
</style>

<!-- ══ SOBRE NÓS ══════════════════════════════ -->
<section class="py-5" style="background:var(--white);">
  <div class="container">

    <div class="sec-head reveal">
      <span class="sec-label">Sobre Nós</span>
      <h2 class="sec-title">Plataforma <span>Inter-Religiosa</span><br>de Comunicação para a Saúde</h2>
      <div class="sec-rule"><span></span><span></span><span></span></div>
    </div>

    <div class="row mb-5">
      <div class="col-lg-10 mx-auto reveal">
        <div class="about-card">
          <h3 style="font-size:1.5rem;justify-content:center;">PIRCOM</h3>
          <p class="lead" style="text-align:justify;color:var(--ink-s);line-height:1.85;margin-bottom:1rem;">
            A <strong>Plataforma Inter-Religiosa de Comunicação para a Saúde (PIRCOM)</strong> é uma organização baseada na fé,
            empenhada e comprometida com a melhoria da qualidade de vida e das condições de saúde da população Moçambicana
            através da mobilização das comunidades para se empenharem na eliminação da Malária e na redução da incidência
            e impacto de outros problemas de saúde pública.
          </p>
          <p style="text-align:justify;color:var(--ink-s);line-height:1.85;margin:0;">
            Criada em <strong>19 de abril de 2006</strong>, a PIRCOM é considerada a <strong>primeira aliança múltipla baseada na fé</strong>
            através da colaboração entre comunidades <strong>Cristãs, Muçulmanas, Hindus e Bahai</strong> visando contribuir
            na melhoria do comportamento dos beneficiários prioritários em relação à prevenção e tratamento da Malária,
            Nutrição, Saúde Materno, Neonatal e Infantil e HIV.
          </p>
        </div>
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-lg-6 mb-4 reveal" style="transition-delay:.1s">
        <div class="about-card h-100">
          <h3><i class="bi bi-bullseye"></i> Nossa Missão</h3>
          <p style="text-align:justify;color:var(--ink-s);line-height:1.85;margin:0;">
            <?php echo $config ? nl2br(htmlspecialchars($config['missao'])) :
            'Mobilizar comunidades religiosas para promover a saúde pública em Moçambique, focando na eliminação da malária,
            nutrição, saúde materno-infantil e HIV/SIDA através de comunicação para mudança social e comportamental
            baseada em valores religiosos e escrituras sagradas.'; ?>
          </p>
        </div>
      </div>
      <div class="col-lg-6 mb-4 reveal" style="transition-delay:.2s">
        <div class="about-card h-100">
          <h3><i class="bi bi-eye"></i> Nossa Visão</h3>
          <p style="text-align:justify;color:var(--ink-s);line-height:1.85;margin:0;">
            <?php echo $config ? nl2br(htmlspecialchars($config['visao'])) :
            'Ser a plataforma de referência em Moçambique na articulação inter-religiosa para a promoção da saúde pública,
            contribuindo para comunidades mais saudáveis e resilientes através da colaboração baseada na fé.'; ?>
          </p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══ LIDERANÇA ══════════════════════════════ -->
<section id="team" class="team-section">

  <div class="team-band">
    <div class="container">
      <span class="sec-label">Orgãos Sociais</span>
      <h2 class="sec-title" style="color:var(--white);">Nossa <span>Liderança</span></h2>
      <div class="sec-rule"><span></span><span></span><span></span></div>
      <p class="sec-sub">Conselho de Direcção da PIRCOM</p>
    </div>
  </div>

  <div class="container">

    <?php if (empty($members_by_cat)): ?>
      <p style="text-align:center;color:var(--ink-m);padding:3rem 0;">Nenhum membro publicado ainda.</p>
    <?php else: ?>

      <?php foreach ($cat_labels as $cat_key => $cat_label):
        if (empty($members_by_cat[$cat_key])) continue;
        $grp  = $members_by_cat[$cat_key];
        $pres = $grp[0];
        $rest = array_slice($grp, 1);
      ?>
      <div class="cat-group reveal">
        <h3 class="cat-title"><?= $cat_label ?></h3>

        <!-- Destaque -->
        <div class="grid-president">
          <?php $ini = initials($pres['nome']); ?>
          <div class="mc mc-president"
               data-id="<?= $pres['id'] ?>"
               data-nome="<?= htmlspecialchars($pres['nome'], ENT_QUOTES) ?>"
               data-cargo="<?= htmlspecialchars($pres['cargo'], ENT_QUOTES) ?>"
               data-desc="<?= htmlspecialchars($pres['descricao'] ?? '', ENT_QUOTES) ?>"
               data-email="<?= htmlspecialchars($pres['email'] ?? '', ENT_QUOTES) ?>"
               data-linkedin="<?= htmlspecialchars($pres['linkedin'] ?? '', ENT_QUOTES) ?>"
               data-has-foto="<?= (int)$pres['has_foto'] ?>"
               data-initials="<?= $ini ?>"
               onclick="openModal(this)">
            <div class="mc-photo-wrap">
              <?php if ($pres['has_foto']): ?>
                <img class="mc-photo" style="width:164px;height:164px;border-radius:14px;"
                     src="foto.php?id=<?= $pres['id'] ?>"
                     alt="<?= htmlspecialchars($pres['nome']) ?>" loading="lazy">
              <?php else: ?>
                <div class="mc-photo-ph" style="width:164px;height:164px;border-radius:14px;font-size:2.6rem;"><?= $ini ?></div>
              <?php endif; ?>
              <div class="mc-photo-overlay" style="border-radius:14px;"><i class="bi bi-zoom-in"></i></div>
            </div>
            <div class="mc-name"><?= htmlspecialchars($pres['nome']) ?></div>
            <span class="mc-role-badge"><?= htmlspecialchars($pres['cargo']) ?></span>
            <?php if (!empty($pres['email']) || !empty($pres['linkedin'])): ?>
            <div class="mc-socials">
              <?php if (!empty($pres['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($pres['email']) ?>" onclick="event.stopPropagation()"><i class="bi bi-envelope-fill"></i></a>
              <?php endif; ?>
              <?php if (!empty($pres['linkedin'])): ?>
                <a href="<?= htmlspecialchars($pres['linkedin']) ?>" target="_blank" onclick="event.stopPropagation()"><i class="bi bi-linkedin"></i></a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Restantes -->
        <?php if (!empty($rest)): ?>
        <div class="grid-members">
          <?php foreach ($rest as $idx => $m):
            $ini2 = initials($m['nome']);
          ?>
          <div class="mc reveal" style="transition-delay:<?= ($idx * .07) ?>s"
               data-id="<?= $m['id'] ?>"
               data-nome="<?= htmlspecialchars($m['nome'], ENT_QUOTES) ?>"
               data-cargo="<?= htmlspecialchars($m['cargo'], ENT_QUOTES) ?>"
               data-desc="<?= htmlspecialchars($m['descricao'] ?? '', ENT_QUOTES) ?>"
               data-email="<?= htmlspecialchars($m['email'] ?? '', ENT_QUOTES) ?>"
               data-linkedin="<?= htmlspecialchars($m['linkedin'] ?? '', ENT_QUOTES) ?>"
               data-has-foto="<?= (int)$m['has_foto'] ?>"
               data-initials="<?= $ini2 ?>"
               onclick="openModal(this)">
            <div class="mc-photo-wrap">
              <?php if ($m['has_foto']): ?>
                <img class="mc-photo"
                     src="foto.php?id=<?= $m['id'] ?>"
                     alt="<?= htmlspecialchars($m['nome']) ?>" loading="lazy">
              <?php else: ?>
                <div class="mc-photo-ph"><?= $ini2 ?></div>
              <?php endif; ?>
              <div class="mc-photo-overlay"><i class="bi bi-zoom-in"></i></div>
            </div>
            <div class="mc-name"><?= htmlspecialchars($m['nome']) ?></div>
            <span class="mc-role-badge"><?= htmlspecialchars($m['cargo']) ?></span>
            <?php if (!empty($m['email']) || !empty($m['linkedin'])): ?>
            <div class="mc-socials">
              <?php if (!empty($m['email'])): ?>
                <a href="mailto:<?= htmlspecialchars($m['email']) ?>" onclick="event.stopPropagation()"><i class="bi bi-envelope-fill"></i></a>
              <?php endif; ?>
              <?php if (!empty($m['linkedin'])): ?>
                <a href="<?= htmlspecialchars($m['linkedin']) ?>" target="_blank" onclick="event.stopPropagation()"><i class="bi bi-linkedin"></i></a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

      </div>
      <?php endforeach; ?>

    <?php endif; ?>

    <!-- Inter-religious banner -->
    <div class="row mt-2 reveal">
      <div class="col-lg-10 mx-auto">
        <div class="ir-banner">
          <h3><i class="bi bi-people-fill"></i> Colaboração Inter-Religiosa</h3>
          <p>A PIRCOM reúne líderes e comunidades de diversas religiões (Cristãos, Muçulmanos, Hindus e Bahai)
             trabalhando juntos pela saúde e bem-estar da população moçambicana.</p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══ VALORES ════════════════════════════════ -->
<section class="py-5" style="background:var(--off-white);">
  <div class="container">
    <div class="sec-head reveal">
      <span class="sec-label">O Que Nos Guia</span>
      <h2 class="sec-title">Nossos <span>Valores</span></h2>
      <div class="sec-rule"><span></span><span></span><span></span></div>
      <p class="sec-sub">Princípios que orientam cada acção da PIRCOM</p>
    </div>
    <div class="row g-4">
      <?php
      $values = [
        ['bi-heart-pulse','Saúde para Todos',     'Compromisso com a saúde pública e bem-estar comunitário', '.1s'],
        ['bi-peace',      'Diálogo Inter-Religioso','Respeito e colaboração entre todas as religiões',       '.2s'],
        ['bi-people',     'Comunidade',            'Mobilização e empoderamento comunitário',                '.3s'],
        ['bi-shield-check','Integridade',          'Transparência, ética e responsabilidade em tudo que fazemos', '.4s'],
      ];
      foreach ($values as [$ico, $title, $desc, $delay]):
      ?>
      <div class="col-lg-3 col-md-6 reveal" style="transition-delay:<?= $delay ?>">
        <div class="value-card">
          <div class="value-icon"><i class="bi <?= $ico ?>"></i></div>
          <h4><?= $title ?></h4>
          <p><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ MODAL ══════════════════════════════════ -->
<div class="modal-overlay" id="memberModal" role="dialog" aria-modal="true" aria-labelledby="modalName">
  <div class="modal-box">

    <!-- Black band (close btn only — no photo inside) -->
    <div class="modal-band">
      <button class="modal-close" onclick="closeModal()" aria-label="Fechar">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <!-- Body: photo is first child, pulled up with negative margin -->
    <div class="modal-body">
      <div class="modal-photo-wrap" id="modalPhotoWrap"></div>
      <h2 class="modal-name" id="modalName"></h2>
      <span class="modal-role" id="modalRole"></span>
      <div class="modal-divider"></div>
      <p class="modal-desc" id="modalDesc"></p>
      <div class="modal-contacts" id="modalContacts"></div>
    </div>

  </div>
</div>

<script>
function openModal(el) {
  const d = el.dataset;
  document.getElementById('modalName').textContent = d.nome;
  document.getElementById('modalRole').textContent = d.cargo;
  document.getElementById('modalDesc').textContent = d.desc || '';

  const wrap = document.getElementById('modalPhotoWrap');
  if (d.hasFoto === '1') {
    wrap.innerHTML = '<div class="modal-photo-loading"></div>';
    const img = new Image();
    img.className = 'modal-photo';
    img.alt = d.nome;
    img.onload  = () => { wrap.innerHTML = ''; wrap.appendChild(img); };
    img.onerror = () => { wrap.innerHTML = `<div class="modal-photo-ph">${d.initials}</div>`; };
    img.src = 'foto.php?id=' + d.id;
  } else {
    wrap.innerHTML = `<div class="modal-photo-ph">${d.initials}</div>`;
  }

  let ct = '';
  if (d.email)
    ct += `<a href="mailto:${d.email}" class="modal-contact-btn mc-btn-email"><i class="bi bi-envelope-fill"></i>${d.email}</a>`;
  if (d.linkedin)
    ct += `<a href="${d.linkedin}" target="_blank" class="modal-contact-btn mc-btn-linkedin"><i class="bi bi-linkedin"></i>LinkedIn</a>`;
  document.getElementById('modalContacts').innerHTML = ct;

  document.getElementById('memberModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('memberModal').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('memberModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// Scroll reveal
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
  });
}, { threshold: .1 });
document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
</script>

<?php
include 'includes/footer.php';
$conn->close();
?>