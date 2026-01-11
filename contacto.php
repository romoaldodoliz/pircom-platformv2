<?php
$page_title = "Pircom - Contacto";
include 'includes/navbar.php';
?>

<style>
    .contact-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 70vh;
        padding: 80px 0;
    }

    .contact-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        height: 100%;
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(251, 10, 10, 0.15);
    }

    .contact-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #c70808 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        transition: all 0.3s ease;
    }

    .contact-card:hover .contact-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .contact-icon i {
        font-size: 36px;
        color: white;
    }

    .contact-card h4 {
        color: var(--secondary-color);
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 22px;
    }

    .contact-card p {
        color: #666;
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .contact-card a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .contact-card a:hover {
        color: #c70808;
        text-decoration: underline;
    }

    .map-container {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        margin-top: 50px;
    }

    .map-container h3 {
        color: var(--secondary-color);
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
    }

    .map-frame {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .cta-contact {
        background: linear-gradient(135deg, var(--primary-color) 0%, #c70808 100%);
        color: white;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        margin-top: 50px;
    }

    .cta-contact h3 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .cta-contact p {
        font-size: 18px;
        margin-bottom: 25px;
        opacity: 0.95;
    }

    .btn-contact {
        background: white;
        color: var(--primary-color);
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-contact:hover {
        background: var(--secondary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
    }

    .social-link {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-link:hover {
        background: white;
        color: var(--primary-color);
        transform: translateY(-5px);
    }

    .section-title h2 {
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }

    .section-title h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
    }

    @media (max-width: 768px) {
        .contact-section {
            padding: 50px 0;
        }
        
        .contact-card {
            padding: 30px 20px;
            margin-bottom: 20px;
        }

        .cta-contact {
            padding: 30px 20px;
        }
    }
</style>

<section class="contact-section">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>CONTACTE-NOS</h2>
            <p style="font-size: 18px; color: #666; margin-top: 15px;">Estamos aqui para ouvir você. Entre em contacto connosco!</p>
        </div>

        <div class="row g-4">
            <!-- Localização -->
            <div class="col-lg-6 col-md-6">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h4>Nossa Localização</h4>
                    <p>
                        Rua João Carlos Raposo Beirão, Nº 340<br>
                        Polana Cimento - Maputo<br>
                        Moçambique
                    </p>
                </div>
            </div>

            <!-- Email -->
            <div class="col-lg-6 col-md-6">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <h4>Email</h4>
                    <p>
                        <a href="mailto:pircom@pircom.org.mz">pircom@pircom.org.mz</a><br>
                        <span style="color: #999; font-size: 14px;">Respondemos em até 24 horas</span>
                    </p>
                </div>
            </div>

            <!-- Telefone -->
            <div class="col-lg-6 col-md-6">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h4>Telefone</h4>
                    <p>
                        <a href="tel:+258823070991">(+258) 823 070 991</a><br>
                        <span style="color: #999; font-size: 14px;">Seg - Sex: 08h00 - 17h00</span>
                    </p>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="col-lg-6 col-md-6">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h4>WhatsApp</h4>
                    <p>
                        <a href="https://wa.me/258823070991" target="_blank">(+258) 823 070 991</a><br>
                        <span style="color: #999; font-size: 14px;">Atendimento rápido via mensagem</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="map-container">
            <h3><i class="bi bi-map me-2 text-danger"></i>Encontre-nos no Mapa</h3>
            <div class="map-frame">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3584.4889234567!2d32.589!3d-25.965!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjXCsDU3JzU0LjAiUyAzMsKwMzUnMjAuNCJF!5e0!3m2!1spt-PT!2smz!4v1234567890" 
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
            <p class="text-center text-muted mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Polana Cimento, próximo aos principais pontos de referência da cidade
            </p>
        </div>

        <!-- CTA -->
        <div class="cta-contact">
            <h3><i class="bi bi-chat-dots me-2"></i>Tem Alguma Dúvida?</h3>
            <p>Estamos disponíveis para responder às suas questões e fornecer mais informações sobre o nosso trabalho.</p>
            <a href="mailto:pircom@pircom.org.mz" class="btn-contact">
                <i class="bi bi-envelope-fill me-2"></i>Enviar Mensagem
            </a>
            
            <div class="social-links">
                <a href="https://wa.me/258823070991" target="_blank" class="social-link" title="WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                </a>
                <a href="mailto:pircom@pircom.org.mz" class="social-link" title="Email">
                    <i class="bi bi-envelope-fill"></i>
                </a>
                <a href="tel:+258823070991" class="social-link" title="Telefone">
                    <i class="bi bi-telephone-fill"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>