document.getElementById('resetForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const email = document.getElementById('emailInput').value;
  const submitBtn = this.querySelector('.btn-reset');
  const successMessage = document.getElementById('successMessage');

  // Simulation d'envoi
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Envoi en cours...';
  submitBtn.disabled = true;

  setTimeout(() => {
    // Afficher le message de succès
    successMessage.classList.add('show');

    // Réinitialiser le bouton
    submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Email envoyé !';
    submitBtn.style.background = '#10B981';

    // Mettre à jour les étapes
    document.querySelectorAll('.step')[0].classList.add('completed');
    document.querySelectorAll('.step')[0].classList.remove('active');
    document.querySelectorAll('.step')[1].classList.add('active');

    // Après 3 secondes, proposer de renvoyer
    setTimeout(() => {
      submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i> Renvoyer le lien';
      submitBtn.style.background = 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))';
      submitBtn.disabled = false;
    }, 3000);

  }, 2000);
});

// Animation au chargement
window.addEventListener('load', function() {
  document.querySelector('.reset-container').style.animation = 'fadeIn 0.5s ease';
});
