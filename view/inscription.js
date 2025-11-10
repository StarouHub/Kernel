function togglePassword() {
  const passwordInput = document.getElementById('password');
  const toggleIcon = document.getElementById('toggleIcon');

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

document.getElementById('loginForm').addEventListener('submit', function (e) {
  e.preventDefault();
  alert('Connexion simulée ! Redirection vers le tableau de bord...');
  // 🔧 Ici, ajoute la logique de connexion avec ton backend (PHP, Node.js, etc.)
});
