<?php
/**
 * Chatbot Service
 * Gère les réponses du chatbot d'assistance
 */

class ChatbotService {
    
    private $responses = [
        'lieu' => [
            'keywords' => ['lieu', 'où', 'adresse', 'localisation', 'endroit', 'se déroule', 'se passe'],
            'response' => 'Pour connaître le lieu d\'un événement, consultez la page de détails de l\'événement. Le lieu est indiqué dans les informations de l\'événement.'
        ],
        'inscription' => [
            'keywords' => ['inscrire', 'inscription', 'comment', 's\'inscrire', 'participer', 'rejoindre'],
            'response' => 'Pour vous inscrire à un événement, cliquez sur le bouton "S\'inscrire" sur la page de détails de l\'événement. Remplissez le formulaire avec vos informations (nom, prénom, email) et validez.'
        ],
        'date' => [
            'keywords' => ['date', 'quand', 'jour', 'horaire', 'heure'],
            'response' => 'La date de l\'événement est indiquée sur la page de détails. Vous pouvez également voir la date formatée dans la liste des événements.'
        ],
        'capacite' => [
            'keywords' => ['place', 'capacité', 'complet', 'disponible', 'limite'],
            'response' => 'La capacité d\'un événement indique le nombre maximum de participants. Si un événement est complet, vous pouvez vous inscrire sur la liste d\'attente.'
        ],
        'liste_attente' => [
            'keywords' => ['liste d\'attente', 'attente', 'file', 'complet'],
            'response' => 'Si un événement est complet, vous pouvez vous inscrire sur la liste d\'attente. Vous serez notifié par email si une place se libère.'
        ],
        'annulation' => [
            'keywords' => ['annuler', 'annulation', 'annulé', 'supprimer'],
            'response' => 'Pour annuler votre inscription, veuillez contacter l\'organisateur de l\'événement ou utiliser la fonctionnalité de gestion de votre inscription.'
        ],
        'contact' => [
            'keywords' => ['contact', 'aide', 'support', 'assistance', 'problème'],
            'response' => 'Pour toute question ou problème, vous pouvez contacter l\'organisateur de l\'événement via les informations disponibles sur la page de l\'événement.'
        ],
        'salut' => [
            'keywords' => ['bonjour', 'salut', 'bonsoir', 'hello', 'hi', 'coucou'],
            'response' => 'Bonjour ! Je suis là pour vous aider. Posez-moi vos questions sur les événements, les inscriptions, ou toute autre information.'
        ],
        'aide' => [
            'keywords' => ['aide', 'help', 'question', 'questions fréquentes', 'faq'],
            'response' => 'Je peux vous aider avec :\n- Le lieu des événements\n- Comment s\'inscrire\n- Les dates des événements\n- La capacité et les places disponibles\n- La liste d\'attente\n- L\'annulation d\'inscription\n\nPosez-moi votre question !'
        ]
    ];
    
    /**
     * Trouve la réponse appropriée selon la question
     * 
     * @param string $question
     * @return string
     */
    public function getResponse(string $question): string {
        $question = strtolower(trim($question));
        
        // Si la question est vide
        if (empty($question)) {
            return 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?';
        }
        
        // Chercher la meilleure correspondance
        $bestMatch = null;
        $maxMatches = 0;
        
        foreach ($this->responses as $key => $responseData) {
            $matches = 0;
            foreach ($responseData['keywords'] as $keyword) {
                if (strpos($question, $keyword) !== false) {
                    $matches++;
                }
            }
            
            if ($matches > $maxMatches) {
                $maxMatches = $matches;
                $bestMatch = $responseData['response'];
            }
        }
        
        // Si aucune correspondance trouvée
        if ($bestMatch === null || $maxMatches === 0) {
            return 'Je ne suis pas sûr de comprendre votre question. Essayez de poser une question sur :\n- Le lieu d\'un événement\n- Comment s\'inscrire\n- Les dates\n- La capacité\n- La liste d\'attente\n\nOu tapez "aide" pour voir toutes les options.';
        }
        
        return $bestMatch;
    }
    
    /**
     * Traite une requête du chatbot avec contexte d'événement
     * 
     * @param string $message
     * @param array $evenement
     * @return array
     */
    public function processMessageWithContext(string $message, array $evenement): array {
        $question = strtolower(trim($message));
        
        // Réponses contextuelles basées sur l'événement
        if (strpos($question, 'lieu') !== false || strpos($question, 'où') !== false || strpos($question, 'adresse') !== false) {
            $response = "L'événement \"" . htmlspecialchars($evenement['titre']) . "\" se déroule à : " . htmlspecialchars($evenement['lieu']) . ".";
        } elseif (strpos($question, 'date') !== false || strpos($question, 'quand') !== false) {
            require_once __DIR__ . '/../model/Evenement.php';
            $dateFormatted = Evenement::formatDateForDisplay($evenement['date']);
            $response = "L'événement \"" . htmlspecialchars($evenement['titre']) . "\" a lieu le : " . $dateFormatted . ".";
        } elseif (strpos($question, 'capacité') !== false || strpos($question, 'place') !== false || strpos($question, 'complet') !== false) {
            $response = "L'événement \"" . htmlspecialchars($evenement['titre']) . "\" a une capacité de " . $evenement['capacite'] . " places.";
        } else {
            $response = $this->getResponse($message);
        }
        
        return [
            'success' => true,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Traite une requête du chatbot
     * 
     * @param string $message
     * @return array
     */
    public function processMessage(string $message): array {
        $response = $this->getResponse($message);
        
        return [
            'success' => true,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
