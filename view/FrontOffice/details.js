// ========== VÉRIFIER SI CONNECTÉ ==========
function isConnected() {
    // Vérifier si user connecté
    return localStorage.getItem('userConnected') === 'true';
}

// ========== BOUTON INVESTIR ==========
const investBtn = document.querySelector('.funding-card .btn-invest');
investBtn.addEventListener('click', function() {
    if (!isConnected()) {
        alert('⚠️ Connectez-vous pour investir');
        window.location.href = 'login.html';
        return;
    }
    
    const montant = prompt('💰 Montant en TND:');
    if (montant && montant > 0) {
        alert('✅ Investissement de ' + montant + ' TND réussi !');
    }
});

// ========== BOUTONS SUIVRE ==========
const followBtns = document.querySelectorAll('.btn-follow');
followBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        if (!isConnected()) {
            alert('⚠️ Connectez-vous pour suivre');
            window.location.href = 'login.html';
            return;
        }
        
        if (this.textContent.includes('Suivre')) {
            this.innerHTML = '<i class="bi bi-bookmark-fill me-2"></i> Suivi';
            this.style.background = 'var(--primary-color)';
            this.style.color = 'white';
            alert('✓ Vous suivez ce projet');
        } else {
            this.innerHTML = '<i class="bi bi-bookmark me-2"></i> Suivre';
            this.style.background = 'white';
            this.style.color = 'var(--primary-color)';
            alert('✓ Vous ne suivez plus');
        }
    });
});

// ========== BOUTON PARTAGER ==========
const shareBtn = document.querySelector('.btn-share');
shareBtn.addEventListener('click', function() {
    const url = window.location.href;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url);
        alert('🔗 Lien copié !');
    } else {
        prompt('Copiez ce lien:', url);
    }
});

// ========== BOUTON SAUVEGARDER ==========
const saveBtn = document.querySelectorAll('.btn-share')[1];
saveBtn.addEventListener('click', function() {
    if (!isConnected()) {
        alert('⚠️ Connectez-vous pour sauvegarder');
        return;
    }
    
    if (this.textContent.includes('Sauvegarder')) {
        this.innerHTML = '<i class="bi bi-bookmark-fill"></i> Sauvegardé';
        alert('✓ Projet sauvegardé');
    } else {
        this.innerHTML = '<i class="bi bi-bookmark"></i> Sauvegarder';
        alert('✓ Projet retiré');
    }
});

// ========== BOUTON SIGNALER ==========
const reportBtn = document.querySelectorAll('.btn-share')[2];
reportBtn.addEventListener('click', function() {
    const raison = prompt('⚠️ Raison du signalement:');
    if (raison) {
        alert('✓ Signalement envoyé');
    }
});

// ========== GALERIE IMAGES ==========
const images = document.querySelectorAll('.gallery-item img');
images.forEach(img => {
    img.addEventListener('click', function() {
        // Créer modal
        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:10000; display:flex; align-items:center; justify-content:center; cursor:pointer;';
        
        const bigImg = this.cloneNode();
        bigImg.style.cssText = 'max-width:90%; max-height:90%; border-radius:10px;';
        
        modal.appendChild(bigImg);
        document.body.appendChild(modal);
        
        // Fermer modal
        modal.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
    });
});

// ========== PUBLIER COMMENTAIRE ==========
const commentBox = document.querySelector('.content-section textarea');
const publishBtn = document.querySelector('.content-section .btn-invest');

publishBtn.addEventListener('click', function() {
    if (!isConnected()) {
        alert('⚠️ Connectez-vous pour commenter');
        window.location.href = 'login.html';
        return;
    }
    
    const text = commentBox.value.trim();
    
    if (text.length === 0) {
        alert('⚠️ Écrivez un commentaire');
        return;
    }
    
    if (text.length < 10) {
        alert('⚠️ Minimum 10 caractères');
        return;
    }
    
    // Créer nouveau commentaire
    const newComment = document.createElement('div');
    newComment.className = 'comment-card';
    newComment.innerHTML = `
        <div class="comment-header">
            <div class="comment-avatar">VO</div>
            <div>
                <div style="font-weight: 600;">Vous</div>
                <div style="font-size: 12px; color: #6B7280;">à l'instant</div>
            </div>
        </div>
        <p>${text}</p>
        <div class="d-flex gap-3">
            <button class="btn btn-sm btn-light like-btn"><i class="bi bi-hand-thumbs-up"></i> 0</button>
            <button class="btn btn-sm btn-light delete-btn"><i class="bi bi-trash"></i> Supprimer</button>
        </div>
    `;
    
    // Ajouter avant les autres commentaires
    const firstComment = document.querySelector('.comment-card');
    firstComment.parentElement.insertBefore(newComment, firstComment);
    
    // Vider le textarea
    commentBox.value = '';
    alert('✓ Commentaire publié');
    
    // Ajouter fonction supprimer
    const deleteBtn = newComment.querySelector('.delete-btn');
    deleteBtn.addEventListener('click', function() {
        if (confirm('Supprimer ce commentaire ?')) {
            newComment.remove();
            alert('✓ Commentaire supprimé');
        }
    });
    
    // Ajouter fonction like
    const likeBtn = newComment.querySelector('.like-btn');
    likeBtn.addEventListener('click', function() {
        const likes = parseInt(this.textContent.match(/\d+/)[0]);
        if (this.style.color === 'rgb(37, 99, 235)') {
            this.innerHTML = `<i class="bi bi-hand-thumbs-up"></i> ${likes - 1}`;
            this.style.color = '';
        } else {
            this.innerHTML = `<i class="bi bi-hand-thumbs-up-fill"></i> ${likes + 1}`;
            this.style.color = 'rgb(37, 99, 235)';
        }
    });
});

// ========== LIKE COMMENTAIRES EXISTANTS ==========
const likeBtns = document.querySelectorAll('.comment-card .btn-light:first-child');
likeBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const likes = parseInt(this.textContent.match(/\d+/)[0]);
        if (this.style.color === 'rgb(37, 99, 235)') {
            this.innerHTML = `<i class="bi bi-hand-thumbs-up"></i> ${likes - 1}`;
            this.style.color = '';
        } else {
            this.innerHTML = `<i class="bi bi-hand-thumbs-up-fill"></i> ${likes + 1}`;
            this.style.color = 'rgb(37, 99, 235)';
        }
    });
});

// ========== LIKE DU PROJET ==========
const projectLike = document.querySelector('.meta-item:nth-child(4)');
projectLike.style.cursor = 'pointer';
projectLike.addEventListener('click', function() {
    if (!isConnected()) {
        alert('⚠️ Connectez-vous pour liker');
        return;
    }
    
    const heart = this.querySelector('i');
    const text = this.querySelector('span');
    const likes = parseInt(text.textContent.match(/\d+/)[0]);
    
    if (heart.classList.contains('bi-heart-fill')) {
        heart.classList.remove('bi-heart-fill');
        heart.classList.add('bi-heart');
        text.textContent = (likes - 1) + ' likes';
    } else {
        heart.classList.remove('bi-heart');
        heart.classList.add('bi-heart-fill');
        text.textContent = (likes + 1) + ' likes';
    }
});

// ========== ANIMATION BARRE DE PROGRESSION ==========
window.addEventListener('load', function() {
    const bar = document.querySelector('.progress-fill');
    const width = bar.style.width;
    bar.style.width = '0%';
    
    setTimeout(function() {
        bar.style.width = width;
    }, 300);
});