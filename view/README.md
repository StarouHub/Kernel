# 🚀 Kernel - Plateforme de Projets Innovants

## 📖 Description

Kernel est une plateforme web permettant aux innovateurs de présenter leurs projets technologiques et de rechercher des financements. Partagez vos idées, suivez l'évolution des projets et connectez-vous avec des investisseurs.

## ✨ Fonctionnalités

### 💡 Gestion des Projets
- **Créer un projet** : Présentez votre idée innovante avec un formulaire simple
- **Parcourir les projets** : Découvrez tous les projets avec recherche instantanée
- **Voir les détails** : Informations complètes, budget, progression
- **Modifier** : Mettez à jour vos projets à tout moment
- **Supprimer** : Retirez un projet si nécessaire
- **Catégories** : AI, IoT, Blockchain, Web, Data, Security

### 📰 Actualités des Projets
- **Publier des actualités** : Tenez vos investisseurs informés
- **Suivre l'évolution** : Consultez les dernières nouvelles par projet
- **Types d'actualités** : 
  - 🎯 **Milestone** : Étapes importantes franchies
  - 📢 **Update** : Mises à jour du projet
  - 📣 **Announcement** : Annonces officielles

### 🔍 Recherche et Navigation
- **Recherche en temps réel** : Trouvez rapidement un projet
- **Filtrage** : Par catégorie, statut, budget
- **Interface intuitive** : Navigation simple et claire

## 🛠️ Technologies

- **Frontend** : HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend** : PHP 8.0+
- **Base de données** : MySQL / MariaDB
- **Design** : Responsive, moderne, accessible

## 📦 Installation

### Prérequis
- Serveur web (XAMPP, WAMP, MAMP)
- PHP 8.0 ou supérieur
- MySQL ou MariaDB

### Installation en 5 étapes

1. **Télécharger** le projet dans votre dossier web (htdocs, www, etc.)

2. **Créer la base de données** `kernel1` dans phpMyAdmin

3. **Importer les fichiers SQL** :
   - `kernel1.sql` (structure + projets)
   - `actualites_test_data.sql` (actualités de test)

4. **Vérifier la configuration** dans `config.php` :
   ```php
   $dbname = "kernel1";
   ```

5. **Accéder à la plateforme** :
   ```
   http://localhost/votre-projet/view/FrontOffice/listeprojet.php
   ```

📖 **Guide détaillé** : Consultez [INSTALLATION.md](INSTALLATION.md)

## 📁 Structure du Projet

```
kernel/
├── config.php                      # Configuration
├── kernel1.sql                     # Base de données
├── actualites_test_data.sql        # Données de test
│
├── controller/                     # Logique métier
│   ├── projetcontroller.php
│   ├── categoriecontroller.php
│   └── actualitecontroller.php
│
├── model/                          # Modèles de données
│   ├── projet.php
│   ├── categorie.php
│   └── actualite.php
│
└── view/FrontOffice/               # Interface utilisateur
    ├── ajoutprojet.php             # Créer un projet
    ├── listeprojet.php             # Liste des projets
    ├── detailsprojet.php           # Détails d'un projet
    ├── modifierprojet.php          # Modifier un projet
    ├── ajouterActualite.php        # Publier une actualité
    ├── searchActualites.php        # Rechercher des actualités
    └── listeActualite.php          # Toutes les actualités
```

## 🎯 Guide d'Utilisation

### � Poutr les Innovateurs

#### 1. Créer votre projet
- Allez sur **"Nouveau Projet"**
- Remplissez les informations (titre, description, budget)
- Choisissez une catégorie
- Publiez !

#### 2. Gérer votre projet
- **Modifier** : Mettez à jour les informations
- **Publier des actualités** : Tenez vos investisseurs informés
- **Suivre le financement** : Visualisez la progression

### 💰 Pour les Investisseurs

#### 1. Découvrir les projets
- Parcourez la **liste des projets**
- Utilisez la **recherche** pour filtrer
- Consultez les **détails** de chaque projet

#### 2. Suivre l'évolution
- Lisez les **actualités** des projets
- Filtrez par projet pour voir son historique
- Restez informé des **milestones** importants

### 🔍 Recherche

**Dans la liste des projets :**
- Tapez un mot-clé dans la barre de recherche
- Les résultats s'affichent instantanément
- Recherche dans : titre, description, catégorie

**Dans les actualités :**
- Sélectionnez un projet
- Voir toutes ses actualités
- Triées par date (plus récentes en premier)

## 🎨 Interface

### Design Moderne
- **Cartes interactives** : Effet hover, animations fluides
- **Badges colorés** : Statut du projet (Idée, Prototype, MVP, Production)
- **Barres de progression** : Visualisation du financement
- **Responsive** : Adapté à tous les écrans (mobile, tablette, desktop)

### Navigation Intuitive
- **Menu clair** : Accès rapide à toutes les fonctionnalités
- **Boutons d'action** : Modifier, Supprimer, Publier
- **Messages** : Confirmation des actions, erreurs explicites
- **Recherche** : Barre de recherche toujours accessible

## 🔐 Sécurité

- ✅ Validation des formulaires (JavaScript + PHP)
- ✅ Protection contre les injections SQL
- ✅ Échappement des données affichées
- ✅ Confirmation avant suppression

## 📊 Données Incluses

### Projets de Démonstration
- **Assistant IA** : Intelligence artificielle pour PME
- **Maison Connectée** : Domotique écologique
- **Plateforme NFT** : Marketplace pour artistes
- **App Santé** : Téléconsultation médicale
- **AgriTech** : Agriculture durable

### Catégories
- 🤖 **AI** : Intelligence Artificielle
- 🔌 **IoT** : Internet des Objets
- ⛓️ **Blockchain** : Technologies décentralisées
- 💻 **Web** : Développement web et mobile
- 📊 **Data** : Data Science et Big Data
- 🔒 **Security** : Cybersécurité

## 📚 Documentation

### Pour les Utilisateurs
- **README.md** : Ce fichier (guide utilisateur)
- **INSTALLATION.md** : Installation détaillée

### Pour les Développeurs
- **ENTITE_ACTUALITE_COMPLETE.md** : Documentation technique
- **GUIDE_JURY_ACTUALITES.md** : Démonstration technique
- **FONCTIONNALITE_RECHERCHE.md** : Système de recherche

## 🧪 Test de la Plateforme

Après installation, testez :
```
http://localhost/votre-projet/test_connexion.php
```

Ce script vérifie :
- ✅ Connexion à la base de données
- ✅ Existence des tables
- ✅ Chargement des controllers
- ✅ Données de test disponibles

## 🤝 Support

Besoin d'aide ? Consultez :
- 📖 [INSTALLATION.md](INSTALLATION.md) - Guide d'installation
- � `Atest_connexion.php` - Diagnostic automatique
- 📚 Documentation technique dans le dossier du projet

## 📝 Licence

Projet développé dans un cadre éducatif.

## 👥 Équipe

**Kernel** - Plateforme d'Innovation Technologique  
Développé par l'équipe Webzz

---

**Version :** 2.0  
**Dernière mise à jour :** Novembre 2025  
**Statut :** ✅ Opérationnel

