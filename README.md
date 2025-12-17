# 🚀 Kernel - Innovation Platform

## 📖 Description

Kernel is a comprehensive web platform for innovators to showcase technological projects, seek funding, manage complaints, organize events, and connect with investors.

## ✨ Key Features

### Core Modules
- **Project Management**: Create, browse, edit projects with categories (AI, IoT, Blockchain, Web, Data, Security)
- **Event Management**: Organize conferences, workshops, and networking events
- **Complaint System**: Submit and track issues with priority management
- **User Authentication**: Secure registration with role-based access control
- **Admin Dashboard**: Manage users, complaints, events, and view statistics

### Advanced Features
- **Real-time Search**: Find projects, events instantly with advanced filtering
- **AI Chatbot**: Intelligent assistance and analysis
- **Payment Integration**: Secure transaction processing with invoice generation
- **Notifications**: Real-time updates and alerts
- **Responsive Design**: Mobile-optimized interface

## 🛠️ Technologies

| Layer | Technologies |
|-------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript ES6+, Bootstrap 5 |
| **Backend** | PHP 8.0+, MVC Architecture |
| **Database** | MySQL / MariaDB |
| **Security** | bcrypt hashing, PDO, CSRF protection, session management |
| **Tools** | Composer, PHPMailer |

## 📦 Installation

### Prerequisites
- PHP 8.0+
- MySQL/MariaDB
- Web server (XAMPP, WAMP, MAMP)

### Quick Setup

1. **Clone and Extract**
   ```bash
   git clone https://github.com/StarouHub/Kernel.git
   cd Kernel
   ```

2. **Create Database**
   - Open phpMyAdmin
   - Create database named `kernel`
   - Import `kernel.sql`

3. **Configure**
   ```php
   # Edit config.php
   $dbname = "kernel";
   $host = "localhost";
   $username = "your_username";
   $password = "your_password";
   ```

4. **Install Dependencies**
   ```bash
   composer install
   ```

5. **Access Platform**
   ```
   http://localhost/Kernel/view/FrontOffice/index.php
   ```

## 📁 Project Structure

```
Kernel/
├── config.php              # Database configuration
├── kernel.sql              # Database schema
├── composer.json           # Dependencies
│
├── api/                    # REST endpoints
│   ├── chatbot.php
│   ├── ai-analyze.php
│   ├── get-notifications.php
│   └── counts.php
│
├── controller/             # Business logic
│   ├── projetcontroller.php
│   ├── userController.php
│   ├── EvenementController.php
│   ├── ReclamationController.php
│   ├── Aicontroller.php
│   └── AdminNotificationSystem.php
│
├── model/                  # Data models
│   ├── projet.php
│   ├── user.php
│   ├── Evenement.php
│   ├── Reclamation.php
│   ├── actualite.php
│   └── categorie.php
│
├── view/                   # User interfaces
│   ├── FrontOffice/        # User interface
│   │   ├── index.php
│   │   ├── dashboard.php
│   │   ├── connexion.php
│   │   ├── inscription.php
│   │   ├── listeprojet.php
│   │   ├── detailsprojet.php
│   │   ├── evenements-list.php
│   │   ├── mesreclamations.php
│   │   ├── nouvellereclamation.php
│   │   └── payment.php
│   │
│   ├── BackOffice/         # Admin interface
│   │   ├── dashboard2.php
│   │   ├── admin.php
│   │   ├── gestionreclamations.php
│   │   └── statistiques.php
│   │
│   └── components/         # Reusable components
│       ├── main-navigation.php
│       ├── chatbot-widget.php
│       └── notifications-panel.php
│
├── services/               # Business services
│   ├── ChatbotService.php
│   ├── EmailService.php
│   ├── PaymentService.php
│   └── InvoiceService.php
│
└── assets/                 # Static files
    ├── css/
    ├── js/
    └── images/
```

## 🎯 User Roles & Capabilities

| Role | Capabilities |
|------|--------------|
| **Visitor** | Browse projects, events, view details |
| **User** | Create projects, submit complaints, register for events |
| **Innovator** | Manage projects, publish updates, track funding |
| **Investor** | View projects, track investments, participate in events |
| **Administrator** | User management, complaint handling, event oversight, statistics |

## 🔐 Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection protection (PDO)
- ✅ CSRF protection
- ✅ Session-based authentication
- ✅ Input validation & sanitization
- ✅ Role-based access control
- ✅ Data encryption for sensitive transactions

## 🧪 Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kernel.tn | admin123 |

Create standard user accounts via the registration page.

## 📊 Main Endpoints

| Feature | URL |
|---------|-----|
| Homepage | `/view/FrontOffice/index.php` |
| User Dashboard | `/view/FrontOffice/dashboard.php` |
| Projects List | `/view/FrontOffice/listeprojet.php` |
| Events | `/view/FrontOffice/evenements-list.php` |
| Complaints | `/view/FrontOffice/mesreclamations.php` |
| Admin Dashboard | `/view/BackOffice/dashboard2.php` |
| Admin Users | `/view/BackOffice/admin.php` |
| Complaints Management | `/view/BackOffice/gestionreclamations.php` |
| API Chatbot | `/api/chatbot.php` |
| AI Analysis | `/api/ai-analyze.php` |

## 🚀 Main Workflows

### For Innovators
1. Register and create account
2. Create new project with details
3. Publish project updates and news
4. Track investor interest and funding progress
5. Manage project details anytime

### For Investors
1. Browse available projects
2. View detailed project information
3. Track project news and updates
4. Register for events
5. Submit feedback or complaints if needed

### For Administrators
1. Access admin dashboard
2. Manage user accounts and roles
3. Handle and resolve complaints
4. Monitor system statistics
5. Manage events and approvals

## 💡 Sample Data

### Demo Projects
- AI Assistant for SMEs
- Smart Home Automation
- NFT Marketplace
- Telemedicine Platform
- Sustainable Agriculture

### Event Categories
- Tech Conferences
- Workshops
- Networking Events
- Hackathons

## 📝 License

Educational project - developed for learning purposes

## 👥 Team

**Kernel - Technological Innovation Platform**  
Developed by Team Webzz

## 📞 Support & Documentation

- Installation guide: See `INSTALLATION.md`
- Technical documentation: Check project folders
- Diagnostics: Run `debug.php`

---

**Version**: 4.0  
**Status**: ✅ Production Ready  
**Last Updated**: December 2025  

**Key Modules**: Project Management | Event Management | Complaint System | User Authentication | Admin Dashboard | AI Features | Payment Processing
