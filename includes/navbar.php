<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Pircom - Plataforma Inter-Religiosa de Comunicação para a Saúde</title>
    <meta name="author" content="Romoaldo Edmundo Doliz">
    <meta name="co-author" content="Romoaldo Edmundo Doliz">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="robots" content="Pircom - Plataforma Inter-Religiosa de Comunicação para a Saúde">
    <meta name="description" content="A Plataforma Inter-Religiosa de Comunicação para a Saúde (PIRCOM) é uma organização baseada na fé, empenhada e comprometida com a melhoria da qualidade de ...">
    <title><?php echo isset($page_title) ? $page_title : 'PIRCOM - Plataforma Inter-Religiosa de Comunicação para a Saúde'; ?></title>
    
    
    <!-- Bootstrap CSS -->
     <link rel="icon" href="assets/img/hello.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <?php if (isset($include_swiper) && $include_swiper): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <?php endif; ?>
    
  <style>
    :root {
        --primary-color: #FF6F0F;
        --secondary-color: #000000;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding-top: 70px;
        color: var(--secondary-color);
    }

    /* Navbar */
    .navbar {
        background: white;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        border-bottom: 3px solid var(--primary-color);
        transition: all 0.3s;
    }

    .navbar-brand {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color) !important;
        transition: transform 0.3s;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
    }

    .nav-link {
        color: var(--secondary-color) !important;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 0 10px;
        position: relative;
        padding: 8px 12px !important;
        border-radius: 6px;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%) scaleX(0);
        width: 80%;
        height: 2px;
        background: var(--primary-color);
        transition: transform 0.3s ease;
    }

    .nav-link:hover::after,
    .nav-link.active::after {
        transform: translateX(-50%) scaleX(1);
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
        background-color: rgba(255, 111, 15, 0.05);
        transform: translateY(-1px);
    }

    .nav-link.active {
        color: var(--primary-color) !important;
        font-weight: 600;
    }

    /* Section Titles */
    .section-title {
        text-align: center;
        margin: 70px 0 50px;
    }

    .section-title h2 {
        font-size: 42px;
        font-weight: 800;
        color: var(--secondary-color);
        position: relative;
        display: inline-block;
    }

    .section-title h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), #c70808);
        border-radius: 2px;
    }

    .section-title p {
        font-size: 20px;
        color: #666;
        margin-top: 25px;
        font-weight: 300;
    }

    /* Cards */
    .card {
        border: none;
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 15px;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-15px);
        box-shadow: 0 15px 40px rgba(255, 111, 15, 0.2);
    }

    .card img {
        transition: transform 0.4s;
    }

    .card:hover img {
        transform: scale(1.05);
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #E05A00 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(255, 111, 15, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 111, 15, 0.4);
        background: linear-gradient(135deg, #E05A00 0%, var(--primary-color) 100%);
    }

    .btn-outline-primary {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 111, 15, 0.4);
    }

    /* Icon Boxes */
    .icon-box {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s;
        height: 100%;
    }

    .icon-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(255, 111, 15, 0.2);
    }

    .icon-box i {
        font-size: 48px;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .icon-box h4 {
        font-size: 20px;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 15px;
    }

    /* Dropdown Styles */
    .navbar .dropdown-menu {
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        border-radius: 10px;
        border-top: 3px solid var(--primary-color);
        padding: 10px 0;
    }

    .navbar .dropdown-item {
        padding: 10px 25px;
        font-weight: 500;
        transition: all 0.3s;
        color: var(--secondary-color);
    }

    .navbar .dropdown-item:hover {
        background: rgba(251, 10, 10, 0.1);
        color: var(--primary-color);
        padding-left: 30px;
    }

    .navbar .dropdown-toggle::after {
        vertical-align: 0.1em;
        margin-left: 0.3em;
    }

    @media (max-width: 768px) {
        body {
            padding-top: 60px;
        }
        
        .section-title h2 {
            font-size: 32px;
        }

        .nav-link {
            margin: 5px 0;
        }
    }

    @media (max-width: 991px) {
        .navbar .dropdown-menu {
            border: none;
            box-shadow: none;
            background: #f8f9fa;
            margin-top: 10px;
        }
    }
</style>
</head>
<body>
    <style>
        /* Dropdown Styles */
        .navbar .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border-radius: 10px;
            border-top: 3px solid var(--primary-color);
            padding: 10px 0;
        }

        .navbar .dropdown-item {
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
            color: var(--secondary-color);
        }

        .navbar .dropdown-item:hover {
            background: rgba(255, 111, 15, 0.1);
            color: var(--primary-color);
            padding-left: 30px;
        }

        .navbar .dropdown-toggle::after {
            vertical-align: 0.1em;
            margin-left: 0.3em;
        }

        @media (max-width: 991px) {
            .navbar .dropdown-menu {
                border: none;
                box-shadow: none;
                background: #f8f9fa;
                margin-top: 10px;
            }
        }
    </style>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/pircom.png" alt="PIRCOM" height="45" class="me-2">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                            <span>INÍCIO</span>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['quem-somos.php', 'mapa-cobertura.php', 'contacto.php'])) ? 'active' : ''; ?>" href="#" id="navbarDropdownSobre" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>SOBRE</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownSobre">
                            <li><a class="dropdown-item" href="quem-somos.php">QUEM SOMOS</a></li>
                            <li><a class="dropdown-item" href="mapa-cobertura.php">COBERTURA GEOGRÁFICA</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'noticias.php') ? 'active' : ''; ?>" href="noticias.php">
                            <span>NOTÍCIAS</span>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['galeria.php', 'documentos.php'])) ? 'active' : ''; ?>" href="#" id="navbarDropdownMultimedia" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>MULTIMEDIA</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMultimedia">
                            <li><a class="dropdown-item" href="galeria.php">GALERIA</a></li>
                            <li><a class="dropdown-item" href="documentos.php">DOCUMENTOS</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'eventos.php') ? 'active' : ''; ?>" href="eventos.php">
                            <span>EVENTOS</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['movimentos.php', 'movimento-detalhes.php'])) ? 'active' : ''; ?>" href="movimentos.php">
                            <span>NOSSOS MOVIMENTOS</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'doacoes.php') ? 'active' : ''; ?>" href="doacoes.php">
                            <span>DOAÇÕES</span>
                        </a>
                    </li>

                     <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contacto.php') ? 'active' : ''; ?>" href="contacto.php">
                            <span>CONTACTOS</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
