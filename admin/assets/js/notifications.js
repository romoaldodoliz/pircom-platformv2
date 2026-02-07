/* NotificationManager - small, reusable, accessible toast system
 * - Usage: NotificationManager.success('...'), .error(), .info(), .permissionDenied()
 */
(function (global) {
    class NotificationManager {
        constructor() {
            this.container = null;
            this._initContainer();
        }

        _initContainer() {
            if (this.container) return;
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'notification-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
            this.container = container;
        }

        _createToast(message, type = 'info', opts = {}) {
            const duration = opts.duration === 0 ? 0 : (opts.duration || 5000);
            const toast = document.createElement('div');
            toast.className = `notification notification-${type}`;
            toast.setAttribute('role', 'status');
            toast.tabIndex = 0;

            const iconMap = {
                success: 'bx-check-circle',
                error: 'bx-x-circle',
                warning: 'bx-exclamation-circle',
                info: 'bx-info-circle'
            };

            const icon = iconMap[type] || iconMap.info;

            toast.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon"><i class="bx ${icon}" aria-hidden="true"></i></div>
                    <div class="notification-message">${this._escapeHtml(message)}</div>
                    <button class="notification-close" aria-label="Fechar notificação"> <i class="bx bx-x"></i></button>
                </div>
                <div class="notification-progress"></div>
            `;

            // close handler
            toast.querySelector('.notification-close').addEventListener('click', () => {
                this._removeToast(toast);
            });

            this.container.appendChild(toast);
            // focus for accessibility
            toast.focus();

            if (duration > 0) {
                const progress = toast.querySelector('.notification-progress');
                progress.style.transition = `width ${duration}ms linear`;
                // kick to next tick
                setTimeout(() => (progress.style.width = '0%'), 10);
                // remove after duration
                setTimeout(() => this._removeToast(toast), duration + 300);
            }

            return toast;
        }

        _removeToast(toast) {
            if (!toast) return;
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 260);
        }

        _escapeHtml(text) {
            if (!text && text !== 0) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        success(msg, opts) { return this._createToast(msg, 'success', opts); }
        error(msg, opts) { return this._createToast(msg, 'error', opts); }
        warning(msg, opts) { return this._createToast(msg, 'warning', opts); }
        info(msg, opts) { return this._createToast(msg, 'info', opts); }

        permissionDenied(msg) {
            const title = msg || 'Ação não autorizada para o seu nível de acesso.';
            // longer visible duration and calmer tone
            return this._createToast(title, 'error', { duration: 7000 });
        }
    }

    // singleton
    const instance = new NotificationManager();
    global.NotificationManager = instance;

})(window);

// Backwards-compatible helpers
function showSuccess(msg, duration) { return NotificationManager.success(msg, { duration }); }
function showError(msg, duration) { return NotificationManager.error(msg, { duration }); }
function showWarning(msg, duration) { return NotificationManager.warning(msg, { duration }); }
function showInfo(msg, duration) { return NotificationManager.info(msg, { duration }); }

// Global protection: intercept attempts to submit remover_* forms when user is not admin
document.addEventListener('submit', function (e) {
    try {
        const form = e.target;
        if (!form || !form.action) return;
        if (form.action.indexOf('remover_') !== -1 && window.__isAdmin !== true && window.__isAdmin !== 'true') {
            e.preventDefault();
            const itemName = form.dataset.itemName || '';
            const msg = itemName ? `Apenas administradores podem remover: ${itemName}` : 'Apenas administradores podem remover conteúdo.';
            NotificationManager.permissionDenied(msg);
        }
    } catch (err) {
        // swallow
    }
});

// Intercept clicks on explicit disabled-delete buttons (for UI cases where button is not a submit)
document.addEventListener('click', function (e) {
    const btn = e.target.closest && e.target.closest('.disabled-delete');
    if (!btn) return;
    e.preventDefault();
    const name = btn.getAttribute('data-item-name') || '';
    const message = name ? `Apenas administradores podem remover: ${name}` : 'Apenas administradores podem remover conteúdo.';
    NotificationManager.permissionDenied(message);
});

