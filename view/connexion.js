function togglePassword(inputId, iconId) {
  const passwordInput = document.getElementById(inputId);
  const toggleIcon = document.getElementById(iconId);

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleIcon.classList.remove('bi-eye');
    toggleIcon.classList.add('bi-eye-slash');
  } else {
    passwordInput.type = 'password';
    toggleIcon.classList.remove('bi-eye-slash');
    toggleIcon.classList.add('bi-eye');
  }
}

function checkPasswordStrength() {
  const password = document.getElementById('password').value;
  const strengthBar = document.getElementById('strengthBar');
  const strengthText = document.getElementById('strengthText');

  let strength = 0;
  if (password.length >= 8) strength++;
  if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
  if (password.match(/[0-9]/)) strength++;
  if (password.match(/[^a-zA-Z0-9]/)) strength++;

  strengthBar.className = 'password-strength-bar';

  if (strength === 0 || strength === 1) {
    strengthBar.classList.add('strength-weak');
    strengthText.textContent = 'Mot de passe faible';
    strengthText.style.color = '#EF4444';
  } else if (strength === 2 || strength === 3) {
    strengthBar.classList.add('strength-medium');
    strengthText.textContent = 'Mot de passe moyen';
    strengthText.style.color = '#F59E0B';
  } else {
    strengthBar.classList.add('strength-strong');
    strengthText.textContent = 'Mot de passe fort';
    strengthText.style.color = '#10B981';
  }
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirmPassword').value;

  if (password !== confirmPassword) {
    alert('Les mots de passe ne correspondent pas !');
    return;
  }

  alert('Inscription réussie ! Bienvenue sur Kernel 🎉');
  // Vous pouvez ici ajouter la logique backend (PHP, Node, etc.)
});
