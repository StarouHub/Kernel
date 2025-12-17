<?php
/**
 * Service de Génération PDF Professionnel pour Kernel
 * Génère automatiquement des fiches projet complètes en PDF
 */

require_once __DIR__ . '/../config.php';

class PDFGeneratorService
{
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }
    
    /**
     * Génère un PDF complet pour un projet
     */
    public function generateProjectPDF($projetId)
    {
        try {
            // Récupérer les données complètes du projet
            $projectData = $this->getCompleteProjectData($projetId);
            
            if (!$projectData) {
                throw new Exception("Projet introuvable avec l'ID: $projetId");
            }
            
            // Initialiser FPDF
            require_once __DIR__ . '/../lib/fpdf/fpdf.php';
            
            $pdf = new FPDF('P', 'mm', 'A4');
            
            // Configuration du document
            $this->setupPDFDocument($pdf, $projectData);
            
            // Page 1: Informations générales
            $this->addGeneralInfoPage($pdf, $projectData);
            
            // Page 2: Actualités
            $this->addActualitesPage($pdf, $projectData);
            
            // Page 3: Contact et statistiques
            $this->addContactStatsPage($pdf, $projectData);
            
            // Générer le nom du fichier
            $filename = $this->generateFilename($projectData);
            
            // Retourner le PDF
            return [
                'success' => true,
                'pdf' => $pdf,
                'filename' => $filename,
                'project_title' => $projectData['projet']['titre']
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupère toutes les données nécessaires pour le projet
     */
    private function getCompleteProjectData($projetId)
    {
        // Données du projet principal
        $sql = "SELECT p.*, u.nom as owner_nom, u.prenom as owner_prenom, u.email as owner_email, u.telephone as owner_telephone,
                       GROUP_CONCAT(DISTINCT c.nom SEPARATOR ', ') as categories
                FROM projet p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN projet_categorie pc ON p.id = pc.projet_id
                LEFT JOIN categorie c ON pc.categorie_id = c.id
                WHERE p.id = :projet_id
                GROUP BY p.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['projet_id' => $projetId]);
        $projet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$projet) {
            return null;
        }
        
        // Actualités du projet
        $sql = "SELECT * FROM actualite WHERE projet_id = :projet_id ORDER BY date_publication DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['projet_id' => $projetId]);
        $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Statistiques
        $sql = "SELECT 
                    COUNT(DISTINCT a.id) as nb_actualites,
                    COUNT(DISTINCT i.id) as nb_investisseurs,
                    COALESCE(SUM(i.montant), 0) as total_investissements,
                    COUNT(DISTINCT CASE WHEN a.type = 'milestone' THEN a.id END) as nb_milestones,
                    COUNT(DISTINCT CASE WHEN a.type = 'update' THEN a.id END) as nb_updates,
                    COUNT(DISTINCT CASE WHEN a.type = 'announcement' THEN a.id END) as nb_announcements
                FROM projet p
                LEFT JOIN actualite a ON p.id = a.projet_id
                LEFT JOIN investissement i ON p.id = i.projet_id
                WHERE p.id = :projet_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['projet_id' => $projetId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'projet' => $projet,
            'actualites' => $actualites,
            'stats' => $stats
        ];
    }
    
    /**
     * Configure le document PDF
     */
    private function setupPDFDocument($pdf, $projectData)
    {
        // Métadonnées du document
        $pdf->SetTitle('Fiche Projet: ' . $projectData['projet']['titre']);
        $pdf->SetAuthor('Kernel Platform');
        $pdf->SetCreator('Kernel - Plateforme d\'Innovation Tunisienne');
        
        // Configuration des marges
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(true, 20);
        
        // Police par défaut
        $pdf->SetFont('Arial', '', 11);
    }
    
    /**
     * Page 1: Informations générales du projet
     */
    private function addGeneralInfoPage($pdf, $projectData)
    {
        $pdf->AddPage();
        
        $projet = $projectData['projet'];
        
        // Titre principal
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 15, $projet['titre'], 0, 1, 'C');
        $pdf->Ln(5);
        
        // Ligne de séparation
        $pdf->SetDrawColor(41, 128, 185);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(10);
        
        // Informations de base dans un tableau
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'INFORMATIONS GÉNÉRALES', 0, 1, 'L');
        $pdf->Ln(3);
        
        // Tableau des informations
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(240, 248, 255);
        
        $infos = [
            ['Catégorie(s)', $projet['categories'] ?: 'Non spécifiée'],
            ['Statut', ucfirst($projet['statut'])],
            ['Budget requis', number_format($projet['budget_requis'], 0, ',', ' ') . ' TND'],
            ['Budget actuel', number_format($projet['budget_actuel'], 0, ',', ' ') . ' TND'],
            ['Progression', $this->calculateProgress($projet['budget_actuel'], $projet['budget_requis']) . '%'],
            ['Date de création', date('d/m/Y', strtotime($projet['date_creation']))]
        ];
        
        foreach ($infos as $i => $info) {
            $fill = ($i % 2 == 0) ? 1 : 0;
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(50, 8, $info[0] . ':', 1, 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(120, 8, $info[1], 1, 1, 'L', $fill);
        }
        
        $pdf->Ln(10);
        
        // Description
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'DESCRIPTION DU PROJET', 0, 1, 'L');
        $pdf->Ln(3);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 6, $projet['description'], 0, 'J');
        
        $pdf->Ln(10);
        
        // Barre de progression visuelle
        $this->addProgressBar($pdf, $projet['budget_actuel'], $projet['budget_requis']);
        
        $pdf->Ln(10);
        
        // Propriétaire du projet
        if ($projet['owner_nom']) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(0, 8, 'PROPRIÉTAIRE DU PROJET', 0, 1, 'L');
            $pdf->Ln(3);
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->Cell(0, 8, $projet['owner_prenom'] . ' ' . $projet['owner_nom'], 1, 1, 'L', 1);
            if ($projet['owner_email']) {
                $pdf->Cell(0, 8, 'Email: ' . $projet['owner_email'], 1, 1, 'L', 0);
            }
            if ($projet['owner_telephone']) {
                $pdf->Cell(0, 8, 'Téléphone: ' . $projet['owner_telephone'], 1, 1, 'L', 1);
            }
        }
    }
    
    /**
     * Page 2: Toutes les actualités du projet
     */
    private function addActualitesPage($pdf, $projectData)
    {
        $pdf->AddPage();
        
        $actualites = $projectData['actualites'];
        $stats = $projectData['stats'];
        
        // Titre de la page
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 12, 'ACTUALITÉS DU PROJET', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Statistiques rapides
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(240, 248, 255);
        
        $pdf->Cell(42, 8, 'Total: ' . $stats['nb_actualites'], 1, 0, 'C', 1);
        $pdf->Cell(42, 8, 'Milestones: ' . $stats['nb_milestones'], 1, 0, 'C', 1);
        $pdf->Cell(42, 8, 'Updates: ' . $stats['nb_updates'], 1, 0, 'C', 1);
        $pdf->Cell(44, 8, 'Annonces: ' . $stats['nb_announcements'], 1, 1, 'C', 1);
        
        $pdf->Ln(10);
        
        if (empty($actualites)) {
            $pdf->SetFont('helvetica', 'I', 12);
            $pdf->SetTextColor(150, 150, 150);
            $pdf->Cell(0, 20, 'Aucune actualité publiée pour ce projet.', 0, 1, 'C');
            return;
        }
        
        // Liste des actualités
        foreach ($actualites as $i => $actualite) {
            // Éviter le débordement de page
            if ($pdf->GetY() > 250) {
                $pdf->AddPage();
            }
            
            $this->addActualiteBlock($pdf, $actualite, $i + 1);
            $pdf->Ln(8);
        }
    }
    
    /**
     * Page 3: Contact et statistiques détaillées
     */
    private function addContactStatsPage($pdf, $projectData)
    {
        $pdf->AddPage();
        
        $projet = $projectData['projet'];
        $stats = $projectData['stats'];
        
        // Titre de la page
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 12, 'STATISTIQUES & CONTACT', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Statistiques détaillées
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'STATISTIQUES DU PROJET', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Graphique de progression (textuel)
        $this->addDetailedStats($pdf, $stats, $projet);
        
        $pdf->Ln(15);
        
        // Informations de contact
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'INFORMATIONS DE CONTACT', 0, 1, 'L');
        $pdf->Ln(5);
        
        $this->addContactInfo($pdf, $projet);
        
        $pdf->Ln(15);
        
        // Pied de page avec QR code (simulé) et informations Kernel
        $this->addFooterInfo($pdf, $projet);
    }
    
    /**
     * Ajoute un bloc d'actualité
     */
    private function addActualiteBlock($pdf, $actualite, $numero)
    {
        // Icône selon le type
        $icons = [
            'milestone' => '🎯',
            'update' => '📢',
            'announcement' => '📣'
        ];
        
        $colors = [
            'milestone' => [46, 125, 50],
            'update' => [255, 152, 0],
            'announcement' => [156, 39, 176]
        ];
        
        $icon = $icons[$actualite['type']] ?? '📰';
        $color = $colors[$actualite['type']] ?? [100, 100, 100];
        
        // En-tête de l'actualité
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor($color[0], $color[1], $color[2]);
        $pdf->Cell(10, 8, $numero . '.', 0, 0, 'L');
        $pdf->Cell(0, 8, $icon . ' ' . $actualite['titre'], 0, 1, 'L');
        
        // Date
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(10, 6, '', 0, 0);
        $pdf->Cell(0, 6, date('d/m/Y à H:i', strtotime($actualite['date_publication'])), 0, 1, 'L');
        
        // Contenu
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->SetLeftMargin(30);
        $pdf->MultiCell(0, 5, $actualite['contenu'], 0, 'J');
        $pdf->SetLeftMargin(20); // Reset margin
        
        // Ligne de séparation
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Line(20, $pdf->GetY() + 3, 190, $pdf->GetY() + 3);
    }
    
    /**
     * Ajoute une barre de progression visuelle
     */
    private function addProgressBar($pdf, $current, $target)
    {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'PROGRESSION DU FINANCEMENT', 0, 1, 'L');
        $pdf->Ln(3);
        
        $progress = $this->calculateProgress($current, $target);
        
        // Barre de progression
        $barWidth = 150;
        $barHeight = 15;
        $filledWidth = ($progress / 100) * $barWidth;
        
        // Fond de la barre
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Rect(20, $pdf->GetY(), $barWidth, $barHeight, 'F');
        
        // Partie remplie
        if ($filledWidth > 0) {
            $pdf->SetFillColor(76, 175, 80);
            $pdf->Rect(20, $pdf->GetY(), $filledWidth, $barHeight, 'F');
        }
        
        // Bordure
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->Rect(20, $pdf->GetY(), $barWidth, $barHeight);
        
        // Texte de progression
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(20 + ($barWidth / 2) - 10, $pdf->GetY() + 3);
        $pdf->Cell(20, 9, $progress . '%', 0, 0, 'C');
        
        $pdf->SetY($pdf->GetY() + $barHeight + 5);
        
        // Détails financiers
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->Cell(0, 6, 'Financé: ' . number_format($current, 0, ',', ' ') . ' TND sur ' . number_format($target, 0, ',', ' ') . ' TND', 0, 1, 'C');
    }
    
    /**
     * Ajoute les statistiques détaillées
     */
    private function addDetailedStats($pdf, $stats, $projet)
    {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(248, 249, 250);
        
        $statsData = [
            ['Nombre d\'actualités', $stats['nb_actualites']],
            ['Milestones atteints', $stats['nb_milestones']],
            ['Mises à jour publiées', $stats['nb_updates']],
            ['Annonces importantes', $stats['nb_announcements']],
            ['Nombre d\'investisseurs', $stats['nb_investisseurs']],
            ['Total des investissements', number_format($stats['total_investissements'], 0, ',', ' ') . ' TND'],
            ['Âge du projet', $this->calculateProjectAge($projet['date_creation'])],
            ['Dernière activité', $this->getLastActivity($projet['id'])]
        ];
        
        foreach ($statsData as $i => $stat) {
            $fill = ($i % 2 == 0) ? 1 : 0;
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(80, 8, $stat[0] . ':', 1, 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(90, 8, $stat[1], 1, 1, 'L', $fill);
        }
    }
    
    /**
     * Ajoute les informations de contact
     */
    private function addContactInfo($pdf, $projet)
    {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(240, 248, 255);
        
        if ($projet['owner_nom']) {
            $pdf->Cell(0, 8, 'Innovateur: ' . $projet['owner_prenom'] . ' ' . $projet['owner_nom'], 1, 1, 'L', 1);
        }
        
        if ($projet['owner_email']) {
            $pdf->Cell(0, 8, 'Email: ' . $projet['owner_email'], 1, 1, 'L', 0);
        }
        
        if ($projet['owner_telephone']) {
            $pdf->Cell(0, 8, 'Téléphone: ' . $projet['owner_telephone'], 1, 1, 'L', 1);
        }
        
        $pdf->Cell(0, 8, 'Plateforme: https://kernel.tn/projet/' . $projet['id'], 1, 1, 'L', 0);
    }
    
    /**
     * Ajoute les informations de pied de page
     */
    private function addFooterInfo($pdf, $projet)
    {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 10, 'À PROPOS DE KERNEL', 0, 1, 'L');
        $pdf->Ln(3);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 5, 'Kernel est la première plateforme tunisienne dédiée à l\'innovation et au financement participatif de projets technologiques. Notre mission est de connecter les innovateurs avec les investisseurs pour transformer les idées en réalité.', 0, 'J');
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Document généré automatiquement le ' . date('d/m/Y à H:i'), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 6, 'Kernel Platform - Innovation Made in Tunisia', 0, 1, 'C');
    }
    
    /**
     * Calcule le pourcentage de progression
     */
    private function calculateProgress($current, $target)
    {
        if ($target <= 0) return 0;
        return min(100, round(($current / $target) * 100, 1));
    }
    
    /**
     * Calcule l'âge du projet
     */
    private function calculateProjectAge($dateCreation)
    {
        $created = new DateTime($dateCreation);
        $now = new DateTime();
        $diff = $now->diff($created);
        
        if ($diff->y > 0) {
            return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return $diff->m . ' mois';
        } else {
            return $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        }
    }
    
    /**
     * Récupère la dernière activité
     */
    private function getLastActivity($projetId)
    {
        $sql = "SELECT MAX(date_publication) as last_activity FROM actualite WHERE projet_id = :projet_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['projet_id' => $projetId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['last_activity']) {
            $lastActivity = new DateTime($result['last_activity']);
            $now = new DateTime();
            $diff = $now->diff($lastActivity);
            
            if ($diff->d == 0) {
                return 'Aujourd\'hui';
            } elseif ($diff->d == 1) {
                return 'Hier';
            } elseif ($diff->d < 7) {
                return 'Il y a ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
            } else {
                return date('d/m/Y', strtotime($result['last_activity']));
            }
        }
        
        return 'Aucune activité';
    }
    
    /**
     * Génère le nom du fichier PDF
     */
    private function generateFilename($projectData)
    {
        $titre = $projectData['projet']['titre'];
        $titre = preg_replace('/[^a-zA-Z0-9\s]/', '', $titre);
        $titre = preg_replace('/\s+/', '_', trim($titre));
        $titre = substr($titre, 0, 50);
        
        return 'Kernel_Projet_' . $titre . '_' . date('Y-m-d') . '.pdf';
    }
}
?>