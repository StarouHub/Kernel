<?php
// View helper functions for formatting, badges, truncate, time-ago

/**
 * Format time ago in French
 */
function timeAgo($datetime) {
    $date = new DateTime($datetime);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'à l\'instant';
            } else {
                return 'il y a ' . $diff->i . ' min';
            }
        } else {
            return 'il y a ' . $diff->h . ' h';
        }
    } else if ($diff->days == 1) {
        return 'hier';
    } else {
        return 'il y a ' . $diff->days . ' jours';
    }
}

/**
 * Truncate text with ellipsis
 */
function truncate($text, $length = 200) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Extract hashtags from text
 */
function extractHashtags($text) {
    preg_match_all('/#(\w+)/', $text, $matches);
    return array_slice($matches[0], 0, 3);
}
?>

