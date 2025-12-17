<?php
class AIController {
    public function analyzeReclamation($titre, $description) {
        // Simulation d'API OpenAI (dans un vrai projet, utiliser l'API réelle)
        $prompt = "Analyse cette réclamation et suggère:\n";
        $prompt .= "1. Le type le plus approprié (bug, technique, contenu, suggestion, autre)\n";
        $prompt .= "2. La priorité (urgente, haute, normale, basse)\n";
        $prompt .= "3. Une reformulation professionnelle\n";
        $prompt .= "4. Les pièces jointes recommandées\n\n";
        $prompt .= "Titre: $titre\nDescription: $description";
        
        // Simulation de réponse
        $response = [
            'type' => $this->detectType($titre, $description),
            'priorite' => $this->detectPriority($titre, $description),
            'titre_reformule' => $this->reformulateTitle($titre),
            'description_reformulee' => $this->reformulateDescription($description),
            'suggestions' => $this->suggestAttachments($titre, $description),
            'confidence' => 0.85
        ];
        
        return ['success' => true, 'analysis' => $response];
    }
    
    private function detectType($titre, $description) {
        $text = strtolower($titre . ' ' . $description);
        
        if (preg_match('/bug|erreur|plant|crash|ne marche pas/', $text)) return 'bug';
        if (preg_match('/technique|connexion|lent|performance/', $text)) return 'technique';
        if (preg_match('/suggestion|amélioration|idée|fonctionnalité/', $text)) return 'suggestion';
        if (preg_match('/contenu|texte|orthographe|faute/', $text)) return 'contenu';
        
        return 'autre';
    }
    
    private function detectPriority($titre, $description) {
        $text = strtolower($titre . ' ' . $description);
        
        if (preg_match('/urgent|bloquant|critique|important/', $text)) return 'urgente';
        if (preg_match('/haute|prioritaire|sérieux/', $text)) return 'haute';
        if (preg_match('/mineur|petit|faible/', $text)) return 'basse';
        
        return 'normale';
    }
    
    private function reformulateTitle($titre) {
        $titre = trim($titre);
        if (!preg_match('/^[A-Z]/', $titre)) {
            $titre = ucfirst($titre);
        }
        return $titre;
    }
    
    private function reformulateDescription($description) {
        return ucfirst(trim($description));
    }
    
    private function suggestAttachments($titre, $description) {
        $text = strtolower($titre . ' ' . $description);
        $suggestions = [];
        
        if (preg_match('/écran|capture|screenshot/', $text)) {
            $suggestions[] = 'capture d\'écran';
        }
        if (preg_match('/facture|commande|achat/', $text)) {
            $suggestions[] = 'numéro de facture';
        }
        if (preg_match('/log|erreur|trace/', $text)) {
            $suggestions[] = 'fichier log';
        }
        if (preg_match('/vidéo|enregistrement/', $text)) {
            $suggestions[] = 'vidéo de démonstration';
        }
        
        return $suggestions;
    }
}
?>