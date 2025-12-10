<?php
/**
 * Service Chatbot Simple
 * Répond aux questions sur les projets et actualités basé sur les données de la base
 */

include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../controller/projetcontroller.php');
include_once(__DIR__ . '/../controller/actualitecontroller.php');

class ChatbotService
{
    private $projetController;
    private $actualiteController;
    
    public function __construct()
    {
        $this->projetController = new ProjetController();
        $this->actualiteController = new ActualiteController();
    }
    
    /**
     * Traite une question de l'utilisateur
     */
    public function processQuestion($question)
    {
        $question = strtolower(trim($question));
        
        // Détection des intentions
        if ($this->containsKeywords($question, ['combien', 'nombre', 'projets'])) {
            return $this->countProjets();
        }
        
        if ($this->containsKeywords($question, ['dernier', 'récent', 'nouveau', 'projet'])) {
            return $this->getLatestProjet();
        }
        
        if ($this->containsKeywords($question, ['actualité', 'news', 'update'])) {
            return $this->getLatestActualites();
        }
        
        if ($this->containsKeywords($question, ['recherche', 'cherche', 'trouve']) && 
            $this->containsKeywords($question, ['projet'])) {
            return $this->searchProjet($question);
        }
        
        if ($this->containsKeywords($question, ['catégorie', 'categorie', 'type'])) {
            return $this->getCategories();
        }
        
        if ($this->containsKeywords($question, ['budget', 'financement', 'montant'])) {
            return $this->getBudgetInfo();
        }
        
        if ($this->containsKeywords($question, ['aide', 'help', 'commande'])) {
            return $this->getHelp();
        }
        
        // Réponse par défaut
        return [
            'success' => true,
            'message' => "Je n'ai pas compris votre question. Tapez 'aide' pour voir ce que je peux faire.",
            'type' => 'info'
        ];
    }
    
