<?php
/**
 * Footer Global
 * PIRCOM – Plataforma Inter-Religiosa de Comunicação para a Saúde
 *
 * Inclui também:
 *  – Bootstrap JS
 *  – Swiper JS (condicional via $include_swiper)
 *  – Scripts globais (navbar toggler, back-to-top, newsletter)
 */
$_year = date('Y');
?>
</main><!-- /#main-content -->

<!-- ═══════════════════════════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════════════════════════ -->
<footer class="pircom-footer" role="contentinfo">

    <!-- Faixa superior de destaque -->
    <div class="footer-cta-band">
        <div class="container">
            <div class="footer-cta-inner">
                <div class="footer-cta-text">
                    <span class="footer-cta-label">Junte-se à nossa missão</span>
                    <p>Cada contribuição transforma vidas. Apoie a saúde comunitária em Moçambique.</p>
                </div>
                <a href="doacoes.php" class="footer-cta-btn">
                    <i class="bi bi-heart-fill"></i> Fazer uma Doação
                </a>
            </div>
        </div>
    </div>

    <!-- Corpo principal -->
    <div class="footer-body">
        <div class="container">
            <div class="row g-5">

                <!-- Coluna 1 – Identidade -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <img src="assets/pircom.png" alt="PIRCOM" class="footer-logo">
                        <p class="footer-about-text">
                            A PIRCOM é uma organização baseada na fé, comprometida com a melhoria
                            da qualidade de vida das comunidades através de acções integradas de
                            saúde, educação e desenvolvimento social.
                        </p>
                        <nav aria-label="Redes sociais PIRCOM">
                            <ul class="footer-social">
                                <li>
                                    <a href="#" target="_blank" rel="noopener" aria-label="Facebook da PIRCOM" class="social-btn social-fb">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank" rel="noopener" aria-label="Twitter / X da PIRCOM" class="social-btn social-x">
                                        <i class="bi bi-twitter-x"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram da PIRCOM" class="social-btn social-ig">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank" rel="noopener" aria-label="YouTube da PIRCOM" class="social-btn social-yt">
                                        <i class="bi bi-youtube"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank" rel="noopener" aria-label="LinkedIn da PIRCOM" class="social-btn social-li">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Coluna 2 – Links rápidos -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h4 class="footer-heading">Navegação</h4>
                    <ul class="footer-nav-list">
                        <li><a href="index.php">Início</a></li>
                        <li><a href="sobre-nos.php">Quem Somos</a></li>
                        <li><a href="orgaos-sociais.php">Órgãos Sociais</a></li>
                        <li><a href="noticias.php">Notícias</a></li>
                        <li><a href="eventos.php">Eventos</a></li>
                        <li><a href="movimentos.php">Movimentos</a></li>
                    </ul>
                </div>

                <!-- Coluna 3 – Recursos -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h4 class="footer-heading">Recursos</h4>
                    <ul class="footer-nav-list">
                        <li><a href="galeria.php">Galeria</a></li>
                        <li><a href="documentos.php">Documentos</a></li>
                        <li><a href="mapa-cobertura.php">Cobertura</a></li>
                        <li><a href="doacoes.php">Doações</a></li>
                    </ul>
                </div>

                <!-- Coluna 4 – Contacto + Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="footer-heading">Contacte-nos</h4>

                    <address class="footer-address">
                        <div class="address-item">
                            <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                            <span>Av. 24 de Julho, n.º 345<br>Maputo – Moçambique</span>
                        </div>
                        <div class="address-item">
                            <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                            <a href="tel:+258841234567">+258 84 123 4567</a>
                        </div>
                        <div class="address-item">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i>
                            <a href="https://wa.me/258849876543" target="_blank" rel="noopener">+258 84 987 6543</a>
                        </div>
                        <div class="address-item">
                            <i class="bi bi-envelope-fill" aria-hidden="true"></i>
                            <a href="mailto:info@pircom.org">info@pircom.org</a>
                        </div>
                        <div class="address-item">
                            <i class="bi bi-clock-fill" aria-hidden="true"></i>
                            <span>Seg – Sex: 08:00 – 17:00</span>
                        </div>
                    </address>

                    <!-- Newsletter -->
                    <div class="footer-newsletter">
                        <p class="newsletter-label">
                            <i class="bi bi-envelope-paper-fill" aria-hidden="true"></i>
                            Receba as nossas novidades
                        </p>
                        <form class="newsletter-form" id="newsletterForm" novalidate>
                            <label for="newsletterEmail" class="visually-hidden">Endereço de e-mail</label>
                            <input
                                type="email"
                                id="newsletterEmail"
                                name="email"
                                placeholder="O seu e-mail"
                                autocomplete="email"
                                required>
                            <button type="submit" aria-label="Subscrever newsletter">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Barra de créditos de desenvolvimento -->
    <div class="footer-dev-bar">
        <div class="container">
            <div class="dev-bar-inner">
                <div class="dev-bar-text">
                    <span class="dev-label">Desenvolvido por</span>
                    <a href="https://www.conexarmanagement.com/" target="_blank" rel="noopener" class="dev-link">
                        Conexar Management – Digital Solutions Lda
                        <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    </a>
                </div>
                <p class="footer-copy">
                    &copy; <?= $_year ?> <strong>PIRCOM</strong>.
                    Todos os direitos reservados.
                    <a href="#">Privacidade</a> &middot; <a href="#">Termos</a>
                </p>
            </div>
        </div>
    </div>

