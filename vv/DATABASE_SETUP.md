# Configuration de la Base de Données - Guide Complet

## 📋 Résumé des Ajouts

Une base de code de base de données complète a été ajoutée à votre projet. Voici ce qui a été créé :

### ✅ Fichiers Créés

1. **`app/core/Model.php`** - Classe de base pour tous les modèles
   - Méthodes CRUD génériques (create, read, update, delete)
   - Support des transactions
   - Recherche et filtrage
   - Peut être étendue par vos modèles existants

2. **`app/core/Database.php`** - Classe d'abstraction de base de données
   - Pattern Singleton pour une seule connexion
   - Méthodes utilitaires (fetchAll, fetchOne, execute)
   - Gestion des transactions
   - Vérification de l'existence des tables

3. **`app/core/bootstrap.php`** - Fichier de démarrage
   - Charge automatiquement les classes core
   - Auto-loader pour les modèles
   - Configuration centralisée

4. **`config/database.php`** - Amélioré
   - Ajout du port de base de données
   - Meilleure gestion des erreurs
   - Fonctions utilitaires (testDBConnection, getDBInfo)
   - Support du mode DEBUG

5. **`database/schema.sql`** - Schéma complet de la base de données
   - Création de toutes les tables
   - Index pour les performances
   - Clés étrangères avec CASCADE
   - Vues pour requêtes complexes
   - Procédures stockées
   - Triggers automatiques
   - Données d'exemple

6. **`database/install.php`** - Script d'installation automatique
   - Installe le schéma en une commande
   - Gestion des erreurs
   - Messages informatifs

7. **`database/README.md`** - Documentation complète
   - Guide d'installation
   - Structure des tables
   - Exemples d'utilisation

## 🚀 Installation Rapide

### Option 1 : Script PHP (Recommandé)

```bash
cd vv
php database/install.php
```

### Option 2 : phpMyAdmin

1. Ouvrez phpMyAdmin
2. Sélectionnez votre base de données `kernel_forum`
3. Allez dans l'onglet "Importer"
4. Sélectionnez le fichier `database/schema.sql`
5. Cliquez sur "Exécuter"

### Option 3 : Ligne de commande MySQL

```bash
mysql -u root -p kernel_forum < vv/database/schema.sql
```

## 📊 Structure de la Base de Données

### Tables Principales

- **`categories`** - Catégories du forum
- **`sujets`** - Sujets de discussion
- **`reponses`** - Réponses aux sujets
- **`users`** - Utilisateurs (optionnel, pour futures fonctionnalités)

### Vues Disponibles

- **`sujet_with_stats`** - Sujets avec statistiques
- **`categorie_with_stats`** - Catégories avec statistiques

### Procédures Stockées

- **`sp_get_sujet_full(sujet_id)`** - Récupère un sujet complet
- **`sp_get_recent_sujets(limit_count)`** - Récupère les sujets récents

## 💡 Utilisation de la Classe Model

Vous pouvez maintenant étendre vos modèles avec la classe `Model` de base :

```php
<?php
require_once __DIR__ . '/../core/Model.php';

class Sujet extends Model {
    protected $table = 'sujets';
    protected $primaryKey = 'id';
    
    // Vos méthodes personnalisées
    public function getByCategorie($categorie_id) {
        return $this->findBy('categorie_id', $categorie_id);
    }
    
    // Les méthodes de base sont déjà disponibles :
    // - getAll()
    // - getById($id)
    // - create($data)
    // - update($id, $data)
    // - delete($id)
    // - count($conditions)
}
?>
```

## 🔧 Configuration

Vérifiez que votre fichier `config/database.php` contient les bonnes informations :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'kernel_forum');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', 3306);
```

## 📝 Notes Importantes

1. **Compatibilité** : Les modèles existants continuent de fonctionner. La nouvelle structure est optionnelle.

2. **Migration** : Vous pouvez progressivement migrer vos modèles pour étendre la classe `Model` si vous le souhaitez.

3. **Performance** : Des index ont été ajoutés sur les colonnes fréquemment utilisées pour améliorer les performances.

4. **Sécurité** : Toutes les requêtes utilisent des prepared statements pour prévenir les injections SQL.

5. **UTF-8** : Le charset `utf8mb4` est utilisé pour supporter tous les caractères Unicode, y compris les emojis.

## 🆘 Dépannage

### Erreur de connexion

Vérifiez :
- Que MySQL/MariaDB est démarré
- Les identifiants dans `config/database.php`
- Que la base de données `kernel_forum` existe

### Erreur "Table already exists"

C'est normal si vous exécutez le script plusieurs fois. Les tables existantes ne seront pas écrasées.

### Tester la connexion

Vous pouvez tester la connexion avec :

```php
<?php
require_once 'config/database.php';
if (testDBConnection()) {
    echo "Connexion réussie !";
} else {
    echo "Échec de la connexion";
}
?>
```

## 📚 Documentation Complète

Consultez `database/README.md` pour plus de détails sur :
- La structure complète des tables
- Les vues disponibles
- Les procédures stockées
- Les triggers

---

**Date de création** : $(Get-Date -Format "yyyy-MM-dd")
**Version** : 1.0

