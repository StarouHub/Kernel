// ========== TABS DE FILTRAGE ==========
const tabs = document.querySelectorAll('.filter-tab');
tabs.forEach(tab => {
    tab.addEventListener('click', function() {
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});

// ========== BUDGET SLIDER ==========
const budgetSlider = document.getElementById('budgetRange');
const budgetText = document.getElementById('budgetValue');

budgetSlider.addEventListener('input', function() {
    const value = this.value;
    if (value >= 200000) {
        budgetText.textContent = '200K+ TND';
    } else {
        budgetText.textContent = parseInt(value).toLocaleString() + ' TND';
    }
});

// ========== RECHERCHE ==========
const searchBox = document.querySelector('.search-input input');
if (searchBox) {
    searchBox.addEventListener('input', function() {
        const search = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.project-card');
        let count = 0;
        
        cards.forEach(card => {
            const title = card.querySelector('.project-title')?.textContent.toLowerCase() || '';
            const desc = card.querySelector('.project-description')?.textContent.toLowerCase() || '';
            const category = card.querySelector('.project-category')?.textContent.toLowerCase() || '';
            
            // Chercher dans le titre, la description et la catégorie
            if (search === '' || title.includes(search) || desc.includes(search) || category.includes(search)) {
                card.parentElement.style.display = 'block';
                count++;
            } else {
                card.parentElement.style.display = 'none';
            }
        });
        
        // Mettre à jour le compteur
        const resultsCount = document.querySelector('.results-count');
        if (resultsCount) {
            resultsCount.innerHTML = `<strong>${count}</strong> projet${count > 1 ? 's' : ''} trouvé${count > 1 ? 's' : ''}`;
        }
        
        // Afficher un message si aucun résultat
        let noResultMsg = document.getElementById('no-results-message');
        if (count === 0 && search !== '') {
            if (!noResultMsg) {
                noResultMsg = document.createElement('div');
                noResultMsg.id = 'no-results-message';
                noResultMsg.className = 'col-12';
                noResultMsg.innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-search me-2"></i>
                        Aucun projet ne correspond à votre recherche "<strong>${search}</strong>".
                    </div>
                `;
                document.querySelector('.row').appendChild(noResultMsg);
            }
        } else if (noResultMsg) {
            noResultMsg.remove();
        }
    });
}

// ========== RÉINITIALISER FILTRES ==========
const resetBtn = document.querySelector('.filters-sidebar button:last-child');
if (resetBtn) {
    resetBtn.addEventListener('click', function() {
        // Décocher toutes les cases
        document.querySelectorAll('.form-check-input').forEach(box => box.checked = false);
        
        // Réinitialiser le budget
        if (budgetSlider) {
            budgetSlider.value = 100000;
            budgetText.textContent = '100,000 TND';
        }
        
        // Réinitialiser la recherche
        if (searchBox) {
            searchBox.value = '';
        }
        
        // Tout afficher
        document.querySelectorAll('.project-card').forEach(card => {
            card.parentElement.style.display = 'block';
        });
        
        // Supprimer le message "aucun résultat"
        const noResultMsg = document.getElementById('no-results-message');
        if (noResultMsg) {
            noResultMsg.remove();
        }
        
        const total = document.querySelectorAll('.project-card').length;
        const resultsCount = document.querySelector('.results-count');
        if (resultsCount) {
            resultsCount.innerHTML = `<strong>${total}</strong> projet${total > 1 ? 's' : ''} trouvé${total > 1 ? 's' : ''}`;
        }
    });
}

// ========== CLIC SUR CARTE ==========
// Les cartes sont déjà des liens <a href="detailsprojet.php?id=...">
// Pas besoin d'ajouter un événement click supplémentaire