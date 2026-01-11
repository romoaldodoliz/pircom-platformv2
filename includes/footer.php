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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            color: white;
            margin-top: auto;
            border-top: 4px solid var(--primary-color);
        }

        .footer-top {
            padding: 60px 0 30px;
            background: rgba(0, 0, 0, 0.2);
        }

        .footer-bottom {
            background: rgba(0, 0, 0, 0.3);
            padding: 25px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo {
            max-width: 200px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }

        .footer-title {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .footer-links a:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .footer-links a i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .contact-info {
            color: #ddd;
        }

        .contact-info p {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .contact-info i {
            color: var(--primary-color);
            margin-right: 10px;
            margin-top: 3px;
            min-width: 20px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        /* Developer Credit */
        .developer-credit {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .developer-credit p {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .developer-link {
            color: var(--primary-color) !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .developer-link:hover {
            color: #ff8c00 !important;
            text-decoration: underline;
        }

        .developer-link i {
            margin-right: 5px;
        }

        .developer-company {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 111, 15, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid rgba(255, 111, 15, 0.3);
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(255, 111, 15, 0.3);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: #e05a00;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 111, 15, 0.4);
        }

        /* Responsive Footer */
        @media (max-width: 768px) {
            .footer-top {
                padding: 40px 0 20px;
            }
            
            .footer-title {
                margin-top: 20px;
            }
            
            .developer-company {
                flex-direction: column;
                padding: 10px;
                text-align: center;
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
                            <p class="mt-3">A Plataforma Inter-Religiosa de Comunicação para a Saúde (PIRCOM) é uma organização baseada na fé, empenhada e comprometida com a melhoria da qualidade de vida das comunidades.</p>
                            <div class="social-links">
                                <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
                                <a href="#" title="Twitter"><i class="bi bi-twitter"></i></a>
                                <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                                <a href="#" title="YouTube"><i class="bi bi-youtube"></i></a>
                                <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h4 class="footer-title">Links Rápidos</h4>
                        <ul class="footer-links">
                            <li><a href="index.php"><i class="bi bi-chevron-right"></i> Início</a></li>
                            <li><a href="quem-somos.php"><i class="bi bi-chevron-right"></i> Quem Somos</a></li>
                            <li><a href="noticias.php"><i class="bi bi-chevron-right"></i> Notícias</a></li>
                            <li><a href="eventos.php"><i class="bi bi-chevron-right"></i> Eventos</a></li>
                            <li><a href="doacoes.php"><i class="bi bi-chevron-right"></i> Doações</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h4 class="footer-title">Multimédia</h4>
                        <ul class="footer-links">
                            <li><a href="galeria.php"><i class="bi bi-images"></i> Galeria</a></li>
                            <li><a href="documentos.php"><i class="bi bi-file-earmark-text"></i> Documentos</a></li>
                            <li><a href="mapa-cobertura.php"><i class="bi bi-map"></i> Cobertura</a></li>
                            <li><a href="movimentos.php"><i class="bi bi-people"></i> Movimentos</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h4 class="footer-title">Contactos</h4>
                        <div class="contact-info">
                            <p><i class="bi bi-geo-alt"></i> Av. 24 de Julho, nº 345<br>Maputo - Moçambique</p>
                            <p><i class="bi bi-telephone"></i> +258 84 123 4567</p>
                            <p><i class="bi bi-envelope"></i> info@pircom.org</p>
                            <p><i class="bi bi-clock"></i> Seg - Sex: 8:00 - 17:00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> PIRCOM - Plataforma Inter-Religiosa de Comunicação para a Saúde. Todos os direitos reservados.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="developer-credit">
                            <p>Desenvolvido por</p>
                            <a href="https://pircom.org.mz/index.php" target="_blank" class="developer-link">
                                <span class="developer-company">
                                    <i class="bi bi-code-slash"></i>
                                    <span>PIRCOM</span>
                                </span>
                            </a>
                        </div>
                    </div>
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
    </script>
</body>
</html>