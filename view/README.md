# 🚀 Kernel - Innovation Platform

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
5. **Verify Configuration** in `config.php`:
   ```php
   $dbname = "kernel";
   ```

6. **Access the Platform**:
   ```
   http://localhost/your-project/view/FrontOffice/index.php
   ```

📖 **Detailed Guide**: Check [INSTALLATION.md](INSTALLATION.md)

## 📁 Project Structure

```
kernel/
├── config.php                      # Database configuration
├── kernel.sql                      # Complete database
├── composer.json                   # PHP dependencies
├── vendor/                         # External libraries
│
├── controller/                     # Business logic (MVC)
│   ├── projetcontroller.php        # Project management
│   ├── categoriecontroller.php     # Category management
│   ├── actualitecontroller.php     # News management
│   └── userController.php          # User management
│
├── model/                          # Data models
│   ├── projet.php                  # Project model
│   ├── categorie.php               # Category model
│   ├── actualite.php               # News model
│   ├── model.php                   # Generic models
│   └── user.php                    # User model
│
├── view/FrontOffice/               # User interface
│   ├── index.php                   # Homepage
│   ├── connexion.php               # Login
│   ├── inscription.php             # Registration
│   ├── logout.php                  # Logout
│   ├── profil-utilisateur.php      # User profile
│   ├── ajoutprojet.php             # Create project
│   ├── listeprojet.php             # Project list
│   ├── detailsprojet.php           # Project details
│   ├── modifierprojet.php          # Edit project
│   ├── ajouterActualite.php        # Publish news
│   └── listeActualite.php          # All news
│
├── view/BackOffice/                # Admin interface
│   ├── admin-users.php             # User management
│   └── modify-user.php             # User modification
│
├── api/                            # API endpoints
│   ├── chatbot.php                 # Chatbot API
│   └── counts.php                  # Statistics API
│
└── services/                       # Business services
    ├── ChatbotService.php          # Chatbot service
    └── MailingService.php          # Email service
```

## 🎯 User Guide

### 🔐 Authentication

#### 1. Create Account
- Go to **"Sign Up"**
- Fill in your information (name, first name, email, phone)
- Choose a secure password
- Confirm your registration

#### 2. Sign In
- Use your email and password
- "Remember me" option available
- Different access based on your role

#### 3. Profile Management
- Update your personal information
- Change your password
- Manage preferences

### 💡 For Innovators

#### 1. Create Your Project
- Go to **"New Project"**
- Fill in information (title, description, budget)
- Choose a category
- Publish!

#### 2. Manage Your Project
- **Edit**: Update information
- **Publish News**: Keep your investors informed
- **Track Funding**: Visualize progress

### 💰 For Investors

#### 1. Discover Projects
- Browse the **project list**
- Use **search** to filter
- Check **details** of each project

#### 2. Track Evolution
- Read project **news**
- Filter by project to see history
- Stay informed of **milestones**

### 🛡️ For Administrators

#### 1. Access Admin Panel
- Sign in with admin account
- Access **"Admin Panel"**
- Overview of statistics

#### 2. Manage Users
- **View All Users**: Complete list with search
- **Edit User**: Change role, information
- **Delete User**: Permanent deletion
- **Statistics**: Total count, by role, etc.

#### 3. Advanced Search
- Filter by name, email, role
- Real-time search
- Instant results

### 🔍 Search

**In project list:**
- Type a keyword in search bar
- Results display instantly
- Search in: title, description, category

**In news:**
- Select a project
- View all its news
- Sorted by date (most recent first)

**In administration:**
- Search users by name, first name, email
- Real-time filtering
- Instant results

## 🎨 Interface

### Modern Design
- **Interactive Cards**: Hover effects, smooth animations
- **Colored Badges**: Project status (Idea, Prototype, MVP, Production)
- **Progress Bars**: Funding visualization
- **Responsive**: Adapted to all screens (mobile, tablet, desktop)

### Intuitive Navigation
- **Clear Menu**: Quick access to all features
- **Action Buttons**: Edit, Delete, Publish
- **Messages**: Action confirmations, clear errors
- **Search**: Always accessible search bar

## 🔐 Security

### Authentication
- ✅ Password hashing (bcrypt)
- ✅ Secure PHP session management
- ✅ Input data validation
- ✅ Protection against brute force attacks

### Data Protection
- ✅ Form validation (JavaScript + PHP)
- ✅ SQL injection protection (PDO)
- ✅ Output data escaping (htmlspecialchars)
- ✅ Confirmation before deletion

### Access Control
- ✅ Role and permission system
- ✅ Authorization verification
- ✅ Automatic role-based redirection
- ✅ Admin page protection

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

## 📚 Documentation

### For Users
- **README.md**: This file (user guide)
- **INSTALLATION.md**: Detailed installation

### For Developers
- **ENTITE_ACTUALITE_COMPLETE.md**: Technical documentation
- **GUIDE_JURY_ACTUALITES.md**: Technical demo
- **FONCTIONNALITE_RECHERCHE.md**: Search system

## 🧪 Platform Testing

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
http://localhost/your-project/view/FrontOffice/connexion.php    # Login
http://localhost/your-project/view/FrontOffice/inscription.php  # Registration
http://localhost/your-project/view/BackOffice/admin-users.php   # Administration
```

### Automatic Checks
The system automatically verifies:
- ✅ Database connection
- ✅ User tables existence
- ✅ Controller loading
- ✅ Authentication functionality

## 📝 License

Project developed for educational purposes.

## 👥 Team

**Kernel** - Technological Innovation Platform
Developed by Team Webzz

## 🆕 New Features (v3.0)

### ✨ Complete Authentication System
- **Sign Up/Sign In**: Modern and secure interface
- **Role Management**: 5 different access levels
- **User Profiles**: Complete information management
- **Secure Sessions**: Robust authentication

### 🛡️ Admin Panel
- **Dedicated Interface**: Modern design for administrators
- **User Management**: Complete CRUD
- **Real-time Statistics**: Data overview
- **Advanced Search**: Intelligent user filtering

### 🔧 Technical Improvements
- **MVC Architecture**: Organized and maintainable code
- **Enhanced Security**: Protection against common attacks
- **Optimized Database**: New tables for authentication
- **Clean Code**: PHP best practices

---

**Version**: 3.0
**Last Update**: December 2025
**Status**: ✅ Operational with Complete Authentication

**New Features**: Authentication system, admin panel, user management, enhanced security
