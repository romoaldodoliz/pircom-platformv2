<?php
// Incluir helper de autenticação
require_once(__DIR__ . '/helpers/auth.php');
if (file_exists(__DIR__ . '/helpers/notifications.php')) {
    require_once(__DIR__ . '/helpers/notifications.php');
}

// Verificar se usuário está autenticado
requireAuth();

// Verificar timeout de sessão
checkSessionTimeout();

$current_page = basename($_SERVER['PHP_SELF']);
$notificacoes_nao_lidas = [];
if (function_exists('obterNotificacoesNaoLidas') && isset($_SESSION['user_id'])) {
    $notificacoes_nao_lidas = obterNotificacoesNaoLidas($_SESSION['user_id']);
}
$total_notificacoes = count($notificacoes_nao_lidas);
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
    <link rel="shortcut icon" href="assets/pircom.png" />
    <link rel="apple-touch-icon" href="assets/pircom.png" />

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
    
    <!-- Notification Styles -->
    <link rel="stylesheet" href="assets/css/notifications.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
    
    <!-- Notification System JS - Carregar antes de usar -->
    <script src="assets/js/notifications.js"></script>

    <script>
      // Disponibiliza flag de role para o JS (true se admin)
      window.__isAdmin = <?php echo isAdmin() ? 'true' : 'false'; ?>;
    </script>

    <!-- Modern Responsive Styles -->
    <style>
      * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        box-sizing: border-box;
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
        --sidebar-width: 280px;
        --sidebar-collapsed-width: 80px;
        --navbar-height: 70px;
        --transition-speed: 0.3s;
      }

      /* ========== LAYOUT BASE ========== */
      .layout-wrapper {
        min-height: 100vh;
        overflow-x: hidden;
      }

      .layout-container {
        display: flex;
        min-height: 100vh;
      }

      .layout-page {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
      }

      /* ========== SIDEBAR MODERN RESPONSIVE ========== */
      #layout-menu.layout-menu {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: linear-gradient(180deg, var(--darker-bg) 0%, var(--dark-bg) 100%);
        border-right: 1px solid var(--border-color);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.4);
        transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1100;
        overflow: hidden;
        display: flex;
        flex-direction: column;
      }

      /* Logo Area */
      #layout-menu .app-brand {
        padding: 20px;
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.1) 0%, transparent 100%);
        border-bottom: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
        min-height: var(--navbar-height);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
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
        transition: opacity var(--transition-speed);
      }

      #layout-menu .app-brand-link img {
        max-width: 85px;
        height: auto;
        filter: brightness(1.1) drop-shadow(0 4px 12px rgba(255, 111, 15, 0.3));
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      }

      #layout-menu .app-brand-link:hover img {
        transform: scale(1.05);
        filter: brightness(1.2) drop-shadow(0 6px 20px rgba(255, 111, 15, 0.5));
      }

      /* Menu Toggle Button - Desktop */
      .layout-menu-toggle {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
      }

      .layout-menu-toggle:hover {
        background: rgba(255, 111, 15, 0.2);
        transform: rotate(180deg);
      }

      .layout-menu-toggle i {
        color: var(--text-secondary);
        font-size: 1.5rem;
        transition: color 0.3s ease;
      }

      /* Menu Container */
      #layout-menu .menu-inner {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 16px 12px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
      }

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

      /* Menu Sections */
      #layout-menu .menu-header {
        padding: 0;
        margin: 24px 20px 12px;
      }

      #layout-menu .menu-header-text {
        color: var(--text-secondary) !important;
        font-weight: 700;
        font-size: 10px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding-left: 16px;
        position: relative;
        display: block;
        transition: opacity var(--transition-speed);
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
        display: flex;
        align-items: center;
        white-space: nowrap;
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
        flex-shrink: 0;
      }

      #layout-menu .menu-item > .menu-link div {
        transition: opacity var(--transition-speed);
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

      /* Remove sombra ao fazer scroll */
      #layout-menu .menu-inner-shadow {
        display: none !important;
      }

      /* ========== NAVBAR MODERN RESPONSIVE ========== */
      .layout-navbar {
        position: sticky;
        top: 0;
        height: var(--navbar-height);
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 0 1.5rem;
        z-index: 1050;
        transition: all var(--transition-speed);
      }

      .navbar-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 100%;
        gap: 1.5rem;
      }

      /* Welcome Section */
      .welcome-section {
        flex: 1;
        min-width: 0;
      }

      .welcome-text {
        font-size: clamp(1.1rem, 2.5vw, 1.5rem);
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .welcome-name {
        color: var(--primary-red);
        -webkit-text-fill-color: var(--primary-red);
        font-weight: 800;
      }

      .welcome-subtitle {
        font-size: clamp(0.75rem, 1.5vw, 0.875rem);
        color: #666;
        margin-top: 2px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      /* User Section */
      .user-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-shrink: 0;
      }

      /* Mobile Menu Toggle */
      .mobile-menu-toggle {
        display: none;
        background: #f5f5f5;
        border: none;
        border-radius: 12px;
        width: 44px;
        height: 44px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .mobile-menu-toggle:hover {
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.1) 0%, rgba(255, 111, 15, 0.05) 100%);
        transform: scale(1.05);
      }

      .mobile-menu-toggle i {
        color: var(--primary-red);
        font-size: 1.5rem;
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
        flex-shrink: 0;
      }

      .notification-bell:hover {
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.1) 0%, rgba(255, 111, 15, 0.05) 100%);
        border-color: rgba(255, 111, 15, 0.2);
        transform: translateY(-2px);
      }

      .notification-bell i {
        font-size: 1.4rem;
        color: #333;
        transition: transform 0.3s ease;
      }

      .notification-bell:hover i {
        animation: bellRing 0.5s ease;
      }

      @keyframes bellRing {
        0%, 100% { transform: rotate(0); }
        25% { transform: rotate(-15deg); }
        50% { transform: rotate(15deg); }
        75% { transform: rotate(-10deg); }
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
        box-shadow: 0 2px 8px rgba(255, 111, 15, 0.4);
        animation: badgePulse 2s infinite;
      }

      @keyframes badgePulse {
        0%, 100% { 
          transform: scale(1);
          box-shadow: 0 2px 8px rgba(255, 111, 15, 0.4);
        }
        50% { 
          transform: scale(1.1);
          box-shadow: 0 4px 12px rgba(255, 111, 15, 0.6);
        }
      }

      /* User Avatar */
      .user-avatar {
        position: relative;
        cursor: pointer;
        padding: 4px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e5e5e5 100%);
        transition: all 0.3s ease;
        flex-shrink: 0;
      }

      .user-avatar:hover {
        background: linear-gradient(135deg, rgba(255, 111, 15, 0.1) 0%, rgba(255, 111, 15, 0.05) 100%);
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
        max-width: 90vw;
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
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
        white-space: nowrap;
      }

      .dropdown-item i {
        font-size: 1.25rem;
        color: #666;
        transition: color 0.2s ease;
        flex-shrink: 0;
      }

      .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(255, 111, 15, 0.08) 0%, transparent 100%);
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

      /* Notification Dropdown Specific Styles */
      .notification-dropdown {
        max-height: 400px;
        overflow-y: auto;
      }

      .notification-dropdown .dropdown-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      }

      .notification-dropdown .dropdown-item:last-child {
        border-bottom: none;
      }

      .notification-dropdown .dropdown-item span {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
      }

      .notification-dropdown .dropdown-item small {
        color: #999;
        font-size: 0.75rem;
        margin-top: 0.25rem;
      }

      /* Backdrop para mobile */
      .sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1099;
        opacity: 0;
        transition: opacity var(--transition-speed);
      }

      .sidebar-backdrop.show {
        display: block;
        opacity: 1;
      }

      /* ========== RESPONSIVE BREAKPOINTS ========== */
      
      /* Large Desktop (>1400px) */
      @media (min-width: 1400px) {
        :root {
          --sidebar-width: 300px;
        }
      }

      /* Desktop (992px - 1199px) */
      @media (max-width: 1199.98px) {
        :root {
          --sidebar-width: 260px;
        }

        .welcome-text {
          font-size: 1.25rem;
        }

        .welcome-subtitle {
          font-size: 0.8rem;
        }
      }

      /* Tablet (768px - 991px) */
      @media (max-width: 991.98px) {
        #layout-menu.layout-menu {
          transform: translateX(-100%);
        }

        #layout-menu.layout-menu.show {
          transform: translateX(0);
        }

        .layout-page {
          margin-left: 0 !important;
        }

        .welcome-section {
          max-width: 60%;
        }

        .user-section {
          gap: 0.75rem;
        }

        .mobile-menu-toggle {
          display: flex;
        }

        .layout-menu-toggle.d-xl-none {
          display: none !important;
        }
      }

      /* Mobile (576px - 767px) */
      @media (max-width: 767.98px) {
        :root {
          --navbar-height: 65px;
        }

        .layout-navbar {
          padding: 0 1rem;
        }

        .navbar-content {
          gap: 0.75rem;
        }

        .welcome-section {
          max-width: 50%;
        }

        .welcome-subtitle {
          display: none;
        }

        .notification-bell,
        .user-avatar {
          width: 40px;
          height: 40px;
        }

        .notification-bell {
          padding: 0;
        }

        .notification-bell i {
          font-size: 1.25rem;
        }

        .user-avatar img {
          width: 40px;
          height: 40px;
        }

        .status-indicator {
          width: 12px;
          height: 12px;
          border-width: 2px;
        }

        .dropdown-menu {
          min-width: 260px;
          max-width: calc(100vw - 2rem);
        }

        .mobile-menu-toggle {
          width: 40px;
          height: 40px;
        }

        .mobile-menu-toggle i {
          font-size: 1.35rem;
        }
      }

      /* Small Mobile (<576px) */
      @media (max-width: 575.98px) {
        :root {
          --sidebar-width: 280px;
          --navbar-height: 60px;
        }

        .layout-navbar {
          padding: 0 0.75rem;
        }

        .navbar-content {
          gap: 0.5rem;
        }

        .welcome-text {
          font-size: 1rem;
        }

        .welcome-section {
          max-width: 45%;
        }

        .user-section {
          gap: 0.5rem;
        }

        .notification-bell,
        .user-avatar,
        .mobile-menu-toggle {
          width: 38px;
          height: 38px;
        }

        .notification-bell i {
          font-size: 1.15rem;
        }

        .user-avatar img {
          width: 38px;
          height: 38px;
        }

        .notification-badge {
          min-width: 18px;
          height: 18px;
          font-size: 10px;
        }

        .dropdown-menu {
          min-width: 240px;
        }

        .dropdown-header {
          padding: 1.25rem 1rem;
        }

        .dropdown-item {
          padding: 0.75rem 1rem;
          font-size: 0.9rem;
        }

        #layout-menu .menu-item > .menu-link {
          padding: 12px 14px;
        }

        #layout-menu .menu-header-text {
          font-size: 9px;
        }
      }

      /* Extra Small Mobile (<400px) */
      @media (max-width: 399.98px) {
        .welcome-text {
          font-size: 0.9rem;
        }

        .welcome-section {
          max-width: 40%;
        }

        .notification-bell,
        .user-avatar,
        .mobile-menu-toggle {
          width: 36px;
          height: 36px;
        }

        .user-avatar img {
          width: 36px;
          height: 36px;
        }

        .dropdown-menu {
          min-width: 220px;
        }
      }

      /* Landscape Mobile */
      @media (max-height: 500px) and (orientation: landscape) {
        :root {
          --navbar-height: 55px;
        }

        .dropdown-menu {
          max-height: 70vh;
          overflow-y: auto;
        }
      }

      /* Print Styles */
      @media print {
        #layout-menu,
        .layout-navbar {
          display: none;
        }

        .layout-page {
          margin-left: 0 !important;
        }
      }

      /* Touch Device Optimizations */
      @media (hover: none) and (pointer: coarse) {
        #layout-menu .menu-item > .menu-link {
          min-height: 48px;
        }

        .notification-bell,
        .user-avatar,
        .mobile-menu-toggle {
          min-width: 44px;
          min-height: 44px;
        }
      }

      /* Accessibility - Reduced Motion */
      @media (prefers-reduced-motion: reduce) {
        * {
          animation-duration: 0.01ms !important;
          animation-iteration-count: 1 !important;
          transition-duration: 0.01ms !important;
        }
      }

      /* Dark Mode Support (opcional) */
      @media (prefers-color-scheme: dark) {
        /* Manter cores atuais ou adaptar se necessário */
      }
    </style>
