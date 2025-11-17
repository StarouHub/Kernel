// discussion.js - À ajouter à disscussion.html

document.addEventListener('DOMContentLoaded', () => {
  // Récupérer le sujet sélectionné depuis localStorage
  const selectedTopic = JSON.parse(localStorage.getItem('selectedTopic'));
  
  if (!selectedTopic) {
    // Si aucun sujet n'est sélectionné, rediriger vers le forum
    window.location.href = 'forum.html';
    return;
  }

  // Initialiser les réponses si elles n'existent pas
  if (!selectedTopic.replyList) {
    selectedTopic.replyList = [];
  }

  // Mettre à jour le breadcrumb
  const breadcrumb = document.querySelector('.breadcrumb');
  if (breadcrumb) {
    breadcrumb.innerHTML = `
      <a href="index.html">Accueil</a> » 
      <a href="forum.html">Forum</a> » 
      <span>${selectedTopic.title}</span>
    `;
  }

  // Mettre à jour le titre de la page
  document.title = `${selectedTopic.title} - Kernel`;

  // Mettre à jour le titre de la discussion
  const discussionTitle = document.querySelector('.discussion-title');
  if (discussionTitle) {
    discussionTitle.textContent = selectedTopic.title;
  }

  // Mettre à jour les métadonnées
  const discussionMeta = document.querySelector('.discussion-meta');
  if (discussionMeta) {
    discussionMeta.innerHTML = `
      <div class="meta-item">Publié <strong>${selectedTopic.time || 'récemment'}</strong></div>
      <div class="meta-item">Par <strong>${selectedTopic.author || 'Anonyme'}</strong></div>
      <div class="meta-item">
        ${(selectedTopic.tags || []).map(tag => `<span class="tag">${tag}</span>`).join('')}
      </div>
    `;
  }

  // Mettre à jour l'avatar de l'auteur
  const userAvatar = document.querySelector('.user-avatar');
  if (userAvatar && selectedTopic.author) {
    const initials = selectedTopic.author.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    userAvatar.textContent = initials;
  }

  // Mettre à jour le nom de l'auteur
  const userName = document.querySelector('.user-name');
  if (userName) {
    userName.innerHTML = `${selectedTopic.author || 'Anonyme'} <span class="user-badge">Auteur</span>`;
  }

  // Mettre à jour le temps
  const postTime = document.querySelector('.post-time');
  if (postTime) {
    postTime.textContent = selectedTopic.time || 'Récemment';
  }

  // Mettre à jour le contenu du post
  const postContent = document.querySelector('.post-content');
  if (postContent) {
    // Afficher la description complète du sujet
    const content = selectedTopic.fullContent || selectedTopic.content || selectedTopic.description || 'Aucun contenu disponible.';
    // Convertir les retours à la ligne en paragraphes
    const formattedContent = content.split('\n').map(p => p.trim()).filter(p => p).map(p => `<p>${p}</p>`).join('');
    postContent.innerHTML = formattedContent || `<p>${content}</p>`;
  }

  // Gérer les boutons d'action
  const likeBtn = document.querySelector('.action-btn');
  if (likeBtn) {
    // Afficher le nombre actuel de likes
    if (selectedTopic.likes > 0) {
      likeBtn.innerHTML = `<i class="bi bi-hand-thumbs-up"></i> ${selectedTopic.likes} J'aime`;
    }
    
    likeBtn.onclick = () => {
      // Incrémenter les likes
      selectedTopic.likes = (selectedTopic.likes || 0) + 1;
      likeBtn.classList.add('liked');
      likeBtn.innerHTML = `<i class="bi bi-hand-thumbs-up-fill"></i> ${selectedTopic.likes} J'aime`;
      
      // Mettre à jour dans localStorage
      updateTopicInStorage(selectedTopic);
    };
  }

  // Afficher les réponses existantes
  renderReplies();

  // Gérer le formulaire de réponse
  const replyForm = document.querySelector('.reply-form');
  if (replyForm) {
    const submitBtn = replyForm.querySelector('.btn-submit');
    const textarea = replyForm.querySelector('textarea');
    
    if (submitBtn) {
      submitBtn.onclick = () => {
        const replyText = textarea.value.trim();
        if (!replyText) {
          alert('Veuillez saisir une réponse');
          return;
        }

        // Créer une nouvelle réponse
        const newReply = {
          id: Date.now(),
          author: 'Vous',
          content: replyText,
          time: 'À l\'instant',
          likes: 0
        };

        // Ajouter la réponse à la liste
        selectedTopic.replyList.push(newReply);
        selectedTopic.replies = selectedTopic.replyList.length;
        
        // Mettre à jour dans localStorage
        updateTopicInStorage(selectedTopic);
        localStorage.setItem('selectedTopic', JSON.stringify(selectedTopic));

        // Vider le textarea
        textarea.value = '';
        
        // Réafficher les réponses
        renderReplies();
        
        // Message de confirmation
        showNotification('Votre réponse a été publiée avec succès !');
      };
    }
  }

  // Incrémenter les vues
  selectedTopic.views = (selectedTopic.views || 0) + 1;
  updateTopicInStorage(selectedTopic);
});

