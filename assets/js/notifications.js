class NotificationManager {
    constructor() {
        this.count = 0;
        this.init();
    }

    init() {
        this.updateBadge();
        setInterval(() => this.checkNew(), 15000);
    }

    async checkNew() {
        try {
            const res = await fetch('../../init.php?action=check_notifications&t=' + Date.now());
            const data = await res.json();
            if (data.count > this.count) {
                this.showToast(`Vous avez ${data.count - this.count} nouvelle(s) notification(s)`);
                this.playSound();
            }
            this.count = data.count;
            this.updateBadge();
        } catch (e) { console.error(e); }
    }

    updateBadge() {
        const badge = document.querySelector('.notification-count');
        if (badge) badge.textContent = this.count > 99 ? '99+' : this.count;
        if (this.count === 0) badge?.classList.add('d-none');
        else badge?.classList.remove('d-none');
    }

    showToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-primary border-0 position-fixed bottom-0 end-0 m-3';
        toast.style.zIndex = 9999;
        toast.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast, {delay: 5000}).show();
        setTimeout(() => toast.remove(), 6000);
    }

    playSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA='); // beep court
        audio.play().catch(() => {});
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.notif = new NotificationManager();
});