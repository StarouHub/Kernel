# 🚀 Kernel - Plateforme de Projets Innovants

## 📖 Description

Kernel est une plateforme web permettant aux innovateurs de présenter leurs projets technologiques et de rechercher des financements. Le système inclut un **CRUD complet** pour la gestion des projets et des actualités, avec une architecture MVC et des **jointures SQL** démontrées.

## ✨ Fonctionnalités

### ✅ Entité PROJET (Complète)
- **CREATE** : Formulaire d'ajout avec validation JavaScript et PHP
- **READ** : Liste des projets avec recherche en temps réel
- **UPDATE** : Modification des projets existants
- **DELETE** : Suppression avec confirmation
- **Gestion des catégories** : Association avec catégories (AI, IoT, Blockchain, Web, Data, Security)
- **Calcul automatique** : Pourcentage de financement et statistiques
- **Design responsive** : Interface adaptée à tous les écrans

### ✅ Entité ACTUALITÉ (Complète avec JOINTURE)
- **CREATE** : Publication d'actualités pour un projet
- **READ** : Liste et recherche d'actualités par projet
- **JOINTURE SQL** : Affichage des actualités avec le nom du projet (INNER JOIN)
- **Types d'actualités** : Milestone, Update, Announcement
- **Intégration** : Actualités affichées dans les détails du projet
- **Relation 1-to-Many** : Un projet peut avoir plusieurs actualités

### 🔍 Fonctionnalités Avancées
- **Recherche en temps réel** : Filtrage instantané des projets
- **Validation double** : JavaScript + PHP pour plus de sécurité
- **Messages d'erreur** : Affichage clair des erreurs de validation
- **Données de test** : SQL fourni pour tester rapidement

## 🛠️ Technologies Utilisées

- **Frontend** : HTML5, CSS3, JavaScript ES6, Bootstrap 5.3
- **Backend** : PHP 8.0+ (POO)
- **Base de données** : MySQL 8.0+ / MariaDB 10.4+
- **Architecture** : MVC (Model-View-Controller)
- **Sécurité** : PDO avec requêtes préparées, htmlspecialchars()
- **Design Pattern** : Singleton pour la connexion DB

## 📦 Installation

Consultez le fichier [INSTALLATION.md](INSTALLATION.md) pour les instructions détaillées.

### Installation Rapide

1. **Créer la base de données** : `kernel1` dans phpMyAdmin
2. **Importer la structure** : `kernel1.sql`
3. **Importer les données de test** :
   - Projets : Déjà inclus dans `kernel1.sql`
   - Actualités : `actualites_test_data.sql`
4. **Configurer** : Vérifier `config.php` (base = `kernel1`)
5. **Tester** : `http://localhost/votre-projet/test_connexion.php`
6. **Accéder** : `http://localhost/votre-projet/view/FrontOffice/listeprojet.php`

## 📁 Structure du Projet

```
kernel/
├── config.php                          # Configuration BDD (kernel1)
├── kernel1.sql                         # Structure complète de la BDD
├── actualites_test_data.sql            # Données de test pour actualités
├── test_connexion.php                  # Script de test de connexion
├── INSTALLATION.md                     # Guide d'installation détaillé
├── README_ACTUALITES.md                # Documentation Actualité
├── GUIDE_JURY_ACTUALITES.md            # Guide pour démonstration
├── ENTITE_ACTUALITE_COMPLETE.md        # Documentation technique complète
│
├── controller/
│   ├── projetcontroller.php           # CRUD Projet + Jointures
│   ├── categoriecontroller.php        # CRUD Catégorie
│   └── actualitecontroller.php        # CRUD Actualité + JOINTURE ⭐
│
├── model/
│   ├── projet.php                     # Modèle Projet (POO)
│   ├── categorie.php                  # Modèle Catégorie (POO)
│   ├── actualite.php                  # Modèle Actualité (POO) ⭐
│   └── model.php                      # Modèle de base
│
└── view/FrontOffice/
    ├── PROJETS
    │   ├── ajoutprojet.php            # CREATE Projet
    │   ├── listeprojet.php            # READ Projets (avec recherche)
    │   ├── detailsprojet.php          # READ Projet (avec actualités)
    │   ├── modifierprojet.php         # UPDATE Projet
    │   ├── supprimerprojet.php        # DELETE Projet
    │   ├── script.js                  # Validation formulaire
    │   └── recherche.js               # Recherche en temps réel
    │
    └── ACTUALITÉS ⭐
        ├── searchActualites.php       # JOINTURE Projet-Actualité ⭐
        ├── ajouterActualite.php       # CREATE Actualité
        └── listeActualite.php         # READ Actualités
```

## 🎯 Utilisation

### 📋 Gestion des Projets

#### Créer un Projet
1. Accédez à `ajoutprojet.php`
2. Remplissez le formulaire (champs * obligatoires)
3. Sélectionnez une catégorie dans le menu déroulant
4. Cliquez sur "Publier le Projet"

#### Voir les Projets
1. Accédez à `listeprojet.php`
2. Utilisez la barre de recherche pour filtrer
3. Cliquez sur une carte pour voir les détails

#### Modifier un Projet
1. Dans `detailsprojet.php`, cliquez sur "Modifier le projet"
2. Modifiez les informations
3. Cliquez sur "Enregistrer les modifications"

#### Supprimer un Projet
1. Dans `detailsprojet.php`, cliquez sur "Supprimer le projet"
2. Confirmez la suppression dans la popup

### 📰 Gestion des Actualités (JOINTURE)

