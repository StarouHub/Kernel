<<<<<<< HEAD
# 🚀 Kernel - Complete Innovation Platform

## 📖 Description

Kernel is a comprehensive web platform that enables innovators to showcase their technological projects, seek funding, manage complaints, organize events, and connect with investors. Share your ideas, track project evolution, connect with the community, and manage your user base through an integrated administration system.

## ✨ Key Features

### 👥 Complete Authentication System
- **Secure Registration**: Account creation with data validation
- **Login System**: Authentication with session management
- **Role Management**: Visitor, User, Innovator, Investor, Administrator
- **User Profiles**: Personal information management
- **Advanced Security**: Password hashing, CSRF protection, session security

### 💡 Project Management
- **Create Projects**: Present your innovative ideas with simple forms
- **Browse Projects**: Discover all projects with instant search
- **View Details**: Complete information, budget, progress tracking
- **Edit Projects**: Update your projects anytime
- **Delete Projects**: Remove projects when necessary
- **Categories**: AI, IoT, Blockchain, Web, Data, Security

### 📰 Project News & Updates
- **Publish News**: Keep your investors informed
- **Track Evolution**: Check latest news by project
- **News Types**:
  - 🎯 **Milestone**: Important stages reached
  - 📢 **Update**: Project updates
  - 📣 **Announcement**: Official announcements

### 🎫 Event Management
- **Event Creation**: Organize tech conferences, workshops, meetups
- **Event Discovery**: Browse upcoming and past events
- **Event Details**: Complete information, schedules, speakers
- **Registration System**: Manage attendee registrations
- **Event Categories**: Conferences, Workshops, Networking, Hackathons

### 📋 Complaint Management System
- **Submit Complaints**: Report issues with detailed forms
- **Track Status**: Monitor complaint resolution progress
- **Priority System**: Automatic and manual priority escalation
- **Admin Response**: Direct communication with administrators
- **Status Updates**: Real-time status tracking (Pending, In Progress, Resolved, Closed)
- **Priority Levels**: Critical, Urgent, High, Normal, Low

### 🤖 AI-Powered Features
- **Intelligent Chatbot**: Get instant help and information
- **AI Analysis**: Automated project and complaint analysis
- **Priority Manager**: AI-driven priority management
- **Smart Recommendations**: Personalized content suggestions

### 🔍 Search & Navigation
- **Real-time Search**: Find projects, events, and content instantly
- **Advanced Filtering**: By category, status, budget, date
- **Intuitive Interface**: Simple and clear navigation
- **Responsive Design**: Works on all devices

### 🛡️ Administration Panel
- **Admin Dashboard**: Dedicated interface for administrators
- **User Management**: Create, modify, delete accounts
- **Statistics**: Overview of users and activities
- **Complaint Management**: Handle and resolve user complaints
- **Event Oversight**: Manage and moderate events
- **Advanced Search**: Filter users by multiple criteria

### 💳 Payment Integration
- **Secure Payments**: Integrated payment processing
- **Multiple Methods**: Support for various payment options
- **Transaction Tracking**: Complete payment history
- **Invoice Generation**: Automated invoice creation

## 🛠️ Technologies

### Frontend
- **HTML5 & CSS3**: Modern web standards
- **JavaScript ES6+**: Interactive functionality
- **Bootstrap 5**: Responsive design framework
- **Bootstrap Icons**: Comprehensive icon library

### Backend
- **PHP 8.0+**: Object-oriented programming, MVC architecture
- **MySQL/MariaDB**: Robust database management
- **Composer**: Dependency management
- **PHPMailer**: Email functionality

### Security
- **PHP Sessions**: Secure session management
- **bcrypt Hashing**: Password security
- **Data Validation**: Input sanitization
- **CSRF Protection**: Cross-site request forgery prevention

### Design
- **Responsive**: Mobile-first approach
- **Modern UI**: Clean and intuitive interface
- **Accessibility**: WCAG compliant
- **Performance**: Optimized loading times

## 📦 Installation

### Prerequisites
- Web server (XAMPP, WAMP, MAMP, or similar)
- PHP 8.0 or higher
- MySQL or MariaDB
- Composer (optional, for dependencies)

### Installation Steps

1. **Download the Project**
   ```bash
   git clone [repository-url]
   # Or download and extract to your web directory (htdocs, www, etc.)
   ```

2. **Create Database**
   - Open phpMyAdmin
   - Create a new database named `kernel`

3. **Import Database Structure**
   ```sql
   # Import one of these files:
   kernel.sql                    # Complete structure + sample data
   kernel_cirine_schema.sql      # Schema only
   ```

4. **Configure Database Connection**
   ```php
   # Edit config.php or config/database.php
   $dbname = "kernel";
   $username = "your_username";
   $password = "your_password";
   $host = "localhost";
   ```

5. **Install Dependencies (Optional)**
=======
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
>>>>>>> origin/MohamedChaouachi
   ```bash
   composer install
   ```

