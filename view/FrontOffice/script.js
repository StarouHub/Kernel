function submitReply() {
  const text = document.getElementById('replyText').value.trim();
  if (!text) {
    alert('Veuillez écrire un message');
    return;
  }
  alert('Votre commentaire a été envoyé avec succès !');
  document.getElementById('replyText').value = '';
  // Ici tu feras l'appel AJAX vers ton backend
}

function shareReclamation() {
  if (navigator.share) {
    navigator.share({
      title: 'Réclamation #REC-2024-001',
      text: 'Impossible d’investir dans un projet',
      url: window.location.href
    });
  } else {
    navigator.clipboard.writeText(window.location.href);
    alert('Lien copié dans le presse-papiers !');
  }
}