#### Publier une Actualité
1. Accédez à `ajouterActualite.php`
2. Sélectionnez le projet concerné
3. Choisissez le type (Milestone, Update, Announcement)
4. Rédigez le contenu
5. Cliquez sur "Publier l'actualité"

#### Rechercher par Projet (JOINTURE SQL)
1. Accédez à `searchActualites.php` ⭐
2. Sélectionnez un projet dans le menu déroulant
3. Cliquez sur "Rechercher"
4. **Résultat** : Affiche toutes les actualités du projet avec INNER JOIN

#### Voir Toutes les Actualités
1. Accédez à `listeActualite.php`
2. Toutes les actualités sont affichées avec leur projet (JOINTURE)

## 🎨 Fonctionnalités Visuelles

### Projets
- **Formulaire d'ajout** : Validation en temps réel, messages d'erreur clairs
- **Liste des projets** : Cartes interactives, badges de statut, recherche instantanée
- **Détails du projet** : Informations complètes, statistiques, actualités intégrées
- **Modification** : Formulaire pré-rempli avec les données actuelles
- **Suppression** : Confirmation avant suppression

### Actualités ⭐
- **Recherche par projet** : Select avec tous les projets, affichage avec JOINTURE
- **Liste complète** : Toutes les actualités avec leur projet associé
- **Types colorés** : Badges visuels (Milestone, Update, Announcement)
- **Intégration** : Section actualités dans la page de détails du projet

### Design
- **Responsive** : Adapté mobile, tablette, desktop
- **Moderne** : Dégradés, ombres, animations
- **Cohérent** : Palette de couleurs uniforme
- **Accessible** : Contrastes respectés, navigation claire

## 🔐 Sécurité

- ✅ **Validation double** : JavaScript (client) + PHP (serveur)
- ✅ **Requêtes préparées** : PDO avec bindValue() pour éviter les injections SQL
- ✅ **Échappement HTML** : htmlspecialchars() sur toutes les sorties
- ✅ **Protection XSS** : Filtrage des entrées utilisateur
- ✅ **Intégrité référentielle** : Clés étrangères avec ON DELETE CASCADE
- ✅ **Confirmation** : Popup JavaScript avant suppression

## 📊 Base de Données

### Tables Principales

| Table | Description | Relations |
|-------|-------------|-----------|
| `projet` | Projets innovants | 1-to-Many avec `actualite` |
| `actualite` | Actualités des projets | Many-to-1 avec `projet` ⭐ |
| `categorie` | Catégories (AI, IoT, etc.) | Many-to-Many avec `projet` |
| `projet_categorie` | Table de liaison | - |
| `utilisateur` | Utilisateurs de la plateforme | 1-to-Many avec `projet` |

### Relation Projet-Actualité (JOINTURE) ⭐

```sql
-- Exemple de JOINTURE dans ActualiteController.php
SELECT a.*, p.titre as projet_titre 
FROM actualite a 
INNER JOIN projet p ON a.projet_id = p.id 
WHERE a.projet_id = :projet_id 
ORDER BY a.date_publication DESC
```

**Relation :** Un projet (1) peut avoir plusieurs actualités (N)

### Données de Test

- **5 projets** : Assistant IA, Maison Connectée, NFT, App Santé, AgriTech
- **6 catégories** : AI, IoT, Blockchain, Web, Data, Security
- **10 actualités** : Réparties sur les 5 projets (fichier `actualites_test_data.sql`)

## 🎓 Pour le Jury

### Démonstration de la JOINTURE SQL

**Page principale :** `searchActualites.php`

Cette page démontre une **JOINTURE SQL** entre les tables `projet` et `actualite` :

1. L'utilisateur sélectionne un projet
2. La requête SQL joint les deux tables : `INNER JOIN projet p ON a.projet_id = p.id`
3. Les actualités sont affichées avec le nom du projet

**Fichiers à montrer :**
- `controller/actualitecontroller.php` (ligne 15) : Requête avec JOINTURE
- `view/FrontOffice/searchActualites.php` : Interface de recherche
- `model/actualite.php` : Modèle POO avec attributs et méthodes

### CRUD Complet Démontré

| Entité | CREATE | READ | UPDATE | DELETE |
|--------|--------|------|--------|--------|
| **Projet** | ✅ ajoutprojet.php | ✅ listeprojet.php | ✅ modifierprojet.php | ✅ supprimerprojet.php |
| **Actualité** | ✅ ajouterActualite.php | ✅ searchActualites.php | ✅ Méthode dans controller | ✅ Méthode dans controller |

### Architecture MVC Respectée

```
Model (POO) → Controller (CRUD + SQL) → View (Interface)
```

Chaque entité suit cette architecture strictement.

## 📚 Documentation

- **README.md** : Ce fichier (vue d'ensemble)
- **INSTALLATION.md** : Guide d'installation détaillé
- **README_ACTUALITES.md** : Documentation Actualité (résumé)
- **ENTITE_ACTUALITE_COMPLETE.md** : Documentation technique complète
- **GUIDE_JURY_ACTUALITES.md** : Guide pour la démonstration au jury
- **SIMPLIFICATION_CATEGORIE.md** : Explication du système de catégories
- **FONCTIONNALITE_RECHERCHE.md** : Documentation de la recherche



### Test des Fonctionnalités
1. **Projets** : Créer, lister, modifier, supprimer
2. **Actualités** : Créer, rechercher par projet (JOINTURE)
3. **Recherche** : Taper dans la barre de recherche de la liste

## 📝 Licence

Ce projet est développé par Equipe Webzz

## 👥 Auteur

Projet Kernel - Plateforme d'Innovation Technologique

