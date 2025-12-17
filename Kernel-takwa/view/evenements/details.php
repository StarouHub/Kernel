<?php
$pageTitle = htmlspecialchars($evenement['titre']);
require_once __DIR__ . '/../layouts/header.php';

$dateFormatted = Evenement::formatDateWithDay($evenement['date']);
$dateShort = Evenement::formatDateForDisplay($evenement['date']);

$typeColors = [
  'Workshop' => '#2563EB',
  'Hackathon' => '#F59E0B',
  'Conférence' => '#7C3AED',
  'Meetup' => '#10B981',
  'Webinaire' => '#EF4444'
];
$typeColor = $typeColors[$evenement['type']] ?? '#2563EB';
?>

<style>
  .event-hero {
    background: white;
    padding: 40px 0;
    margin-bottom: 30px;
  }

  .event-cover {
    width: 100%;
    height: 450px;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  .event-header {
    margin-top: 30px;
  }

  .event-title {
    font-size: 36px;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 15px;
    font-family: 'Raleway', sans-serif;
  }

  .event-meta {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    color: #6B7280;
  }

  .meta-item i {
    color: var(--primary-color);
    font-size: 20px;
  }

  .meta-item strong {
    color: var(--dark-color);
  }

  .tag {
    display: inline-block;
    padding: 6px 15px;
    background: var(--light-bg);
    color: var(--primary-color);
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    margin-right: 8px;
    margin-bottom: 8px;
  }

  .content-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  .section-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .registration-card {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 20px;
    position: sticky;
    top: 100px;
  }

  .price-tag {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .price-label {
    font-size: 16px;
    opacity: 0.9;
    margin-bottom: 25px;
  }

  .spots-remaining {
    background: rgba(255,255,255,0.2);
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
  }

  .spots-number {
    font-size: 32px;
    font-weight: 700;
    display: block;
  }

  .btn-register-main {
    background: var(--accent-color);
    color: white;
    padding: 15px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    width: 100%;
    font-size: 16px;
    margin-bottom: 10px;
    transition: all 0.3s;
  }

  .btn-register-main:hover {
    background: #E68A00;
    transform: translateY(-2px);
  }

  .btn-add-calendar {
    background: white;
    color: var(--primary-color);
    padding: 12px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s;
  }

  .btn-add-calendar:hover {
    background: #E5E7EB;
  }

  .organizer-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 450px;
  }

  .organizer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 32px;
    margin: 0 auto 15px;
  }

  .organizer-name {
    text-align: center;
    font-weight: 600;
    font-size: 20px;
    margin-bottom: 5px;
  }

  .organizer-role {
    text-align: center;
    color: #6B7280;
    margin-bottom: 15px;
  }

  .btn-share {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border: 2px solid var(--primary-color);
    border-radius: 10px;
    background: white;
    color: var(--primary-color);
    font-weight: 600;
    transition: all 0.3s;
    cursor: pointer;
  }

  .btn-share:hover {
    background: var(--primary-color);
    color: white;
  }

  .btn-action-toolbar {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
  }

  .btn-small {
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-edit {
    background: #2563EB;
    color: #fff;
  }

  .btn-edit:hover {
    background: #1D4ED8;
    color: #fff;
  }

  .btn-delete {
    background: #DC2626;
    color: #fff;
  }

  .btn-delete:hover {
    background: #B91C1C;
    color: #fff;
  }

  .btn-back {
    background: #E5E7EB;
    color: #111827;
  }

  .btn-back:hover {
    background: #D1D5DB;
    color: #111827;
  }

  .info-box {
    background: var(--light-bg);
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    margin-bottom: 20px;
  }
</style>

<div class="event-hero">
  <div class="container">
    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1200' height='450' viewBox='0 0 1200 450'%3E%3Crect fill='<?php echo urlencode($typeColor); ?>' width='1200' height='450'/%3E%3Ccircle cx='300' cy='225' r='100' fill='%2360A5FA' opacity='0.4'/%3E%3Ccircle cx='900' cy='225' r='120' fill='%237C3AED' opacity='0.3'/%3E%3Crect x='450' y='150' width='300' height='150' fill='white' opacity='0.1' rx='20'/%3E%3Ctext x='50%25' y='45%25' font-size='70' fill='white' text-anchor='middle' font-weight='bold'%3E<?php echo urlencode($evenement['type']); ?>%3C/text%3E%3Ctext x='50%25' y='55%25' font-size='50' fill='%23FCD34D' text-anchor='middle' font-weight='bold'%3E<?php echo urlencode(substr($evenement['titre'], 0, 20)); ?>%3C/text%3E%3C/svg%3E"
         alt="Event Cover" class="event-cover">
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <div class="event-header">
        <h1 class="event-title"><?php echo htmlspecialchars($evenement['titre']); ?></h1>

        <div class="event-meta">
          <div class="meta-item">
            <i class="bi bi-calendar-event"></i>
            <span><strong><?php echo $dateFormatted; ?></strong></span>
          </div>
          <div class="meta-item">
            <i class="bi bi-geo-alt"></i>
            <span><strong><?php echo htmlspecialchars($evenement['lieu']); ?></strong></span>
          </div>
          <div class="meta-item">
            <i class="bi bi-people"></i>
            <span><strong><?php echo $evenement['capacite']; ?> places</strong></span>
          </div>
          <div class="meta-item">
            <i class="bi bi-tag"></i>
            <span><strong><?php echo htmlspecialchars($evenement['type']); ?></strong></span>
          </div>
        </div>

        <div class="mb-3">
          <span class="tag">#<?php echo htmlspecialchars($evenement['type']); ?></span>
          <span class="tag">#Événement</span>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3">
          <button class="btn-share">
            <i class="bi bi-share"></i> Partager
          </button>
          <button class="btn-share">
            <i class="bi bi-bookmark"></i> Sauvegarder
          </button>
        </div>

        <div class="btn-action-toolbar">
          <a href="index.php" class="btn-small btn-back">
            <i class="bi bi-arrow-left"></i> Retour à la liste
          </a>
          <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="index.php?action=edit&id=<?php echo $evenement['id']; ?>" class="btn-small btn-edit">
              <i class="bi bi-pencil"></i> Modifier
            </a>
            <form method="POST" action="index.php?action=delete" style="display: inline;" 
                  onsubmit="return confirm('Voulez-vous vraiment supprimer cet événement ?');">
              <input type="hidden" name="id" value="<?php echo $evenement['id']; ?>">
              <button type="submit" class="btn-small btn-delete">
                <i class="bi bi-trash"></i> Supprimer
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <div class="content-section">
        <div class="section-title">
          <i class="bi bi-info-circle"></i> À propos de l'événement
        </div>

        <p><?php echo nl2br(htmlspecialchars($evenement['description'])); ?></p>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="registration-card">
        <?php 
        // Prix par défaut selon le type d'événement
        $prices = [
            'Workshop' => 50.00,
            'Hackathon' => 0.00,
            'Conférence' => 75.00,
            'Meetup' => 0.00,
            'Webinaire' => 25.00
        ];
        $eventPrice = $prices[$evenement['type']] ?? 0.00;
        ?>
        <div class="price-tag"><?php echo $eventPrice > 0 ? number_format($eventPrice, 2, ',', ' ') . ' €' : 'GRATUIT'; ?></div>
        <div class="price-label"><?php echo $eventPrice > 0 ? 'Prix par personne' : 'Places limitées'; ?></div>

        <div class="spots-remaining <?php echo isset($isFull) && $isFull ? 'spots-full' : ''; ?>">
          <?php if (isset($isFull) && $isFull): ?>
            <span class="spots-number" style="color: #EF4444;">COMPLET</span>
            <span>Cet événement est complet</span>
          <?php else: ?>
            <span class="spots-number"><?php echo isset($remainingSpots) ? $remainingSpots : $evenement['capacite']; ?></span>
            <span>places disponibles</span>
          <?php endif; ?>
        </div>

        <?php if (isset($isFull) && $isFull): ?>
          <a href="index.php?action=inscription&id=<?php echo $evenement['id']; ?>" class="btn-register-main" style="text-decoration: none; text-align: center; display: inline-block; background: #F59E0B;">
            <i class="bi bi-clock-history me-2"></i> S'inscrire sur la liste d'attente
          </a>
        <?php else: ?>
          <a href="index.php?action=inscription&id=<?php echo $evenement['id']; ?>" class="btn-register-main" style="text-decoration: none; text-align: center; display: inline-block;">
            <i class="bi bi-box-arrow-right me-2"></i> S'inscrire Maintenant
          </a>
        <?php endif; ?>

        <button class="btn-add-calendar">
          <i class="bi bi-calendar-plus me-2"></i> Ajouter au calendrier
        </button>

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 14px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>📅 Date</span>
            <strong><?php echo $dateShort; ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>📍 Lieu</span>
            <strong><?php echo htmlspecialchars($evenement['lieu']); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>🎫 Type</span>
            <strong><?php echo htmlspecialchars($evenement['type']); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span>👥 Capacité</span>
            <strong><?php echo $evenement['capacite']; ?> places</strong>
          </div>
          <?php if (isset($remainingSpots)): ?>
          <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.2);">
            <span>✅ Inscrits</span>
            <strong><?php echo $evenement['capacite'] - $remainingSpots; ?> / <?php echo $evenement['capacite']; ?></strong>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="organizer-card">
        <div class="organizer-avatar">U<?php echo $evenement['user_id']; ?></div>
        <div class="organizer-name">Organisateur</div>
        <div class="organizer-role">User ID: <?php echo $evenement['user_id']; ?></div>

        <p style="text-align: center; color: #6B7280; font-size: 14px;">Organisateur de cet événement.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

