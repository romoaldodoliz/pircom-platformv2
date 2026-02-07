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
            --dark-bg: #0a1929;
            --light-bg: #f8f9fa;
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        /* Footer Styles - Redesenhado */
        footer {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #000000 100%);
            color: white;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, #ff8c00 50%, var(--primary-color) 100%);
        }

        .footer-top {
            padding: 70px 0 40px;
            position: relative;
            z-index: 1;
        }

        .footer-top::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><path d="M0,50 Q25,25 50,50 T100,50" stroke="rgba(255,111,15,0.05)" stroke-width="2" fill="none"/></svg>');
            opacity: 0.3;
            z-index: -1;
        }

        .footer-bottom {
            background: rgba(0, 0, 0, 0.8);
            padding: 25px 0;
            position: relative;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo {
            max-width: 200px;
            margin-bottom: 25px;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }

        .footer-logo:hover {
            transform: scale(1.05);
        }

        .footer-title {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), #ff8c00);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .footer-col:hover .footer-title::after {
            width: 80px;
        }

        .footer-about p {
            color: #b0b0b0;
            line-height: 1.8;
            font-size: 0.95rem;
            margin-bottom: 25px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
            position: relative;
        }

        .footer-links a {
            color: #d1d1d1;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            padding: 5px 0;
        }

        .footer-links a::before {
            content: '›';
            color: var(--primary-color);
            margin-right: 10px;
            font-weight: bold;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
            transform: translateX(8px);
        }

        .footer-links a:hover::before {
            transform: translateX(3px);
        }

        .contact-info {
            color: #d1d1d1;
        }

        .contact-info p {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .contact-info i {
            color: var(--primary-color);
            margin-right: 15px;
            margin-top: 3px;
            min-width: 20px;
            font-size: 1.1rem;
        }

        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            color: white;
            font-size: 18px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c00 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .social-links a i {
            position: relative;
            z-index: 1;
        }

        .social-links a:hover {
            transform: translateY(-3px) rotate(5deg);
            box-shadow: 0 10px 20px rgba(255, 111, 15, 0.3);
        }

        .social-links a:hover::before {
            opacity: 1;
        }

        .social-links a:hover i {
            color: white;
        }

        /* Developer Section - Melhorado */
        .developer-section {
            background: linear-gradient(135deg, rgba(255, 111, 15, 0.1) 0%, rgba(0, 0, 0, 0.3) 100%);
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            border: 1px solid rgba(255, 111, 15, 0.2);
            position: relative;
            overflow: hidden;
        }

        .developer-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="rgba(255,111,15,0.05)" d="M50,0 L100,50 L50,100 L0,50 Z"/></svg>');
            opacity: 0.5;
        }

        .developer-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .developer-header h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .developer-header p {
            color: #b0b0b0;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .developer-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .conexar-logo {
            width: 180px;
            height: auto;
            filter: brightness(0) invert(1);
            transition: all 0.3s ease;
        }

        .conexar-logo:hover {
            transform: scale(1.05);
            filter: brightness(0) invert(1) drop-shadow(0 0 10px rgba(255, 111, 15, 0.3));
        }

        .developer-info {
            flex: 1;
            min-width: 250px;
        }

        .developer-info h6 {
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .developer-info p {
            color: #b0b0b0;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .developer-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c00 100%);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
        }

        .developer-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 111, 15, 0.4);
            color: white;
            gap: 12px;
        }

        .developer-btn i {
            transition: transform 0.3s ease;
        }

        .developer-btn:hover i {
            transform: translateX(3px);
        }

        /* Copyright */
        .copyright {
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
        }

        .copyright p {
            color: #8a8a8a;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .copyright a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .copyright a:hover {
            color: #ff8c00;
            text-decoration: underline;
        }

        /* Back to Top Button - Melhorado */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c00 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 22px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(255, 111, 15, 0.3);
            border: 3px solid rgba(255, 255, 255, 0.1);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 5px 20px rgba(255, 111, 15, 0.3);
            }
            50% {
                box-shadow: 0 5px 25px rgba(255, 111, 15, 0.5);
            }
            100% {
                box-shadow: 0 5px 20px rgba(255, 111, 15, 0.3);
            }
        }

        .back-to-top:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 30px rgba(255, 111, 15, 0.5);
            animation: none;
        }

        /* Newsletter Section */
        .newsletter-section {
            background: linear-gradient(135deg, rgba(255, 111, 15, 0.15) 0%, rgba(0, 0, 0, 0.2) 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 111, 15, 0.2);
        }

        .newsletter-title {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .newsletter-title i {
            color: var(--primary-color);
        }

        .newsletter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .newsletter-form input {
            flex: 1;
            min-width: 200px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .newsletter-form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 111, 15, 0.2);
        }

        .newsletter-form input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .newsletter-form button {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff8c00 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .newsletter-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 111, 15, 0.4);
        }

        /* Responsive Footer */
        @media (max-width: 992px) {
            .footer-top {
                padding: 50px 0 30px;
            }
            
            .developer-content {
                flex-direction: column;
                text-align: center;
            }
            
            .developer-info {
                text-align: center;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-form input {
                min-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .footer-top {
                padding: 40px 0 20px;
            }
            
            .footer-title {
                margin-top: 30px;
            }
            
            .social-links {
                justify-content: center;
            }
            
            .back-to-top {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                right: 20px;
            }
        }

        @media (max-width: 576px) {
            .developer-section {
                padding: 20px;
            }
            
            .conexar-logo {
                width: 150px;
            }
        }

        /* Navbar Styles (existing) */
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

    <main>
        <!-- O conteúdo da página vai aqui -->
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-about">
                            <img src="assets/pircom.png" alt="PIRCOM Logo" class="footer-logo">
                            <p>A Plataforma Inter-Religiosa de Comunicação para a Saúde (PIRCOM) é uma organização baseada na fé, empenhada e comprometida com a melhoria da qualidade de vida das comunidades através de ações integradas de saúde, educação e desenvolvimento social.</p>
                            <div class="social-links">
                                <a href="#" title="Facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                                <a href="#" title="Twitter" target="_blank"><i class="bi bi-twitter-x"></i></a>
                                <a href="#" title="Instagram" target="_blank"><i class="bi bi-instagram"></i></a>
                                <a href="#" title="YouTube" target="_blank"><i class="bi bi-youtube"></i></a>
                                <a href="#" title="LinkedIn" target="_blank"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h4 class="footer-title">Links Rápidos</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Início</a></li>
                            <li><a href="quem-somos.php">Quem Somos</a></li>
                            <li><a href="noticias.php">Notícias</a></li>
                            <li><a href="eventos.php">Eventos</a></li>
                            <li><a href="doacoes.php">Doações</a></li>
                            <li><a href="contacto.php">Contactos</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h4 class="footer-title">Recursos</h4>
                        <ul class="footer-links">
                            <li><a href="galeria.php">Galeria Multimédia</a></li>
                            <li><a href="documentos.php">Documentos</a></li>
                            <li><a href="mapa-cobertura.php">Áreas de Cobertura</a></li>
                            <li><a href="movimentos.php">Nossos Movimentos</a></li>
                            <li><a href="#">Relatórios Anuais</a></li>
                            <li><a href="#">Política de Privacidade</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h4 class="footer-title">Contacte-nos</h4>
                        <div class="contact-info">
                            <p><i class="bi bi-geo-alt"></i> Av. 24 de Julho, nº 345<br>Maputo - Moçambique</p>
                            <p><i class="bi bi-telephone"></i> +258 84 123 4567</p>
                            <p><i class="bi bi-whatsapp"></i> +258 84 987 6543</p>
                            <p><i class="bi bi-envelope"></i> info@pircom.org</p>
                            <p><i class="bi bi-clock"></i> Seg - Sex: 8:00 - 17:00</p>
                        </div>
                        
                        <!-- Newsletter -->
                        <div class="newsletter-section">
                            <div class="newsletter-title">
                                <i class="bi bi-envelope-paper"></i>
                                <span>Receba as nossas novidades</span>
                            </div>
                            <form class="newsletter-form">
                                <input type="email" placeholder="Seu email" required>
                                <button type="submit">Subscrever</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Developer Section -->
                <div class="developer-section">
                    <div class="developer-header">
                        <h5>Desenvolvimento Tecnológico</h5>
                        <p>Este projeto foi desenvolvido com expertise técnica por:</p>
                    </div>
                    
                    <div class="developer-content">
                        <img src="https://www.conexarmanagement.com/assets/logo-conexar-DR0P-qf3.png" 
                             alt="Conexar Management - Digital Solutions Lda" 
                             class="conexar-logo"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/180x60/FF6F0F/FFFFFF?text=Conexar+Management'">
                        
                        <div class="developer-info">
                            <h6>Conexar Management - Digital Solutions Lda</h6>
                            <p>Especialistas em soluções digitais inovadoras, desenvolvimento web e gestão de projetos tecnológicos. Transformamos ideias em soluções digitais eficientes e escaláveis.</p>
                            <a href="https://www.conexarmanagement.com/" target="_blank" class="developer-btn">
                                <span>Visitar Website</span>
                                <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Copyright -->
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <strong>PIRCOM</strong> - Plataforma Inter-Religiosa de Comunicação para a Saúde. Todos os direitos reservados.</p>
                    <p class="mt-2">
                        <a href="#">Política de Privacidade</a> | 
                        <a href="#">Termos de Uso</a> | 
                        <a href="#">Mapa do Site</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top">
        <i class="bi bi-chevron-up"></i>
    </a>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Back to Top Button
        const backToTopButton = document.querySelector('.back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Newsletter form submission
        document.querySelector('.newsletter-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Simulate submission
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="bi bi-check-circle"></i> Inscrito!';
            button.disabled = true;
            button.style.background = '#28a745';
            
            // Reset after 3 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.style.background = '';
                this.reset();
            }, 3000);
            
            // Here you would typically send the email to your server
            console.log('Newsletter subscription:', email);
        });

        // Add hover effects to footer sections
        document.querySelectorAll('.footer-col').forEach(col => {
            col.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            col.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>