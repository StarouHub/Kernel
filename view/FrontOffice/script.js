// Variables globales
let selectedCategory = null;
let tags = [];

// Fonction pour afficher les messages d'erreur ou de succès
function displayMessage(id, message, isError) {
    const element = document.getElementById(id + "_error");
    if (element) {
        element.style.color = isError ? "#EF4444" : "#10B981";
        element.innerText = message;
        
        // Ajouter/enlever la classe error sur l'input
        const inputElement = document.getElementById(id);
        if (inputElement) {
            if (isError) {
                inputElement.classList.add('error');
            } else {
                inputElement.classList.remove('error');
            }
        }
    }
}

// Fonction pour effacer tous les messages d'erreur
function clearAllErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.innerText = '';
    });
    
    const inputs = document.querySelectorAll('.form-control, .form-select, .upload-zone');
    inputs.forEach(input => {
        input.classList.remove('error');
    });
}

// Gestion des catégories - Version simplifiée
function initCategories() {
    const categoryCards = document.querySelectorAll('.category-card');
    const categoryInput = document.getElementById('categoryInput');
    
    if (!categoryCards.length) return;
    
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            // Retirer la sélection précédente
            categoryCards.forEach(c => c.classList.remove('selected'));
            
            // Ajouter la nouvelle sélection
            this.classList.add('selected');
            selectedCategory = this.getAttribute('data-category');
            
            // Mettre à jour le champ caché
            if (categoryInput) {
                categoryInput.value = selectedCategory;
                console.log('✅ Catégorie sélectionnée:', selectedCategory);
            }
            
            // Effacer le message d'erreur
            const errorSpan = document.getElementById('category_error');
            if (errorSpan) {
                errorSpan.textContent = '';
            }
        });
    });
}

// Initialiser les catégories dès que possible
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategories);
} else {
    initCategories();
}

// Gestion des tags (optionnel - si les éléments existent)
const tagInput = document.getElementById('tagInput');
const tagContainer = document.getElementById('tagContainer');

if (tagInput && tagContainer) {
    tagInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tagValue = this.value.trim();
            
            if (tagValue !== '' && !tags.includes(tagValue)) {
                tags.push(tagValue);
                addTagToUI(tagValue);
                this.value = '';
            }
        }
    });
}

function addTagToUI(tagValue) {
    if (!tagContainer) return;
    const tagElement = document.createElement('div');
    tagElement.className = 'tag-item';
    tagElement.innerHTML = `
        <span>${tagValue}</span>
        <span class="tag-remove" onclick="removeTag('${tagValue}', this)">×</span>
    `;
    tagContainer.insertBefore(tagElement, tagInput);
}

function removeTag(tagValue, element) {
    tags = tags.filter(tag => tag !== tagValue);
    element.parentElement.remove();
}

// Gestion de l'upload de l'image de couverture (optionnel - si l'élément existe)
const coverImageInput = document.getElementById('coverImage');
if (coverImageInput) {
    coverImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérifier la taille (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                displayMessage('coverImage', 'L\'image ne doit pas dépasser 5MB', true);
                this.value = '';
                return;
            }
            
            // Vérifier le type
            if (!file.type.startsWith('image/')) {
                displayMessage('coverImage', 'Veuillez sélectionner une image valide', true);
                this.value = '';
                return;
            }
            
            displayMessage('coverImage', '✓ Image téléchargée avec succès', false);
            const coverImageZone = document.getElementById('coverImageZone');
            if (coverImageZone) {
                coverImageZone.classList.remove('error');
            }
        }
    });
}

// Validation du formulaire lors de la soumission
const projectForm = document.getElementById('projectForm');
if (projectForm) {
    projectForm.addEventListener('submit', function(event) {
        // Valider avant la soumission
        if (!validateForm()) {
            event.preventDefault();
            return false;
        }
        return true;
    });
}

