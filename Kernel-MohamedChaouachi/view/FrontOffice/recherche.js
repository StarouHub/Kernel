// ========== RECHERCHE DE PROJETS - VERSION SIMPLE ==========

console.log('🔍 Script de recherche chargé');

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM chargé, initialisation de la recherche...');
    
    // Récupérer la barre de recherche
    const searchInput = document.getElementById('searchInput');
    
    if (!searchInput) {
        console.error('❌ Barre de recherche non trouvée!');
        return;
    }
    
    console.log('✅ Barre de recherche trouvée');
    
    // Événement de recherche
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        console.log('🔎 Recherche:', searchTerm);
        
        // Récupérer toutes les cartes de projets
        const projectCards = document.querySelectorAll('.project-card');
        console.log('📦 Nombre de cartes:', projectCards.length);
        
        let visibleCount = 0;
        
        // Parcourir chaque carte
        projectCards.forEach(function(card) {
            // Récupérer le conteneur parent (col-md-6 col-lg-4)
            const cardContainer = card.parentElement;
            
            // Récupérer les textes
            const title = card.querySelector('.project-title');
            const description = card.querySelector('.project-description');
            const category = card.querySelector('.project-category');
            
            const titleText = title ? title.textContent.toLowerCase() : '';
            const descText = description ? description.textContent.toLowerCase() : '';
            const catText = category ? category.textContent.toLowerCase() : '';
            
            // Vérifier si le terme de recherche est présent
            const isVisible = searchTerm === '' || 
                            titleText.includes(searchTerm) || 
                            descText.includes(searchTerm) || 
                            catText.includes(searchTerm);
            
            // Afficher ou cacher
            if (isVisible) {
                cardContainer.style.display = 'block';
                visibleCount++;
            } else {
                cardContainer.style.display = 'none';
            }
        });
        
        console.log('✅ Projets visibles:', visibleCount);
        
        // Mettre à jour le compteur
        const resultsCount = document.querySelector('.results-count');
        if (resultsCount) {
            resultsCount.innerHTML = '<strong>' + visibleCount + '</strong> projet' + (visibleCount > 1 ? 's' : '') + ' trouvé' + (visibleCount > 1 ? 's' : '');
        }
        
        // Gérer le message "aucun résultat"
        let noResultDiv = document.getElementById('no-result-message');
        
        if (visibleCount === 0 && searchTerm !== '') {
            // Créer le message si nécessaire
            if (!noResultDiv) {
                noResultDiv = document.createElement('div');
                noResultDiv.id = 'no-result-message';
                noResultDiv.className = 'col-12';
                noResultDiv.innerHTML = '<div class="alert alert-warning text-center mt-3">' +
                    '<i class="bi bi-search me-2"></i>' +
                    'Aucun projet ne correspond à votre recherche "<strong>' + searchTerm + '</strong>".' +
                    '</div>';
                
                const projectsContainer = document.getElementById('projectsContainer');
                if (projectsContainer) {
                    projectsContainer.appendChild(noResultDiv);
                }
            }
        } else {
            // Supprimer le message s'il existe
            if (noResultDiv) {
                noResultDiv.remove();
            }
        }
    });
    
    console.log('✅ Recherche initialisée avec succès');
});
