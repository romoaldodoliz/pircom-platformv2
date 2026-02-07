</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->


<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/libs/popper/popper.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js"></script>

<!-- Page JS -->
<script src="assets/js/dashboards-analytics.js"></script>

<!-- Sidebar Scroll Effect -->
<script>
$(document).ready(function() {
    // Detectar scroll no sidebar para aplicar blur effect
    const sidebar = document.querySelector('#layout-menu');
    const menuInner = sidebar.querySelector('.menu-inner');
    
    if (menuInner) {
        menuInner.addEventListener('scroll', function() {
            if (this.scrollTop > 20) {
                sidebar.classList.add('scrolled');
            } else {
                sidebar.classList.remove('scrolled');
            }
        });
    }
    
    // Detectar JSON de erro na página e mostrar pop-up
    const bodyText = document.body.innerText;
    if (bodyText.includes('"success":false') || bodyText.includes('success": false')) {
        try {
            // Tentar extrair JSON do body
            const jsonMatch = document.body.innerHTML.match(/\{"success"\s*:\s*false[^}]*\}/);
            if (jsonMatch && typeof showError === 'function') {
                const data = JSON.parse(jsonMatch[0]);
                setTimeout(() => showError(data.message || 'Erro ao processar requisição', 7000), 800);
            }
        } catch(e) {
            console.log('Erro ao processar resposta:', e);
        }
    }
});
</script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>
