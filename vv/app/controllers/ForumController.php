<?php
require_once __DIR__ . '/../helpers/auth.php';

class ForumController {
    
    public function index() {
        // Mode is already set by handleModeSwitch() in index.php
        // Redirect to sujet index (forum main page)
        header('Location: index.php?controller=sujet&action=index');
        exit;
    }
}

