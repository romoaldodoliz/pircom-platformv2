<?php
// Incluir helper de autenticação
require_once(__DIR__ . '/helpers/auth.php');

// Verificar se usuário está autenticado
requireAuth();

// Verificar timeout de sessão
checkSessionTimeout();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>PIRCOM - Painel Administrativo</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/pircom.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>

    <!-- Modern Styles -->
    <style>
      * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      }
      
      :root {
        --primary-red: #FF6F0F;
        --primary-hover: #E05A00;
        --dark-bg: #0f0f0f;
        --darker-bg: #080808;
        --card-bg: #1a1a1a;
        --border-color: rgba(255, 255, 255, 0.08);
        --text-primary: #ffffff;
        --text-secondary: #a0a0a0;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
        --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.3);
      }

      /* ========== SIDEBAR MODERN ========== */
      #layout-menu.layout-menu {
        background: linear-gradient(180deg, var(--darker-bg) 0%, var(--dark-bg) 100%);
        border-right: 1px solid var(--border-color);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.4);
        width: 280px;
      }

      /* Logo Area */
      #layout-menu .app-brand {
        padding: 24px 20px;
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.1) 0%, transparent 100%);
        border-bottom: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
      }

      #layout-menu .app-brand::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--primary-red), transparent);
      }

      #layout-menu .app-brand-link {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
      }

      #layout-menu .app-brand-link img {
        filter: brightness(1.1) drop-shadow(0 4px 12px rgba(255, 111, 15, 0.3));
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      }

      #layout-menu .app-brand-link:hover img {
        transform: scale(1.08) translateY(-2px);
        filter: brightness(1.2) drop-shadow(0 6px 20px rgba(255, 111, 15, 0.5));
      }

      /* Menu Sections */
      #layout-menu .menu-header-text {
        color: var(--text-secondary) !important;
        font-weight: 700;
        font-size: 10px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin: 24px 20px 12px;
        padding-left: 16px;
        position: relative;
      }

      #layout-menu .menu-header-text::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-red), transparent);
        border-radius: 2px;
      }

      /* Menu Items */
      #layout-menu .menu-inner {
        padding: 16px 12px;
      }

      #layout-menu .menu-item {
        margin: 4px 0;
      }

      #layout-menu .menu-item > .menu-link {
        border-radius: 12px;
        padding: 14px 16px;
        margin: 0 8px;
        color: var(--text-secondary) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        font-weight: 500;
      }

      #layout-menu .menu-item > .menu-link::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(255, 111, 15, 0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      #layout-menu .menu-item > .menu-link .menu-icon {
        color: var(--text-secondary);
        font-size: 1.3rem;
        margin-right: 14px;
        transition: all 0.3s ease;
      }

      /* Hover State */
      #layout-menu .menu-item:hover > .menu-link {
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-primary) !important;
        transform: translateX(4px);
      }

      #layout-menu .menu-item:hover > .menu-link::before {
        opacity: 1;
      }

      #layout-menu .menu-item:hover > .menu-link .menu-icon {
        color: var(--primary-red);
        transform: scale(1.1);
      }

      /* Active State */
      #layout-menu .menu-item.active > .menu-link {
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.15) 0%, rgba(255, 111, 15, 0.05) 100%);
        color: var(--text-primary) !important;
        box-shadow: inset 0 0 0 1px rgba(255, 111, 15, 0.3), var(--shadow-sm);
        font-weight: 600;
      }

      #layout-menu .menu-item.active > .menu-link::after {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        bottom: 20%;
        width: 4px;
        background: linear-gradient(180deg, var(--primary-red), rgba(255, 111, 15, 0.4));
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 12px rgba(255, 111, 15, 0.6);
      }

      #layout-menu .menu-item.active > .menu-link .menu-icon {
        color: var(--primary-red);
        filter: drop-shadow(0 0 8px rgba(255, 111, 15, 0.4));
      }

      /* Scrollbar - Removido efeito ao scroll */
      #layout-menu .ps__rail-y {
        display: none !important;
      }

      #layout-menu .ps__thumb-y {
        display: none !important;
      }

      /* Scrollbar nativa customizada */
      #layout-menu .menu-inner::-webkit-scrollbar {
        width: 6px;
      }

      #layout-menu .menu-inner::-webkit-scrollbar-track {
        background: transparent;
      }

      #layout-menu .menu-inner::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
      }

      #layout-menu .menu-inner::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 111, 15, 0.3);
      }
      
      /* Shadow ao fazer scroll - removida */
      #layout-menu .menu-inner-shadow {
        display: none !important;
      }

      /* ========== NAVBAR MODERN ========== */
      .layout-navbar {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
      }

      .navbar-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        gap: 2rem;
      }

      /* Welcome Section */
      .welcome-section {
        flex: 1;
      }

      .welcome-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      .welcome-name {
        color: var(--primary-red);
        -webkit-text-fill-color: var(--primary-red);
        font-weight: 800;
        position: relative;
      }

      .welcome-subtitle {
        font-size: 0.875rem;
        color: #666;
        margin-top: 4px;
        font-weight: 500;
      }

      /* User Section */
      .user-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
      }

      /* Notification Bell */
      .notification-bell {
        position: relative;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f5f5f5;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
      }

      .notification-bell:hover {
        background: linear-gradient(135deg, rgba(251, 10, 10, 0.1) 0%, rgba(251, 10, 10, 0.05) 100%);
        border-color: rgba(251, 10, 10, 0.2);
        transform: translateY(-2px);
      }

      .notification-bell i {
        font-size: 1.4rem;
        color: #333;
      }

      .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: linear-gradient(135deg, var(--primary-red) 0%, #ff3333 100%);
        color: white;
        border-radius: 10px;
        min-width: 20px;
        height: 20px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(251, 10, 10, 0.4);
        animation: pulse 2s infinite;
      }

      @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
      }

      /* User Avatar */
      .user-avatar {
        position: relative;
        cursor: pointer;
        padding: 4px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e5e5e5 100%);
        transition: all 0.3s ease;
      }

      .user-avatar:hover {
        background: linear-gradient(135deg, rgba(251, 10, 10, 0.1) 0%, rgba(251, 10, 10, 0.05) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      }

      .user-avatar img {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 2px solid #ffffff;
        transition: all 0.3s ease;
        object-fit: cover;
      }

      .user-avatar:hover img {
        border-color: var(--primary-red);
      }

      .status-indicator {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 14px;
        height: 14px;
        background: #10b981;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        animation: statusPulse 2s infinite;
      }

      @keyframes statusPulse {
        0%, 100% { box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }
        50% { box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.4); }
      }

      /* Dropdown Menu */
      .dropdown-menu {
        border: none;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        border-radius: 16px;
        overflow: hidden;
        margin-top: 12px;
        min-width: 280px;
        animation: dropdownSlide 0.3s ease;
      }

      @keyframes dropdownSlide {
        from {
          opacity: 0;
          transform: translateY(-10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .dropdown-header {
        background: linear-gradient(135deg, var(--primary-red) 0%, #ff3333 100%);
        padding: 1.5rem;
        text-align: left;
      }

      .dropdown-header h6 {
        color: white;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.25rem;
      }

      .dropdown-header small {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.75rem;
      }

      .dropdown-item {
        padding: 0.875rem 1.25rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        color: #333;
      }

      .dropdown-item i {
        font-size: 1.25rem;
        color: #666;
        transition: color 0.2s ease;
      }

      .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(251, 10, 10, 0.08) 0%, transparent 100%);
        color: var(--primary-red);
        padding-left: 1.5rem;
      }

      .dropdown-item:hover i {
        color: var(--primary-red);
      }

      .dropdown-divider {
        margin: 0.5rem 0;
        opacity: 0.1;
      }

      /* Mobile Toggle */
      .layout-menu-toggle i {
        color: var(--primary-red) !important;
        font-size: 1.75rem;
      }

      /* Responsive */
      @media (max-width: 1199.98px) {
        .navbar-content {
          flex-direction: column;
          gap: 1rem;
        }

        .welcome-section {
          text-align: center;
        }

        .welcome-text {
          font-size: 1.25rem;
        }

        #layout-menu.layout-menu {
          width: 260px;
        }
      }

      @media (max-width: 767.98px) {
        .welcome-text {
          font-size: 1.1rem;
        }

        .user-section {
          gap: 1rem;
        }

        .notification-bell,
        .user-avatar img {
          width: 40px;
          height: 40px;
        }
      }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand">
                    <a href="dashboard.php" class="app-brand-link">
                        <img src="assets/pircom.png" width="170" style="height:auto; width: 85px;" alt="PIRCOM Logo">
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <li class="menu-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <a href="dashboard.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Página Inicial</span>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'configuracoes.php') ? 'active' : ''; ?>">
                        <a href="configuracoes.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-bullseye"></i>
                            <div>Missão</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'homepagehero.php') ? 'active' : ''; ?>">
                        <a href="homepagehero.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-image-alt"></i>
                            <div>Banner Principal</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'noticias.php') ? 'active' : ''; ?>">
                        <a href="noticias.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-news"></i>
                            <div>Notícias</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'eventos.php') ? 'active' : ''; ?>">
                        <a href="eventos.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                            <div>Eventos</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'utilizadores.php') ? 'active' : ''; ?>">
                        <a href="utilizadores.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-circle"></i>
                            <div>Utilizadores</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Nossas Abordagens</span>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'comunitarias.php') ? 'active' : ''; ?>">
                        <a href="comunitarias.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div>Comunitárias</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo (in_array($current_page, ['provincias.php', 'provinciasform.php'])) ? 'active' : ''; ?>">
                        <a href="provincias.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-map"></i>
                            <div>Cobertura Geográfica</div>
                        </a>
                    </li>
                    
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Multimédia</span>
                    </li>

                    <li class="menu-item <?php echo ($current_page == 'documentos.php') ? 'active' : ''; ?>">
                        <a href="documentos.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-file-blank"></i>
                            <div>Documentos</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'galeria.php') ? 'active' : ''; ?>">
                        <a href="galeria.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-photo-album"></i>
                            <div>Galeria</div>
                        </a>
                    </li>
                    
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Blog & Doações</span>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'movimentos.php') ? 'active' : ''; ?>">
                        <a href="movimentos.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-chat"></i>
                            <div>Nossos Movimentos</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'configuracoes-doacoes.php') ? 'active' : ''; ?>">
                        <a href="configuracoes-doacoes.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-cog"></i>
                            <div>Configurações de Doação</div>
                        </a>
                    </li>
                    
                    <li class="menu-item <?php echo ($current_page == 'doadores.php') ? 'active' : ''; ?>">
                        <a href="doadores.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-donate-heart"></i>
                            <div>Lista de Doadores</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Sidebar -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-content">
                        <!-- Welcome Section -->
                        <div class="welcome-section">
                            <h5 class="welcome-text">
                                Bem-vindo, <span class="welcome-name"><?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?></span>
                            </h5>
                            <p class="welcome-subtitle">Gerencie seu painel PIRCOM</p>
                        </div>

                        <!-- User Section -->
                        <div class="user-section">
                            <!-- Notifications -->
                            <div class="nav-item dropdown">
                                <a class="nav-link notification-bell" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="bx bx-bell"></i>
                                    <span class="notification-badge">3</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="dropdown-header">
                                        <h6>Notificações</h6>
                                        <small>Você tem 3 novas notificações</small>
                                    </div>
                                    <a class="dropdown-item" href="#">
                                        <i class="bx bx-user-plus"></i>
                                        <span>Novo doador registrado</span>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="bx bx-calendar"></i>
                                        <span>Evento próximo amanhã</span>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="bx bx-message-dots"></i>
                                        <span>3 novas mensagens</span>
                                    </a>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="nav-item dropdown">
                                <a class="nav-link user-avatar" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <img src="https://static.vecteezy.com/system/resources/previews/007/296/443/large_2x/user-icon-person-icon-client-symbol-profile-icon-vector.jpg" alt="Avatar" />
                                    <span class="status-indicator"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header">
                                            <h6><?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?></h6>
                                            <small>Administrador</small>
                                        </div>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user"></i>
                                            <span>Meu Perfil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-cog"></i>
                                            <span>Configurações</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-help-circle"></i>
                                            <span>Ajuda & Suporte</span>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="bx bx-power-off"></i>
                                            <span>Sair do Sistema</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- / Navbar -->