<?php
$page_title = "PIRCOM - Quem Somos";
include 'config/conexao.php';

// Buscar missão, visão e valores do banco de dados
$config_query = $conn->query("SELECT * FROM config LIMIT 1");
$config = $config_query->fetch_assoc();

include 'includes/navbar.php';
?>

<style>
    .team-member img {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transition: transform 0.3s;
    }

    .team-member img:hover {
        transform: scale(1.05);
    }

    .social-icons {
        margin-top: 20px;
    }

    .social-icons a {
        display: inline-block;
        width: 40px;
        height: 40px;
        line-height: 40px;
        text-align: center;
        border-radius: 50%;
        background: var(--secondary-color);
        color: white;
        margin: 0 5px;
        transition: all 0.3s;
    }

    .social-icons a:hover {
        background: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 111, 15, 0.3);
    }

    .about-card {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .about-card h3 {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .leader-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        text-align: center;
        transition: all 0.3s;
        height: 100%;
        border: 2px solid transparent;
    }
    
    .leader-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(255, 111, 15, 0.2);
        border-color: var(--primary-color);
    }
    
    .leader-card img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
        border: 5px solid var(--primary-color);
        box-shadow: 0 5px 20px rgba(255, 111, 15, 0.3);
    }
    
    .leader-card h4 {
        color: var(--secondary-color);
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1.3rem;
    }
    
    .leader-card .position {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1rem;
    }
</style>

<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>SOBRE NÓS</h2>
            <p>Plataforma Inter-Religiosa de Comunicação para a Saúde</p>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto">
                <div class="about-card">
                    <h3 style="text-align: center; font-size: 2rem; margin-bottom: 30px;">PIRCOM</h3>
                    <p class="lead" style="text-align: justify;">
                        A <strong>Plataforma Inter-Religiosa de Comunicação para a Saúde (PIRCOM)</strong> é uma organização baseada na fé, 
                        empenhada e comprometida com a melhoria da qualidade de vida e das condições de saúde da população Moçambicana 
                        através da mobilização das comunidades para se empenharem na eliminação da Malária e na redução da incidência e 
                        impacto de outros problemas de saúde pública.
                    </p>
                    <p style="text-align: justify;">
                        Criada em <strong>19 de abril de 2006</strong>, a PIRCOM é considerada a <strong>primeira aliança múltipla baseada na fé</strong> 
                        através da colaboração entre comunidades <strong>Cristãs, Muçulmanas, Hindus e Bahai</strong> visando contribuir na melhoria 
                        do comportamento dos beneficiários prioritários em relação à prevenção e tratamento da Malária, Nutrição, 
                        Saúde Materno, Neonatal e Infantil e HIV.
                    </p>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="about-card">
                    <h3><i class="bi bi-bullseye"></i> Nossa Missão</h3>
                    <p style="text-align: justify;">
                        <?php echo $config ? nl2br(htmlspecialchars($config['missao'])) : 
                        'Mobilizar comunidades religiosas para promover a saúde pública em Moçambique, focando na eliminação da malária, ' .
                        'nutrição, saúde materno-infantil e HIV/SIDA através de comunicação para mudança social e comportamental baseada ' .
                        'em valores religiosos e escrituras sagradas.'; ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="about-card">
                    <h3><i class="bi bi-eye"></i> Nossa Visão</h3>
                    <p style="text-align: justify;">
                        <?php echo $config ? nl2br(htmlspecialchars($config['visao'])) : 
                        'Ser a plataforma de referência em Moçambique na articulação inter-religiosa para a promoção da saúde pública, ' .
                        'contribuindo para comunidades mais saudáveis e resilientes através da colaboração baseada na fé, promovendo ' .
                        'também os direitos humanos, paz e reconciliação.'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Liderança -->
<section id="team" class="team py-5" style="background: linear-gradient(135deg, #f9f9f9 0%, #ffffff 100%);">
    <div class="container">
        <div class="section-title">
            <h2>NOSSA LIDERANÇA</h2>
            <p>Conselho de Direcção da PIRCOM</p>
        </div>
        
        <div class="row g-4 mb-5">
            <!-- Bispo Dinis Matsolo -->
            <div class="col-lg-6 col-md-6">
                <div class="leader-card">
                    <img src="assets/members/cropped.jpg" alt="Bispo Dinis Matsolo">
                    <h4>Bispo Dinis Matsolo</h4>
                    <p class="position">Director Executivo</p>
                    <p style="color: #666; text-align: center; line-height: 1.8;">
                        Líder dedicado com vasta experiência em mobilização comunitária e gestão de programas de saúde pública. 
                        Responsável pela coordenação geral das operações e estratégias da PIRCOM.
                    </p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-envelope"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Dom Dinis Sengulare -->
            <div class="col-lg-6 col-md-6">
                <div class="leader-card">
                    <img src="assets/members/dom.jpg" alt="Dom Dinis Sengulare">
                    <h4>Dom Dinis Sengulare</h4>
                    <p class="position">PR do Conselho de Direcção</p>
                    <p style="color: #666; text-align: center; line-height: 1.8;">
                        Responsável pelas relações públicas e comunicação institucional da PIRCOM, promovendo o diálogo inter-religioso 
                        e fortalecendo parcerias com comunidades e organizações.
                    </p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-envelope"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informação adicional -->
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="about-card" style="background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%); color: white;">
                    <h3 style="color: white; text-align: center;">
                        <i class="bi bi-people-fill"></i> Colaboração Inter-Religiosa
                    </h3>
                    <p style="text-align: center; font-size: 1.1rem; margin-bottom: 0;">
                        A PIRCOM reúne líderes e comunidades de diversas religiões (Cristãos, Muçulmanos, Hindus e Bahai) 
                        trabalhando juntos pela saúde e bem-estar da população moçambicana.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Valores -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>NOSSOS VALORES</h2>
            <p>Princípios que nos orientam</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="icon-box text-center">
                    <i class="bi bi-heart-pulse" style="font-size: 48px; color: var(--primary-color);"></i>
                    <h4 class="mt-3">Saúde para Todos</h4>
                    <p>Compromisso com a saúde pública e bem-estar comunitário</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="icon-box text-center">
                    <i class="bi bi-peace" style="font-size: 48px; color: var(--primary-color);"></i>
                    <h4 class="mt-3">Diálogo Inter-Religioso</h4>
                    <p>Respeito e colaboração entre todas as religiões</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="icon-box text-center">
                    <i class="bi bi-people" style="font-size: 48px; color: var(--primary-color);"></i>
                    <h4 class="mt-3">Comunidade</h4>
                    <p>Mobilização e empoderamento comunitário</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="icon-box text-center">
                    <i class="bi bi-shield-check" style="font-size: 48px; color: var(--primary-color);"></i>
                    <h4 class="mt-3">Integridade</h4>
                    <p>Transparência, ética e responsabilidade</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
include 'includes/footer.php';
$conn->close();
?>
