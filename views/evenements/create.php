<?php
$pageTitle = 'Créer un événement';
require_once __DIR__ . '/../layouts/header.php';

// La validation est maintenant gérée uniquement en JavaScript
?>

<style>
  .page-header {
    background: white;
    padding: 40px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid #E5E7EB;
  }

  .page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 8px;
    font-family: 'Raleway', sans-serif;
  }

  .breadcrumb-link {
    font-size: 14px;
    color: #6B7280;
  }

  .breadcrumb-link a {
    color: var(--primary-color);
    text-decoration: none;
  }

  .form-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
  }

  .form-section-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 4px;
  }

  .form-control,
  .form-select,
  textarea {
    border-radius: 10px;
    border: 2px solid #E5E7EB;
    font-size: 14px;
  }

  .form-control:focus,
  .form-select:focus,
  textarea:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .error-message {
    font-size: 13px;
    color: #DC2626;
    margin-top: 4px;
    display: block;
  }

  .field-invalid {
    border-color: #DC2626 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
  }

  .btn-primary-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    color: #fff;
    border-radius: 999px;
    padding: 10px 26px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
    transition: all 0.25s;
  }

  .btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(37, 99, 235, 0.6);
    color: #fff;
  }

  .btn-secondary-outline {
    border-radius: 999px;
    padding: 9px 20px;
    border: 2px solid #D1D5DB;
    background: #fff;
    font-weight: 600;
    font-size: 14px;
    color: #111827;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s;
  }

  .btn-secondary-outline:hover {
    background: #F3F4F6;
    color: #111827;
  }

  .hint {
    font-size: 12px;
    color: #6B7280;
  }
</style>

