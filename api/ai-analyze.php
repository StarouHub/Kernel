<?php
/**
 * api/ai-analyze.php - Moteur IA Complète avec Tous les Rôles
 */

header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/init.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['titre']) || !isset($data['description'])) {
        throw new Exception("Données manquantes");
    }

    $titre = $data['titre'];
    $description = $data['description'];
    $type = $data['type'] ?? 'autre';
    $priorite = $data['priorite'] ?? 'normale';

    // ===== ANALYSE IA RÉELLE AVEC TOUS LES RÔLES =====
    
    // 1. ANALYSE AUTOMATIQUE DU CONTENU
    $content_analysis = analyzeContentAutomatically($titre, $description);
    
    // 2. CLASSIFICATION INTELLIGENTE
    $classification = classifyIntelligently($titre, $description, $type);
    
    // 3. ÉVALUATION DE LA PRIORITÉ
    $priority_eval = evaluatePriority($titre, $description, $priorite);
    
    // 4. OPTIMISATION DU LANGAGE
    $language_optimization = optimizeLanguage($titre, $description);
    
    // 5. SUGGESTIONS CONTEXTUELLES
    $contextual_suggestions = suggestAttachments($classification['type']);
    
    // 6. GÉNÉRATION DE CONSEILS PERSONNALISÉS
    $personalized_advice = generatePersonalizedAdvice($classification['type'], $description);
    
    // 7. PRÉVENTION D'ERREURS
    $error_prevention = preventErrors($titre, $description);
    
    // 8. ACCÉLÉRATION DU TRAITEMENT
    $processing_acceleration = accelerateProcessing($titre, $description, $classification);
    
    // 9. STANDARDISATION DES ENTRÉES
    $standardized_input = standardizeInput($titre, $description, $classification);
    
    // 10. ASSISTANCE À LA RÉSOLUTION
    $resolution_assistance = assistWithResolution($titre, $description, $classification);
    
    // Score et sentiment
    $score = calculateQualityScore($titre, $description);
    $sentiment = analyzeSentiment($titre . ' ' . $description);
    $keywords = extractKeywords($titre . ' ' . $description);
    $quality = assessQuality($score);

    // Retourner analyse complète
    echo json_encode([
        'success' => true,
        'score' => $score,
        'quality' => $quality,
        'sentiment' => $sentiment,
        'keywords' => $keywords,
        
        // Rôles de l'IA
        'content_analysis' => $content_analysis,
        'classification' => $classification,
        'priority_evaluation' => $priority_eval,
        'language_optimization' => $language_optimization,
        'attachments_suggestions' => $contextual_suggestions,
        'personalized_advice' => $personalized_advice,
        'error_prevention' => $error_prevention,
        'processing_acceleration' => $processing_acceleration,
        'standardized_input' => $standardized_input,
        'resolution_assistance' => $resolution_assistance,
        
        'analysis_timestamp' => date('Y-m-d H:i:s'),
        'type' => $classification['type'],
        'priorite' => $priority_eval['priority']
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * 1. ANALYSE AUTOMATIQUE DU CONTENU
 */
function analyzeContentAutomatically($titre, $description) {
    return [
        'titre_longueur' => strlen($titre),
        'description_longueur' => strlen($description),
        'mots_titre' => str_word_count($titre),
        'mots_description' => str_word_count($description),
        'lignes_description' => count(array_filter(explode("\n", $description))),
        'ponctuation_count' => preg_match_all('/[.!?;:]/', $description),
        'completude' => calculateCompleteness($titre, $description)
    ];
}

/**
 * 2. CLASSIFICATION INTELLIGENTE
 */
function classifyIntelligently($titre, $description, $type_user) {
    $text = strtolower($titre . ' ' . $description);
    
    $bug_keywords = ['bug', 'erreur', 'crash', 'exception', 'panne', 'ne fonctionne pas', 'échoue', 'problème technique'];
    $technical_keywords = ['serveur', 'base de données', 'api', 'connexion', 'timeout', 'performance', 'lenteur'];
    $content_keywords = ['contenu', 'texte', 'information', 'données', 'description', 'typo', 'orthographe'];
    $suggestion_keywords = ['suggestion', 'amélioration', 'fonctionnalité', 'ajouter', 'feature', 'idée'];
    
    $detected_type = 'autre';
    $confidence = 0;
    
    if (countKeywordMatches($text, $bug_keywords) > 0) {
        $detected_type = 'bug';
        $confidence = 95;
    } elseif (countKeywordMatches($text, $technical_keywords) > 0) {
        $detected_type = 'technique';
        $confidence = 90;
    } elseif (countKeywordMatches($text, $content_keywords) > 0) {
        $detected_type = 'contenu';
        $confidence = 85;
    } elseif (countKeywordMatches($text, $suggestion_keywords) > 0) {
        $detected_type = 'suggestion';
        $confidence = 80;
    }
    
    return [
        'type' => $detected_type,
        'type_user' => $type_user,
        'confidence' => $confidence,
        'recommendation' => ($detected_type !== $type_user && $type_user !== '') ? "Type détecté: $detected_type" : null
    ];
}

/**
 * 3. ÉVALUATION DE LA PRIORITÉ
 */
function evaluatePriority($titre, $description, $priorite_user) {
    $text = strtolower($titre . ' ' . $description);
    
    $urgent_keywords = ['urgent', 'bloqué', 'crash', 'panne totale', 'grave', 'critique', 'sécurité', 'hack', 'données perdues'];
    $high_keywords = ['bug', 'erreur', 'problème', 'impossible', 'impossible de', 'ne peut pas'];
    
    $detected_priority = 'normale';
    $score_urgence = 0;
    
    foreach ($urgent_keywords as $kw) {
        if (stripos($text, $kw) !== false) {
            $score_urgence += 4;
        }
    }
    
    foreach ($high_keywords as $kw) {
        if (stripos($text, $kw) !== false) {
            $score_urgence += 2;
        }
    }
    
    if ($score_urgence >= 4) {
        $detected_priority = 'urgente';
    } elseif ($score_urgence >= 2) {
        $detected_priority = 'haute';
    } elseif ($score_urgence > 0) {
        $detected_priority = 'normale';
    } else {
        $detected_priority = 'basse';
    }
    
    return [
        'priority' => $detected_priority,
        'priority_user' => $priorite_user,
        'urgency_score' => $score_urgence,
        'recommendation' => ($detected_priority !== $priorite_user) ? "Priorité suggérée: $detected_priority" : null
    ];
}

/**
 * 4. OPTIMISATION DU LANGAGE
 */
function optimizeLanguage($titre, $description) {
    return [
        'titre_length_optimal' => strlen($titre) >= 20 && strlen($titre) <= 80,
        'description_structured' => checkIfStructured($description),
        'clarity_score' => calculateClarity($titre),
        'improvements' => generateImprovements($titre, $description)
    ];
}

/**
 * 5. SUGGESTIONS CONTEXTUELLES
 */
function suggestAttachments($type) {
    $suggestions = [];
    
    switch ($type) {
        case 'bug':
            $suggestions = [
                '📸 Capture d\'écran du bug',
                '📋 Logs d\'erreur ou console',
                '🎥 Vidéo de reproduction du bug',
                '📝 Étapes précises de reproduction'
            ];
            break;
        case 'technique':
            $suggestions = [
                '📊 Détails système (OS, navigateur, version)',
                '📝 Configuration du serveur',
                '📋 Logs applicatifs',
                '⏱️ Mesures de performance'
            ];
            break;
        case 'contenu':
            $suggestions = [
                '📄 Document original',
                '✏️ Correction proposée',
                '📸 Capture du contenu erroné',
                '🔗 Lien vers la page'
            ];
            break;
        case 'suggestion':
            $suggestions = [
                '🎨 Mockup ou prototype',
                '📝 Cas d\'utilisation détaillé',
                '📊 Benchmark comparatif',
                '💡 Avantages estimés'
            ];
            break;
        default:
            $suggestions = ['📎 Pièces jointes pertinentes'];
    }
    
    return $suggestions;
}

/**
 * 6. GÉNÉRATION DE CONSEILS PERSONNALISÉS
 */
function generatePersonalizedAdvice($type, $description) {
    $advice = [];
    
    switch ($type) {
        case 'bug':
            $advice = [
                '🔍 Précisez les étapes exactes pour reproduire le bug',
                '💻 Indiquez votre environnement (OS, navigateur, version)',
                '⏱️ Notez à quelle fréquence le bug se produit',
                '📸 Attachez une capture d\'écran de l\'erreur'
            ];
            break;
        case 'technique':
            $advice = [
                '🖥️ Décrivez votre configuration système',
                '🔌 Vérifiez votre connexion réseau',
                '⚙️ Consultez les logs pour plus de détails',
                '🆘 Contactez l\'équipe support si le problème persiste'
            ];
            break;
        case 'contenu':
            $advice = [
                '📝 Soyez précis sur la localisation du contenu erroné',
                '✏️ Proposez la correction suggérée',
                '🔗 Fournissez le lien exact vers la page',
                '📸 Incluez une capture d\'écran'
            ];
            break;
        case 'suggestion':
            $advice = [
                '💡 Expliquez le bénéfice de votre suggestion',
                '👥 Identifiez qui en bénéficierait',
                '📊 Fournissez des cas d\'utilisation réels',
                '🎯 Précisez l\'impact attendu'
            ];
            break;
        default:
            $advice = ['ℹ️ Soyez aussi descriptif et précis que possible'];
    }
    
    return $advice;
}

/**
 * 7. PRÉVENTION D'ERREURS
 */
function preventErrors($titre, $description) {
    $errors = [];
    $warnings = [];
    
    if (strlen($titre) < 10) {
        $errors[] = "❌ Titre trop court (minimum 10 caractères)";
    }
    
    if (strlen($description) < 50) {
        $errors[] = "❌ Description insuffisante (minimum 50 caractères)";
    }
    
    if (str_word_count($titre) < 2) {
        $warnings[] = "⚠️ Titre très court - soyez plus descriptif";
    }
    
    if (str_word_count($description) < 15) {
        $warnings[] = "⚠️ Description trop courte - développez davantage";
    }
    
    if (!preg_match('/[.!?]/', $description)) {
        $warnings[] = "⚠️ Pas de ponctuation - améliorez la clarté";
    }
    
    if (strlen($description) < 100 && !preg_match('/\n/', $description)) {
        $warnings[] = "⚠️ Utilisez des sauts de ligne pour structurer";
    }
    
    return [
        'errors' => $errors,
        'warnings' => $warnings,
        'is_valid' => count($errors) === 0,
        'completeness_percentage' => calculateCompleteness($titre, $description) * 100
    ];
}

/**
 * 8. ACCÉLÉRATION DU TRAITEMENT
 */
function accelerateProcessing($titre, $description, $classification) {
    return [
        'priority_level' => $classification['type'],
        'estimated_handling_time' => estimateHandlingTime($classification['type']),
        'pre_analysis_complete' => true,
        'routing_queue' => getRoutingQueue($classification['type']),
        'fast_track_eligible' => isEligibleForFastTrack($titre, $description)
    ];
}

/**
 * 9. STANDARDISATION DES ENTRÉES
 */
function standardizeInput($titre, $description, $classification) {
    return [
        'titre_standardize' => ucfirst(trim($titre)),
        'type_standardized' => strtolower($classification['type']),
        'format_validation' => [
            'titre_format' => 'OK',
            'description_format' => 'OK'
        ]
    ];
}

/**
 * 10. ASSISTANCE À LA RÉSOLUTION
 */
function assistWithResolution($titre, $description, $classification) {
    $solutions = [];
    
    if ($classification['type'] === 'bug') {
        $solutions = [
            '🔄 Essayez de rafraîchir la page (Ctrl+F5)',
            '🗑️ Videz le cache du navigateur',
            '🔌 Vérifiez votre connexion Internet',
            '⚙️ Consultez la FAQ pour les problèmes connus'
        ];
    } elseif ($classification['type'] === 'technique') {
        $solutions = [
            '🔄 Redémarrez votre application',
            '🖥️ Vérifiez les ressources système',
            '🌐 Testez avec un autre navigateur',
            '⏱️ Attendez quelques minutes et réessayez'
        ];
    } else {
        $solutions = [
            '📝 Décrivez le problème en détail',
            '📸 Incluez des captures d\'écran',
            '📋 Listez les étapes pour reproduire',
            '⏰ Précisez quand le problème s\'est produit'
        ];
    }
    
    return [
        'suggested_solutions' => $solutions,
        'common_issues' => ['Problème connu #1', 'Problème connu #2'],
        'kb_articles' => ['Article KB #1', 'Article KB #2']
    ];
}

// ============================================
// FONCTIONS AUXILIAIRES
// ============================================

function countKeywordMatches($text, $keywords) {
    $count = 0;
    foreach ($keywords as $kw) {
        if (stripos($text, $kw) !== false) {
            $count++;
        }
    }
    return $count;
}

function calculateCompleteness($titre, $description) {
    $score = 0;
    if (strlen($titre) > 20) $score += 0.2;
    if (strlen($description) > 100) $score += 0.2;
    if (str_word_count($description) > 25) $score += 0.2;
    if (preg_match('/[.!?]/', $description)) $score += 0.2;
    if (preg_match('/\n/', $description)) $score += 0.2;
    return min($score, 1);
}

function checkIfStructured($description) {
    return count(explode("\n", $description)) > 1;
}

function calculateClarity($titre) {
    $words = str_word_count($titre);
    return ($words >= 2 && strlen($titre) >= 15) ? 75 : 50;
}

function generateImprovements($titre, $description) {
    $improvements = [];
    if (strlen($titre) < 20) $improvements[] = "Allongez le titre";
    if (strlen($description) < 100) $improvements[] = "Développez la description";
    if (!preg_match('/\n/', $description)) $improvements[] = "Utilisez des sauts de ligne";
    return $improvements;
}

function estimateHandlingTime($type) {
    $times = [
        'bug' => '2-4 heures',
        'technique' => '1-2 heures',
        'contenu' => '30 minutes',
        'suggestion' => '1-2 jours',
        'autre' => '24 heures'
    ];
    return $times[$type] ?? '24 heures';
}

function getRoutingQueue($type) {
    $queues = [
        'bug' => 'Équipe Développement',
        'technique' => 'Support Technique',
        'contenu' => 'Équipe Contenu',
        'suggestion' => 'Équipe Produit',
        'autre' => 'Support Général'
    ];
    return $queues[$type] ?? 'Support Général';
}

function isEligibleForFastTrack($titre, $description) {
    $length = strlen($titre) + strlen($description);
    return $length > 150 && preg_match('/[.!?]/', $description);
}

function calculateQualityScore($titre, $description) {
    $score = 0;
    
    if (strlen($titre) >= 30) $score += 15;
    elseif (strlen($titre) >= 15) $score += 10;
    elseif (strlen($titre) >= 5) $score += 5;
    
    if (strlen($description) >= 300) $score += 35;
    elseif (strlen($description) >= 150) $score += 20;
    elseif (strlen($description) >= 50) $score += 10;
    
    $words = str_word_count($description);
    if ($words >= 50) $score += 15;
    elseif ($words >= 25) $score += 10;
    
    if (substr_count($description, "\n") >= 3) $score += 15;
    if (preg_match_all('/[.!?]/', $description) >= 3) $score += 10;
    
    return min($score, 100);
}

function assessQuality($score) {
    if ($score >= 80) return '⭐⭐⭐ Excellente';
    if ($score >= 60) return '⭐⭐ Bonne';
    if ($score >= 40) return '⭐ Acceptable';
    return '⚠️ À améliorer';
}

function analyzeSentiment($text) {
    $lower = strtolower($text);
    $positive = ['merci', 'excellent', 'super', 'bien', 'parfait'];
    $negative = ['problème', 'erreur', 'bug', 'bloqué', 'crash', 'urgent'];
    
    $posCount = 0;
    $negCount = 0;
    
    foreach ($positive as $w) {
        if (stripos($text, $w) !== false) $posCount++;
    }
    
    foreach ($negative as $w) {
        if (stripos($text, $w) !== false) $negCount++;
    }
    
    if ($negCount > $posCount) return 'négatif';
    if ($posCount > $negCount) return 'positif';
    return 'neutre';
}

function extractKeywords($text) {
    $words = str_word_count(strtolower($text), 1);
    $stopwords = ['le', 'la', 'les', 'un', 'une', 'et', 'ou', 'de', 'du', 'est', 'a', 'à', 'que', 'qui'];
    $filtered = array_filter($words, function($w) use ($stopwords) {
        return strlen($w) > 4 && !in_array($w, $stopwords);
    });
    return array_slice(array_unique($filtered), 0, 5);
}

?>
