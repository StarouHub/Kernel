<?php
/**
 * Notification Toast System - Pour afficher les notifications en temps réel
 */
?>
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
    <!-- Les toasts seront ajoutés ici par JavaScript -->
</div>

<script>
// Système de notifications Toast
class NotificationToast {
    static show(message, type = 'info', duration = 5000) {
        const container = document.getElementById('toastContainer');
        const id = 'toast_' + Date.now();
        
        const colors = {
            success: '#16a34a',
            danger: '#dc2626',
            warning: '#f59e0b',
            info: '#0284c7'
        };
        
        const icons = {
            success: '✅',
            danger: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        const html = `
            <div id="${id}" class="toast" role="alert" style="
                background: white;
                border-left: 4px solid ${colors[type] || colors.info};
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                animation: slideIn 0.3s ease;
            ">
                <div class="toast-body" style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                    <div style="font-size: 1.5rem;">${icons[type]}</div>
                    <div>${message}</div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        
        // Animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(100px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        `;
        document.head.appendChild(style);
        
        // Auto-remove après duration
        setTimeout(() => {
            const toast = document.getElementById(id);
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }
    
    static success(message, duration) {
        this.show(message, 'success', duration);
    }
    
    static error(message, duration) {
        this.show(message, 'danger', duration);
    }
    
    static warning(message, duration) {
        this.show(message, 'warning', duration);
    }
    
    static info(message, duration) {
        this.show(message, 'info', duration);
    }
}

// Utilisation:
// NotificationToast.success('Réclamation envoyée!');
// NotificationToast.error('Erreur lors de l\'envoi');
// NotificationToast.warning('Attention!');
// NotificationToast.info('Information');
</script>

<style>
    #toastContainer {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .toast {
        min-width: 300px;
        border-radius: 12px;
        animation: slideIn 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .toast {
            min-width: 100vw;
            margin: 0 -1rem;
            border-radius: 0;
        }
    }
</style>