</head>

<body>
    <!-- Backdrop para mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand">
                    <a href="dashboard.php" class="app-brand-link">
                        <img src="assets/pircom.png" width="170" style="height:auto; width: 85px;" alt="PIRCOM Logo">
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none" id="menuToggleBtn">
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
                    <div class="navbar-content">
                        <!-- Mobile Menu Toggle -->
                        <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button">
                            <i class="bx bx-menu"></i>
                        </button>

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
                                <a class="nav-link notification-bell" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-bell"></i>
                                    <?php if ($total_notificacoes > 0): ?>
                                    <span class="notification-badge" id="notificationBadge"><?php echo $total_notificacoes; ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end notification-dropdown" id="notificationDropdown" style="min-width: 350px; max-width: 400px;">
                                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                                        <h6 style="margin: 0;">Notificações</h6>
                                        <?php if ($total_notificacoes > 0): ?>
                                        <small style="cursor: pointer; color: var(--primary-red); font-weight: 600;" onclick="marcarTodasComoLidas()">Marcar todas</small>
                                        <?php endif; ?>
                                    </div>
                                    <div id="notificationsList" style="max-height: 400px; overflow-y: auto;">
                                        <?php if ($total_notificacoes > 0): ?>
                                            <?php foreach ($notificacoes_nao_lidas as $notif): ?>
                                            <a class="dropdown-item notification-item" data-id="<?php echo $notif['id']; ?>" href="javascript:void(0);" onclick="marcarNotificacao(<?php echo $notif['id']; ?>)">
                                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                                    <div style="flex: 1;">
                                                        <strong style="color: #333; display: block;"><?php echo htmlspecialchars($notif['titulo']); ?></strong>
                                                        <small style="color: #666; display: block; margin-top: 4px;"><?php echo htmlspecialchars($notif['mensagem']); ?></small>
                                                        <small style="color: #999; display: block; margin-top: 6px;">
                                                            <?php 
                                                                $time = strtotime($notif['criada_em']);
                                                                $diff = time() - $time;
                                                                if ($diff < 60) echo 'Há alguns segundos';
                                                                elseif ($diff < 3600) echo 'Há ' . floor($diff/60) . ' minutos';
                                                                elseif ($diff < 86400) echo 'Há ' . floor($diff/3600) . ' horas';
                                                                else echo 'Há ' . floor($diff/86400) . ' dias';
                                                            ?>
                                                        </small>
                                                    </div>
                                                    <i class="bx bx-x" style="cursor: pointer; margin-left: 8px; color: #999;" onclick="event.stopPropagation(); deletarNotificacao(<?php echo $notif['id']; ?>)"></i>
                                                </div>
                                            </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div style="padding: 20px; text-align: center; color: #999;">
                                                <i class="bx bx-info-circle" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                                <small>Nenhuma notificação no momento</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="nav-item dropdown">
                                <a class="nav-link user-avatar" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://static.vecteezy.com/system/resources/previews/007/296/443/large_2x/user-icon-person-icon-client-symbol-profile-icon-vector.jpg" alt="Avatar" />
                                    <span class="status-indicator"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header">
                                            <h6><?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?></h6>
                                            <small><?php 
                                                $role = getUserRole();
                                                echo ($role === 'admin') ? 'Administrador' : 'Gerenciador de Conteúdo';
                                            ?></small>
                                        </div>
                                    </li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="editar-perfil.php">
                                            <i class="bx bx-user"></i>
                                            <span>Meu Perfil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="editar-perfil.php?tab=password">
                                            <i class="bx bx-lock"></i>
                                            <span>Alterar Senha</span>
                                        </a>
                                    </li>
                                    <?php if (isAdmin()): ?>
                                    <li>
                                        <a class="dropdown-item" href="configuracoes.php">
                                            <i class="bx bx-cog"></i>
                                            <span>Configurações do Sistema</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="actions/logoutAction.php">
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

    <script>
        // Mobile Menu Toggle Script
        (function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const layoutMenu = document.getElementById('layout-menu');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const menuToggleBtn = document.getElementById('menuToggleBtn');

            // Toggle menu em mobile
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    layoutMenu.classList.toggle('show');
                    sidebarBackdrop.classList.toggle('show');
                    document.body.style.overflow = layoutMenu.classList.contains('show') ? 'hidden' : '';
                });
            }

            // Fechar menu ao clicar no backdrop
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    layoutMenu.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            // Fechar menu ao clicar no botão de fechar
            if (menuToggleBtn) {
                menuToggleBtn.addEventListener('click', function() {
                    layoutMenu.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            // Fechar menu ao clicar em um link (mobile)
            const menuLinks = document.querySelectorAll('#layout-menu .menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        setTimeout(() => {
                            layoutMenu.classList.remove('show');
                            sidebarBackdrop.classList.remove('show');
                            document.body.style.overflow = '';
                        }, 200);
                    }
                });
            });

            // Fechar menu ao redimensionar para desktop
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth >= 992) {
                        layoutMenu.classList.remove('show');
                        sidebarBackdrop.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }, 250);
            });

            // Prevenir scroll do body quando menu está aberto em mobile
            const preventScroll = (e) => {
                if (layoutMenu.classList.contains('show') && window.innerWidth < 992) {
                    e.preventDefault();
                }
            };

            document.addEventListener('touchmove', preventScroll, { passive: false });

            // Accessibility: ESC para fechar menu
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && layoutMenu.classList.contains('show')) {
                    layoutMenu.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        })();

        // ========== SISTEMA DE NOTIFICAÇÕES ==========
        
        /**
         * Marcar uma notificação como lida
         */
        function marcarNotificacao(id) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('action', 'marcar-lida');
            
            fetch('actions/notificacoesAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    document.querySelector(`[data-id="${id}"]`).style.opacity = '0.5';
                    atualizarContagemNotificacoes();
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        /**
         * Marcar todas as notificações como lidas
         */
        function marcarTodasComoLidas() {
            const formData = new FormData();
            formData.append('action', 'marcar-todas');
            
            fetch('actions/notificacoesAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    carregarNotificacoes();
                    atualizarContagemNotificacoes();
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        /**
         * Deletar uma notificação
         */
        function deletarNotificacao(id) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('action', 'deletar');
            
            fetch('actions/notificacoesAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    const elem = document.querySelector(`[data-id="${id}"]`);
                    if (elem) {
                        elem.remove();
                    }
                    atualizarContagemNotificacoes();
                    carregarNotificacoes();
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        /**
         * Carregar notificações via AJAX
         */
        function carregarNotificacoes() {
            fetch('actions/notificacoesAction.php?action=listar')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('notificationsList');
                if (data.total > 0) {
                    let html = '';
                    data.notificacoes.forEach(notif => {
                        const time = new Date(notif.criada_em);
                        const agora = new Date();
                        const diff = Math.floor((agora - time) / 1000);
                        let timeText = 'Há alguns segundos';
                        if (diff > 60) timeText = 'Há ' + Math.floor(diff/60) + ' minutos';
                        if (diff > 3600) timeText = 'Há ' + Math.floor(diff/3600) + ' horas';
                        if (diff > 86400) timeText = 'Há ' + Math.floor(diff/86400) + ' dias';
                        
                        html += `
                        <a class="dropdown-item notification-item" data-id="${notif.id}" href="javascript:void(0);" onclick="marcarNotificacao(${notif.id})">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <strong style="color: #333; display: block;">${notif.titulo}</strong>
                                    <small style="color: #666; display: block; margin-top: 4px;">${notif.mensagem}</small>
                                    <small style="color: #999; display: block; margin-top: 6px;">${timeText}</small>
                                </div>
                                <i class="bx bx-x" style="cursor: pointer; margin-left: 8px; color: #999;" onclick="event.stopPropagation(); deletarNotificacao(${notif.id})"></i>
                            </div>
                        </a>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #999;">
                        <i class="bx bx-info-circle" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                        <small>Nenhuma notificação no momento</small>
                    </div>
                    `;
                }
            })
            .catch(error => console.error('Erro ao carregar notificações:', error));
        }

        /**
         * Atualizar contagem de notificações
         */
        function atualizarContagemNotificacoes() {
            fetch('actions/notificacoesAction.php?action=contar')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (data.total > 0) {
                    if (!badge) {
                        const bell = document.querySelector('.notification-bell');
                        const newBadge = document.createElement('span');
                        newBadge.className = 'notification-badge';
                        newBadge.id = 'notificationBadge';
                        newBadge.textContent = data.total;
                        bell.appendChild(newBadge);
                    } else {
                        badge.textContent = data.total;
                    }
                } else if (badge) {
                    badge.remove();
                }
            })
            .catch(error => console.error('Erro ao atualizar contagem:', error));
        }

        // Atualizar notificações a cada 30 segundos
        setInterval(atualizarContagemNotificacoes, 30000);
        setInterval(carregarNotificacoes, 30000);
    </script>