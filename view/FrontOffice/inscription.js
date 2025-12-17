// views/frontoffice/inscription.js

function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function checkPasswordStrength() {
    // your existing strength code (keep it – it's fine)
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    if (password.length === 0) {
        strengthBar.className = 'password-strength-bar';
        strengthText.textContent = 'Le mot de passe doit contenir au moins 8 caractères';
        return;
    }

    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;

    strengthBar.className = 'password-strength-bar';
    if (strength <= 1) strengthBar.classList.add('strength-weak');
    else if (strength <= 2) strengthBar.classList.add('strength-medium');
    else strengthBar.classList.add('strength-strong');

    strengthText.textContent = strength <= 1 ? 'Faible' : strength <= 2 ? 'Moyen' : 'Fort';
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

    const inputs = document.querySelectorAll('#registerForm input[type="text"], #registerForm input[type="password"]');
    const prenom = inputs[0], nom = inputs[1], email = inputs[2], telephone = inputs[3];
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');

    const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[\d\s\+\-\(\)]+$/;

    // prénom, nom, email, telephone, password validations (same as before)
    // ... (keep your existing checks)

    if (password.value.length < 8) {
        showError(password, 'Minimum 8 caractères');
        valid = false;
    }
    if (password.value !== confirmPassword.value) {
        showError(confirmPassword, 'Les mots de passe ne correspondent pas');
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    }
    // No alert → real PHP registration happens
});

function showError(input, message) {
    let error = input.parentElement.parentElement.querySelector('.error-msg');
    if (!error) {
        error = document.createElement('small');
        error.className = 'text-danger error-msg d-block mt-1';
        input.parentElement.parentElement.appendChild(error);
    }
    error.textContent = message;
}