<?php
$pageTitle = 'Inscription à l\'événement';
require_once __DIR__ . '/../layouts/header.php';

// On prépare une date d'inscription par défaut (aujourd'hui)
$today = date('Y-m-d');
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
  .form-select {
    border-radius: 10px;
    border: 2px solid #E5E7EB;
    font-size: 14px;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .error-message {
    font-size: 13px;
    color: #DC2626;
    margin-top: 4px;
    display: none;
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
    <h1><i class="bi bi-person-plus"></i> Inscription à l'événement</h1>
    <div class="breadcrumb-link">
      <a href="index.php?action=details&id=<?php echo $evenement['id']; ?>">
        <i class="bi bi-arrow-left"></i> Retour aux détails de l'événement
      </a>
    </div>
  </div>
</div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="form-card">
        <div class="form-section-title">
          <i class="bi bi-card-checklist"></i>
          Formulaire d'inscription
        </div>

        <?php if (isset($isFull) && $isFull): ?>
          <div class="alert alert-warning" style="background: #FEF3C7; border: 2px solid #F59E0B; color: #92400E; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Événement complet</strong><br>
            Cet événement a atteint sa capacité maximale. Vous pouvez vous inscrire sur la liste d'attente. 
            Vous serez notifié si une place se libère.
          </div>
        <?php elseif (isset($remainingSpots) && $remainingSpots <= 5): ?>
          <div class="alert alert-info" style="background: #DBEAFE; border: 2px solid #2563EB; color: #1E40AF; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <i class="bi bi-info-circle-fill"></i> <strong>Plus que <?php echo $remainingSpots; ?> place<?php echo $remainingSpots > 1 ? 's' : ''; ?> disponible<?php echo $remainingSpots > 1 ? 's' : ''; ?> !</strong>
          </div>
        <?php endif; ?>

        <p class="hint" style="margin-bottom: 20px;">
          <?php if (isset($isFull) && $isFull): ?>
            Vous souhaitez vous inscrire sur la liste d'attente pour : <strong><?php echo htmlspecialchars($evenement['titre']); ?></strong>
          <?php else: ?>
            Vous vous inscrivez à : <strong><?php echo htmlspecialchars($evenement['titre']); ?></strong>
          <?php endif; ?>
          (<?php echo htmlspecialchars(Evenement::formatDateForDisplay($evenement['date'])); ?>)
        </p>

        <form id="inscriptionForm" action="index.php?action=inscription_save" method="post" onsubmit="return validerInscription();">
          <!-- id_evenement correspond à la colonne de ta base -->
          <input type="hidden" name="id_evenement" value="<?php echo htmlspecialchars($evenement['id']); ?>">
          <?php if (isset($isFull) && $isFull): ?>
            <input type="hidden" name="waitlist" value="1">
          <?php endif; ?>

          <div class="row">
            <div class="mb-3 col-md-6">
              <label class="form-label" for="nom">Nom</label>
              <input class="form-control" id="nom" name="nom" type="text" placeholder="Votre nom">
              <div class="error-message" id="err-nom">Le nom est obligatoire.</div>
            </div>

            <div class="mb-3 col-md-6">
              <label class="form-label" for="prenom">Prénom</label>
              <input class="form-control" id="prenom" name="prenom" type="text" placeholder="Votre prénom">
              <div class="error-message" id="err-prenom">Le prénom est obligatoire.</div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="email">Adresse e-mail</label>
            <!-- On garde l'id "email" pour la validation JS, mais le name = adresse_mail comme dans ta base -->
            <input class="form-control" id="email" name="adresse_mail" type="email" placeholder="exemple@domaine.com">
            <div class="error-message" id="err-email">Veuillez saisir une adresse e-mail valide.</div>
          </div>

          <div class="row">
            <div class="mb-3 col-md-6">
              <label class="form-label" for="date_inscription">Date d'inscription</label>
              <input class="form-control" id="date_inscription" name="date_inscription" type="date" value="<?php echo $today; ?>" readonly>
              <div class="error-message" id="err-date-inscription">Veuillez saisir une date d'inscription.</div>
            </div>

            <div class="mb-3 col-md-6">
              <label class="form-label" for="statut">Statut</label>
              <input class="form-control" id="statut" name="statut" type="text" value="Confirmé" readonly>
              <div class="error-message" id="err-statut">Veuillez choisir un statut.</div>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn-primary-gradient">
              <?php if (isset($isFull) && $isFull): ?>
                <i class="bi bi-clock-history"></i> S'inscrire sur la liste d'attente
              <?php else: ?>
                <i class="bi bi-check2-circle"></i> S'inscrire
              <?php endif; ?>
            </button>
            <a href="index.php?action=details&id=<?php echo $evenement['id']; ?>" class="btn-secondary-outline">
              <i class="bi bi-x-circle"></i> Annuler
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function resetErreursInscription() {
    var champs = ["nom", "prenom", "email", "date_inscription", "statut"];
    for (var i = 0; i < champs.length; i++) {
      var input = document.getElementById(champs[i]);
      if (input) {
        input.classList.remove("field-invalid");
      }
    }

    var erreurs = document.querySelectorAll("#inscriptionForm .error-message");
    erreurs.forEach(function (e) {
      e.style.display = "none";
    });
  }

  function validerInscription() {
    resetErreursInscription();
    var ok = true;

    var nom = document.getElementById("nom");
    if (!nom.value || nom.value.trim().length < 2) {
      ok = false;
      nom.classList.add("field-invalid");
      document.getElementById("err-nom").style.display = "block";
    }

    var prenom = document.getElementById("prenom");
    if (!prenom.value || prenom.value.trim().length < 2) {
      ok = false;
      prenom.classList.add("field-invalid");
      document.getElementById("err-prenom").style.display = "block";
    }

    var email = document.getElementById("email");
    var emailVal = email.value.trim();
    var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailVal || !regexEmail.test(emailVal)) {
      ok = false;
      email.classList.add("field-invalid");
      document.getElementById("err-email").style.display = "block";
    }

    var dateInscription = document.getElementById("date_inscription");
    var todayDate = "<?php echo $today; ?>";
    if (!dateInscription.value || dateInscription.value !== todayDate) {
      ok = false;
      dateInscription.classList.add("field-invalid");
      document.getElementById("err-date-inscription").style.display = "block";
      document.getElementById("err-date-inscription").textContent = "La date d'inscription doit être la date du jour.";
    }

    var statut = document.getElementById("statut");
    if (statut.value !== "Confirmé") {
      ok = false;
      statut.classList.add("field-invalid");
      document.getElementById("err-statut").style.display = "block";
    }

    if (!ok) {
      alert("Merci de corriger les erreurs du formulaire avant de continuer.");
      return false;
    }

    return true;
  }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>


