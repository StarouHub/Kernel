<?php
require_once __DIR__ . '/../helpers/auth.php';

class AdminController {
    
    public function __construct() {
        requireAdmin();
    }
    
    public function dashboard() {
        require_once __DIR__ . '/../models/Sujet.php';
        require_once __DIR__ . '/../models/Reponse.php';
        require_once __DIR__ . '/../models/Categorie.php';
        
        $sujetModel = new Sujet();
        $reponseModel = new Reponse();
        $categorieModel = new Categorie();
        
        $totalSujets = count($sujetModel->getAll());
        $totalReponses = count($reponseModel->getAll());
        $totalCategories = count($categorieModel->getAll());
        
        $pageTitle = 'Tableau de bord Admin';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function categories() {
        require_once __DIR__ . '/../models/Categorie.php';
        $categorieModel = new Categorie();
        $categories = $categorieModel->getAll();
        
        $pageTitle = 'Gestion des Catégories';
        require_once __DIR__ . '/../views/admin/categories.php';
    }
    
    public function sujets() {
        require_once __DIR__ . '/../models/Sujet.php';
        require_once __DIR__ . '/../models/Categorie.php';
        
        $sujetModel = new Sujet();
        $categorieModel = new Categorie();
        
        $sujets = $sujetModel->getAll();
        $categories = $categorieModel->getAll();
        
        // Create a map for quick category lookup
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat;
        }
        
        $pageTitle = 'Gestion des Sujets';
        require_once __DIR__ . '/../views/admin/sujets.php';
    }
    
    public function reponses() {
        require_once __DIR__ . '/../models/Reponse.php';
        require_once __DIR__ . '/../models/Sujet.php';
        
        $reponseModel = new Reponse();
        $sujetModel = new Sujet();
        
        $reponses = $reponseModel->getAll();
        
        $pageTitle = 'Gestion des Réponses';
        require_once __DIR__ . '/../views/admin/reponses.php';
    }
}
