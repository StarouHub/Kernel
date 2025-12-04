<?php
require_once __DIR__ . '/../helpers/auth.php';

class RoleController {
    
    /**
     * Switch role between admin and user
     * Redirects back to the same page
     */
    public function switchRole() {
        // Get current role
        $currentRole = getRole();
        
        // Toggle role
        $_SESSION['role'] = ($currentRole === 'admin') ? 'user' : 'admin';
        
        // Get redirect URL from session (stored before navigation) or referrer
        $redirectUrl = $_SESSION['redirect_after_role_switch'] ?? $_SERVER['HTTP_REFERER'] ?? null;
        
        // Clear the session variable
        unset($_SESSION['redirect_after_role_switch']);
        
        // If we have a referrer, extract the path and query
        if ($redirectUrl && strpos($redirectUrl, 'http') === 0) {
            $parsed = parse_url($redirectUrl);
            $redirectUrl = $parsed['path'] ?? 'index.php';
            if (isset($parsed['query'])) {
                // Remove role controller actions from query
                $query = $parsed['query'];
                $query = preg_replace('/[?&]controller=role(&|$)/', '', $query);
                $query = preg_replace('/[?&]action=switchRole(&|$)/', '', $query);
                $query = rtrim($query, '?&');
                if (!empty($query)) {
                    $redirectUrl .= '?' . $query;
                }
            }
        }
        
        // If no valid redirect URL, default to forum index
        if (empty($redirectUrl) || $redirectUrl === '/' || strpos($redirectUrl, 'role') !== false) {
            $redirectUrl = 'index.php?controller=sujet&action=index';
        }
        
        // Redirect back
        header('Location: ' . $redirectUrl);
        exit;
    }
}

