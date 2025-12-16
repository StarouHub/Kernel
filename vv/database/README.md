# Base de Données - Kernel Forum

Ce dossier contient tous les fichiers liés à la base de données du projet.

## Fichiers

### `schema.sql`
Le schéma complet de la base de données incluant :
- Création de la base de données
- Toutes les tables (categories, sujets, reponses, users)
- Index pour optimiser les performances
- Clés étrangères avec contraintes CASCADE
- Vues pour les requêtes complexes
- Procédures stockées
- Triggers automatiques
- Données d'exemple

### `install.php`
Script PHP pour installer automatiquement le schéma de base de données.

## Installation

### Méthode 1 : Utiliser le script PHP (Recommandé)

1. Assurez-vous que votre configuration dans `config/database.php` est correcte
2. Exécutez le script depuis la ligne de commande :
   ```bash
   php database/install.php
   ```
   Ou via votre navigateur : `http://localhost/vv16f/vv/database/install.php`

### Méthode 2 : Utiliser phpMyAdmin ou MySQL CLI

1. Ouvrez phpMyAdmin ou connectez-vous à MySQL
2. Importez le fichier `schema.sql` directement

### Méthode 3 : Ligne de commande MySQL

```bash
mysql -u root -p < database/schema.sql
```

## Structure de la Base de Données

### Tables Principales

#### `categories`
- `id` : Identifiant unique
- `name` : Nom de la catégorie
- `color` : Couleur d'affichage (hex)
- `description` : Description de la catégorie
- `created_at` : Date de création
- `updated_at` : Date de mise à jour

#### `sujets`
- `id` : Identifiant unique
- `titre` : Titre du sujet
- `contenu` : Contenu du sujet
- `categorie_id` : Référence à la catégorie
- `user_id` : Référence à l'utilisateur (optionnel)
- `views` : Nombre de vues
- `is_pinned` : Sujet épinglé
- `is_locked` : Sujet verrouillé
- `date_creation` : Date de création
- `date_modification` : Date de modification

#### `reponses`
- `id` : Identifiant unique
- `contenu` : Contenu de la réponse
- `sujet_id` : Référence au sujet
- `user_id` : Référence à l'utilisateur (optionnel)
- `likes` : Nombre de likes
- `is_edited` : Réponse modifiée
- `date` : Date de création
- `date_modification` : Date de modification

#### `users` (Optionnel - pour futures fonctionnalités)
- `id` : Identifiant unique
- `username` : Nom d'utilisateur
- `email` : Email
- `password` : Mot de passe hashé
- `role` : Rôle (user, moderator, admin)
- `avatar` : URL de l'avatar
- `created_at` : Date de création
- `updated_at` : Date de mise à jour
- `last_login` : Dernière connexion

## Vues Disponibles

### `sujet_with_stats`
Vue qui combine les sujets avec leurs statistiques (nombre de réponses, dernière réponse, etc.)

### `categorie_with_stats`
Vue qui combine les catégories avec leurs statistiques (nombre de sujets, nombre de réponses, etc.)

## Procédures Stockées

### `sp_get_sujet_full(sujet_id)`
Récupère un sujet complet avec toutes ses informations liées.

### `sp_get_recent_sujets(limit_count)`
Récupère les sujets les plus récents.

## Configuration

Assurez-vous que les constantes dans `config/database.php` correspondent à votre environnement :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'kernel_forum');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', 3306);
```

## Notes

- Toutes les tables utilisent le moteur InnoDB pour supporter les transactions et les clés étrangères
- Le charset utf8mb4 est utilisé pour supporter tous les caractères Unicode (emojis inclus)
- Les clés étrangères sont configurées avec CASCADE pour la suppression automatique
- Des index sont créés sur les colonnes fréquemment utilisées dans les requêtes
- Des index FULLTEXT sont créés pour la recherche de texte

