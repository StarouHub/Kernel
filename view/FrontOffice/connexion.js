// views/frontoffice/connexion.js
// VERSION FINALE – Validation complète + aucun popup bidon

function togglePassword() {
    const field = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Nettoie les anciens messages d'erreur
function clearErrors() {
    document.querySelectorAll('.error-msg').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

// Affiche un message d'erreur sous l'input
function showError(input, message) {
    clearErrors(); // on nettoie d'abord

    const error = document.createElement('small');
    error.className = 'text-danger error-msg d-block mt-1';
    error.textContent = message;

    const inputGroup = input.closest('.input-group');
    inputGroup.parentElement.appendChild(error);
    input.classList.add('is-invalid');
}

// Validation du formulaire
document.getElementById('loginForm').addEventListener('submit', function (e) {
    clearErrors();
    let valid = true;

    const email = document.querySelector('input[name="email"]');
    const password = document.getElementById('password');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Email
    if (!email.value.trim()) {
        showError(email, "L'email est obligatoire");
        valid = false;
    } else if (!emailRegex.test(email.value.trim())) {
        showError(email, "Veuillez entrer un email valide");
        valid = false;
    }

    // Mot de passe
    if (!password.value.trim()) {
        showError(password, "Le mot de passe est obligatoire");
        valid = false;
    }

    // Si erreur → on bloque l'envoi
    if (!valid) {
        e.preventDefault();
    }
    // Sinon → le formulaire part normalement vers le PHP (aucun alert, aucune redirection JS)
});