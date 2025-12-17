document.addEventListener('DOMContentLoaded', function() {
    initSujetCreateValidation();
    initSujetEditValidation();
    initReponseCreateValidation();
    initReponseEditValidation();
    initCategorieCreateValidation();
    initCategorieEditValidation();
    initColorPickers();
});

// === Generic helpers ===
function showFieldError(form, fieldName, message) {
    var field = form.querySelector('[name="' + fieldName + '"]');
    var error = form.querySelector('[data-error-for="' + fieldName + '"]');
    if (!error) return;
    if (message) {
        error.textContent = message;
        error.style.display = 'block';
        if (field) field.classList.add('has-error');
    } else {
        error.textContent = '';
        error.style.display = 'none';
        if (field) field.classList.remove('has-error');
    }
}

function getValue(form, name) {
    var field = form.querySelector('[name="' + name + '"]');
    return field ? field.value.trim() : '';
}

// === Sujet: create ===
function initSujetCreateValidation() {
    var form = document.getElementById('form-sujet-create');
    if (!form) return;

    var validate = function () {
        var isValid = true;
        var titre = getValue(form, 'titre');
        var contenu = getValue(form, 'contenu');
        var categorieId = getValue(form, 'categorie_id');

        if (!titre) {
            showFieldError(form, 'titre', 'Le titre est requis.');
            isValid = false;
        } else if (titre.length < 3) {
            showFieldError(form, 'titre', 'Le titre doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            showFieldError(form, 'titre', '');
        }

        if (!contenu) {
            showFieldError(form, 'contenu', 'La description est requise.');
            isValid = false;
        } else if (contenu.length < 10) {
            showFieldError(form, 'contenu', 'La description doit contenir au moins 10 caractères.');
            isValid = false;
        } else {
            showFieldError(form, 'contenu', '');
        }

        if (!categorieId) {
            showFieldError(form, 'categorie_id', 'Veuillez sélectionner une catégorie.');
            isValid = false;
        } else {
            showFieldError(form, 'categorie_id', '');
        }

        return isValid;
    };

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    ['titre', 'contenu', 'categorie_id'].forEach(function (name) {
        var field = form.querySelector('[name="' + name + '"]');
        if (field) {
            field.addEventListener('input', validate);
            field.addEventListener('change', validate);
        }
    });
}

// === Sujet: edit ===
function initSujetEditValidation() {
    var form = document.getElementById('form-sujet-edit');
    if (!form) return;

    var validate = function () {
        var isValid = true;
        var titre = getValue(form, 'titre');
        var contenu = getValue(form, 'contenu');
        var categorieId = getValue(form, 'categorie_id');

        if (!titre) {
            showFieldError(form, 'titre', 'Le titre est requis.');
            isValid = false;
        } else if (titre.length < 3) {
            showFieldError(form, 'titre', 'Le titre doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            showFieldError(form, 'titre', '');
        }

        if (!contenu) {
            showFieldError(form, 'contenu', 'La description est requise.');
            isValid = false;
        } else if (contenu.length < 10) {
            showFieldError(form, 'contenu', 'La description doit contenir au moins 10 caractères.');
            isValid = false;
        } else {
            showFieldError(form, 'contenu', '');
        }

        if (!categorieId) {
            showFieldError(form, 'categorie_id', 'Veuillez sélectionner une catégorie.');
            isValid = false;
        } else {
            showFieldError(form, 'categorie_id', '');
        }

        return isValid;
    };

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    ['titre', 'contenu', 'categorie_id'].forEach(function (name) {
        var field = form.querySelector('[name="' + name + '"]');
        if (field) {
            field.addEventListener('input', validate);
            field.addEventListener('change', validate);
        }
    });
}

// === Réponse: create ===
function initReponseCreateValidation() {
    var form = document.getElementById('form-reponse-create');
    if (!form) return;

    var validate = function () {
        var isValid = true;
        var contenu = getValue(form, 'contenu');

        if (!contenu) {
            showFieldError(form, 'contenu', 'Le message est requis.');
            isValid = false;
        } else if (contenu.length < 3) {
            showFieldError(form, 'contenu', 'Le message doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            // Validation des caractères interdits
            var errors = [];
            
            // Vérifier le caractère #
            if (contenu.includes('#')) {
                errors.push('Le caractère # n\'est pas autorisé.');
            }
            
            // Vérifier la séquence **
            if (contenu.includes('**')) {
                errors.push('La séquence ** n\'est pas autorisée.');
            }
            
            // Vérifier le mot "fruit" (insensible à la casse)
            var fruitRegex = /\bfruit\b/i;
            if (fruitRegex.test(contenu)) {
                errors.push('Le mot "fruit" n\'est pas autorisé.');
            }
            
            if (errors.length > 0) {
                showFieldError(form, 'contenu', errors.join(' '));
                isValid = false;
            } else {
                showFieldError(form, 'contenu', '');
            }
        }

        return isValid;
    };

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    var field = form.querySelector('[name="contenu"]');
    if (field) {
        field.addEventListener('input', validate);
    }
}

// === Réponse: edit ===
function initReponseEditValidation() {
    var form = document.getElementById('form-reponse-edit');
    if (!form) return;

    var validate = function () {
        var isValid = true;
        var contenu = getValue(form, 'contenu');

        if (!contenu) {
            showFieldError(form, 'contenu', 'Le message est requis.');
            isValid = false;
        } else if (contenu.length < 3) {
            showFieldError(form, 'contenu', 'Le message doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            // Validation des caractères interdits
            var errors = [];
            
            // Vérifier le caractère #
            if (contenu.includes('#')) {
                errors.push('Le caractère # n\'est pas autorisé.');
            }
            
            // Vérifier la séquence **
            if (contenu.includes('**')) {
                errors.push('La séquence ** n\'est pas autorisée.');
            }
            
            // Vérifier le mot "fruit" (insensible à la casse)
            var fruitRegex = /\bfruit\b/i;
            if (fruitRegex.test(contenu)) {
                errors.push('Le mot "fruit" n\'est pas autorisé.');
            }
            
            if (errors.length > 0) {
                showFieldError(form, 'contenu', errors.join(' '));
                isValid = false;
            } else {
                showFieldError(form, 'contenu', '');
            }
        }

        return isValid;
    };

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    var field = form.querySelector('[name="contenu"]');
    if (field) {
        field.addEventListener('input', validate);
    }
}

// === Catégorie: create ===
function initCategorieCreateValidation() {
    var form = document.getElementById('form-categorie-create');
    if (!form) return;

    var validate = function () {
        var isValid = true;
        var name = getValue(form, 'name');
        var color = getValue(form, 'color');

        if (!name) {
            showFieldError(form, 'name', 'Le nom est requis.');
            isValid = false;
        } else if (name.length < 3) {
            showFieldError(form, 'name', 'Le nom doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            showFieldError(form, 'name', '');
        }

        if (!color) {
            showFieldError(form, 'color', 'La couleur est requise.');
            isValid = false;
        } else {
            showFieldError(form, 'color', '');
        }

        return isValid;
    };

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    ['name', 'color'].forEach(function (name) {
        var field = form.querySelector('[name="' + name + '"]');
        if (field) {
            field.addEventListener('input', validate);
        }
    });
}

// === Catégorie: edit ===
function initCategorieEditValidation() {
    var form = document.getElementById('form-categorie-edit');
    if (!form) return;

    var validate = function () {
        var isValid = true;
        var name = getValue(form, 'name');
        var color = getValue(form, 'color');

        if (!name) {
            showFieldError(form, 'name', 'Le nom est requis.');
            isValid = false;
        } else if (name.length < 3) {
            showFieldError(form, 'name', 'Le nom doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            showFieldError(form, 'name', '');
        }

        if (!color) {
            showFieldError(form, 'color', 'La couleur est requise.');
            isValid = false;
        } else {
            showFieldError(form, 'color', '');
        }

        return isValid;
    };

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    ['name', 'color'].forEach(function (name) {
        var field = form.querySelector('[name="' + name + '"]');
        if (field) {
            field.addEventListener('input', validate);
        }
    });
}

function initColorPickers() {
    var pickers = document.querySelectorAll('.js-color-picker');
    if (!pickers.length) {
        return;
    }
    
    pickers.forEach(function(picker) {
        var input = picker.querySelector('[data-color-input]');
        var preview = picker.querySelector('[data-color-preview]');
        var swatches = picker.querySelectorAll('[data-color-option]');
        if (!input || !preview) {
            return;
        }
        
        var sanitizeColor = function(value) {
            if (typeof value !== 'string') return '';
            var trimmed = value.trim().toUpperCase();
            if (trimmed && trimmed[0] !== '#') {
                trimmed = '#' + trimmed;
            }
            return trimmed;
        };
        
        var isValidHex = function(value) {
            return /^#[0-9A-F]{6}$/.test(value);
        };
        
        var updatePreview = function(color) {
            preview.style.backgroundColor = color;
            input.classList.remove('is-invalid');
        };
        
        var markInvalid = function() {
            input.classList.add('is-invalid');
        };
        
        var setActiveSwatch = function(color) {
            swatches.forEach(function(swatch) {
                if (swatch.getAttribute('data-color-option').toUpperCase() === color) {
                    swatch.classList.add('is-active');
                } else {
                    swatch.classList.remove('is-active');
                }
            });
        };
        
        var applyColor = function(rawValue) {
            var normalized = sanitizeColor(rawValue);
            input.value = normalized;
            
            if (isValidHex(normalized)) {
                updatePreview(normalized);
                setActiveSwatch(normalized);
            } else {
                markInvalid();
            }
        };
        
        // Initialize with current value or default blue
        applyColor(input.value || '#2563EB');
        
        input.addEventListener('input', function(e) {
            var value = e.target.value;
            // allow typing partial values without flickering
            var normalized = sanitizeColor(value);
            if (normalized.length <= 7) {
                input.value = normalized;
            }
            if (isValidHex(normalized)) {
                updatePreview(normalized);
                setActiveSwatch(normalized);
            } else {
                markInvalid();
            }
        });
        
        input.addEventListener('blur', function() {
            var normalized = sanitizeColor(input.value);
            if (isValidHex(normalized)) {
                input.value = normalized;
                updatePreview(normalized);
                setActiveSwatch(normalized);
            } else {
                // revert to last valid color stored in preview
                var computedColor = window.getComputedStyle(preview).backgroundColor;
                var fallbackHex = rgbToHex(computedColor) || '#2563EB';
                input.value = fallbackHex;
                updatePreview(fallbackHex);
                setActiveSwatch(fallbackHex);
            }
        });
        
        swatches.forEach(function(swatch) {
            swatch.addEventListener('click', function() {
                var color = swatch.getAttribute('data-color-option').toUpperCase();
                input.value = color;
                updatePreview(color);
                setActiveSwatch(color);
                input.focus();
            });
        });
    });
}

function rgbToHex(rgbString) {
    var match = rgbString.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (!match) {
        return null;
    }
    var r = parseInt(match[1], 10);
    var g = parseInt(match[2], 10);
    var b = parseInt(match[3], 10);
    
    return '#' + [r, g, b].map(function(x) {
        var hex = x.toString(16).toUpperCase();
        return hex.length === 1 ? '0' + hex : hex;
    }).join('');
}

