function changeStatus(status) {
  if (confirm(`Changer le statut en "${status.toUpperCase()}" ?`)) {
    alert(`Statut mis à jour : ${status}`);
    // Ici appel AJAX vers ton backend PHP
    location.reload();
  }
}

function submitAdminReply() {
  const text = document.getElementById('adminReply').value.trim();
  if (!text) return alert("Veuillez écrire une réponse");
  alert("Réponse administrateur envoyée avec succès !");
  document.getElementById('adminReply').value = '';
  // AJAX ici
}

function assignToMe() {
  if (confirm("Vous assigner cette réclamation ?")) {
    alert("Réclamation assignée à vous-même");
  }
}

function closeReclamation() {
  if (confirm("Fermer définitivement cette réclamation ?")) {
    alert("Réclamation fermée");
    window.location.href = "admin-reclamations-list.html";
  }
}

function deleteReclamation() {
  if (confirm("SUPPRIMER DÉFINITIVEMENT cette réclamation ? Action irréversible !")) {
    if (confirm("Vraiment sûr ?")) {
      alert("Réclamation supprimée");
      window.location.href = "admin-reclamations-list.html";
    }
  }
}