function validateForm() {
    
    // Effacer les erreurs précédentes
    clearAllErrors();
    
    let isValid = true;
    
    // Debug: Afficher la valeur de la catégorie
    const categoryInput = document.getElementById('categoryInput');
    console.log('Validation - Catégorie:', categoryInput ? categoryInput.value : 'Input non trouvé');
    
    // 1. Validation du titre du projet
    const projectTitle = document.getElementById('projectTitle');
    if (projectTitle.value.trim().length === 0) {
        displayMessage('projectTitle', 'Le titre du projet est obligatoire', true);
        isValid = false;
    } else if (projectTitle.value.trim().length < 5) {
        displayMessage('projectTitle', 'Le titre doit contenir au moins 5 caractères', true);
        isValid = false;
    }
    
    // 2. Validation du statut
    const projectStatus = document.getElementById('projectStatus');
    if (projectStatus.value === '') {
        displayMessage('projectStatus', 'Veuillez sélectionner un statut', true);
        isValid = false;
    }
    
    // 3. Validation de la description courte
    const shortDescription = document.getElementById('shortDescription');
    if (shortDescription.value.trim().length === 0) {
        displayMessage('shortDescription', 'La description courte est obligatoire', true);
        isValid = false;
    } else if (shortDescription.value.trim().length > 150) {
        displayMessage('shortDescription', 'La description ne doit pas dépasser 150 caractères', true);
        isValid = false;
    }
    
    // 4. Validation de la description détaillée
    const detailedDescription = document.getElementById('detailedDescription');
    if (detailedDescription.value.trim().length === 0) {
        displayMessage('detailedDescription', 'La description détaillée est obligatoire', true);
        isValid = false;
    } else if (detailedDescription.value.trim().length < 50) {
        displayMessage('detailedDescription', 'La description détaillée doit contenir au moins 50 caractères', true);
        isValid = false;
    }
    
    // 5. Validation de la catégorie (DÉSACTIVÉE - validation PHP uniquement)
    const categoryInput = document.getElementById('categoryInput');
    if (categoryInput && categoryInput.value) {
        // Mettre à jour selectedCategory pour cohérence
        selectedCategory = categoryInput.value;
        console.log('✅ Catégorie validée:', selectedCategory);
    } else {
        console.log('⚠️ Aucune catégorie sélectionnée - PHP validera');
        // Ne pas bloquer ici, laisser PHP valider
    }
    
    // 6. Validation du budget (optionnel mais si renseigné doit être valide)
    const budget = document.getElementById('budget');
    if (budget.value !== '' && (isNaN(budget.value) || parseFloat(budget.value) <= 0)) {
        displayMessage('budget', 'Le budget doit être un nombre positif', true);
        isValid = false;
    }
    
    // 7. Validation des liens (optionnels - si les éléments existent)
    const videoLink = document.getElementById('videoLink');
    if (videoLink && videoLink.value !== '' && !isValidURL(videoLink.value)) {
        displayMessage('videoLink', 'Veuillez entrer une URL valide', true);
        isValid = false;
    }
    
    const websiteLink = document.getElementById('websiteLink');
    if (websiteLink && websiteLink.value !== '' && !isValidURL(websiteLink.value)) {
        displayMessage('websiteLink', 'Veuillez entrer une URL valide', true);
        isValid = false;
    }
    
    // 8. Validation de l'image de couverture (optionnel - si l'élément existe)
    const coverImage = document.getElementById('coverImage');
    if (coverImage && (!coverImage.files || coverImage.files.length === 0)) {
        displayMessage('coverImage', 'L\'image de couverture est obligatoire', true);
        const coverImageZone = document.getElementById('coverImageZone');
        if (coverImageZone) {
            coverImageZone.classList.add('error');
        }
        isValid = false;
    }
    
    // Si tout est valide, retourner true pour soumettre le formulaire
    if (!isValid) {
        alert('❌ Veuillez corriger les erreurs dans le formulaire');
        // Scroll vers la première erreur
        const firstError = document.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

// Fonction de validation d'URL
function isValidURL(string) {
    try {
        const url = new URL(string);
        return url.protocol === "http:" || url.protocol === "https:";
    } catch (_) {
        return false;
    }
}

// Bouton "Sauvegarder comme brouillon" (optionnel - si l'élément existe)
const draftBtn = document.getElementById('draftBtn');
if (draftBtn) {
    draftBtn.addEventListener('click', function() {
        const projectTitle = document.getElementById('projectTitle');
        
        if (projectTitle && projectTitle.value.trim().length === 0) {
            alert('⚠️ Veuillez au moins donner un titre à votre projet avant de le sauvegarder comme brouillon');
            return;
        }
        
        alert('💾 Projet sauvegardé comme brouillon !');
        console.log('Brouillon sauvegardé');
    });
}

// Validation en temps réel pour certains champs
const shortDescription = document.getElementById('shortDescription');
if (shortDescription) {
    shortDescription.addEventListener('input', function() {
        const charCount = this.value.length;
        if (charCount > 150) {
            this.value = this.value.substring(0, 150);
            displayMessage('shortDescription', 'Limite de 150 caractères atteinte', true);
        } else if (charCount > 130) {
            displayMessage('shortDescription', `${150 - charCount} caractères restants`, false);
        } else {
            displayMessage('shortDescription', '', false);
        }
    });
}

// Empêcher la soumission du formulaire avec la touche Entrée (sauf pour les tags)
if (projectForm) {
    projectForm.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.id !== 'tagInput') {
            e.preventDefault();
        }
    });
}