// Fonction pour mettre à jour un sujet dans localStorage
function updateTopicInStorage(topic) {
  let topics = JSON.parse(localStorage.getItem('forumTopics')) || [];
  const index = topics.findIndex(t => t.id === topic.id);
  if (index !== -1) {
    topics[index] = topic;
    localStorage.setItem('forumTopics', JSON.stringify(topics));
  }
}

// Fonction pour afficher les réponses
function renderReplies() {
  const selectedTopic = JSON.parse(localStorage.getItem('selectedTopic'));
  const repliesContainer = document.getElementById('replies-container');
  
  if (!repliesContainer) return;
  
  repliesContainer.innerHTML = '';
  
  if (!selectedTopic.replyList || selectedTopic.replyList.length === 0) {
    repliesContainer.innerHTML = '<p class="text-muted text-center py-4">Aucune réponse pour le moment. Soyez le premier à répondre !</p>';
    return;
  }

  selectedTopic.replyList.forEach(reply => {
    const replyCard = document.createElement('div');
    replyCard.className = 'reply-card';
    
    const initials = reply.author.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    
    replyCard.innerHTML = `
      <div class="reply-header">
        <div class="reply-avatar">${initials}</div>
        <div class="user-info">
          <div class="user-name">${reply.author}</div>
          <div class="post-time">${reply.time}</div>
        </div>
      </div>
      <div class="post-content">
        <p>${reply.content}</p>
      </div>
      <div class="post-actions">
        <button class="action-btn reply-like-btn" data-reply-id="${reply.id}">
          <i class="bi bi-hand-thumbs-up"></i> ${reply.likes > 0 ? reply.likes + ' ' : ''}J'aime
        </button>
      </div>
    `;
    
    repliesContainer.appendChild(replyCard);
  });

  // Ajouter les événements pour les likes des réponses
  document.querySelectorAll('.reply-like-btn').forEach(btn => {
    btn.onclick = function() {
      const replyId = parseInt(this.dataset.replyId);
      const reply = selectedTopic.replyList.find(r => r.id === replyId);
      if (reply) {
        reply.likes = (reply.likes || 0) + 1;
        this.classList.add('liked');
        this.innerHTML = `<i class="bi bi-hand-thumbs-up-fill"></i> ${reply.likes} J'aime`;
        updateTopicInStorage(selectedTopic);
        localStorage.setItem('selectedTopic', JSON.stringify(selectedTopic));
      }
    };
  });
}

// Fonction pour afficher une notification
function showNotification(message) {
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed;
    top: 100px;
    right: 20px;
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    padding: 15px 25px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: slideIn 0.3s ease;
  `;
  notification.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}