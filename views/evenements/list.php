<?php
$pageTitle = 'Événements Tech';
require_once __DIR__ . '/../layouts/header.php';

$currentType = $_GET['type'] ?? 'Tous';
$currentSearch = $_GET['search'] ?? '';
?>

<style>
  .page-header {
    background: white;
    padding: 40px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid #E5E7EB;
  }

  .page-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 10px;
    font-family: 'Raleway', sans-serif;
  }

  .btn-create-event {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: #fff;
    padding: 10px 22px;
    border-radius: 999px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    transition: all 0.3s;
  }

  .btn-create-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(37, 99, 235, 0.55);
    color: #fff;
  }

  .search-bar {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
  }

  .search-input {
    position: relative;
    flex: 1;
  }

  .search-input input {
    width: 100%;
    padding: 12px 45px 12px 15px;
    border: 2px solid #E5E7EB;
    border-radius: 10px;
    transition: all 0.3s;
  }

  .search-input input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }

  .search-input i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6B7280;
  }

  .filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
  }

  .filter-tab {
    padding: 10px 20px;
    border: 2px solid #E5E7EB;
    border-radius: 25px;
    background: white;
    color: #6B7280;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
  }

  .filter-tab:hover,
  .filter-tab.active {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
  }

  .event-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s;
    margin-bottom: 20px;
  }

  .event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  }

  .event-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    position: relative;
  }

  .event-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--accent-color);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .event-date-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  }

  .event-day {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
  }

  .event-month {
    font-size: 12px;
    color: #6B7280;
    text-transform: uppercase;
  }

  .event-content {
    padding: 25px;
  }

  .event-category {
    display: inline-block;
    padding: 5px 12px;
    background: var(--light-bg);
    color: var(--primary-color);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 12px;
  }

  .event-title {
    font-size: 22px;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 10px;
  }

  .event-description {
    color: #6B7280;
    font-size: 14px;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .event-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #E5E7EB;
  }

  .meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #6B7280;
    font-size: 13px;
  }

  .meta-item i {
    color: var(--primary-color);
  }

  .event-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .btn-details {
    border-radius: 20px;
    padding: 8px 18px;
    border: 2px solid var(--primary-color);
    background: #fff;
    color: var(--primary-color);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
  }

  .btn-details:hover {
    background: var(--primary-color);
    color: #fff;
  }

  .btn-edit, .btn-delete {
    border-radius: 20px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
  }

  .btn-edit {
    background: var(--primary-color);
    color: white;
  }

  .btn-edit:hover {
    background: #1D4ED8;
    color: white;
  }

  .btn-delete {
    background: #DC2626;
    color: white;
  }

  .btn-delete:hover {
    background: #B91C1C;
    color: white;
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  .empty-state i {
    font-size: 64px;
    color: #D1D5DB;
    margin-bottom: 20px;
  }
</style>

<div class="page-header">
  <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1><i class="bi bi-calendar-event"></i> Événements Tech</h1>
      <p>Participez aux hackathons, conférences et workshops de la communauté</p>
    </div>
    <?php if (isset($_SESSION['user_role'])): ?>
      <a href="index.php?action=create" class="btn-create-event">
        <i class="bi bi-plus-circle"></i> Créer un événement
      </a>
    <?php endif; ?>
  </div>
</div>

<div class="container">
  <form method="GET" action="index.php" class="search-bar">
    <div class="row align-items-center">
      <div class="col-lg-12">
        <div class="search-input">
          <input type="text" name="search" placeholder="Rechercher un événement, un lieu, un thème..." 
                 value="<?php echo htmlspecialchars($currentSearch); ?>">
          <i class="bi bi-search"></i>
        </div>
      </div>
    </div>
  </form>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div class="filter-tabs">
      <a href="index.php?type=Tous<?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
         class="filter-tab <?php echo $currentType === 'Tous' ? 'active' : ''; ?>">Tous</a>
      <a href="index.php?type=Hackathon<?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
         class="filter-tab <?php echo $currentType === 'Hackathon' ? 'active' : ''; ?>">Hackathons</a>
      <a href="index.php?type=Conférence<?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
         class="filter-tab <?php echo $currentType === 'Conférence' ? 'active' : ''; ?>">Conférences</a>
      <a href="index.php?type=Workshop<?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
         class="filter-tab <?php echo $currentType === 'Workshop' ? 'active' : ''; ?>">Workshops</a>
      <a href="index.php?type=Meetup<?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
         class="filter-tab <?php echo $currentType === 'Meetup' ? 'active' : ''; ?>">Meetups</a>
      <a href="index.php?type=Webinaire<?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
         class="filter-tab <?php echo $currentType === 'Webinaire' ? 'active' : ''; ?>">Webinaires</a>
    </div>
    
    <div class="sort-controls">
      <label for="sortDate" style="font-weight: 500; color: #374151; margin-right: 8px;">
        <i class="bi bi-sort-down"></i> Trier par date :
      </label>
      <select id="sortDate" class="form-select" style="display: inline-block; width: auto; min-width: 200px;">
        <option value="desc">Du plus récent au plus ancien</option>
        <option value="asc" selected>Du plus ancien au plus récent</option>
      </select>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <h4 class="mb-3" style="font-weight: 600; color: var(--dark-color);">Événements à venir</h4>

      <?php if (empty($evenements)): ?>
        <div class="empty-state">
          <i class="bi bi-calendar-x"></i>
          <h3>Aucun événement trouvé</h3>
          <p>Il n'y a pas d'événements correspondant à vos critères.</p>
          <a href="index.php?action=create" class="btn-create-event mt-3">
            <i class="bi bi-plus-circle"></i> Créer le premier événement
          </a>
        </div>
      <?php else: ?>
        <div class="row" id="eventsContainer">
          <?php foreach ($evenements as $event): 
            $dateFormatted = Evenement::formatDateForDisplay($event['date']);
            $dateParts = explode('/', $dateFormatted);
            $day = $dateParts[0] ?? '';
            $monthNum = $dateParts[1] ?? '';
            $months = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            $month = $months[(int)$monthNum] ?? '';
            
            $typeColors = [
              'Workshop' => '#2563EB',
              'Hackathon' => '#F59E0B',
              'Conférence' => '#7C3AED',
              'Meetup' => '#10B981',
              'Webinaire' => '#EF4444'
            ];
            $typeColor = $typeColors[$event['type']] ?? '#2563EB';
          ?>
            <div class="col-md-6 mb-4" data-event-date="<?php echo htmlspecialchars($event['date']); ?>">
              <div class="event-card">
                <a href="index.php?action=details&id=<?php echo $event['id']; ?>" style="text-decoration:none; color:inherit;">
                  <div style="position: relative;">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='250' viewBox='0 0 400 250'%3E%3Crect fill='<?php echo urlencode($typeColor); ?>' width='400' height='250'/%3E%3Ctext x='50%25' y='50%25' font-size='35' fill='white' text-anchor='middle' dy='.3em'%3E<?php echo urlencode($event['type']); ?>%3C/text%3E%3C/svg%3E"
                         alt="Event" class="event-image">
                    <div class="event-date-badge">
                      <div class="event-day"><?php echo $day; ?></div>
                      <div class="event-month"><?php echo $month; ?></div>
                    </div>
                    <span class="event-badge" style="background: <?php echo $typeColor; ?>;"><?php echo htmlspecialchars($event['type']); ?></span>
                  </div>
                </a>
                <div class="event-content">
                  <span class="event-category"><?php echo htmlspecialchars($event['type']); ?></span>
                  <h3 class="event-title"><?php echo htmlspecialchars($event['titre']); ?></h3>
                  <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>

                  <div class="event-meta">
                    <div class="meta-item">
                      <i class="bi bi-calendar"></i>
                      <span><?php echo $dateFormatted; ?></span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-geo-alt"></i>
                      <span><?php echo htmlspecialchars($event['lieu']); ?></span>
                    </div>
                    <div class="meta-item">
                      <i class="bi bi-people"></i>
                      <span><?php echo $event['capacite']; ?> places</span>
                    </div>
                  </div>

                  <div class="event-footer">
                    <div class="d-flex gap-2 flex-wrap">
                      <a href="index.php?action=details&id=<?php echo $event['id']; ?>" class="btn-details">
                        <i class="bi bi-eye"></i> Voir les détails
                      </a>
                      <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="index.php?action=edit&id=<?php echo $event['id']; ?>" class="btn-edit">
                          <i class="bi bi-pencil"></i> Modifier
                        </a>
                        <form method="POST" action="index.php?action=delete" style="display: inline;" 
                              onsubmit="return confirm('Voulez-vous vraiment supprimer cet événement ?');">
                          <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                          <button type="submit" class="btn-delete">
                            <i class="bi bi-trash"></i> Supprimer
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  // Auto-submit search on Enter
  document.querySelector('.search-input input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      this.closest('form').submit();
    }
  });

  // Tri dynamique des événements par date
  document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sortDate');
    const eventsContainer = document.getElementById('eventsContainer');
    
    if (!sortSelect || !eventsContainer) return;
    
    // Récupérer tous les événements
    const eventCards = Array.from(eventsContainer.querySelectorAll('[data-event-date]'));
    
    sortSelect.addEventListener('change', function() {
      const sortOrder = this.value;
      
      // Trier les événements par date
      eventCards.sort(function(a, b) {
        const dateA = new Date(a.getAttribute('data-event-date'));
        const dateB = new Date(b.getAttribute('data-event-date'));
        
        if (sortOrder === 'asc') {
          // Du plus récent au plus ancien (dates décroissantes)
          return dateB - dateA;
        } else {
          // Du plus ancien au plus récent (dates croissantes)
          return dateA - dateB;
        }
      });
      
      // Réorganiser les événements dans le DOM avec animation
      eventsContainer.style.opacity = '0.5';
      eventsContainer.style.transition = 'opacity 0.3s';
      
      setTimeout(function() {
        // Vider le conteneur
        eventsContainer.innerHTML = '';
        
        // Réinsérer les événements triés
        eventCards.forEach(function(card) {
          eventsContainer.appendChild(card);
        });
        
        // Restaurer l'opacité
        eventsContainer.style.opacity = '1';
      }, 150);
    });
  });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

