// Toggle password visibility
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

// Event listeners for toggle buttons
document.getElementById('toggleNew').addEventListener('click', function() {
  togglePassword('newPassword', 'toggleIcon1');
});

document.getElementById('toggleConfirm').addEventListener('click', function() {
  togglePassword('confirmPassword', 'toggleIcon2');
});

// Check password strength
function checkPasswordStrength() {
  const password = document.getElementById('newPassword').value;
  const strengthBar = document.getElementById('strengthBar');
  const strengthText = document.getElementById('strengthText');
  
  // Check requirements
  const hasLength = password.length >= 8;
  const hasUppercase = /[A-Z]/.test(password);
  const hasLowercase = /[a-z]/.test(password);
  const hasNumber = /[0-9]/.test(password);
  const hasSpecial = /[@$!%*?&]/.test(password);
  
  // Update requirement items
  updateRequirement('req-length', hasLength);
  updateRequirement('req-uppercase', hasUppercase);
  updateRequirement('req-lowercase', hasLowercase);
  updateRequirement('req-number', hasNumber);
  updateRequirement('req-special', hasSpecial);
  
  // Calculate strength
  let strength = 0;
  if (hasLength) strength++;
  if (hasUppercase && hasLowercase) strength++;
  if (hasNumber) strength++;
  if (hasSpecial) strength++;
  
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
  
  checkPasswordMatch();
  updateSaveButton();
}

// Update requirement item
function updateRequirement(id, isValid) {
  const element = document.getElementById(id);
  const icon = element.querySelector('i');
  
  if (isValid) {
    element.classList.add('valid');
    icon.className = 'bi bi-check-circle-fill';
  } else {
    element.classList.remove('valid');
    icon.className = 'bi bi-circle';
  }
}

// Check if passwords match
function checkPasswordMatch() {
  const password = document.getElementById('newPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const matchText = document.getElementById('matchText');
  
  if (confirmPassword.length === 0) {
    matchText.textContent = '';
    return;
  }
  
  if (password === confirmPassword) {
    matchText.textContent = '✓ Les mots de passe correspondent';
    matchText.style.color = '#10B981';
  } else {
    matchText.textContent = '✗ Les mots de passe ne correspondent pas';
    matchText.style.color = '#EF4444';
  }
  
  updateSaveButton();
}

// Update save button state
function updateSaveButton() {
  const password = document.getElementById('newPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const saveBtn = document.getElementById('saveBtn');
  
  const hasLength = password.length >= 8;
  const hasUppercase = /[A-Z]/.test(password);
  const hasLowercase = /[a-z]/.test(password);
  const hasNumber = /[0-9]/.test(password);
  const hasSpecial = /[@$!%*?&]/.test(password);
  
  const allRequirements = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
  const passwordsMatch = password === confirmPassword && confirmPassword.length > 0;
  
  saveBtn.disabled = !(allRequirements && passwordsMatch);
}

// Event listeners for password inputs
document.getElementById('newPassword').addEventListener('input', checkPasswordStrength);
document.getElementById('confirmPassword').addEventListener('input', checkPasswordMatch);

// ✅ CORRECTION : Laisser le formulaire se soumettre normalement au serveur PHP
document.getElementById('passwordForm').addEventListener('submit', function(e) {
  const password = document.getElementById('newPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  
  // Validation côté client
  if (password !== confirmPassword) {
    e.preventDefault();
    alert('Les mots de passe ne correspondent pas !');
    return false;
  }
  
  // Vérifier les exigences
  const hasLength = password.length >= 8;
  const hasUppercase = /[A-Z]/.test(password);
  const hasLowercase = /[a-z]/.test(password);
  const hasNumber = /[0-9]/.test(password);
  const hasSpecial = /[@$!%*?&]/.test(password);
  
  if (!hasLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecial) {
    e.preventDefault();
    alert('Le mot de passe ne respecte pas tous les critères de sécurité.');
    return false;
  }
  
  // Afficher un indicateur de chargement
  const saveBtn = document.getElementById('saveBtn');
  saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enregistrement...';
  saveBtn.disabled = true;
  
  // ✅ Le formulaire va maintenant s'envoyer au serveur PHP normalement
  // Pas de e.preventDefault() ici si la validation passe
});

// Animation au chargement
window.addEventListener('load', function() {
  document.querySelector('.password-container').style.animation = 'fadeIn 0.5s ease';
});