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
searchBox.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const cards = document.querySelectorAll('.project-card');
    let count = 0;
    
    cards.forEach(card => {
        const title = card.querySelector('.project-title').textContent.toLowerCase();
        const desc = card.querySelector('.project-description').textContent.toLowerCase();
        
        if (title.includes(search) || desc.includes(search)) {
            card.parentElement.style.display = 'block';
            count++;
        } else {
            card.parentElement.style.display = 'none';
        }
    });
    
    document.querySelector('.results-count').innerHTML = `<strong>${count}</strong> projets trouvés`;
});

// ========== RÉINITIALISER FILTRES ==========
const resetBtn = document.querySelector('.filters-sidebar button:last-child');
resetBtn.addEventListener('click', function() {
    // Décocher toutes les cases
    document.querySelectorAll('.form-check-input').forEach(box => box.checked = false);
    
    // Réinitialiser le budget
    budgetSlider.value = 100000;
    budgetText.textContent = '100,000 TND';
    
    // Réinitialiser la recherche
    searchBox.value = '';
    
    // Tout afficher
    document.querySelectorAll('.project-card').forEach(card => {
        card.parentElement.style.display = 'block';
    });
    
    const total = document.querySelectorAll('.project-card').length;
    document.querySelector('.results-count').innerHTML = `<strong>${total}</strong> projets trouvés`;
});

// ========== CLIC SUR CARTE ==========
const cards = document.querySelectorAll('.project-card');
cards.forEach(card => {
    card.addEventListener('click', function() {
        window.location.href = 'detailsprojet.html';
    });
});