</footer>

<!-- Back to Top -->
<a href="#main-content" class="back-to-top" id="backToTop" aria-label="Voltar ao topo">
    <i class="bi bi-chevron-up" aria-hidden="true"></i>
</a>

<!-- ═══════════════════════════════════════════════════════════════════════════
     FOOTER CSS (scoped)
═══════════════════════════════════════════════════════════════════════════ -->
<style>
    /* ── Tokens já definidos no header, só extensões ── */

    /* ── CTA Band ── */
    .footer-cta-band {
        background: var(--clr-brand);
        padding: 28px 0;
    }

    .footer-cta-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .footer-cta-label {
        display: block;
        font-family: var(--font-display);
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
    }

    .footer-cta-text p {
        color: rgba(255,255,255,.82);
        font-size: .95rem;
        margin: 0;
    }

    .footer-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 28px;
        background: #fff;
        color: var(--clr-brand);
        font-weight: 700;
        font-size: .9rem;
        letter-spacing: .04em;
        border-radius: 99px;
        text-decoration: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.15);
        white-space: nowrap;
        transition: transform var(--transition), box-shadow var(--transition);
    }

    .footer-cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(0,0,0,.22);
        color: var(--clr-brand-dark);
    }

    /* ── Footer Body ── */
    .footer-body {
        background: #0C0C14;
        padding: 72px 0 56px;
        position: relative;
        overflow: hidden;
    }

    /* Textura subtil */
    .footer-body::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 10% 30%, rgba(255,111,15,.06) 0%, transparent 50%),
            radial-gradient(circle at 90% 80%, rgba(255,111,15,.04) 0%, transparent 40%);
        pointer-events: none;
    }

    /* ── Brand ── */
    .footer-logo {
        width: 150px;
        height: auto;
        filter: brightness(0) invert(1);
        margin-bottom: 20px;
        display: block;
    }

    .footer-about-text {
        color: #8E8E9E;
        font-size: .9rem;
        line-height: 1.75;
        margin-bottom: 24px;
    }

    /* ── Social ── */
    .footer-social {
        display: flex;
        gap: 10px;
        list-style: none;
        padding: 0;
        flex-wrap: wrap;
    }

    .social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        font-size: 1rem;
        color: #fff;
        text-decoration: none;
        transition: transform var(--transition), opacity var(--transition);
    }

    .social-btn:hover { transform: translateY(-3px); opacity: .85; color: #fff; }

    .social-fb { background: #1877F2; }
    .social-x  { background: #000; }
    .social-ig { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); }
    .social-yt { background: #FF0000; }
    .social-li { background: #0A66C2; }

    /* ── Headings ── */
    .footer-heading {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 600;
        color: #fff;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    /* ── Nav List ── */
    .footer-nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .footer-nav-list a {
        color: #8E8E9E;
        text-decoration: none;
        font-size: .9rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color var(--transition), gap var(--transition);
    }

    .footer-nav-list a::before {
        content: '›';
        color: var(--clr-brand);
        font-weight: 700;
        flex-shrink: 0;
    }

    .footer-nav-list a:hover {
        color: #fff;
        gap: 10px;
    }

    /* ── Address ── */
    .footer-address {
        font-style: normal;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 28px;
    }

    .address-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        color: #8E8E9E;
        font-size: .9rem;
        line-height: 1.55;
    }

    .address-item i {
        color: var(--clr-brand);
        font-size: 1rem;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .address-item a {
        color: #8E8E9E;
        text-decoration: none;
        transition: color var(--transition);
    }

    .address-item a:hover { color: #fff; }

    /* ── Newsletter ── */
    .footer-newsletter {
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: var(--radius-lg);
        padding: 20px;
    }

    .newsletter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .88rem;
        font-weight: 600;
        color: #C8C8D4;
        margin-bottom: 14px;
    }

    .newsletter-label i { color: var(--clr-brand); }

    .newsletter-form {
        display: flex;
        gap: 8px;
    }

    .newsletter-form input {
        flex: 1;
        min-width: 0;
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.14);
        color: #fff;
        padding: 10px 14px;
        border-radius: var(--radius-md);
        font-size: .88rem;
        font-family: var(--font-body);
        transition: border-color var(--transition);
    }

    .newsletter-form input::placeholder { color: #5A5A6A; }
    .newsletter-form input:focus {
        outline: none;
        border-color: var(--clr-brand);
        box-shadow: 0 0 0 3px rgba(255,111,15,.15);
    }

    .newsletter-form button {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--clr-brand);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-size: 1rem;
        cursor: pointer;
        transition: background var(--transition), transform var(--transition);
    }

    .newsletter-form button:hover {
        background: var(--clr-brand-dark);
        transform: scale(1.05);
    }

    /* ── Dev Bar ── */
    .footer-dev-bar {
        background: #060609;
        padding: 18px 0;
        border-top: 1px solid rgba(255,255,255,.06);
    }

    .dev-bar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .dev-label {
        font-size: .78rem;
        color: #5A5A6A;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-right: 6px;
    }

    .dev-link {
        font-size: .88rem;
        font-weight: 600;
        color: var(--clr-brand);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: color var(--transition);
    }

    .dev-link:hover { color: #FFAA6B; }
    .dev-link i { font-size: .8rem; }

    .footer-copy {
        font-size: .82rem;
        color: #5A5A6A;
        margin: 0;
    }

    .footer-copy a {
        color: #5A5A6A;
        text-decoration: none;
        transition: color var(--transition);
    }

    .footer-copy a:hover { color: var(--clr-brand); }

    /* ── Back to Top ── */
    .back-to-top {
        position: fixed;
        bottom: 28px;
        right: 28px;
        width: 48px;
        height: 48px;
        background: var(--clr-brand);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        text-decoration: none;
        box-shadow: 0 4px 18px rgba(255,111,15,.4);
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px);
        transition: opacity var(--transition), visibility var(--transition), transform var(--transition);
        z-index: 1020;
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .back-to-top:hover {
        background: var(--clr-brand-dark);
        color: #fff;
        transform: translateY(-3px);
    }

    /* ── Responsive ── */
    @media (max-width: 767.98px) {
        .footer-cta-inner { flex-direction: column; text-align: center; }
        .footer-cta-btn   { width: 100%; justify-content: center; }
        .footer-body      { padding: 48px 0 36px; }
        .dev-bar-inner    { flex-direction: column; text-align: center; }
    }

    @media (max-width: 575.98px) {
        .footer-newsletter { padding: 16px; }
        .newsletter-form   { flex-direction: column; }
        .newsletter-form button { width: 100%; height: 42px; border-radius: var(--radius-md); }
    }
</style>

<!-- ═══════════════════════════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($include_swiper)): ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<?php endif; ?>

<script>
(function () {
    'use strict';

    /* ── Navbar toggler ─────────────────────────────────────────────────── */
    const toggler = document.getElementById('navToggler');
    const nav     = document.getElementById('pircomNav');
    const icon    = document.getElementById('navTogglerIcon');

    if (toggler && nav) {
        toggler.addEventListener('click', function () {
            const isOpen = nav.classList.toggle('show');
            toggler.setAttribute('aria-expanded', String(isOpen));
            icon.className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggler.contains(e.target)) {
                nav.classList.remove('show');
                toggler.setAttribute('aria-expanded', 'false');
                icon.className = 'bi bi-list';
            }
        });
    }

    /* ── Back to top ────────────────────────────────────────────────────── */
    const btt = document.getElementById('backToTop');

    if (btt) {
        const toggleBtt = () =>
            btt.classList.toggle('visible', window.scrollY > 400);

        window.addEventListener('scroll', toggleBtt, { passive: true });

        btt.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ── Newsletter ─────────────────────────────────────────────────────── */
    const form = document.getElementById('newsletterForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            const btn        = this.querySelector('button[type="submit"]');

            if (!emailInput.validity.valid) {
                emailInput.focus();
                return;
            }

            const original = btn.innerHTML;
            btn.innerHTML  = '<i class="bi bi-check-lg"></i>';
            btn.disabled   = true;
            btn.style.background = '#28a745';

            // TODO: substituir pelo endpoint real (ex: fetch('/api/newsletter', ...))
            console.info('[PIRCOM] Newsletter subscription:', emailInput.value);

            setTimeout(() => {
                btn.innerHTML  = original;
                btn.disabled   = false;
                btn.style.background = '';
                form.reset();
            }, 3500);
        });
    }

    /* ── Navbar scroll shadow ───────────────────────────────────────────── */
    const header = document.querySelector('.pircom-nav');

    if (header) {
        const onScroll = () =>
            header.style.boxShadow = window.scrollY > 10
                ? '0 4px 32px rgba(0,0,0,.12)'
                : '0 4px 24px rgba(0,0,0,.08)';

        window.addEventListener('scroll', onScroll, { passive: true });
    }
}());
</script>

</body>
</html>