<?php
/**
 * Header / Navbar Global
 * PIRCOM – Plataforma Inter-Religiosa de Comunicação para a Saúde
 *
 * Variáveis esperadas (opcionais, definir ANTES do include):
 *   $page_title    string  – título da aba
 *   $include_swiper bool   – carrega CSS do Swiper
 */

// ── Helpers ──────────────────────────────────────────────────────────────────
$_currentPage = basename($_SERVER['PHP_SELF']);

/**
 * Retorna 'active' se a página actual corresponder ao ficheiro dado.
 */
function nav_is_active(string $page): string {
    global $_currentPage;
    return $_currentPage === $page ? 'active' : '';
}

/**
 * Retorna 'active' se a página actual estiver no array dado.
 */
function nav_is_active_group(array $pages): string {
    global $_currentPage;
    return in_array($_currentPage, $pages, true) ? 'active' : '';
}

// ── Título padrão ────────────────────────────────────────────────────────────
$_pageTitle = isset($page_title) && trim($page_title) !== ''
    ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' | PIRCOM'
    : 'PIRCOM – Plataforma Inter-Religiosa de Comunicação para a Saúde';
?>
<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A Plataforma Inter-Religiosa de Comunicação para a Saúde (PIRCOM) é uma organização baseada na fé, empenhada na melhoria da qualidade de vida das comunidades.">
    <meta name="author" content="Conexar Management – Digital Solutions Lda">
    <meta name="theme-color" content="#FF6F0F">

    <title><?= $_pageTitle ?></title>

    <link rel="icon" href="assets/img/hello.png" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:opsz,wght@9..144,300;9..144,600;9..144,700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <?php if (!empty($include_swiper)): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <?php endif; ?>

    <style>
        /* ═══════════════════════════════════════════════════════════════════
           DESIGN TOKENS
        ═══════════════════════════════════════════════════════════════════ */
        :root {
            --clr-brand:        #FF6F0F;
            --clr-brand-dark:   #D4560A;
            --clr-brand-light:  #FFF3EC;
            --clr-ink:          #0D0D0D;
            --clr-ink-muted:    #5A5A6A;
            --clr-surface:      #FFFFFF;
            --clr-border:       #EDEDED;

            --font-display: 'Fraunces', Georgia, serif;
            --font-body:    'Plus Jakarta Sans', system-ui, sans-serif;

            --nav-height:   72px;
            --radius-md:    10px;
            --radius-lg:    16px;

            --shadow-sm:  0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-nav: 0 4px 24px rgba(0,0,0,.08);
            --shadow-drop: 0 8px 32px rgba(255,111,15,.18);

            --transition: 220ms cubic-bezier(.4,0,.2,1);
        }

        /* ═══════════════════════════════════════════════════════════════════
           GLOBAL RESET / BASE
        ═══════════════════════════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            color: var(--clr-ink);
            background: var(--clr-surface);
            padding-top: var(--nav-height);
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════════════════════════════════════════════════════════════════
           NAVBAR
        ═══════════════════════════════════════════════════════════════════ */
        .pircom-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1030;
            height: var(--nav-height);
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(14px) saturate(180%);
            -webkit-backdrop-filter: blur(14px) saturate(180%);
            border-bottom: 1px solid var(--clr-border);
            box-shadow: var(--shadow-nav);
            transition: background var(--transition), box-shadow var(--transition);
        }

        /* Subtil accent bar no topo */
        .pircom-nav::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--clr-brand) 0%, #FFAA6B 50%, var(--clr-brand) 100%);
            background-size: 200% 100%;
            animation: shimmer 4s linear infinite;
        }

        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .pircom-nav .container {
            height: 100%;
            display: flex;
            align-items: center;
        }

        /* ── Brand ── */
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
            transition: opacity var(--transition);
        }

        .nav-brand:hover { opacity: .85; }

        .nav-brand img {
            height: 42px;
            width: auto;
            display: block;
        }

        .nav-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .nav-brand-name {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--clr-brand);
            letter-spacing: -.01em;
        }

        .nav-brand-tagline {
            font-size: .62rem;
            font-weight: 500;
            color: var(--clr-ink-muted);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        /* ── Collapse ── */
        .navbar-collapse { flex-grow: 0; }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* ── Nav Link ── */
        .nav-menu .nav-item > .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 8px 13px;
            font-family: var(--font-body);
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--clr-ink-muted);
            text-decoration: none;
            border-radius: var(--radius-md);
            position: relative;
            white-space: nowrap;
            transition: color var(--transition), background var(--transition);
        }

        .nav-menu .nav-item > .nav-link:hover,
        .nav-menu .nav-item > .nav-link.active {
            color: var(--clr-brand);
            background: var(--clr-brand-light);
        }

        /* Indicador activo */
        .nav-menu .nav-item > .nav-link.active::before {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 20%; right: 20%;
            height: 3px;
            background: var(--clr-brand);
            border-radius: 99px;
        }

        /* ── Dropdown ── */
        .nav-menu .dropdown-toggle::after {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: none;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") center/contain no-repeat;
            vertical-align: middle;
            margin-left: 1px;
            transition: transform var(--transition);
        }

        .nav-menu .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        .pircom-dropdown {
            min-width: 220px;
            padding: 8px;
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 12px 40px rgba(0,0,0,.12);
            background: var(--clr-surface);
            margin-top: 8px !important;
        }

        .pircom-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            font-size: .83rem;
            font-weight: 600;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: var(--clr-ink-muted);
            border-radius: var(--radius-md);
            transition: color var(--transition), background var(--transition), transform var(--transition);
        }

        .pircom-dropdown .dropdown-item i {
            font-size: 1rem;
            color: var(--clr-brand);
            width: 20px;
            text-align: center;
        }

        .pircom-dropdown .dropdown-item:hover,
        .pircom-dropdown .dropdown-item.active {
            color: var(--clr-brand);
            background: var(--clr-brand-light);
            transform: translateX(4px);
        }

        .pircom-dropdown .dropdown-divider {
            margin: 6px 0;
            border-color: var(--clr-border);
        }

        /* ── CTA button ── */
        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-left: 8px;
            padding: 9px 20px;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #fff !important;
            background: var(--clr-brand);
            border-radius: 99px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(255,111,15,.35);
            transition: background var(--transition), box-shadow var(--transition), transform var(--transition);
        }

        .nav-cta:hover {
            background: var(--clr-brand-dark);
            box-shadow: var(--shadow-drop);
            transform: translateY(-1px);
        }

        .nav-cta i { font-size: .9rem; }

        /* ── Toggler ── */
        .nav-toggler {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1.5px solid var(--clr-border);
            border-radius: var(--radius-md);
            background: transparent;
            cursor: pointer;
            padding: 0;
            transition: border-color var(--transition), background var(--transition);
        }

        .nav-toggler:hover {
            border-color: var(--clr-brand);
            background: var(--clr-brand-light);
        }

        .nav-toggler i { font-size: 1.25rem; color: var(--clr-ink); }

        /* ═══════════════════════════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════════════════════════ */
        @media (max-width: 991.98px) {
            .nav-toggler { display: flex; }

            .navbar-collapse {
                position: absolute;
                top: var(--nav-height);
                left: 0; right: 0;
                background: var(--clr-surface);
                border-bottom: 3px solid var(--clr-brand);
                box-shadow: 0 16px 40px rgba(0,0,0,.1);
                padding: 16px;
                max-height: calc(100vh - var(--nav-height));
                overflow-y: auto;
                display: none;
            }

            .navbar-collapse.show { display: block; }

            .nav-menu {
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
            }

            .nav-menu .nav-item > .nav-link {
                width: 100%;
                justify-content: space-between;
                padding: 12px 16px;
                font-size: .85rem;
            }

            .nav-menu .nav-item > .nav-link.active::before { display: none; }

            .pircom-dropdown {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                border: none;
                background: #FAFAFA;
                margin: 4px 0 0 !important;
                padding: 4px 0 4px 16px;
            }

            .pircom-dropdown .dropdown-item {
                font-size: .82rem;
                padding: 9px 12px;
            }

            .pircom-dropdown .dropdown-item:hover {
                transform: none;
            }

            .nav-cta {
                margin: 12px 0 0;
                justify-content: center;
                padding: 12px 20px;
            }

            .nav-brand-tagline { display: none; }
        }

        @media (max-width: 575.98px) {
            :root { --nav-height: 64px; }
            .nav-brand img { height: 36px; }
            .nav-brand-name { font-size: 1.15rem; }
        }

        /* ═══════════════════════════════════════════════════════════════════
           UTILITÁRIOS GLOBAIS (partilhados com o resto do site)
        ═══════════════════════════════════════════════════════════════════ */
        .section-title { text-align: center; margin: 72px 0 48px; }

        .section-title h2 {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 700;
            color: var(--clr-ink);
            position: relative;
            display: inline-block;
            padding-bottom: 18px;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 56px; height: 4px;
            background: var(--clr-brand);
            border-radius: 99px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--clr-ink-muted);
            margin-top: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Cards */
        .card {
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition), box-shadow var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 48px rgba(0,0,0,.1);
        }

        /* Buttons */
        .btn-primary {
            background: var(--clr-brand);
            border: none;
            padding: 11px 28px;
            font-weight: 700;
            font-size: .9rem;
            letter-spacing: .04em;
            border-radius: 99px;
            transition: background var(--transition), box-shadow var(--transition), transform var(--transition);
            box-shadow: 0 4px 14px rgba(255,111,15,.3);
        }

        .btn-primary:hover {
            background: var(--clr-brand-dark);
            box-shadow: var(--shadow-drop);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border: 2px solid var(--clr-brand);
            color: var(--clr-brand);
            padding: 11px 28px;
            font-weight: 700;
            font-size: .9rem;
            border-radius: 99px;
            transition: all var(--transition);
        }

        .btn-outline-primary:hover {
            background: var(--clr-brand);
            color: #fff;
            box-shadow: var(--shadow-drop);
            transform: translateY(-1px);
        }

        /* Icon Box */
        .icon-box {
            background: var(--clr-surface);
            border-radius: var(--radius-lg);
            padding: 32px;
            border: 1px solid var(--clr-border);
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition), box-shadow var(--transition);
            height: 100%;
        }

        .icon-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 48px rgba(255,111,15,.12);
        }

        .icon-box i {
            font-size: 2.75rem;
            color: var(--clr-brand);
            margin-bottom: 16px;
            display: block;
        }

        .icon-box h4 {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--clr-ink);
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════════════════════════════════ -->
<header class="pircom-nav" role="banner">
    <div class="container">

        <!-- Brand -->
        <a href="index.php" class="nav-brand" aria-label="PIRCOM – Página Inicial">
            <img src="assets/pircom.png" alt="PIRCOM" width="42" height="42">
            <div class="nav-brand-text">
            </div>
        </a>

        <!-- Toggler mobile -->
        <button class="nav-toggler ms-auto me-0 d-lg-none"
                aria-label="Abrir menu"
                aria-expanded="false"
                aria-controls="pircomNav"
                id="navToggler">
            <i class="bi bi-list" id="navTogglerIcon"></i>
        </button>

        <!-- Menu -->
        <nav id="pircomNav" class="navbar-collapse ms-auto" aria-label="Navegação principal">
            <ul class="nav-menu" role="list">

                <li class="nav-item">
                    <a class="nav-link <?= nav_is_active('index.php') ?>" href="index.php">
                        Início
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= nav_is_active_group(['sobre-nos.php','quem-somos.php','mapa-cobertura.php']) ?>"
                       href="#"
                       id="ddSobre"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Sobre
                    </a>
                    <ul class="dropdown-menu pircom-dropdown" aria-labelledby="ddSobre">
                        <li>
                            <a class="dropdown-item <?= nav_is_active('sobre-nos.php') ?>" href="sobre-nos.php">
                                <i class="bi bi-info-circle"></i> Quem Somos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= nav_is_active('orgaos-sociais.php') ?>" href="orgaos-sociais.php">
                                <i class="bi bi-people"></i> Órgãos Sociais
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item <?= nav_is_active('mapa-cobertura.php') ?>" href="mapa-cobertura.php">
                                <i class="bi bi-geo-alt"></i> Cobertura Geográfica
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= nav_is_active('noticias.php') ?>" href="noticias.php">
                        Notícias
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= nav_is_active_group(['galeria.php','documentos.php']) ?>"
                       href="#"
                       id="ddMultimedia"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Multimédia
                    </a>
                    <ul class="dropdown-menu pircom-dropdown" aria-labelledby="ddMultimedia">
                        <li>
                            <a class="dropdown-item <?= nav_is_active('galeria.php') ?>" href="galeria.php">
                                <i class="bi bi-images"></i> Galeria
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= nav_is_active('documentos.php') ?>" href="documentos.php">
                                <i class="bi bi-file-earmark-text"></i> Documentos
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= nav_is_active('eventos.php') ?>" href="eventos.php">
                        Eventos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= nav_is_active_group(['movimentos.php','movimento-detalhes.php']) ?>"
                       href="movimentos.php">
                        Movimentos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-cta" href="doacoes.php" aria-label="Fazer uma doação">
                        <i class="bi bi-heart-fill"></i> Doar
                    </a>
                </li>

            </ul>
        </nav>

    </div>
</header>

<!-- Conteúdo principal da página -->
<main id="main-content">