    /**
     * Vérifie si la question contient certains mots-clés
     */
    private function containsKeywords($text, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Compte le nombre de projets
     */
    private function countProjets()
    {
        $projets = $this->projetController->listProjets();
        $count = count($projets);
        
        return [
            'success' => true,
            'message' => "Il y a actuellement <strong>$count projet" . ($count > 1 ? 's' : '') . "</strong> sur la plateforme Kernel.",
            'type' => 'success',
            'data' => ['count' => $count]
        ];
    }
    
    /**
     * Récupère le dernier projet créé
     */
    private function getLatestProjet()
    {
        $projets = $this->projetController->listProjets();
        
        if (empty($projets)) {
            return [
                'success' => false,
                'message' => "Aucun projet n'est disponible pour le moment.",
                'type' => 'warning'
            ];
        }
        
        $latest = $projets[0];
        $message = "Le dernier projet est : <strong>" . htmlspecialchars($latest['titre']) . "</strong><br>";
        $message .= "📊 Budget requis : " . number_format($latest['budget_requis'], 0, ',', ' ') . " €<br>";
        $message .= "📈 Statut : " . ucfirst($latest['statut']);
        
        return [
            'success' => true,
            'message' => $message,
            'type' => 'success',
            'data' => $latest
        ];
    }
    
    /**
     * Récupère les dernières actualités
     */
    private function getLatestActualites()
    {
        $actualites = $this->actualiteController->listActualites();
        
        if (empty($actualites)) {
            return [
                'success' => false,
                'message' => "Aucune actualité n'est disponible.",
                'type' => 'warning'
            ];
        }
        
        $latest = array_slice($actualites, 0, 3);
        $message = "📰 <strong>Dernières actualités :</strong><br><br>";
        
        foreach ($latest as $actu) {
            $message .= "• <strong>" . htmlspecialchars($actu['titre']) . "</strong><br>";
            $message .= "  Projet : " . htmlspecialchars($actu['projet_titre']) . "<br>";
            $message .= "  Date : " . date('d/m/Y', strtotime($actu['date_publication'])) . "<br><br>";
        }
        
        return [
            'success' => true,
            'message' => $message,
            'type' => 'success',
            'data' => $latest
        ];
    }
    
    /**
     * Recherche un projet
     */
    private function searchProjet($question)
    {
        // Extraire les mots-clés de recherche
        $keywords = ['recherche', 'cherche', 'trouve', 'projet', 'sur', 'le', 'la', 'un', 'une'];
        $words = explode(' ', $question);
        $searchTerm = '';
        
        foreach ($words as $word) {
            if (!in_array($word, $keywords) && strlen($word) > 2) {
                $searchTerm = $word;
                break;
            }
        }
        
        if (empty($searchTerm)) {
            return [
                'success' => false,
                'message' => "Veuillez préciser ce que vous recherchez.",
                'type' => 'warning'
            ];
        }
        
        $projets = $this->projetController->listProjets();
        $results = [];
        
        foreach ($projets as $projet) {
            if (stripos($projet['titre'], $searchTerm) !== false || 
                stripos($projet['description'], $searchTerm) !== false) {
                $results[] = $projet;
            }
        }
        
        if (empty($results)) {
            return [
                'success' => false,
                'message' => "Aucun projet trouvé pour '<strong>$searchTerm</strong>'.",
                'type' => 'warning'
            ];
        }
        
        $message = "🔍 J'ai trouvé <strong>" . count($results) . " projet" . (count($results) > 1 ? 's' : '') . "</strong> :<br><br>";
        
        foreach (array_slice($results, 0, 3) as $projet) {
            $message .= "• <strong>" . htmlspecialchars($projet['titre']) . "</strong><br>";
            $message .= "  " . substr(htmlspecialchars($projet['description']), 0, 100) . "...<br><br>";
        }
        
        return [
            'success' => true,
            'message' => $message,
            'type' => 'success',
            'data' => $results
        ];
    }
    
    /**
     * Liste les catégories disponibles
     */
    private function getCategories()
    {
        $categories = ['AI', 'IoT', 'Blockchain', 'Web', 'Data', 'Security'];
        
        $message = "📂 <strong>Catégories disponibles :</strong><br><br>";
        $message .= "🤖 AI - Intelligence Artificielle<br>";
        $message .= "🔌 IoT - Internet des Objets<br>";
        $message .= "⛓️ Blockchain - Technologies décentralisées<br>";
        $message .= "💻 Web - Développement web et mobile<br>";
        $message .= "📊 Data - Data Science et Big Data<br>";
        $message .= "🔒 Security - Cybersécurité";
        
        return [
            'success' => true,
            'message' => $message,
            'type' => 'success',
            'data' => $categories
        ];
    }
    
    /**
     * Informations sur les budgets
     */
    private function getBudgetInfo()
    {
        $projets = $this->projetController->listProjets();
        
        if (empty($projets)) {
            return [
                'success' => false,
                'message' => "Aucune donnée disponible.",
                'type' => 'warning'
            ];
        }
        
        $totalBudget = 0;
        $totalActuel = 0;
        
        foreach ($projets as $projet) {
            $totalBudget += $projet['budget_requis'];
            $totalActuel += $projet['budget_actuel'];
        }
        
        $message = "💰 <strong>Informations financières :</strong><br><br>";
        $message .= "Budget total requis : <strong>" . number_format($totalBudget, 0, ',', ' ') . " €</strong><br>";
        $message .= "Montant collecté : <strong>" . number_format($totalActuel, 0, ',', ' ') . " €</strong><br>";
        $message .= "Taux de financement : <strong>" . round(($totalActuel / $totalBudget) * 100, 1) . "%</strong>";
        
        return [
            'success' => true,
            'message' => $message,
            'type' => 'success',
            'data' => [
                'total_requis' => $totalBudget,
                'total_actuel' => $totalActuel,
                'taux' => ($totalActuel / $totalBudget) * 100
            ]
        ];
    }
    
    /**
     * Affiche l'aide
     */
    private function getHelp()
    {
        $message = "🤖 <strong>Voici ce que je peux faire :</strong><br><br>";
        $message .= "• 'Combien de projets ?' - Nombre total de projets<br>";
        $message .= "• 'Dernier projet' - Affiche le projet le plus récent<br>";
        $message .= "• 'Actualités' - Dernières news des projets<br>";
        $message .= "• 'Recherche projet [mot-clé]' - Trouve un projet<br>";
        $message .= "• 'Catégories' - Liste des catégories<br>";
        $message .= "• 'Budget' - Informations financières<br><br>";
        $message .= "💡 <em>Posez-moi une question en langage naturel !</em>";
        
        return [
            'success' => true,
            'message' => $message,
            'type' => 'info'
        ];
    }
}
?>
