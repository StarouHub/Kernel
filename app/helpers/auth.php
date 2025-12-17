<?php
// Authentication helper functions

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default role to 'user' if not set
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'user';
}

// Set default user_id if not set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Default user ID
}

// Set default mode to 'user' if not set
if (!isset($_SESSION['mode'])) {
    $_SESSION['mode'] = 'user';
}

/**
 * Get current user ID
 * @return int
 */
function getUserId() {
    return $_SESSION['user_id'] ?? 1;
}

/**
 * Get current user role
 * @return string 'admin' or 'user'
 */
function getRole() {
    return $_SESSION['role'] ?? 'user';
}

/**
 * Check if current user is admin
 * @return bool
 */
function isAdmin() {
    return getRole() === 'admin';
}

/**
 * Check if current user is regular user
 * @return bool
 */
function isUser() {
    return getRole() === 'user';
}

/**
 * Require admin role, redirect if not admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = "Accès refusé. Cette action nécessite les droits administrateur.";
        header('Location: index.php?controller=sujet&action=index');
        exit;
    }
}

/**
 * Check if user can edit/delete sujet
 * @param int $sujet_user_id
 * @return bool
 */
function canEditSujet($sujet_user_id) {
    if (isAdmin()) {
        return true;
    }
    return ($sujet_user_id == getUserId());
}

/**
 * Check if user can edit/delete reponse
 * @param int $reponse_user_id
 * @return bool
 */
function canEditReponse($reponse_user_id) {
    if (isAdmin()) {
        return true;
    }
    return ($reponse_user_id == getUserId());
}

/**
 * Get current mode
 * @return string 'admin' or 'user'
 */
function getMode() {
    return $_SESSION['mode'] ?? 'user';
}

/**
 * Switch role (for testing)
 */
function switchRole() {
    if (isset($_GET['switch_role'])) {
        $_SESSION['role'] = ($_SESSION['role'] === 'admin') ? 'user' : 'admin';
        
        // Remove switch_role parameter from URL
        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/[?&]switch_role=1(&|$)/', '', $url);
        $url = rtrim($url, '?&');
        
        // If URL becomes empty, redirect to index
        if (empty($url) || $url === '/') {
            $url = 'index.php?controller=sujet&action=index';
        }
        
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Handle mode switching
 */
function handleModeSwitch() {
    if (isset($_GET['set_mode']) && isAdmin()) {
        $mode = $_GET['set_mode'];
        if (in_array($mode, ['admin', 'user'])) {
            $_SESSION['mode'] = $mode;
        }
    }
}
?>