<<<<<<< HEAD
6. **Access the Platform**
   ```
   http://localhost/your-project/view/FrontOffice/index.php
   ```

📖 **Detailed Guide**: Check `INSTALLATION.md` for complete setup instructions

## 📁 Project Structure

```
kernel/
├── 📄 config.php                      # Database configuration
├── 📄 kernel.sql                      # Complete database
├── 📄 composer.json                   # PHP dependencies
├── 📁 vendor/                         # External libraries
│
├── 📁 controller/                     # Business logic (MVC)
│   ├── projetcontroller.php          # Project management
│   ├── EvenementController.php       # Event management
│   ├── ReclamationController.php     # Complaint management
│   ├── userController.php            # User management
│   ├── Aicontroller.php              # AI features
│   └── AdminNotificationSystem.php   # Admin notifications
│
├── 📁 model/                          # Data models
│   ├── projet.php                    # Project model
│   ├── evenement.php                 # Event model
│   ├── reclamation.php               # Complaint model
│   └── user.php                      # User model
│
├── 📁 view/                           # User interfaces
│   ├── 📁 FrontOffice/               # User interface
│   │   ├── index.php                 # Homepage
│   │   ├── dashboard.php             # User dashboard
│   │   ├── evenements-list.php       # Events listing
│   │   ├── mesreclamations.php       # My complaints
│   │   ├── nouvellereclamation.php   # New complaint
│   │   └── detailreclamation.php     # Complaint details
│   │
│   ├── 📁 BackOffice/                # Admin interface
│   │   ├── dashboard2.php            # Admin dashboard
│   │   ├── gestionreclamations.php   # Complaint management
│   │   ├── statistiques.php         # Statistics
│   │   └── admin.php                 # User administration
│   │
│   └── 📁 components/                # Reusable components
│       ├── main-navigation.php       # Navigation bar
│       ├── chatbot-widget.php        # Chatbot interface
│       └── notifications-panel.php   # Notification system
│
├── 📁 api/                           # API endpoints
│   ├── chatbot.php                   # Chatbot API
│   ├── ai-analyze.php                # AI analysis
│   ├── get-notifications.php         # Notifications API
│   └── counts.php                    # Statistics API
│
├── 📁 services/                      # Business services
│   ├── ChatbotService.php            # Chatbot service
│   ├── MailingService.php            # Email service
│   └── NotificationService.php       # Notification service
│
└── 📁 assets/                        # Static assets
    ├── 📁 css/                       # Stylesheets
    ├── 📁 js/                        # JavaScript files
    └── 📁 images/                    # Images and media
```

## 🎯 User Guide

### 🔐 Authentication

#### 1. Create an Account
- Go to "Sign Up"
- Fill in your information (name, email, phone)
- Choose a secure password
- Confirm your registration

#### 2. Sign In
- Use your email and password
- "Remember me" option available
- Different access levels based on your role

#### 3. Profile Management
- Update personal information
- Change password
- Manage preferences

### 💡 For Innovators

#### 1. Create Your Project
- Go to "New Project"
- Fill in details (title, description, budget)
- Choose a category
- Publish!

#### 2. Manage Your Project
- **Edit**: Update project information
- **Publish News**: Keep investors informed
- **Track Funding**: Visualize progress

### 🎫 For Event Organizers

#### 1. Create Events
- Access event creation form
- Set date, time, and location
- Add speakers and agenda
- Publish event

#### 2. Manage Events
- Track registrations
- Send updates to attendees
- Manage event logistics

### 💰 For Investors

#### 1. Discover Projects
- Browse project listings
- Use search to filter
- Check project details

#### 2. Track Evolution
- Read project news
- Filter by project
- Stay informed about milestones

### 📋 For Users (Complaint System)

#### 1. Submit Complaints
- Access complaint form
- Describe your issue
- Set priority level
- Submit for review

#### 2. Track Complaints
- View complaint status
- Receive admin responses
- Monitor resolution progress

### 🛡️ For Administrators

#### 1. Access Admin Panel
- Sign in with admin account
- Access "Admin Dashboard"
- View system statistics

#### 2. Manage Users
- **View All Users**: Complete list with search
- **Edit Users**: Change roles, information
- **Delete Users**: Permanent removal
- **Statistics**: Total count, by role, etc.

#### 3. Manage Complaints
- View all complaints
- Assign priorities
- Respond to users
- Track resolution status

#### 4. Event Management
- Approve/reject events
- Monitor event activities
- Generate reports

## 🔍 Search Functionality

### Project Search
- Type keywords in search bar
- Instant results display
- Search in: title, description, category

### Event Search
- Filter by date, location, type
- Search speakers and topics
- Advanced filtering options

### Complaint Search
- Filter by status, priority
- Search by user or topic
- Date range filtering

### Admin Search
- Search users by name, email
- Real-time filtering
- Instant results

## 🎨 Interface Design

