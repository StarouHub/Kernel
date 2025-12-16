# Forum MVC Application

Une application PHP 7.4 MVC simple pour gérer un forum avec des catégories, sujets et réponses.

## Structure du Projet

```
/app
    /controllers      - Contrôleurs MVC
    /models          - Modèles de données
    /views           - Vues (templates)
        /layout      - Layout commun (header, footer)
/config             - Configuration (base de données)
/public             - Point d'entrée public
    /assets
        /css        - Fichiers CSS
        /js         - Fichiers JavaScript
```

## Installation

1. **Créer la base de données MySQL:**

```sql
CREATE DATABASE forum_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE forum_db;

-- Table categorie
CREATE TABLE categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table sujet
CREATE TABLE sujet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    categorie_id INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table reponse
CREATE TABLE reponse (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    sujet_id INT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sujet_id) REFERENCES sujet(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

2. **Configurer la base de données:**

Éditez le fichier `config/database.php` et modifiez les constantes selon votre configuration:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'forum_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

3. **Configurer le serveur web:**

- Point de document root vers le dossier `/public`
- Assurez-vous que mod_rewrite est activé (pour Apache)
- Pour XAMPP, le document root devrait pointer vers `C:\xampp\htdocs\vv\public`

## Utilisation

### Routes disponibles:

- **Catégories:**
  - `index.php?controller=categorie&action=index` - Liste des catégories
  - `index.php?controller=categorie&action=create` - Créer une catégorie
  - `index.php?controller=categorie&action=edit&id=1` - Modifier une catégorie
  - `index.php?controller=categorie&action=delete&id=1` - Supprimer une catégorie

- **Sujets:**
  - `index.php?controller=sujet&action=index` - Liste des sujets
  - `index.php?controller=sujet&action=create` - Créer un sujet
  - `index.php?controller=sujet&action=show&id=1` - Voir un sujet
  - `index.php?controller=sujet&action=edit&id=1` - Modifier un sujet
  - `index.php?controller=sujet&action=delete&id=1` - Supprimer un sujet

- **Réponses:**
  - `index.php?controller=reponse&action=index` - Liste des réponses
  - `index.php?controller=reponse&action=create&sujet_id=1` - Créer une réponse
  - `index.php?controller=reponse&action=edit&id=1` - Modifier une réponse
  - `index.php?controller=reponse&action=delete&id=1` - Supprimer une réponse

## Fonctionnalités

- ✅ CRUD complet pour toutes les entités
- ✅ Validation côté client (JavaScript)
- ✅ Validation côté serveur (PHP)
- ✅ Protection XSS (htmlspecialchars)
- ✅ Protection SQL Injection (PDO prepared statements)
- ✅ Design moderne avec CSS réutilisé
- ✅ Interface responsive

## Technologies utilisées

- PHP 7.4
- MySQL/MariaDB
- PDO
- HTML5/CSS3
- JavaScript (Vanilla)

## Notes

- Tous les champs requis sont validés avant soumission
- Les redirections se font après chaque action CRUD
- Les erreurs sont affichées de manière conviviale
- Le design utilise les fichiers CSS du projet précédent

