# 🚀 Kernel - Plateforme de Projets Innovants

## 📖 Description

Kernel est une plateforme web complète permettant aux innovateurs de présenter leurs projets technologiques et de rechercher des financements. Partagez vos idées, suivez l'évolution des projets, connectez-vous avec des investisseurs et gérez votre communauté d'utilisateurs.

## ✨ Fonctionnalités

### 👥 Système d'Authentification Complet
- **Inscription sécurisée** : Création de compte avec validation des données
- **Connexion** : Authentification avec gestion des sessions
- **Gestion des rôles** : Visiteur, Utilisateur, Innovateur, Investisseur, Administrateur
- **Profils utilisateurs** : Gestion des informations personnelles
- **Sécurité avancée** : Hashage des mots de passe, protection CSRF

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

### 🛡️ Administration
- **Panneau d'administration** : Interface dédiée pour les administrateurs
- **Gestion des utilisateurs** : Créer, modifier, supprimer des comptes
- **Statistiques** : Vue d'ensemble des utilisateurs et activités
- **Recherche avancée** : Filtrage des utilisateurs par critères

## 🛠️ Technologies

- **Frontend** : HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend** : PHP 8.0+ (POO, MVC)
- **Base de données** : MySQL / MariaDB
- **Sécurité** : Sessions PHP, hashage bcrypt, validation des données
- **Dépendances** : Composer, PHPMailer
- **Design** : Responsive, moderne, accessible

## 📦 Installation

### Prérequis
- Serveur web (XAMPP, WAMP, MAMP)
- PHP 8.0 ou supérieur
- MySQL ou MariaDB

### Installation en 6 étapes

1. **Télécharger** le projet dans votre dossier web (htdocs, www, etc.)

2. **Créer la base de données** `kernel` dans phpMyAdmin

