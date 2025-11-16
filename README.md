# Kernel
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagrammes UML - Kernel (Mis à Jour)</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f5f5;
      padding: 40px 20px;
      max-width: 1400px;
      margin: 0 auto;
    }
    
    h1 {
      text-align: center;
      color: #2563EB;
      font-size: 36px;
      margin-bottom: 10px;
    }
    
    .subtitle {
      text-align: center;
      color: #6B7280;
      margin-bottom: 40px;
      font-size: 18px;
    }
    
    .diagram-section {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    
    h2 {
      color: #1F2937;
      border-bottom: 3px solid #2563EB;
      padding-bottom: 10px;
      margin-bottom: 25px;
      font-size: 28px;
    }
    
    .module-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    
    .module-card {
      background: linear-gradient(135deg, #2563EB, #7C3AED);
      color: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }
    
    .module-card h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .module-card .member {
      background: rgba(255,255,255,0.2);
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 14px;
      display: inline-block;
      margin-bottom: 10px;
    }
    
    .module-card .entities {
      background: rgba(255,255,255,0.1);
      padding: 15px;
      border-radius: 8px;
      margin-top: 10px;
    }
    
    .entity-list {
      list-style: none;
      padding: 0;
      margin: 10px 0 0 0;
    }
    
    .entity-list li {
      padding: 5px 0;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .entity-list li:last-child {
      border-bottom: none;
    }
    
    .use-case-diagram {
      background: linear-gradient(to bottom, #EFF6FF, #DBEAFE);
      border: 2px solid #2563EB;
      border-radius: 15px;
      padding: 30px;
      margin: 20px 0;
    }
    
    .actor {
      background: white;
      border: 2px solid #F59E0B;
      border-radius: 50%;
      width: 80px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 10px auto;
      font-weight: bold;
      color: #F59E0B;
      box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
    }
    
    .use-cases {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }
    
    .use-case {
      background: white;
      border: 2px solid #10B981;
      border-radius: 30px;
      padding: 15px 20px;
      text-align: center;
      font-weight: 500;
      color: #065F46;
      box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
    }
    
    .legend {
      background: #FEF3C7;
      border-left: 4px solid #F59E0B;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
    }
    
    .legend strong {
      color: #92400E;
    }
    
    .pages-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 12px;
      margin: 15px 0;
    }
    
    .class-box {
      border: 2px solid #2563EB;
      border-radius: 8px;
      margin: 20px auto;
      max-width: 450px;
      background: #F9FAFB;
    }
    
    .class-name {
      background: #2563EB;
      color: white;
      padding: 12px;
      text-align: center;
      font-weight: bold;
      font-size: 18px;
      border-radius: 6px 6px 0 0;
    }
    
    .class-attributes, .class-methods {
      padding: 15px;
      border-top: 2px solid #2563EB;
    }
    
    .class-attributes div, .class-methods div {
      padding: 5px 0;
      color: #1F2937;
    }
    
    .relationship {
      text-align: center;
      font-size: 16px;
      color: #7C3AED;
      font-weight: bold;
      margin: 15px 0;
    }
    
    .update-badge {
      display: inline-block;
      background: #10B981;
      color: white;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: bold;
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <h1>🎯 Projet Kernel - Documentation UML Complète</h1>
  <p class="subtitle">Plateforme d'Innovation Collaborative</p>

  <!-- Répartition des Modules -->
  <div class="diagram-section">
    <h2>📦 Répartition des Modules par Membre</h2>
    
    <div class="module-grid">
      <div class="module-card">
        <h3>👨‍💻 Module 1 : PROJETS</h3>
        <div class="member">Membre : Mohamed</div>
        <div class="entities">
          <strong>Entités :</strong>
          <ul class="entity-list">
            <li>📋 Projet (id, titre, description, budget_requis, statut, date_creation, user_id)</li>
            <li>🏷️ Categorie (id, nom, icone, description)</li>
            <li>🔗 Projet_Categorie (projet_id, categorie_id)</li>
          </ul>
          <strong style="display: block; margin-top: 10px;">Relation :</strong> Many-to-Many
        </div>
        <div style="margin-top: 15px; font-size: 14px;">
          <strong>Pages HTML :</strong>
          <div class="pages-list" style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ projets-list.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ projet-details.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ ajouter-projet.html</div>
          </div>
        </div>
      </div>

      <div class="module-card" style="background: linear-gradient(135deg, #7C3AED, #F59E0B);">
        <h3>💬 Module 2 : FORUM</h3>
        <div class="member">Membre : Ons</div>
        <div class="entities">
          <strong>Entités :</strong>
          <ul class="entity-list">
            <li>💭 Sujet (id, titre, contenu, date_creation, user_id, categorie_id)</li>
            <li>💬 Reponse (id, contenu, date, user_id, sujet_id)</li>
          </ul>
          <strong style="display: block; margin-top: 10px;">Relation :</strong> One-to-Many
        </div>
        <div style="margin-top: 15px; font-size: 14px;">
          <strong>Pages HTML :</strong>
          <div class="pages-list" style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ forum.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ discussion-details.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ ajouter-sujet.html</div>
          </div>
        </div>
      </div>

      <div class="module-card" style="background: linear-gradient(135deg, #10B981, #06B6D4);">
        <h3>🔐 Module 3 : AUTHENTIFICATION</h3>
        <div class="member">Membre : Wissem</div>
        <div class="entities">
          <strong>Entités :</strong>
          <ul class="entity-list">
            <li>👤 Utilisateur (id, nom, prenom, email, password, role, telephone, date_inscription)</li>
            <li>📝 Profil (id, bio, avatar, competences, liens_sociaux, domaine_expertise, user_id)</li>
          </ul>
          <strong style="display: block; margin-top: 10px;">Relation :</strong> One-to-One
        </div>
        <div style="margin-top: 15px; font-size: 14px;">
          <strong>Pages HTML :</strong>
          <div class="pages-list" style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ login.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ register.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ mot-de-passe-oublie.html</div>
          </div>
        </div>
      </div>

      <div class="module-card" style="background: linear-gradient(135deg, #F59E0B, #EF4444);">
        <h3>🎉 Module 4 : ÉVÉNEMENTS</h3>
        <div class="member">Membre : Takwa</div>
        <div class="entities">
          <strong>Entités :</strong>
          <ul class="entity-list">
            <li>📅 Evenement (id, titre, description, date, lieu, capacite, type, user_id)</li>
            <li>✅ Inscription (id, statut, date_inscription, user_id, evenement_id)</li>
          </ul>
          <strong style="display: block; margin-top: 10px;">Relation :</strong> Many-to-Many
        </div>
        <div style="margin-top: 15px; font-size: 14px;">
          <strong>Pages HTML :</strong>
          <div class="pages-list" style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ evenements-list.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ evenement-details.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ creer-evenement.html</div>
          </div>
        </div>
      </div>

      <div class="module-card" style="background: linear-gradient(135deg, #DC2626, #991B1B);">
        <h3>📢 Module 5 : RÉCLAMATIONS <span class="update-badge">NOUVEAU</span></h3>
        <div class="member">Membre : Cyrine</div>
        <div class="entities">
          <strong>Entités :</strong>
          <ul class="entity-list">
            <li>📝 Reclamation (id, objet, description, type, priorite, statut, date_creation, user_id)</li>
            <li>💬 Reponse_Reclamation (id, message, date_reponse, admin_id, reclamation_id)</li>
          </ul>
          <strong style="display: block; margin-top: 10px;">Relation :</strong> One-to-Many
          <div style="margin-top: 10px; padding: 10px; background: rgba(255,255,255,0.15); border-radius: 5px; font-size: 13px;">
            <strong>Types de réclamation :</strong><br>
            • Problème technique<br>
            • Contenu inapproprié<br>
            • Bug/Erreur<br>
            • Suggestion d'amélioration<br>
            • Autre
          </div>
        </div>
        <div style="margin-top: 15px; font-size: 14px;">
          <strong>Pages HTML :</strong>
          <div class="pages-list" style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ reclamations-list.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ creer-reclamation.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ reclamation-details.html</div>
          </div>
        </div>
      </div>

      <div class="module-card" style="background: linear-gradient(135deg, #06B6D4, #3B82F6);">
        <h3>💰 Module 6 : INVESTISSEMENTS</h3>
        <div class="member">Membre : Ali</div>
        <div class="entities">
          <strong>Entités :</strong>
          <ul class="entity-list">
            <li>💵 Investissement (id, montant, date, message, statut, user_id, projet_id)</li>
            <li>🧾 Transaction (id, montant, type, statut, date, investissement_id)</li>
          </ul>
          <strong style="display: block; margin-top: 10px;">Relation :</strong> One-to-Many
        </div>
        <div style="margin-top: 15px; font-size: 14px;">
          <strong>Pages HTML :</strong>
          <div class="pages-list" style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✓ mes-investissements.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ investir.html</div>
            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 5px;">✗ historique-transactions.html</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Diagramme de Cas d'Utilisation -->
  <div class="diagram-section">
    <h2>🎭 Diagramme de Cas d'Utilisation <span class="update-badge">MIS À JOUR</span></h2>
    
    <div class="legend">
      <strong>Système :</strong> Kernel - Plateforme d'Innovation Collaborative<br>
      <strong>Date :</strong> Novembre 2024
    </div>

    <div class="use-case-diagram">
      <h3 style="text-align: center; color: #2563EB; margin-bottom: 30px;">Acteurs du Système</h3>
      
      <div style="display: flex; justify-content: space-around; flex-wrap: wrap; margin-bottom: 40px; gap: 20px;">
        <div style="text-align: center;">
          <div class="actor">👁️</div>
          <strong>Visiteur</strong>
          <p style="font-size: 12px; color: #6B7280;">Non authentifié</p>
        </div>
        <div style="text-align: center;">
          <div class="actor" style="border-color: #10B981; color: #10B981;">👤</div>
          <strong>User (Utilisateur)</strong>
          <p style="font-size: 12px; color: #6B7280;">Connecté - accès de base</p>
        </div>
        <div style="text-align: center;">
          <div class="actor" style="border-color: #2563EB; color: #2563EB;">👨‍💼</div>
          <strong>Innovateur</strong>
          <p style="font-size: 12px; color: #6B7280;">Créateur de projets</p>
        </div>
        <div style="text-align: center;">
          <div class="actor" style="border-color: #F59E0B; color: #F59E0B;">💰</div>
          <strong>Investisseur</strong>
          <p style="font-size: 12px; color: #6B7280;">Finance les projets</p>
        </div>
        <div style="text-align: center;">
          <div class="actor" style="border-color: #7C3AED; color: #7C3AED;">👨‍💻</div>
          <strong>Administrateur</strong>
          <p style="font-size: 12px; color: #6B7280;">Gère la plateforme</p>
        </div>
      </div>

      <h4 style="color: #1F2937; margin-bottom: 20px;">Cas d'Utilisation Principaux</h4>
      
      <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h5 style="color: #6B7280; margin-bottom: 15px;">👁️ Accessible à TOUS (Visiteur Non Connecté)</h5>
        <div class="use-cases">
          <div class="use-case">Consulter les projets</div>
          <div class="use-case">Rechercher des projets</div>
          <div class="use-case">Voir détails projet</div>
          <div class="use-case">Consulter événements</div>
          <div class="use-case">Lire le forum</div>
        </div>
      </div>

      <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h5 style="color: #10B981; margin-bottom: 15px;">👤 User (Utilisateur Authentifié de Base)</h5>
        <div class="use-cases">
          <div class="use-case">S'inscrire</div>
          <div class="use-case">Se connecter</div>
          <div class="use-case">Se déconnecter</div>
          <div class="use-case">Voir son profil</div>
          <div class="use-case">Modifier son profil</div>
          <div class="use-case">Participer au forum</div>
          <div class="use-case">S'inscrire à un événement</div>
          <div class="use-case">Créer une réclamation</div>
          <div class="use-case">Suivre ses réclamations</div>
        </div>
      </div>

      <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h5 style="color: #2563EB; margin-bottom: 15px;">👨‍💼 Innovateur (User + rôle Innovateur)</h5>
        <div class="use-cases">
          <div class="use-case" style="border-color: #2563EB; color: #1E40AF;">Créer un projet</div>
          <div class="use-case" style="border-color: #2563EB; color: #1E40AF;">Modifier son projet</div>
          <div class="use-case" style="border-color: #2563EB; color: #1E40AF;">Supprimer son projet</div>
          <div class="use-case" style="border-color: #2563EB; color: #1E40AF;">Suivre les investissements</div>
          <div class="use-case" style="border-color: #2563EB; color: #1E40AF;">Créer un événement</div>
        </div>
      </div>

      <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <h5 style="color: #F59E0B; margin-bottom: 15px;">💰 Investisseur (User + rôle Investisseur)</h5>
        <div class="use-cases">
          <div class="use-case" style="border-color: #F59E0B; color: #92400E;">Investir dans projet</div>
          <div class="use-case" style="border-color: #F59E0B; color: #92400E;">Suivre ses investissements</div>
          <div class="use-case" style="border-color: #F59E0B; color: #92400E;">Consulter historique</div>
          <div class="use-case" style="border-color: #F59E0B; color: #92400E;">Annuler investissement</div>
        </div>
      </div>

      <div style="background: white; padding: 20px; border-radius: 10px;">
        <h5 style="color: #7C3AED; margin-bottom: 15px;">👨‍💻 Administrateur (User + rôle Admin)</h5>
        <div class="use-cases">
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Modérer les projets</div>
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Valider les événements</div>
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Modérer le forum</div>
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Gérer les utilisateurs</div>
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Traiter les réclamations</div>
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Répondre aux réclamations</div>
          <div class="use-case" style="border-color: #7C3AED; color: #5B21B6;">Voir statistiques</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Nouveau Diagramme de Classes pour Réclamations -->
  <div class="diagram-section">
    <h2>📋 Diagramme de Classes - Module Réclamations <span class="update-badge">NOUVEAU</span></h2>

    <div class="legend">
      <strong>⚠️ Important :</strong> Ce module remplace le système de Badges & Récompenses pour mieux répondre aux besoins de support utilisateur.
    </div>

    <!-- Classe Reclamation -->
    <div class="class-box">
      <div class="class-name" style="background: #DC2626;">📝 Reclamation</div>
      <div class="class-attributes">
        <div><strong>Attributs :</strong></div>
        <div>- id : int (PK)</div>
        <div>- objet : string (titre court)</div>
        <div>- description : text (détails complets)</div>
        <div>- type : enum ('technique', 'contenu', 'bug', 'suggestion', 'autre')</div>
        <div>- priorite : enum ('basse', 'normale', 'haute', 'urgente')</div>
        <div>- statut : enum ('en_attente', 'en_cours', 'resolue', 'fermee')</div>
        <div>- date_creation : datetime</div>
        <div>- date_resolution : datetime (nullable)</div>
        <div>- user_id : int (FK) → Utilisateur</div>
      </div>
      <div class="class-methods">
        <div><strong>Méthodes :</strong></div>
        <div>+ creer()</div>
        <div>+ modifier()</div>
        <div>+ suivreStatut()</div>
        <div>+ annuler()</div>
      </div>
    </div>

    <div class="relationship">⬇️ 1:N (One-to-Many) ⬇️</div>

    <!-- Classe Reponse_Reclamation -->
    <div class="class-box">
      <div class="class-name" style="background: #991B1B;">💬 Reponse_Reclamation</div>
      <div class="class-attributes">
        <div><strong>Attributs :</strong></div>
        <div>- id : int (PK)</div>
        <div>- message : text</div>
        <div>- date_reponse : datetime</div>
        <div>- admin_id : int (FK) → Utilisateur (admin)</div>
        <div>- reclamation_id : int (FK) → Reclamation</div>
      </div>
      <div class="class-methods">
        <div><strong>Méthodes :</strong></div>
        <div>+ ajouter()</div>
        <div>+ notifierUtilisateur()</div>
      </div>
    </div>

    <div style="margin-top: 30px; padding: 20px; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 8px;">
      <h4 style="color: #92400E; margin-top: 0;">📌 Justification du Module Réclamations</h4>
      <p style="color: #78350F; line-height: 1.6; margin: 10px 0;">
        <strong>Pourquoi ce module ?</strong><br>
        Pour une plateforme d'innovation collaborative professionnelle comme Kernel, un système de réclamations est essentiel pour :
      </p>
      <ul style="color: #78350F; line-height: 1.8;">
        <li>🔧 <strong>Support technique :</strong> Permettre aux utilisateurs de signaler des bugs et problèmes</li>
        <li>🛡️ <strong>Modération :</strong> Signaler du contenu inapproprié ou des abus</li>
        <li>💡 <strong>Amélioration continue :</strong> Collecter les suggestions d'amélioration</li>
        <li>📞 <strong>Communication :</strong> Canal direct entre utilisateurs et administration</li>
        <li>📊 <strong>Qualité :</strong> Suivre et résoudre les problèmes de manière traçable</li>
      </ul>
    </div>
  </div>

</body>
</html>
