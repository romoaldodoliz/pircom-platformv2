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

// Determinar título da página
$page_titles = [
    'dashboard.php' => 'Dashboard',
    'configuracoes.php' => 'Configurações da Missão',
    'homepagehero.php' => 'Banner Principal',
    'noticias.php' => 'Gestão de Notícias',
    'eventos.php' => 'Eventos',
    'utilizadores.php' => 'Utilizadores',
    'comunitarias.php' => 'Ações Comunitárias',
    'provincias.php' => 'Cobertura Geográfica',
    'documentos.php' => 'Documentos',
    'galeria.php' => 'Galeria',
    'movimentos.php' => 'Nossos Movimentos',
    'configuracoes-doacoes.php' => 'Configurações de Doações',
    'doadores.php' => 'Doadores'
];
$current_title = $page_titles[$current_page] ?? 'Painel Administrativo';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PIRCOM · <?php echo $current_title; ?></title>
    <meta name="description" content="Plataforma de Gestão PIRCOM - Administração Enterprise" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/pircom.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Core CSS (mantido para compatibilidade) -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/css/notifications.css" />
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Scripts -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>
    <script src="assets/js/notifications.js"></script>
    
    <script>
      window.__isAdmin = <?php echo isAdmin() ? 'true' : 'false'; ?>;
      window.__userName = '<?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>';
    </script>

    <!-- PIRCOM Design System - Classes Exclusivas -->
    <style>
        /* ===== RESET E VARIÁVEIS ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --pircom-primary: #FF6F0F;
            --pircom-primary-dark: #E05A00;
            --pircom-primary-light: #FFF1E6;
            --pircom-primary-gradient: linear-gradient(135deg, #FF6F0F, #FF8A3F);
            
            --pircom-dark: #0A0C14;
            --pircom-dark-light: #1E1F2E;
            --pircom-gray-900: #1A1F2C;
            --pircom-gray-800: #2D3748;
            --pircom-gray-700: #4A5568;
            --pircom-gray-600: #718096;
            --pircom-gray-500: #A0AEC0;
            --pircom-gray-400: #CBD5E0;
            --pircom-gray-300: #E2E8F0;
            --pircom-gray-200: #EDF2F7;
            --pircom-gray-100: #F7FAFC;
            --pircom-white: #FFFFFF;
            
            --pircom-success: #10B981;
            --pircom-warning: #F59E0B;
            --pircom-danger: #EF4444;
            --pircom-info: #3B82F6;
            
            --pircom-sidebar-width: 280px;
            --pircom-sidebar-collapsed: 80px;
            --pircom-navbar-height: 60px;
            --pircom-border-radius: 10px;
            --pircom-border-radius-sm: 6px;
            
            --pircom-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --pircom-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --pircom-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
            --pircom-shadow-primary: 0 4px 16px rgba(255, 111, 15, 0.2);
            
            --pircom-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Espaçamentos */
            --pircom-spacing-xs: 4px;
            --pircom-spacing-sm: 8px;
            --pircom-spacing-md: 12px;
            --pircom-spacing-lg: 16px;
            --pircom-spacing-xl: 20px;
            --pircom-spacing-xxl: 24px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--pircom-gray-100);
            color: var(--pircom-dark);
            line-height: 1.5;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* ===== LAYOUT PRINCIPAL ===== */
        .pircom-layout-wrapper {
            min-height: 100vh;
            display: flex;
            background: var(--pircom-gray-100);
        }

        .pircom-main-content {
            flex: 1;
            margin-left: var(--pircom-sidebar-width);
            transition: var(--pircom-transition);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--pircom-gray-100);
        }

        .pircom-main-content.pircom-expanded {
            margin-left: var(--pircom-sidebar-collapsed);
        }

        /* ===== SIDEBAR ===== */
        .pircom-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--pircom-sidebar-width);
            background: linear-gradient(180deg, var(--pircom-dark) 0%, #0F1320 100%);
            box-shadow: var(--pircom-shadow-lg);
            transition: var(--pircom-transition);
            z-index: 1100;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pircom-sidebar.pircom-collapsed {
            width: var(--pircom-sidebar-collapsed);
        }

        .pircom-sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: var(--pircom-spacing-lg) var(--pircom-spacing-sm);
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }

        .pircom-sidebar-nav::-webkit-scrollbar {
            width: 3px;
        }

        .pircom-sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .pircom-sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .pircom-sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: var(--pircom-primary);
        }

        .pircom-sidebar-logo {
            padding: 0 var(--pircom-spacing-lg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
            height: var(--pircom-navbar-height);
        }

        .pircom-logo-wrapper {
            display: flex;
            align-items: center;
            gap: var(--pircom-spacing-sm);
            overflow: hidden;
        }

        .pircom-logo-img {
            height: 32px;
            width: auto;
            filter: brightness(1.1) drop-shadow(0 2px 4px rgba(255, 111, 15, 0.3));
            transition: var(--pircom-transition);
            flex-shrink: 0;
        }

        .pircom-logo-text {
            color: var(--pircom-white);
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.3px;
            white-space: nowrap;
            transition: var(--pircom-transition);
            opacity: 1;
        }

        .pircom-sidebar.pircom-collapsed .pircom-logo-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        .pircom-collapse-btn {
            width: 28px;
            height: 28px;
            border-radius: var(--pircom-border-radius-sm);
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--pircom-white);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--pircom-transition);
            flex-shrink: 0;
        }

        .pircom-collapse-btn:hover {
            background: var(--pircom-primary);
        }

        .pircom-sidebar.pircom-collapsed .pircom-collapse-btn i {
            transform: rotate(180deg);
        }

        .pircom-nav-section {
            margin-bottom: var(--pircom-spacing-lg);
        }

        .pircom-nav-section:last-child {
            margin-bottom: 0;
        }

        .pircom-nav-section-title {
            color: var(--pircom-gray-500);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 var(--pircom-spacing-md);
            margin-bottom: var(--pircom-spacing-sm);
            transition: var(--pircom-transition);
            white-space: nowrap;
        }

        .pircom-sidebar.pircom-collapsed .pircom-nav-section-title {
            opacity: 0;
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .pircom-nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .pircom-nav-item {
            margin-bottom: 2px;
            list-style: none;
        }

        .pircom-nav-link {
            display: flex;
            align-items: center;
            padding: var(--pircom-spacing-sm) var(--pircom-spacing-md);
            color: var(--pircom-gray-400);
            text-decoration: none;
            border-radius: var(--pircom-border-radius-sm);
            transition: var(--pircom-transition);
            white-space: nowrap;
            position: relative;
            height: 42px;
        }

        .pircom-nav-link i {
            font-size: 20px;
            min-width: 40px;
            text-align: center;
            transition: var(--pircom-transition);
            color: var(--pircom-gray-400);
        }

        .pircom-nav-link span {
            opacity: 1;
            transition: var(--pircom-transition);
            font-weight: 500;
            font-size: 13px;
            margin-left: 0;
        }

        .pircom-sidebar.pircom-collapsed .pircom-nav-link {
            justify-content: center;
            padding: var(--pircom-spacing-sm) 0;
            height: 46px;
        }

        .pircom-sidebar.pircom-collapsed .pircom-nav-link i {
            min-width: auto;
            font-size: 22px;
            margin: 0;
        }

        .pircom-sidebar.pircom-collapsed .pircom-nav-link span {
            opacity: 0;
            width: 0;
            display: none;
        }

        .pircom-nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--pircom-white);
        }

        .pircom-nav-link:hover i {
            color: var(--pircom-primary);
            transform: scale(1.1);
        }

        .pircom-nav-item.pircom-active .pircom-nav-link {
            background: linear-gradient(90deg, rgba(255, 111, 15, 0.15) 0%, transparent 100%);
            color: var(--pircom-white);
            border-left: 2px solid var(--pircom-primary);
        }

        .pircom-nav-item.pircom-active .pircom-nav-link i {
            color: var(--pircom-primary);
        }

        .pircom-sidebar.pircom-collapsed .pircom-nav-link:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: var(--pircom-dark);
            color: var(--pircom-white);
            padding: 6px 10px;
            border-radius: var(--pircom-border-radius-sm);
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            margin-left: 8px;
            box-shadow: var(--pircom-shadow-md);
            z-index: 1200;
            animation: pircom-tooltip-fade 0.15s ease;
        }

        .pircom-sidebar.pircom-collapsed .pircom-nav-link:hover::before {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: var(--pircom-dark);
            margin-left: 0;
            z-index: 1201;
        }

        @keyframes pircom-tooltip-fade {
            from { 
                opacity: 0; 
                transform: translateY(-50%) translateX(-5px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(-50%) translateX(0); 
            }
        }

        /* ===== NAVBAR ===== */
        .pircom-navbar {
            height: var(--pircom-navbar-height);
            background: var(--pircom-white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 0 var(--pircom-spacing-lg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            flex-shrink: 0;
            border-bottom: 1px solid var(--pircom-gray-200);
        }

        .pircom-navbar-left {
            display: flex;
            align-items: center;
            gap: var(--pircom-spacing-md);
        }

        .pircom-mobile-btn {
            display: none;
            width: 36px;
            height: 36px;
            border-radius: var(--pircom-border-radius-sm);
            background: var(--pircom-gray-100);
            border: none;
            color: var(--pircom-gray-700);
            font-size: 22px;
            cursor: pointer;
            transition: var(--pircom-transition);
            align-items: center;
            justify-content: center;
        }

        .pircom-mobile-btn:hover {
            background: var(--pircom-primary-light);
            color: var(--pircom-primary);
        }

        .pircom-page-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--pircom-gray-800);
        }

        .pircom-page-title span {
            color: var(--pircom-gray-400);
            font-weight: 400;
            margin: 0 var(--pircom-spacing-xs);
        }

        .pircom-navbar-right {
            display: flex;
            align-items: center;
            gap: var(--pircom-spacing-sm);
        }

        .pircom-notification-wrapper {
            position: relative;
        }

        .pircom-notification-btn {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: var(--pircom-border-radius-sm);
            background: var(--pircom-gray-100);
            border: none;
            color: var(--pircom-gray-700);
            font-size: 20px;
            cursor: pointer;
            transition: var(--pircom-transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pircom-notification-btn:hover {
            background: var(--pircom-primary-light);
            color: var(--pircom-primary);
        }

        .pircom-notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: linear-gradient(135deg, var(--pircom-primary), var(--pircom-danger));
            color: var(--pircom-white);
            font-size: 9px;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid var(--pircom-white);
        }

        .pircom-user-wrapper {
            position: relative;
        }

        .pircom-user-btn {
            display: flex;
            align-items: center;
            gap: var(--pircom-spacing-sm);
            padding: 2px 2px 2px 2px;
            border-radius: 30px;
            background: var(--pircom-gray-100);
            border: none;
            cursor: pointer;
            transition: var(--pircom-transition);
        }

        .pircom-user-btn:hover {
            background: var(--pircom-primary-light);
        }

        .pircom-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 30px;
            background: var(--pircom-primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pircom-white);
            font-weight: 600;
            font-size: 14px;
            box-shadow: var(--pircom-shadow-primary);
            flex-shrink: 0;
        }

        .pircom-user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
            margin-right: 2px;
        }

        .pircom-user-name {
            font-weight: 600;
            font-size: 12px;
            color: var(--pircom-gray-800);
            line-height: 1.3;
        }

        .pircom-user-role {
            font-size: 10px;
            color: var(--pircom-gray-600);
        }

        .pircom-user-btn i {
            color: var(--pircom-gray-600);
            font-size: 16px;
            transition: var(--pircom-transition);
            margin-right: 6px;
        }

        .pircom-user-btn:hover i {
            color: var(--pircom-primary);
        }

        .pircom-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            background: var(--pircom-white);
            border-radius: var(--pircom-border-radius);
            box-shadow: var(--pircom-shadow-lg);
            min-width: 300px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-5px);
            transition: var(--pircom-transition);
            z-index: 1100;
            border: 1px solid var(--pircom-gray-200);
        }

        .pircom-dropdown.pircom-show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .pircom-dropdown-header {
            padding: var(--pircom-spacing-md) var(--pircom-spacing-lg);
            background: var(--pircom-primary-gradient);
            color: var(--pircom-white);
            border-radius: var(--pircom-border-radius) var(--pircom-border-radius) 0 0;
        }

        .pircom-dropdown-header h6 {
            font-weight: 600;
            margin: 0 0 2px 0;
            color: var(--pircom-white);
            font-size: 14px;
        }

        .pircom-dropdown-header small {
            opacity: 0.9;
            font-size: 11px;
        }

        .pircom-dropdown-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .pircom-dropdown-item {
            padding: var(--pircom-spacing-md) var(--pircom-spacing-lg);
            display: flex;
            align-items: flex-start;
            gap: var(--pircom-spacing-sm);
            text-decoration: none;
            color: var(--pircom-gray-700);
            transition: var(--pircom-transition);
            border-bottom: 1px solid var(--pircom-gray-200);
            cursor: pointer;
        }

        .pircom-dropdown-item:last-child {
            border-bottom: none;
        }

        .pircom-dropdown-item:hover {
            background: var(--pircom-gray-100);
        }

        .pircom-dropdown-item i {
            font-size: 18px;
            color: var(--pircom-gray-500);
            margin-top: 2px;
        }

        .pircom-item-content {
            flex: 1;
        }

        .pircom-item-title {
            font-weight: 600;
            font-size: 13px;
            color: var(--pircom-gray-800);
            margin-bottom: 2px;
        }

        .pircom-item-subtitle {
            font-size: 11px;
            color: var(--pircom-gray-600);
            margin-bottom: 2px;
            line-height: 1.4;
        }

        .pircom-item-time {
            font-size: 10px;
            color: var(--pircom-gray-500);
        }

        .pircom-dropdown-footer {
            padding: var(--pircom-spacing-sm) var(--pircom-spacing-lg);
            text-align: center;
            border-top: 1px solid var(--pircom-gray-200);
            background: var(--pircom-gray-50);
        }

        .pircom-dropdown-footer button,
        .pircom-dropdown-footer a {
            background: none;
            border: none;
            color: var(--pircom-primary);
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: var(--pircom-transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pircom-dropdown-footer button:hover,
        .pircom-dropdown-footer a:hover {
            color: var(--pircom-primary-dark);
        }

        .pircom-empty-state {
            padding: var(--pircom-spacing-xl);
            text-align: center;
            color: var(--pircom-gray-500);
        }

        .pircom-empty-state i {
            font-size: 36px;
            margin-bottom: var(--pircom-spacing-sm);
            display: block;
            color: var(--pircom-gray-400);
        }

        .pircom-empty-state p {
            font-weight: 500;
            margin-bottom: 2px;
            color: var(--pircom-gray-700);
        }

        .pircom-empty-state small {
            font-size: 11px;
            color: var(--pircom-gray-500);
        }

        /* ===== CONTENT AREA - CORRIGIDO: espaçamento mínimo no topo ===== */
        .pircom-content-wrapper {
            padding: 12px 16px 16px 16px;
            flex: 1;
            background: var(--pircom-gray-100);
        }

        /* Dashboard Components */
        .pircom-dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--pircom-gray-900);
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .pircom-dashboard-subtitle {
            font-size: 14px;
            color: var(--pircom-gray-600);
            margin: 0 0 20px 0;
        }

        .pircom-section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--pircom-gray-800);
            margin: 24px 0 16px 0;
        }

        .pircom-section-title:first-of-type {
            margin-top: 0;
        }

        .pircom-metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .pircom-metric-card {
            background: var(--pircom-white);
            border-radius: var(--pircom-border-radius);
            padding: 16px;
            box-shadow: var(--pircom-shadow-sm);
            border: 1px solid var(--pircom-gray-200);
            transition: var(--pircom-transition);
        }

        .pircom-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--pircom-shadow-md);
            border-color: var(--pircom-primary-light);
        }

        .pircom-metric-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--pircom-gray-900);
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .pircom-metric-label {
            font-size: 13px;
            color: var(--pircom-gray-600);
            font-weight: 500;
        }

        .pircom-dashboard-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .pircom-dashboard-card {
            background: var(--pircom-white);
            border-radius: var(--pircom-border-radius);
            padding: 16px;
            box-shadow: var(--pircom-shadow-sm);
            border: 1px solid var(--pircom-gray-200);
        }

        .pircom-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .pircom-card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--pircom-gray-800);
            margin: 0;
        }

        .pircom-card-header .pircom-badge {
            background: var(--pircom-primary-light);
            color: var(--pircom-primary);
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .pircom-chart-placeholder {
            height: 200px;
            background: var(--pircom-gray-100);
            border-radius: var(--pircom-border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pircom-gray-500);
            font-size: 13px;
            border: 1px dashed var(--pircom-gray-300);
        }

        .pircom-intervention-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pircom-intervention-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--pircom-gray-200);
        }

        .pircom-intervention-item:last-child {
            border-bottom: none;
        }

        .pircom-intervention-month {
            font-size: 13px;
            font-weight: 500;
            color: var(--pircom-gray-700);
        }

        .pircom-intervention-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--pircom-primary);
            background: var(--pircom-primary-light);
            padding: 4px 10px;
            border-radius: 20px;
        }

        .pircom-progress-bar {
            height: 8px;
            background: var(--pircom-gray-200);
            border-radius: 4px;
            overflow: hidden;
            margin: 8px 0;
        }

        .pircom-progress-fill {
            height: 100%;
            background: var(--pircom-primary-gradient);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        /* Backdrop mobile */
        .pircom-sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transition: var(--pircom-transition);
        }

        .pircom-sidebar-backdrop.pircom-show {
            opacity: 1;
            visibility: visible;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 1024px) {
            .pircom-sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            
            .pircom-sidebar.pircom-mobile-open {
                transform: translateX(0);
                box-shadow: var(--pircom-shadow-lg);
            }
            
            .pircom-main-content {
                margin-left: 0 !important;
            }
            
            .pircom-mobile-btn {
                display: flex;
            }
            
            .pircom-page-title {
                font-size: 15px;
            }
            
            .pircom-metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .pircom-dashboard-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .pircom-navbar {
                padding: 0 var(--pircom-spacing-md);
            }
            
            .pircom-user-info {
                display: none;
            }
            
            .pircom-page-title {
                font-size: 14px;
            }
            
            .pircom-page-title span {
                margin: 0 2px;
            }
            
            .pircom-content-wrapper {
                padding: 8px 12px 12px 12px;
            }
            
            .pircom-dashboard-title {
                font-size: 24px;
                margin-bottom: 6px;
            }
            
            .pircom-dashboard-subtitle {
                margin-bottom: 16px;
            }
            
            .pircom-metrics-grid {
                gap: 12px;
                margin-bottom: 20px;
            }
            
            .pircom-metric-card {
                padding: 12px;
            }
            
            .pircom-metric-value {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .pircom-navbar {
                padding: 0 var(--pircom-spacing-sm);
            }
            
            .pircom-content-wrapper {
                padding: 6px 10px 10px 10px;
            }
            
            .pircom-notification-btn {
                width: 34px;
                height: 34px;
                font-size: 18px;
            }
            
            .pircom-user-btn {
                padding: 0;
            }
            
            .pircom-user-avatar {
                width: 30px;
                height: 30px;
                font-size: 13px;
            }
            
            .pircom-dropdown {
                min-width: 260px;
                right: -5px;
            }
            
            .pircom-metrics-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .pircom-dashboard-title {
                font-size: 22px;
            }
            
            .pircom-section-title {
                font-size: 16px;
                margin: 20px 0 12px 0;
            }
        }

        @keyframes pircom-slide-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pircom-content-wrapper {
            animation: pircom-slide-in 0.2s ease;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- Backdrop para mobile -->
    <div class="pircom-sidebar-backdrop" id="pircomSidebarBackdrop"></div>
    
    <div class="pircom-layout-wrapper">
        <!-- Sidebar -->
        <aside class="pircom-sidebar" id="pircomSidebar">
            <div class="pircom-sidebar-logo">
                <div class="pircom-logo-wrapper">
                    <img src="assets/pircom.png" alt="PIRCOM" class="pircom-logo-img">
                    <span class="pircom-logo-text">PIRCOM</span>
                </div>
                <button class="pircom-collapse-btn" id="pircomToggleSidebar">
                    <i class='bx bx-chevron-left'></i>
                </button>
            </div>
            
            <div class="pircom-sidebar-nav">
                <!-- Principal -->
                <div class="pircom-nav-section">
                    <div class="pircom-nav-section-title">PRINCIPAL</div>
                    <ul class="pircom-nav-list">
                        <li class="pircom-nav-item <?php echo ($current_page == 'dashboard.php') ? 'pircom-active' : ''; ?>">
                            <a href="dashboard.php" class="pircom-nav-link" data-tooltip="Dashboard">
                                <i class='bx bx-home-circle'></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Página Inicial -->
                <div class="pircom-nav-section">
                    <div class="pircom-nav-section-title">PÁGINA INICIAL</div>
                    <ul class="pircom-nav-list">
                        <li class="pircom-nav-item <?php echo ($current_page == 'configuracoes.php') ? 'pircom-active' : ''; ?>">
                            <a href="configuracoes.php" class="pircom-nav-link" data-tooltip="Missão">
                                <i class='bx bx-target-lock'></i>
                                <span>Missão</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'homepagehero.php') ? 'pircom-active' : ''; ?>">
                            <a href="homepagehero.php" class="pircom-nav-link" data-tooltip="Banner Principal">
                                <i class='bx bx-slideshow'></i>
                                <span>Banner Principal</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'noticias.php') ? 'pircom-active' : ''; ?>">
                            <a href="noticias.php" class="pircom-nav-link" data-tooltip="Notícias">
                                <i class='bx bx-news'></i>
                                <span>Notícias</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'eventos.php') ? 'pircom-active' : ''; ?>">
                            <a href="eventos.php" class="pircom-nav-link" data-tooltip="Eventos">
                                <i class='bx bx-calendar'></i>
                                <span>Eventos</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'utilizadores.php') ? 'pircom-active' : ''; ?>">
                            <a href="utilizadores.php" class="pircom-nav-link" data-tooltip="Utilizadores">
                                <i class='bx bx-users'></i>
                                <span>Utilizadores</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Abordagens -->
                <div class="pircom-nav-section">
                    <div class="pircom-nav-section-title">ABORDAGENS</div>
                    <ul class="pircom-nav-list">
                        <li class="pircom-nav-item <?php echo ($current_page == 'comunitarias.php') ? 'pircom-active' : ''; ?>">
                            <a href="comunitarias.php" class="pircom-nav-link" data-tooltip="Comunitárias">
                                <i class='bx bx-heart'></i>
                                <span>Comunitárias</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo (in_array($current_page, ['provincias.php', 'provinciasform.php'])) ? 'pircom-active' : ''; ?>">
                            <a href="provincias.php" class="pircom-nav-link" data-tooltip="Cobertura Geográfica">
                                <i class='bx bx-map'></i>
                                <span>Cobertura Geográfica</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Multimédia -->
                <div class="pircom-nav-section">
                    <div class="pircom-nav-section-title">MULTIMÉDIA</div>
                    <ul class="pircom-nav-list">
                        <li class="pircom-nav-item <?php echo ($current_page == 'documentos.php') ? 'pircom-active' : ''; ?>">
                            <a href="documentos.php" class="pircom-nav-link" data-tooltip="Documentos">
                                <i class='bx bx-file'></i>
                                <span>Documentos</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'galeria.php') ? 'pircom-active' : ''; ?>">
                            <a href="galeria.php" class="pircom-nav-link" data-tooltip="Galeria">
                                <i class='bx bx-images'></i>
                                <span>Galeria</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Blog & Doações -->
                <div class="pircom-nav-section">
                    <div class="pircom-nav-section-title">BLOG & DOAÇÕES</div>
                    <ul class="pircom-nav-list">
                        <li class="pircom-nav-item <?php echo ($current_page == 'movimentos.php') ? 'pircom-active' : ''; ?>">
                            <a href="movimentos.php" class="pircom-nav-link" data-tooltip="Nossos Movimentos">
                                <i class='bx bx-chat'></i>
                                <span>Nossos Movimentos</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'configuracoes-doacoes.php') ? 'pircom-active' : ''; ?>">
                            <a href="configuracoes-doacoes.php" class="pircom-nav-link" data-tooltip="Config. Doações">
                                <i class='bx bx-cog'></i>
                                <span>Config. Doações</span>
                            </a>
                        </li>
                        <li class="pircom-nav-item <?php echo ($current_page == 'doadores.php') ? 'pircom-active' : ''; ?>">
                            <a href="doadores.php" class="pircom-nav-link" data-tooltip="Doadores">
                                <i class='bx bx-donate-heart'></i>
                                <span>Doadores</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="pircom-main-content" id="pircomMainContent">
            <!-- Navbar -->
            <nav class="pircom-navbar">
                <div class="pircom-navbar-left">
                    <button class="pircom-mobile-btn" id="pircomMobileBtn">
                        <i class='bx bx-menu'></i>
                    </button>
                    <div class="pircom-page-title">
                        <span>/</span>
                        <?php echo $current_title; ?>
                    </div>
                </div>
                
                <div class="pircom-navbar-right">
                    <!-- Notifications -->
                    <div class="pircom-notification-wrapper">
                        <button class="pircom-notification-btn" id="pircomNotificationBtn">
                            <i class='bx bx-bell'></i>
                            <?php if ($total_notificacoes > 0): ?>
                            <span class="pircom-notification-badge" id="pircomNotificationBadge"><?php echo $total_notificacoes; ?></span>
                            <?php endif; ?>
                        </button>
                        
                        <div class="pircom-dropdown" id="pircomNotificationDropdown">
                            <div class="pircom-dropdown-header">
                                <h6>Notificações</h6>
                                <small><?php echo $total_notificacoes; ?> não lidas</small>
                            </div>
                            
                            <div class="pircom-dropdown-list" id="pircomNotificationsList">
                                <?php if ($total_notificacoes > 0): ?>
                                    <?php foreach ($notificacoes_nao_lidas as $notif): ?>
                                    <div class="pircom-dropdown-item" onclick="marcarNotificacao(<?php echo $notif['id']; ?>)">
                                        <i class='bx bx-bell'></i>
                                        <div class="pircom-item-content">
                                            <div class="pircom-item-title"><?php echo htmlspecialchars($notif['titulo']); ?></div>
                                            <div class="pircom-item-subtitle"><?php echo htmlspecialchars($notif['mensagem']); ?></div>
                                            <div class="pircom-item-time">
                                                <?php 
                                                    $diff = time() - strtotime($notif['criada_em']);
                                                    if ($diff < 60) echo 'Agora mesmo';
                                                    elseif ($diff < 3600) echo 'Há ' . floor($diff/60) . ' min';
                                                    elseif ($diff < 86400) echo 'Há ' . floor($diff/3600) . ' h';
                                                    else echo 'Há ' . floor($diff/86400) . ' d';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="pircom-empty-state">
                                        <i class='bx bx-check-circle'></i>
                                        <p>Tudo em ordem!</p>
                                        <small>Nenhuma notificação no momento</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($total_notificacoes > 0): ?>
                            <div class="pircom-dropdown-footer">
                                <button onclick="marcarTodasComoLidas()">
                                    <i class='bx bx-check-double'></i>
                                    Marcar todas como lidas
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="pircom-user-wrapper">
                        <button class="pircom-user-btn" id="pircomUserBtn">
                            <div class="pircom-user-avatar">
                                <?php echo strtoupper(substr($_SESSION["usuario_nome"], 0, 1)); ?>
                            </div>
                            <div class="pircom-user-info">
                                <span class="pircom-user-name"><?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?></span>
                                <span class="pircom-user-role"><?php echo (getUserRole() === 'admin') ? 'Administrador' : 'Gerenciador'; ?></span>
                            </div>
                            <i class='bx bx-chevron-down'></i>
                        </button>
                        
                        <div class="pircom-dropdown" id="pircomUserDropdown">
                            <div class="pircom-dropdown-header">
                                <h6>Minha Conta</h6>
                                <small><?php echo htmlspecialchars($_SESSION["usuario_email"] ?? ''); ?></small>
                            </div>
                            
                            <div class="pircom-dropdown-list">
                                <a href="editar-perfil.php" class="pircom-dropdown-item">
                                    <i class='bx bx-user-circle'></i>
                                    <div class="pircom-item-content">
                                        <div class="pircom-item-title">Meu Perfil</div>
                                        <div class="pircom-item-subtitle">Visualizar e editar informações</div>
                                    </div>
                                </a>
                                
                                <a href="editar-perfil.php?tab=password" class="pircom-dropdown-item">
                                    <i class='bx bx-lock-alt'></i>
                                    <div class="pircom-item-content">
                                        <div class="pircom-item-title">Alterar Senha</div>
                                        <div class="pircom-item-subtitle">Atualizar sua senha de acesso</div>
                                    </div>
                                </a>
                                
                                <?php if (isAdmin()): ?>
                                <a href="configuracoes.php" class="pircom-dropdown-item">
                                    <i class='bx bx-cog'></i>
                                    <div class="pircom-item-content">
                                        <div class="pircom-item-title">Configurações</div>
                                        <div class="pircom-item-subtitle">Gerenciar sistema</div>
                                    </div>
                                </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="pircom-dropdown-footer">
                                <a href="actions/logoutAction.php" style="color: var(--pircom-danger);">
                                    <i class='bx bx-log-out'></i> Terminar Sessão
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Content Area - ESPAÇAMENTO CORRIGIDO -->
            <div class="pircom-content-wrapper">
                <!-- DASHBOARD CONTEÚDO -->
                <div class="pircom-dashboard">
                    <h1 class="pircom-dashboard-title">Dashboard PIRCOM</h1>
                    <p class="pircom-dashboard-subtitle">Resumo de conteúdo e atividades</p>
                    
                    <!-- Métricas principais -->
                    <div class="pircom-metrics-grid">
                        <div class="pircom-metric-card">
                            <div class="pircom-metric-value">1</div>
                            <div class="pircom-metric-label">Notícias</div>
                        </div>
                        <div class="pircom-metric-card">
                            <div class="pircom-metric-value">4</div>
                            <div class="pircom-metric-label">Eventos</div>
                        </div>
                        <div class="pircom-metric-card">
                            <div class="pircom-metric-value">0</div>
                            <div class="pircom-metric-label">Documentos</div>
                        </div>
                        <div class="pircom-metric-card">
                            <div class="pircom-metric-value">2</div>
                            <div class="pircom-metric-label">Utilizadores</div>
                        </div>
                    </div>
                    
                    <!-- Primeira linha de gráficos -->
                    <div class="pircom-dashboard-row">
                        <div class="pircom-dashboard-card">
                            <div class="pircom-card-header">
                                <h3>Distribuição de Conteúdo</h3>
                                <span class="pircom-badge">Atualizado</span>
                            </div>
                            <div class="pircom-chart-placeholder">
                                <i class='bx bx-pie-chart-alt' style="font-size: 24px; margin-right: 8px;"></i>
                                Gráfico de distribuição
                            </div>
                        </div>
                        
                        <div class="pircom-dashboard-card">
                            <div class="pircom-card-header">
                                <h3>Resumo por Tipo</h3>
                            </div>
                            <div class="pircom-chart-placeholder">
                                <i class='bx bx-bar-chart-alt' style="font-size: 24px; margin-right: 8px;"></i>
                                Gráfico de barras
                            </div>
                        </div>
                    </div>
                    
                    <!-- Segunda linha -->
                    <div class="pircom-dashboard-row">
                        <div class="pircom-dashboard-card">
                            <div class="pircom-card-header">
                                <h3>Crescimento (Últimos 6 Meses)</h3>
                            </div>
                            <div class="pircom-chart-placeholder">
                                <i class='bx bx-line-chart' style="font-size: 24px; margin-right: 8px;"></i>
                                Gráfico de linhas
                            </div>
                        </div>
                        
                        <div class="pircom-dashboard-card">
                            <div class="pircom-card-header">
                                <h3>Áreas de Intervenção</h3>
                                <span class="pircom-badge">6 meses</span>
                            </div>
                            <div class="pircom-intervention-list">
                                <div class="pircom-intervention-item">
                                    <span class="pircom-intervention-month">Sep/2025</span>
                                    <span class="pircom-intervention-value">12</span>
                                </div>
                                <div class="pircom-intervention-item">
                                    <span class="pircom-intervention-month">Oct/2025</span>
                                    <span class="pircom-intervention-value">15</span>
                                </div>
                                <div class="pircom-intervention-item">
                                    <span class="pircom-intervention-month">Nov/2025</span>
                                    <span class="pircom-intervention-value">18</span>
                                </div>
                                <div class="pircom-intervention-item">
                                    <span class="pircom-intervention-month">Dez/2025</span>
                                    <span class="pircom-intervention-value">22</span>
                                </div>
                                <div class="pircom-intervention-item">
                                    <span class="pircom-intervention-month">Jan/2026</span>
                                    <span class="pircom-intervention-value">25</span>
                                </div>
                                <div class="pircom-intervention-item">
                                    <span class="pircom-intervention-month">Feb/2026</span>
                                    <span class="pircom-intervention-value">30</span>
                                </div>
                            </div>
                            
                            <!-- Barra de progresso exemplo -->
                            <div style="margin-top: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span style="font-size: 12px; color: var(--pircom-gray-600);">Meta anual</span>
                                    <span style="font-size: 12px; font-weight: 600; color: var(--pircom-primary);">75%</span>
                                </div>
                                <div class="pircom-progress-bar">
                                    <div class="pircom-progress-fill" style="width: 75%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Aqui vai o conteúdo específico de cada página -->
                <?php
                // O conteúdo da página específica continua aqui
                // Exemplo: include('pagina-especifica.php');
                ?>
            </div>
        </main>
    </div>

    <script>
        (function() {
            'use strict';
            
            const sidebar = document.getElementById('pircomSidebar');
            const mainContent = document.getElementById('pircomMainContent');
            const toggleBtn = document.getElementById('pircomToggleSidebar');
            const mobileBtn = document.getElementById('pircomMobileBtn');
            const backdrop = document.getElementById('pircomSidebarBackdrop');
            const notificationBtn = document.getElementById('pircomNotificationBtn');
            const userBtn = document.getElementById('pircomUserBtn');
            const notificationDropdown = document.getElementById('pircomNotificationDropdown');
            const userDropdown = document.getElementById('pircomUserDropdown');
            
            let sidebarCollapsed = localStorage.getItem('pircomSidebarCollapsed') === 'true';
            
            if (sidebarCollapsed && window.innerWidth > 1024) {
                sidebar.classList.add('pircom-collapsed');
                mainContent.classList.add('pircom-expanded');
            }
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('pircom-collapsed');
                    mainContent.classList.toggle('pircom-expanded');
                    localStorage.setItem('pircomSidebarCollapsed', sidebar.classList.contains('pircom-collapsed'));
                });
            }
            
            if (mobileBtn) {
                mobileBtn.addEventListener('click', () => {
                    sidebar.classList.add('pircom-mobile-open');
                    backdrop.classList.add('pircom-show');
                    document.body.style.overflow = 'hidden';
                });
            }
            
            if (backdrop) {
                backdrop.addEventListener('click', () => {
                    sidebar.classList.remove('pircom-mobile-open');
                    backdrop.classList.remove('pircom-show');
                    document.body.style.overflow = '';
                });
            }
            
            document.querySelectorAll('.pircom-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 1024) {
                        sidebar.classList.remove('pircom-mobile-open');
                        backdrop.classList.remove('pircom-show');
                        document.body.style.overflow = '';
                    }
                });
            });
            
            function toggleDropdown(dropdown, btn) {
                return function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    [notificationDropdown, userDropdown].forEach(d => {
                        if (d && d !== dropdown) {
                            d.classList.remove('pircom-show');
                        }
                    });
                    
                    if (dropdown) {
                        dropdown.classList.toggle('pircom-show');
                    }
                };
            }
            
            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener('click', toggleDropdown(notificationDropdown, notificationBtn));
            }
            
            if (userBtn && userDropdown) {
                userBtn.addEventListener('click', toggleDropdown(userDropdown, userBtn));
            }
            
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.pircom-notification-wrapper') && 
                    !e.target.closest('.pircom-user-wrapper')) {
                    
                    if (notificationDropdown) {
                        notificationDropdown.classList.remove('pircom-show');
                    }
                    if (userDropdown) {
                        userDropdown.classList.remove('pircom-show');
                    }
                }
            });
            
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (notificationDropdown) {
                        notificationDropdown.classList.remove('pircom-show');
                    }
                    if (userDropdown) {
                        userDropdown.classList.remove('pircom-show');
                    }
                    
                    if (sidebar && sidebar.classList.contains('pircom-mobile-open')) {
                        sidebar.classList.remove('pircom-mobile-open');
                        if (backdrop) backdrop.classList.remove('pircom-show');
                        document.body.style.overflow = '';
                    }
                }
            });
            
            window.addEventListener('resize', () => {
                if (window.innerWidth > 1024) {
                    if (sidebar) {
                        sidebar.classList.remove('pircom-mobile-open');
                    }
                    if (backdrop) {
                        backdrop.classList.remove('pircom-show');
                    }
                    document.body.style.overflow = '';
                    
                    if (localStorage.getItem('pircomSidebarCollapsed') === 'true') {
                        sidebar?.classList.add('pircom-collapsed');
                        mainContent?.classList.add('pircom-expanded');
                    } else {
                        sidebar?.classList.remove('pircom-collapsed');
                        mainContent?.classList.remove('pircom-expanded');
                    }
                }
            });
        })();

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
                    atualizarNotificacoes();
                }
            })
            .catch(error => console.error('Erro:', error));
        }

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
                    atualizarNotificacoes();
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        function atualizarNotificacoes() {
            fetch('actions/notificacoesAction.php?action=listar')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('pircomNotificationBadge');
                const list = document.getElementById('pircomNotificationsList');
                const header = document.querySelector('.pircom-dropdown-header small');
                
                if (data.total > 0) {
                    if (badge) {
                        badge.textContent = data.total;
                    } else {
                        const btn = document.getElementById('pircomNotificationBtn');
                        const newBadge = document.createElement('span');
                        newBadge.className = 'pircom-notification-badge';
                        newBadge.id = 'pircomNotificationBadge';
                        newBadge.textContent = data.total;
                        btn.appendChild(newBadge);
                    }
                    
                    let html = '';
                    data.notificacoes.forEach(notif => {
                        const diff = Math.floor((new Date() - new Date(notif.criada_em)) / 1000);
                        let timeText = 'Agora mesmo';
                        if (diff > 60) timeText = 'Há ' + Math.floor(diff/60) + ' min';
                        if (diff > 3600) timeText = 'Há ' + Math.floor(diff/3600) + ' h';
                        if (diff > 86400) timeText = 'Há ' + Math.floor(diff/86400) + ' d';
                        
                        html += `
                            <div class="pircom-dropdown-item" onclick="marcarNotificacao(${notif.id})">
                                <i class='bx bx-bell'></i>
                                <div class="pircom-item-content">
                                    <div class="pircom-item-title">${notif.titulo}</div>
                                    <div class="pircom-item-subtitle">${notif.mensagem}</div>
                                    <div class="pircom-item-time">${timeText}</div>
                                </div>
                            </div>
                        `;
                    });
                    
                    list.innerHTML = html;
                    
                    if (header) {
                        header.textContent = data.total + ' não lidas';
                    }
                    
                } else {
                    if (badge) badge.remove();
                    
                    list.innerHTML = `
                        <div class="pircom-empty-state">
                            <i class='bx bx-check-circle'></i>
                            <p>Tudo em ordem!</p>
                            <small>Nenhuma notificação no momento</small>
                        </div>
                    `;
                    
                    if (header) {
                        header.textContent = '0 não lidas';
                    }
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        setInterval(atualizarNotificacoes, 30000);
    </script>
</body>
</html>