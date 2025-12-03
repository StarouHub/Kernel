// Gestion des inputs de code
const inputs = document.querySelectorAll('.code-input');
const verifyBtn = document.getElementById('verifyBtn');
const fullCodeInput = document.getElementById('fullCode');
const verifyForm = document.getElementById('verifyForm');

inputs.forEach((input, index) => {
  input.addEventListener('input', (e) => {
    const value = e.target.value;
    
    // Accepter uniquement les chiffres
    if (value && !/^\d$/.test(value)) {
      e.target.value = '';
      return;
    }
    
    if (value.length === 1) {
      input.classList.add('filled');
      if (index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    }
    
    // Vérifier si tous les champs sont remplis
    updateFullCode();
  });
  
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !input.value && index > 0) {
      inputs[index - 1].focus();
      inputs[index - 1].classList.remove('filled');
    }
  });
  
  // Permettre le collage du code
  input.addEventListener('paste', (e) => {
    e.preventDefault();
    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
    pastedData.split('').forEach((char, i) => {
      if (inputs[i]) {
        inputs[i].value = char;
        inputs[i].classList.add('filled');
      }
    });
    if (pastedData.length === 6) {
      inputs[5].focus();
      updateFullCode();
    }
  });
});

// Fonction pour mettre à jour le code complet
function updateFullCode() {
  const code = Array.from(inputs).map(inp => inp.value).join('');
  fullCodeInput.value = code;
  
  // Activer/désactiver le bouton
  const allFilled = code.length === 6 && /^\d{6}$/.test(code);
  verifyBtn.disabled = !allFilled;
}

// S'assurer que le code est à jour avant la soumission
verifyForm.addEventListener('submit', (e) => {
  updateFullCode();
  
  const code = fullCodeInput.value;
  
  // Vérification supplémentaire
  if (code.length !== 6 || !/^\d{6}$/.test(code)) {
    e.preventDefault();
    alert('Veuillez entrer un code à 6 chiffres valide.');
    return false;
  }
});

// Timer principal (30 minutes)
let timeLeft = 1800; // 30 minutes en secondes
const timerElement = document.getElementById('timer');

const countdown = setInterval(() => {
  timeLeft--;
  const minutes = Math.floor(timeLeft / 60);
  const seconds = timeLeft % 60;
  timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  
  if (timeLeft <= 0) {
    clearInterval(countdown);
    alert('Le code a expiré. Veuillez redemander un nouveau code.');
    window.location.href = 'mdpo1.php';
  }
}, 1000);

// Animation au chargement
window.addEventListener('load', function() {
  document.querySelector('.verify-container').style.animation = 'fadeIn 0.5s ease';
});