### Modern Design
- **Interactive Cards**: Hover effects, smooth animations
- **Colored Badges**: Project status (Idea, Prototype, MVP, Production)
- **Progress Bars**: Funding visualization
- **Responsive**: Adapted to all screens (mobile, tablet, desktop)

### Intuitive Navigation
- **Clear Menu**: Quick access to all features
- **Action Buttons**: Edit, Delete, Publish
- **Messages**: Action confirmations, clear error messages
- **Search**: Always accessible search bar

## 🔐 Security Features

### Authentication Security
✅ Password hashing (bcrypt)
✅ Secure PHP session management
✅ Input data validation
✅ Brute force attack protection

### Data Protection
✅ Form validation (JavaScript + PHP)
✅ SQL injection protection (PDO)
✅ Output data escaping (htmlspecialchars)
✅ Confirmation before deletion

### Access Control
✅ Role-based permissions system
✅ Authorization verification
✅ Automatic role-based redirection
✅ Admin panel protection

## 📊 Sample Data Included

### Demo Projects
- **AI Assistant**: Artificial intelligence for SMEs
- **Smart Home**: Ecological home automation
- **NFT Platform**: Marketplace for artists
- **Health App**: Medical teleconsultation
- **AgriTech**: Sustainable agriculture

### Categories
- 🤖 **AI**: Artificial Intelligence
- 🔌 **IoT**: Internet of Things
- ⛓️ **Blockchain**: Decentralized technologies
- 💻 **Web**: Web and mobile development
- 📊 **Data**: Data Science and Big Data
- 🔒 **Security**: Cybersecurity

### Event Types
- 📅 **Conferences**: Tech conferences and summits
- 🛠️ **Workshops**: Hands-on learning sessions
- 🤝 **Networking**: Community meetups
- 💻 **Hackathons**: Coding competitions

## 📚 Documentation

### For Users
- `README.md`: This file (user guide)
- `INSTALLATION.md`: Detailed installation guide

### For Developers
- Technical documentation in project folders
- API documentation for integrations
- Database schema documentation

## 🧪 Testing the Platform

### Test Accounts
After installation, you can create accounts or use:

**Administrator:**
- Email: admin@kernel.tn
- Password: admin123
- Access: Complete admin panel

**Standard User:**
- Create your account via registration
- Access: User features

### Test Pages
```
http://localhost/your-project/view/FrontOffice/index.php        # Homepage
http://localhost/your-project/view/FrontOffice/dashboard.php    # User Dashboard
http://localhost/your-project/view/BackOffice/dashboard2.php    # Admin Dashboard
http://localhost/your-project/view/FrontOffice/evenements-list.php  # Events
http://localhost/your-project/view/FrontOffice/mesreclamations.php  # Complaints
```

### Automatic Checks
The system automatically verifies:
✅ Database connection
✅ User table existence
✅ Controller loading
✅ Authentication functionality

## 🤝 Support

Need help? Check:
- 📖 `INSTALLATION.md` - Installation guide
- 🔧 `debug.php` - Automatic diagnostics
- 📚 Technical documentation in project folders

## 📝 License

Project developed for educational purposes.

## 👥 Team

**Kernel - Technological Innovation Platform**
Developed by Team Webzz

## 🆕 Latest Features (v4.0)

### ✨ Complete Platform Integration
- **Unified Dashboard**: Single interface for all features
- **Cross-Module Navigation**: Seamless switching between sections
- **Integrated Search**: Global search across all content types

### 🎫 Event Management System
- **Event Creation**: Full event lifecycle management
- **Registration System**: Attendee management
- **Event Categories**: Multiple event types support
- **Calendar Integration**: Schedule management

### 📋 Advanced Complaint System
- **Priority Management**: AI-powered priority assignment
- **Escalation System**: Automatic priority escalation
- **Admin Response**: Direct communication channel
- **Status Tracking**: Real-time progress monitoring

### 🤖 AI-Powered Features
- **Intelligent Chatbot**: Context-aware assistance
- **AI Analysis**: Automated content analysis
- **Smart Notifications**: Intelligent alert system
- **Predictive Analytics**: Data-driven insights

### 🛡️ Enhanced Security
- **Multi-layer Authentication**: Advanced security measures
- **Role-based Access**: Granular permission system
- **Session Management**: Secure session handling
- **Data Encryption**: Protected data transmission

### 🎨 Modern UI/UX
- **Responsive Design**: Mobile-first approach
- **Interactive Elements**: Smooth animations and transitions
- **Accessibility**: WCAG 2.1 compliant
- **Performance**: Optimized loading and rendering

---

**Version**: 4.0  
**Last Updated**: December 2024  
**Status**: ✅ Fully Operational with Complete Feature Set

**New in this version**: Complete platform integration, event management, advanced complaint system, AI features, enhanced security, modern UI/UX

---

🌟 **Star this project** if you find it useful!  
🐛 **Report issues** to help us improve  
🤝 **Contribute** to make Kernel even better
=======
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

>>>>>>> origin/MohamedChaouachi
