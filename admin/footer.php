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

<script>
(function() {
    'use strict';
    
    // Elementos
    const sidebar = document.getElementById('pircomSidebar');
    const mainContent = document.getElementById('pircomMainContent');
    const toggleBtn = document.getElementById('pircomToggleSidebar');
    const mobileBtn = document.getElementById('pircomMobileBtn');
    const backdrop = document.getElementById('pircomSidebarBackdrop');
    const notificationBtn = document.getElementById('pircomNotificationBtn');
    const userBtn = document.getElementById('pircomUserBtn');
    const notificationDropdown = document.getElementById('pircomNotificationDropdown');
    const userDropdown = document.getElementById('pircomUserDropdown');
    
    // Breakpoint mobile
    const MOBILE_BREAKPOINT = 1024;
    
    // Verificar se é mobile
    function isMobile() {
        return window.innerWidth < MOBILE_BREAKPOINT;
    }
    
    // Estado da sidebar (apenas para desktop)
    let sidebarCollapsed = localStorage.getItem('pircomSidebarCollapsed') === 'true';
    
    // Inicializar sidebar apenas em desktop
    if (!isMobile() && sidebarCollapsed) {
        sidebar.classList.add('pircom-collapsed');
        mainContent.classList.add('pircom-expanded');
    }
    
    // Toggle sidebar (desktop)
    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            if (!isMobile()) {
                sidebar.classList.toggle('pircom-collapsed');
                mainContent.classList.toggle('pircom-expanded');
                localStorage.setItem('pircomSidebarCollapsed', sidebar.classList.contains('pircom-collapsed'));
            }
        });
    }
    
    // Mobile menu - ABRIR
    if (mobileBtn) {
        mobileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            sidebar.classList.add('pircom-mobile-open');
            backdrop.classList.add('pircom-show');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Mobile menu - FECHAR (backdrop)
    if (backdrop) {
        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('pircom-mobile-open');
            backdrop.classList.remove('pircom-show');
            document.body.style.overflow = '';
        });
    }
    
    // Fechar mobile menu ao clicar em link
    document.querySelectorAll('.pircom-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.remove('pircom-mobile-open');
                backdrop.classList.remove('pircom-show');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Dropdowns
    function toggleDropdown(dropdown, btn) {
        return function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            // Fechar outros dropdowns
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
    
    // Fechar dropdowns ao clicar fora
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
    
    // Tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Fechar dropdowns
            if (notificationDropdown) {
                notificationDropdown.classList.remove('pircom-show');
            }
            if (userDropdown) {
                userDropdown.classList.remove('pircom-show');
            }
            
            // Fechar mobile menu
            if (sidebar && sidebar.classList.contains('pircom-mobile-open')) {
                sidebar.classList.remove('pircom-mobile-open');
                if (backdrop) backdrop.classList.remove('pircom-show');
                document.body.style.overflow = '';
            }
        }
    });
    
    // Responsive - atualizar ao redimensionar
    window.addEventListener('resize', () => {
        if (!isMobile()) {
            // Modo desktop
            sidebar.classList.remove('pircom-mobile-open');
            if (backdrop) {
                backdrop.classList.remove('pircom-show');
            }
            document.body.style.overflow = '';
            
            // Restaurar estado da sidebar
            if (localStorage.getItem('pircomSidebarCollapsed') === 'true') {
                sidebar.classList.add('pircom-collapsed');
                mainContent.classList.add('pircom-expanded');
            } else {
                sidebar.classList.remove('pircom-collapsed');
                mainContent.classList.remove('pircom-expanded');
            }
        } else {
            // Modo mobile - garantir que sidebar não está collapsed
            sidebar.classList.remove('pircom-collapsed');
            mainContent.classList.remove('pircom-expanded');
        }
    });
})();

// Funções de notificação
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
            // Atualizar badge
            if (badge) {
                badge.textContent = data.total;
            } else {
                const btn = document.getElementById('pircomNotificationBtn');
                if (btn) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'pircom-notification-badge';
                    newBadge.id = 'pircomNotificationBadge';
                    newBadge.textContent = data.total;
                    btn.appendChild(newBadge);
                }
            }
            
            // Atualizar lista
            if (list) {
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
                                <div class="pircom-item-title">${escapeHtml(notif.titulo)}</div>
                                <div class="pircom-item-subtitle">${escapeHtml(notif.mensagem)}</div>
                                <div class="pircom-item-time">${timeText}</div>
                            </div>
                        </div>
                    `;
                });
                
                list.innerHTML = html;
            }
            
            // Atualizar header
            if (header) {
                header.textContent = data.total + ' não lidas';
            }
            
        } else {
            // Remover badge
            if (badge) badge.remove();
            
            // Mostrar estado vazio
            if (list) {
                list.innerHTML = `
                    <div class="pircom-empty-state">
                        <i class='bx bx-check-circle'></i>
                        <p>Tudo em ordem!</p>
                        <small>Nenhuma notificação no momento</small>
                    </div>
                `;
            }
            
            // Atualizar header
            if (header) {
                header.textContent = '0 não lidas';
            }
        }
    })
    .catch(error => console.error('Erro:', error));
}

// Helper para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Atualizar notificações periodicamente
setInterval(atualizarNotificacoes, 30000);

// Carregar notificações ao iniciar
document.addEventListener('DOMContentLoaded', function() {
    atualizarNotificacoes();
});
</script>

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
