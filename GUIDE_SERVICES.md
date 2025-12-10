# 🎯 Guide Rapide - Services Kernel

## 🚀 Démarrage Rapide

### Accès Principal
```
http://localhost/kernel/index_services.html
```

---

## 🤖 Service 1 : Chatbot

### Accès Direct
```
http://localhost/kernel/view/FrontOffice/chatbot.php
```

### Fonctionnalités
✅ Pose des questions en langage naturel  
✅ Recherche de projets  
✅ Consultation des actualités  
✅ Statistiques et budgets  
✅ Aide contextuelle  

### Exemples de Questions
```
"Combien de projets ?"
"Quel est le dernier projet ?"
"Montre-moi les actualités"
"Recherche projet AI"
"Quelles sont les catégories ?"
"Budget total"
"Aide"
```

### Test
```
http://localhost/kernel/test_chatbot.php
```

---

## 📧 Service 2 : Mailing

### Accès Direct
```
http://localhost/kernel/view/FrontOffice/mailing.php
```

### Fonctionnalités
✅ Notification nouvelle actualité  
✅ Notification mise à jour  
✅ Digest hebdomadaire  
✅ Templates HTML professionnels  
✅ Mode simulation (logs)  

### Mode Simulation
Les emails sont enregistrés dans :
```
logs/emails_sent.log
```

### Test
```
http://localhost/kernel/test_mailing.php
```

---

## 📁 Structure des Fichiers

```
kernel/
├── services/
│   ├── ChatbotService.php          # Service chatbot
│   └── MailingService.php          # Service mailing
│
├── view/FrontOffice/
│   ├── chatbot.php                 # Interface chatbot
│   ├── mailing.php                 # Interface mailing
│   ├── services.php                # Vue d'ensemble
│   ├── modifierActualite.php       # ✨ NOUVEAU
│   └── supprimerActualite.php      # ✨ NOUVEAU
│
├── api/
│   └── chatbot.php                 # API REST chatbot
│
├── logs/
│   └── emails_sent.log             # Log des emails
│
├── test_chatbot.php                # Tests chatbot
├── test_mailing.php                # Tests mailing
├── index_services.html             # Page d'accueil services
├── SERVICES_README.md              # Documentation complète
└── GUIDE_SERVICES.md               # Ce fichier
```

---

## 🔗 Navigation Complète

### Gestion des Actualités (FrontOffice)
- ✅ **Ajouter** : `ajouterActualite.php`
- ✅ **Afficher** : `listeActualite.php`
- ✅ **Rechercher** : `searchActualites.php`
- ✅ **Modifier** : `modifierActualite.php` 
- ✅ **Supprimer** : `supprimerActualite.php` 

### Services
- 🤖 **Chatbot** : `chatbot.php`
- 📧 **Mailing** : `mailing.php`
- 📚 **Vue d'ensemble** : `services.php`

### Bouton Switch
Toutes les pages incluent le bouton **Front/Back Office** pour basculer facilement.

---

## 💡 Utilisation Rapide

### 1. Tester le Chatbot
1. Ouvrir `chatbot.php`
2. Poser une question
3. Voir la réponse instantanée

### 2. Envoyer des Notifications
1. Ouvrir `mailing.php`
2. Sélectionner une actualité
3. Cliquer sur "Envoyer"
4. Vérifier les logs

### 3. Gérer les Actualités
1. Créer une actualité
2. La modifier si besoin
3. La supprimer si nécessaire
4. Notifier automatiquement les abonnés

---

## 🔧 Configuration Production

### Pour envoyer de vrais emails :

1. Installer PHPMailer :
```bash
composer require phpmailer/phpmailer
```

2. Configurer SMTP dans `services/MailingService.php`

3. Décommenter la ligne d'envoi réel

Voir `SERVICES_README.md` pour les détails.

---

## 📊 Statistiques

### Chatbot
- 8+ commandes disponibles
- Réponses en temps réel
- Basé sur données réelles

### Mailing
- 3 types de notifications
- Templates HTML modernes
- Mode simulation inclus

---

## 🎨 Personnalisation

### Ajouter une commande au Chatbot
Éditer `services/ChatbotService.php` :
```php
if ($this->containsKeywords($question, ['votre', 'mot-clé'])) {
    return $this->votreMethode();
}
```

### Personnaliser les emails
Éditer `services/MailingService.php` :
```php
private function buildEmailTemplate(...) {
    // Modifier le HTML
}
```

---

## 🐛 Dépannage

### Chatbot ne répond pas
- Vérifier la connexion DB
- Vérifier que des projets existent
- Consulter les logs PHP

### Emails non envoyés
- Vérifier `logs/emails_sent.log`
- Vérifier la config SMTP (production)
- Vérifier les emails utilisateurs

---

## 📚 Documentation

- **Guide complet** : `SERVICES_README.md`
- **Tests** : `test_chatbot.php` et `test_mailing.php`
- **API** : `api/chatbot.php`

---

## ✨ Nouveautés

### Actualités FrontOffice
✅ Modification d'actualités  
✅ Suppression avec confirmation  
✅ Boutons d'action sur chaque carte  
✅ Messages de confirmation  

### Services Métiers
✅ Chatbot intelligent  
✅ Système de mailing  
✅ API REST  
✅ Tests automatisés  

---

## 🎯 Prochaines Étapes

1. **Tester les services** avec les fichiers de test
2. **Explorer l'interface** via `index_services.html`
3. **Lire la documentation** dans `SERVICES_README.md`
4. **Personnaliser** selon vos besoins

---

## 📞 Liens Utiles

- 🏠 Accueil Services : `index_services.html`
- 🤖 Chatbot : `view/FrontOffice/chatbot.php`
- 📧 Mailing : `view/FrontOffice/mailing.php`
- 📚 Documentation : `SERVICES_README.md`
- 🧪 Tests : `test_chatbot.php` & `test_mailing.php`

---

**Version :** 1.0  
**Date :** Décembre 2024  
**Statut :** ✅ Opérationnel