<div class="page-header">
  <div class="container">
    <h1><i class="bi bi-plus-circle"></i> Créer un événement</h1>
    <div class="breadcrumb-link">
      <a href="index.php"><i class="bi bi-arrow-left"></i> Retour à la liste des événements</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="form-card">
        <div class="form-section-title">
          <i class="bi bi-calendar2-plus"></i> Informations de l'événement
        </div>

        <form id="eventForm" action="index.php?action=save" method="post" onsubmit="return validerEvenement();">
          <div class="mb-3">
            <label class="form-label" for="titre">Titre de l'événement</label>
            <input class="form-control" 
                   id="titre" name="titre" type="text" 
                   placeholder="Ex : Workshop TensorFlow pour débutants"
                   value="">
            <div class="error-message" id="err-titre" style="display: none;">Le titre est obligatoire (au moins 5 caractères).</div>
          </div>

          <div class="row">
            <div class="mb-3 col-md-6">
              <label class="form-label" for="type">Type d'événement</label>
              <select class="form-select" id="type" name="type">
                <option value="">-- Sélectionner --</option>
                <option value="Workshop">Workshop</option>
                <option value="Hackathon">Hackathon</option>
                <option value="Conférence">Conférence</option>
                <option value="Meetup">Meetup</option>
                <option value="Webinaire">Webinaire</option>
              </select>
              <div class="error-message" id="err-type" style="display: none;">Veuillez choisir un type.</div>
            </div>

            <div class="mb-3 col-md-6">
              <label class="form-label" for="date">Date</label>
              <input class="form-control" 
                     id="date" name="date" type="text" 
                     placeholder="Ex : 22/11/2024"
                     value="">
              <div class="hint">Format conseillé : JJ/MM/AAAA</div>
              <div class="error-message" id="err-date" style="display: none;">Veuillez saisir une date au format JJ/MM/AAAA.</div>
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-md-6">
              <label class="form-label" for="lieu">Lieu</label>
              <input class="form-control" 
                     id="lieu" name="lieu" type="text" 
                     placeholder="Ex : Online, Tunis Tech Hub..."
                     value="">
              <div class="error-message" id="err-lieu" style="display: none;">Le lieu est obligatoire.</div>
            </div>

            <div class="mb-3 col-md-6">
              <label class="form-label" for="capacite">Capacité (nombre de places)</label>
              <input class="form-control" 
                     id="capacite" name="capacite" type="text" 
                     placeholder="Ex : 50"
                     value="">
              <div class="error-message" id="err-capacite" style="display: none;">La capacité doit être un nombre entier positif.</div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="userId">Organisateur (user_id)</label>
            <input class="form-control" 
                   id="userId" name="user_id" type="text" 
                   placeholder="Id de l'utilisateur organisateur"
                   value="1">
            <div class="hint">En pratique, ce champ peut être alimenté automatiquement à partir de la session.</div>
            <div class="error-message" id="err-user" style="display: none;">L'identifiant organisateur est obligatoire (nombre).</div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" 
                      id="description" name="description" rows="5" 
                      placeholder="Décrivez le contenu, les objectifs et le public cible de l'événement."></textarea>
            <div class="error-message" id="err-description" style="display: none;">La description est obligatoire (au moins 20 caractères).</div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn-primary-gradient">
              <i class="bi bi-save"></i> Enregistrer l'événement
            </button>
            <a href="index.php" class="btn-secondary-outline">
              <i class="bi bi-x-circle"></i> Annuler
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function resetErreurs() {
    var champs = ["titre", "type", "date", "lieu", "capacite", "userId", "description"];
    for (var i = 0; i < champs.length; i++) {
      var input = document.getElementById(champs[i]);
      if (input) {
        input.classList.remove("field-invalid");
      }
    }

    var erreurs = document.querySelectorAll(".error-message");
    erreurs.forEach(function (e) {
      if (e.id) e.style.display = "none";
    });
  }

  // Fonction pour convertir une date JJ/MM/AAAA en objet Date
  function parseDate(dateStr) {
    var parts = dateStr.split('/');
    if (parts.length !== 3) return null;
    var jour = parseInt(parts[0], 10);
    var mois = parseInt(parts[1], 10) - 1; // Les mois commencent à 0
    var annee = parseInt(parts[2], 10);
    
    if (isNaN(jour) || isNaN(mois) || isNaN(annee)) return null;
    if (jour < 1 || jour > 31 || mois < 0 || mois > 11) return null;
    
    return new Date(annee, mois, jour);
  }

  // Fonction pour vérifier si une date est dans le passé
  function isDateInPast(dateStr) {
    var dateEvent = parseDate(dateStr);
    if (!dateEvent) return false;
    
    var aujourdhui = new Date();
    aujourdhui.setHours(0, 0, 0, 0); // Réinitialiser l'heure à minuit
    dateEvent.setHours(0, 0, 0, 0);
    
    return dateEvent < aujourdhui;
  }

  function validerEvenement() {
    resetErreurs();
    var ok = true;

    // Validation du titre
    var titre = document.getElementById("titre");
    if (!titre.value || titre.value.trim().length < 5) {
      ok = false;
      titre.classList.add("field-invalid");
      var errTitre = document.getElementById("err-titre");
      if (errTitre) {
        errTitre.textContent = "Le titre est obligatoire (au moins 5 caractères).";
        errTitre.style.display = "block";
      }
    }

    // Validation du type
    var type = document.getElementById("type");
    if (!type.value) {
      ok = false;
      type.classList.add("field-invalid");
      var errType = document.getElementById("err-type");
      if (errType) errType.style.display = "block";
    }

    // Validation de la date
    var date = document.getElementById("date");
    var dateValue = date.value.trim();
    var regexDate = /^[0-3][0-9]\/[0-1][0-9]\/[0-9]{4}$/;
    var errDate = document.getElementById("err-date");
    
    if (!dateValue) {
      ok = false;
      date.classList.add("field-invalid");
      if (errDate) {
        errDate.textContent = "Veuillez saisir une date.";
        errDate.style.display = "block";
      }
    } else if (!regexDate.test(dateValue)) {
      ok = false;
      date.classList.add("field-invalid");
      if (errDate) {
        errDate.textContent = "Veuillez saisir une date au format JJ/MM/AAAA.";
        errDate.style.display = "block";
      }
    } else if (isDateInPast(dateValue)) {
      ok = false;
      date.classList.add("field-invalid");
      if (errDate) {
        errDate.textContent = "Vous ne pouvez pas ajouter un événement dans une date passée.";
        errDate.style.display = "block";
      }
    }

    // Validation du lieu
    var lieu = document.getElementById("lieu");
    if (!lieu.value || lieu.value.trim().length === 0) {
      ok = false;
      lieu.classList.add("field-invalid");
      var errLieu = document.getElementById("err-lieu");
      if (errLieu) errLieu.style.display = "block";
    }

    // Validation de la capacité
    var capacite = document.getElementById("capacite");
    var capNum = parseInt(capacite.value, 10);
    if (isNaN(capNum) || capNum <= 0) {
      ok = false;
      capacite.classList.add("field-invalid");
      var errCapacite = document.getElementById("err-capacite");
      if (errCapacite) errCapacite.style.display = "block";
    }

    // Validation de l'organisateur
    var userId = document.getElementById("userId");
    var userNum = parseInt(userId.value, 10);
    if (isNaN(userNum) || userNum <= 0) {
      ok = false;
      userId.classList.add("field-invalid");
      var errUser = document.getElementById("err-user");
      if (errUser) errUser.style.display = "block";
    }

    // Validation de la description
    var description = document.getElementById("description");
    if (!description.value || description.value.trim().length < 20) {
      ok = false;
      description.classList.add("field-invalid");
      var errDescription = document.getElementById("err-description");
      if (errDescription) errDescription.style.display = "block";
    }

    if (!ok) {
      alert("Merci de corriger les erreurs du formulaire avant de continuer.");
      return false;
    }

    return true;
  }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

