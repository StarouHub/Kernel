<?php
/**
 * Générateur PDF Simple avec FPDF pour Kernel
 * Version optimisée et professionnelle
 */

require_once __DIR__ . '/../config.php';

class SimplePDFGenerator
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
            // Récupérer les données du projet
            $projectData = $this->getProjectData($projetId);
            
            if (!$projectData) {
                throw new Exception("Projet introuvable avec l'ID: $projetId");
            }
            
            // Créer le PDF avec FPDF
            $pdf = new KernelPDF();
            $pdf->SetTitle('Fiche Projet: ' . $projectData['projet']['titre']);
            $pdf->SetAuthor('Kernel Platform');
            
            // Page 1: Informations générales
            $this->addPage1($pdf, $projectData);
            
            // Page 2: Actualités
            $this->addPage2($pdf, $projectData);
            
            // Page 3: Contact et statistiques
            $this->addPage3($pdf, $projectData);
            
            $filename = $this->generateFilename($projectData);
            
            return [
                'success' => true,
                'pdf' => $pdf,
                'filename' => $filename
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupère les données du projet
     */
    private function getProjectData($projetId)
    {
        // Projet principal
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
        
        if (!$projet) return null;
        
        // Actualités
        $sql = "SELECT * FROM actualite WHERE projet_id = :projet_id ORDER BY date_publication DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['projet_id' => $projetId]);
        $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Statistiques
        $sql = "SELECT 
                    COUNT(DISTINCT a.id) as nb_actualites,
                    COUNT(DISTINCT i.id) as nb_investisseurs,
                    COALESCE(SUM(i.montant), 0) as total_investissements
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
     * Page 1: Informations générales
     */
    private function addPage1($pdf, $data)
    {
        $pdf->AddPage();
        $projet = $data['projet'];
        
        // Titre principal
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 15, utf8_decode($projet['titre']), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Informations de base
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'INFORMATIONS GENERALES', 0, 1);
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', '', 11);
        $infos = [
            ['Categorie(s)', $projet['categories'] ?: 'Non specifiee'],
            ['Statut', ucfirst($projet['statut'])],
            ['Budget requis', number_format($projet['budget_requis'], 0, ',', ' ') . ' TND'],
            ['Budget actuel', number_format($projet['budget_actuel'], 0, ',', ' ') . ' TND'],
            ['Date creation', date('d/m/Y', strtotime($projet['date_creation']))]
        ];
        
        foreach ($infos as $info) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(50, 8, utf8_decode($info[0] . ':'), 1, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(120, 8, utf8_decode($info[1]), 1, 1);
        }
        
        $pdf->Ln(10);
        
        // Description
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'DESCRIPTION DU PROJET', 0, 1);
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 6, utf8_decode($projet['description']));
        
        $pdf->Ln(10);
        
        // Barre de progression
        $this->addProgressBar($pdf, $projet['budget_actuel'], $projet['budget_requis']);
        
        // Propriétaire
        if ($projet['owner_nom']) {
            $pdf->Ln(15);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'PROPRIETAIRE DU PROJET', 0, 1);
            $pdf->Ln(5);
            
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, utf8_decode($projet['owner_prenom'] . ' ' . $projet['owner_nom']), 1, 1);
            if ($projet['owner_email']) {
                $pdf->Cell(0, 8, 'Email: ' . $projet['owner_email'], 1, 1);
            }
            if ($projet['owner_telephone']) {
                $pdf->Cell(0, 8, utf8_decode('Telephone: ' . $projet['owner_telephone']), 1, 1);
            }
        }
    }
    
    /**
     * Page 2: Actualités
     */
    private function addPage2($pdf, $data)
    {
        $pdf->AddPage();
        $actualites = $data['actualites'];
        
        // Titre
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 15, 'ACTUALITES DU PROJET', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetTextColor(0, 0, 0);
        
        if (empty($actualites)) {
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(0, 20, 'Aucune actualite publiee pour ce projet.', 0, 1, 'C');
            return;
        }
        
        // Liste des actualités
        foreach ($actualites as $i => $actualite) {
            if ($pdf->GetY() > 250) {
                $pdf->AddPage();
            }
            
            $this->addActualiteBlock($pdf, $actualite, $i + 1);
            $pdf->Ln(8);
        }
    }
    
    /**
     * Page 3: Contact et statistiques
     */
    private function addPage3($pdf, $data)
    {
        $pdf->AddPage();
        $projet = $data['projet'];
        $stats = $data['stats'];
        
        // Titre
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 15, 'STATISTIQUES & CONTACT', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetTextColor(0, 0, 0);
        
        // Statistiques
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'STATISTIQUES DU PROJET', 0, 1);
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', '', 11);
        $statsData = [
            ['Nombre d\'actualites', $stats['nb_actualites']],
            ['Nombre d\'investisseurs', $stats['nb_investisseurs']],
            ['Total investissements', number_format($stats['total_investissements'], 0, ',', ' ') . ' TND'],
            ['Age du projet', $this->calculateProjectAge($projet['date_creation'])]
        ];
        
        foreach ($statsData as $stat) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(80, 8, utf8_decode($stat[0] . ':'), 1, 0);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(90, 8, utf8_decode($stat[1]), 1, 1);
        }
        
        $pdf->Ln(15);
        
        // Contact
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'INFORMATIONS DE CONTACT', 0, 1);
        $pdf->Ln(5);
        
        if ($projet['owner_nom']) {
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, utf8_decode('Innovateur: ' . $projet['owner_prenom'] . ' ' . $projet['owner_nom']), 1, 1);
            if ($projet['owner_email']) {
                $pdf->Cell(0, 8, 'Email: ' . $projet['owner_email'], 1, 1);
            }
            if ($projet['owner_telephone']) {
                $pdf->Cell(0, 8, utf8_decode('Telephone: ' . $projet['owner_telephone']), 1, 1);
            }
        }
        
        $pdf->Ln(15);
        
        // Footer Kernel
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(41, 128, 185);
        $pdf->Cell(0, 10, 'A PROPOS DE KERNEL', 0, 1);
        $pdf->Ln(3);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->MultiCell(0, 5, utf8_decode('Kernel est la premiere plateforme tunisienne dediee a l\'innovation et au financement participatif de projets technologiques.'));
        
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 6, utf8_decode('Document genere automatiquement le ' . date('d/m/Y a H:i')), 0, 1, 'C');
    }
    
    /**
     * Ajoute un bloc d'actualité
     */
    private function addActualiteBlock($pdf, $actualite, $numero)
    {
        // Titre de l'actualité
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 8, $numero . '.', 0, 0);
        $pdf->Cell(0, 8, utf8_decode($actualite['titre']), 0, 1);
        
        // Date et type
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(10, 6, '', 0, 0);
        $pdf->Cell(0, 6, utf8_decode(date('d/m/Y a H:i', strtotime($actualite['date_publication'])) . ' - ' . ucfirst($actualite['type'])), 0, 1);
        
        // Contenu
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->SetX(30);
        $pdf->MultiCell(0, 5, utf8_decode($actualite['contenu']));
        $pdf->SetTextColor(0, 0, 0);
        
        // Ligne de séparation
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Line(20, $pdf->GetY() + 3, 190, $pdf->GetY() + 3);
    }
    
    /**
     * Ajoute une barre de progression
     */
    private function addProgressBar($pdf, $current, $target)
    {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'PROGRESSION DU FINANCEMENT', 0, 1);
        $pdf->Ln(3);
        
        $progress = $target > 0 ? min(100, ($current / $target) * 100) : 0;
        
        // Barre de progression
        $barWidth = 150;
        $barHeight = 15;
        $filledWidth = ($progress / 100) * $barWidth;
        
        // Fond
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
        
        $pdf->SetY($pdf->GetY() + $barHeight + 5);
        
        // Texte
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, utf8_decode('Finance: ' . number_format($current, 0, ',', ' ') . ' TND sur ' . number_format($target, 0, ',', ' ') . ' TND (' . round($progress, 1) . '%)'), 0, 1, 'C');
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
     * Génère le nom du fichier
     */
    private function generateFilename($projectData)
    {
        $titre = $projectData['projet']['titre'];
        $titre = preg_replace('/[^a-zA-Z0-9\s]/', '', $titre);
        $titre = preg_replace('/\s+/', '_', trim($titre));
        $titre = substr($titre, 0, 30);
        
        return 'Kernel_Projet_' . $titre . '_' . date('Y-m-d') . '.pdf';
    }
}

/**
 * Classe FPDF personnalisée avec en-tête et pied de page
 */
class KernelPDF extends FPDF
{
    function Header()
    {
        // Logo ou titre Kernel
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(41, 128, 185);
        $this->Cell(0, 10, 'KERNEL PLATFORM', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, utf8_decode('Plateforme d\'Innovation Tunisienne'), 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' - Kernel Platform - www.kernel.tn', 0, 0, 'C');
    }
}
?>