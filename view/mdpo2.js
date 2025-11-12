// Gestion des inputs de code
const inputs = document.querySelectorAll('.code-input');
const verifyBtn = document.getElementById('verifyBtn');
const errorMessage = document.getElementById('errorMessage');

inputs.forEach((input, index) => {
  input.addEventListener('input', (e) => {
    const value = e.target.value;
    
    if (value.length === 1) {
      input.classList.add('filled');
      if (index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    }
    
    // Vérifier si tous les champs sont remplis
    const allFilled = Array.from(inputs).every(inp => inp.value.length === 1);
    verifyBtn.disabled = !allFilled;
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
    const pastedData = e.clipboardData.getData('text').slice(0, 6);
    pastedData.split('').forEach((char, i) => {
      if (inputs[i]) {
        inputs[i].value = char;
        inputs[i].classList.add('filled');
      }
    });
    if (pastedData.length === 6) {
      inputs[5].focus();
      verifyBtn.disabled = false;
    }
  });
});

// Timer principal (5 minutes)
let timeLeft = 300; // 5 minutes
const timerElement = document.getElementById('timer');

const countdown = setInterval(() => {
  timeLeft--;
  const minutes = Math.floor(timeLeft / 60);
  const seconds = timeLeft % 60;
  timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  
  if (timeLeft <= 0) {
    clearInterval(countdown);
    alert('Le code a expiré. Veuillez redemander un nouveau code.');
    window.location.href = 'motdepasse.html';
  }
}, 1000);

// Timer de renvoi (60 secondes)
let resendTimeLeft = 60;
const resendBtn = document.getElementById('resendBtn');
const resendTimerElement = document.getElementById('resendTimer');

const resendCountdown = setInterval(() => {
  resendTimeLeft--;
  resendTimerElement.textContent = resendTimeLeft;
  
  if (resendTimeLeft <= 0) {
    clearInterval(resendCountdown);
    resendBtn.disabled = false;
    resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Renvoyer le code';
  }
}, 1000);

// Gestion du renvoi
resendBtn.addEventListener('click', () => {
  resendBtn.disabled = true;
  resendBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Code renvoyé !';
  
  setTimeout(() => {
    resendTimeLeft = 60;
    resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Renvoyer le code (<span id="resendTimer">60</span>s)';
    const newResendTimerElement = document.getElementById('resendTimer');
    
    const newResendCountdown = setInterval(() => {
      resendTimeLeft--;
      newResendTimerElement.textContent = resendTimeLeft;
      
      if (resendTimeLeft <= 0) {
        clearInterval(newResendCountdown);
        resendBtn.disabled = false;
        resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Renvoyer le code';
      }
    }, 1000);
  }, 2000);
});

// Vérification du code
document.getElementById('verifyForm').addEventListener('submit', (e) => {
  e.preventDefault();
  
  const code = Array.from(inputs).map(input => input.value).join('');
  verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Vérification...';
  verifyBtn.disabled = true;
  
  // Simulation de vérification
  setTimeout(() => {
    // Code correct fictif: 123456
    if (code === '123456') {
      verifyBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Code vérifié !';
      verifyBtn.style.background = '#10B981';
      errorMessage.classList.remove('show');
      
      // Mettre à jour les steps
      document.querySelectorAll('.step')[1].classList.add('completed');
      document.querySelectorAll('.step')[1].classList.remove('active');
      document.querySelectorAll('.step')[2].classList.add('active');
      
      setTimeout(() => {
        window.location.href = 'nouveau-motdepasse.html';
      }, 1500);
    } else {
      // Code incorrect
      errorMessage.classList.add('show');
      verifyBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i> Vérifier le code';
      verifyBtn.disabled = false;
      
      // Réinitialiser les inputs
      inputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled');
      });
      inputs[0].focus();
    }
  }, 2000);
});

// Animation au chargement
window.addEventListener('load', function() {
  document.querySelector('.verify-container').style.animation = 'fadeIn 0.5s ease';
});