3. **Importer le fichier SQL** :
   - `kernel.sql` (structure complète + tables d'authentification)

4. **Installer les dépendances** (optionnel pour PHPMailer) :
   ```bash
   composer install
   ```

5. **Vérifier la configuration** dans `config.php` :
   ```php
   $dbname = "kernel";
   ```

6. **Accéder à la plateforme** :
   ```
   http://localhost/votre-projet/view/FrontOffice/index.php
   ```

📖 **Guide détaillé** : Consultez [INSTALLATION.md](INSTALLATION.md)

## 📁 Structure du Projet

```
kernel/
├── config.php                      # Configuration base de données
├── kernel.sql                      # Base de données complète
├── composer.json                   # Dépendances PHP
├── vendor/                         # Librairies externes
│
├── controller/                     # Logique métier (MVC)
│   ├── projetcontroller.php        # Gestion des projets
│   ├── categoriecontroller.php     # Gestion des catégories
│   ├── actualitecontroller.php     # Gestion des actualités
│   └── userController.php          # Gestion des utilisateurs
│
├── model/                          # Modèles de données
│   ├── projet.php                  # Modèle Projet
│   ├── categorie.php               # Modèle Catégorie
│   ├── actualite.php               # Modèle Actualité
│   ├── model.php                   # Modèles génériques
│   └── user.php                    # Modèle Utilisateur
│
├── view/FrontOffice/               # Interface utilisateur
│   ├── index.php                   # Page d'accueil
│   ├── connexion.php               # Connexion
│   ├── inscription.php             # Inscription
│   ├── logout.php                  # Déconnexion
│   ├── profil-utilisateur.php      # Profil utilisateur
│   ├── ajoutprojet.php             # Créer un projet
│   ├── listeprojet.php             # Liste des projets
│   ├── detailsprojet.php           # Détails d'un projet
│   ├── modifierprojet.php          # Modifier un projet
│   ├── ajouterActualite.php        # Publier une actualité
│   └── listeActualite.php          # Toutes les actualités
│
├── view/BackOffice/                # Interface d'administration
│   ├── admin-users.php             # Gestion des utilisateurs
│   └── modify-user.php             # Modification d'utilisateur
│
├── api/                            # API endpoints
│   ├── chatbot.php                 # API Chatbot
│   └── counts.php                  # API Statistiques
│
└── services/                       # Services métier
    ├── ChatbotService.php          # Service Chatbot
    └── MailingService.php          # Service Email
```

## 🎯 Guide d'Utilisation

### 🔐 Authentification

#### 1. Créer un compte
- Allez sur **"Inscription"**
- Remplissez vos informations (nom, prénom, email, téléphone)
- Choisissez un mot de passe sécurisé
- Confirmez votre inscription

#### 2. Se connecter
- Utilisez votre email et mot de passe
- Option "Rester connecté" disponible
- Accès différencié selon votre rôle

#### 3. Gestion du profil
- Modifiez vos informations personnelles
- Changez votre mot de passe
- Gérez vos préférences

### 💡 Pour les Innovateurs

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

### 🛡️ Pour les Administrateurs

#### 1. Accéder au panneau d'administration
- Connectez-vous avec un compte administrateur
- Accédez au **"Panneau d'administration"**
- Vue d'ensemble des statistiques

#### 2. Gérer les utilisateurs
- **Voir tous les utilisateurs** : Liste complète avec recherche
- **Modifier un utilisateur** : Changer rôle, informations
- **Supprimer un utilisateur** : Suppression définitive
- **Statistiques** : Nombre total, par rôle, etc.

#### 3. Recherche avancée
- Filtrer par nom, email, rôle
- Recherche en temps réel
- Export des données (à venir)

### 🔍 Recherche

**Dans la liste des projets :**
- Tapez un mot-clé dans la barre de recherche
- Les résultats s'affichent instantanément
- Recherche dans : titre, description, catégorie

**Dans les actualités :**
- Sélectionnez un projet
- Voir toutes ses actualités
- Triées par date (plus récentes en premier)

**Dans l'administration :**
- Recherche d'utilisateurs par nom, prénom, email
- Filtrage en temps réel
- Résultats instantanés

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

### Authentification
- ✅ Hashage des mots de passe (bcrypt)
- ✅ Gestion sécurisée des sessions PHP
- ✅ Validation des données d'entrée
- ✅ Protection contre les attaques par force brute

### Protection des données
- ✅ Validation des formulaires (JavaScript + PHP)
- ✅ Protection contre les injections SQL (PDO)
- ✅ Échappement des données affichées (htmlspecialchars)
- ✅ Confirmation avant suppression

### Contrôle d'accès
- ✅ Système de rôles et permissions
- ✅ Vérification des autorisations
- ✅ Redirection automatique selon le rôle
- ✅ Protection des pages d'administration

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

### Comptes de test
Après installation, vous pouvez créer des comptes ou utiliser :

**Administrateur :**
- Email : admin@kernel.tn
- Mot de passe : admin123
- Accès : Panneau d'administration complet

**Utilisateur standard :**
- Créez votre compte via l'inscription
- Accès : Fonctionnalités utilisateur

### Pages de test
```
http://localhost/votre-projet/view/FrontOffice/index.php        # Page d'accueil
http://localhost/votre-projet/view/FrontOffice/connexion.php    # Connexion
http://localhost/votre-projet/view/FrontOffice/inscription.php  # Inscription
http://localhost/votre-projet/view/BackOffice/admin-users.php   # Administration
```

### Vérifications automatiques
Le système vérifie automatiquement :
- ✅ Connexion à la base de données
- ✅ Existence des tables utilisateurs
- ✅ Chargement des contrôleurs
- ✅ Fonctionnement de l'authentification

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

## 🆕 Nouvelles Fonctionnalités (v3.0)

### ✨ Système d'Authentification Complet
- **Inscription/Connexion** : Interface moderne et sécurisée
- **Gestion des rôles** : 5 niveaux d'accès différents
- **Profils utilisateurs** : Gestion complète des informations
- **Sessions sécurisées** : Authentification robuste

### 🛡️ Panneau d'Administration
- **Interface dédiée** : Design moderne pour les administrateurs
- **Gestion des utilisateurs** : CRUD complet
- **Statistiques en temps réel** : Vue d'ensemble des données
- **Recherche avancée** : Filtrage intelligent des utilisateurs

### 🔧 Améliorations Techniques
- **Architecture MVC** : Code organisé et maintenable
- **Sécurité renforcée** : Protection contre les attaques courantes
- **Base de données optimisée** : Nouvelles tables pour l'authentification
- **Code propre** : Respect des bonnes pratiques PHP

---

**Version :** 3.0  
**Dernière mise à jour :** Décembre 2025  
**Statut :** ✅ Opérationnel avec Authentification Complète

**Nouvelles fonctionnalités :** Système d'authentification, panneau d'administration, gestion des utilisateurs, sécurité renforcée

