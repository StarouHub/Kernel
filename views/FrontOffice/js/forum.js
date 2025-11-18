// forum.js

let categories = JSON.parse(localStorage.getItem('forumCategories')) || [];
let topics = JSON.parse(localStorage.getItem('forumTopics')) || [];
let selectedCategory = "Toutes";

const topicsContainer = document.getElementById('topics-container');
const categoryList = document.getElementById('category-list');

function renderCategories() {
  categoryList.innerHTML = '';
  // Ajoute "Toutes"
  const toutes = document.createElement('div');
  toutes.className = 'category-item' + (selectedCategory === "Toutes" ? ' active' : '');
  toutes.innerHTML = '<span><i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i> Toutes</span>';
  toutes.onclick = () => { selectedCategory = "Toutes"; renderCategories(); renderTopics(); }
  categoryList.appendChild(toutes);

  categories.forEach(cat => {
    const catDiv = document.createElement('div');
    catDiv.className = 'category-item' + (selectedCategory === cat.name ? ' active' : '');
    catDiv.innerHTML = `<span><i class="bi bi-circle-fill me-2" style="font-size: 8px; color: ${cat.color};"></i> ${cat.name}</span>
      <span class="category-badge">${topics.filter(t=>t.category === cat.name).length}</span>`;
    catDiv.onclick = () => { selectedCategory = cat.name; renderCategories(); renderTopics(); }
    categoryList.appendChild(catDiv);
  });
}

function renderTopics() {
  topicsContainer.innerHTML = '';
  let filtered = 
    selectedCategory === "Toutes"
    ? topics
    : topics.filter(t => t.category === selectedCategory);

  if (filtered.length === 0) {
    topicsContainer.innerHTML = '<p class="text-muted">Aucun sujet pour cette catégorie.</p>';
    return;
  }
  
  filtered.forEach(t => {
    const card = document.createElement('div');
    card.className = 'topic-card mb-3';
    card.style.cursor = 'pointer';
    
    // Ajouter un événement click pour rediriger vers la page de discussion
    card.onclick = () => {
      // Sauvegarder le sujet sélectionné dans localStorage
      localStorage.setItem('selectedTopic', JSON.stringify(t));
      // Rediriger vers la page de discussion
      window.location.href = 'discussion.html';
    };
    
    card.innerHTML = `
      <div class="topic-header d-flex">
        <div class="user-avatar">${t.author ? (t.author[0] + (t.author.split(" ")[1] ? t.author.split(" ")[1][0]||'' : '')).toUpperCase() : '??'}</div>
        <div class="topic-content ms-3">
          <div class="topic-title">${t.title}</div>
          <div class="topic-meta">Par <strong>${t.author || "Anonyme"}</strong> • dans <strong>${t.category}</strong> • <span class="text-secondary">${t.time || ''}</span></div>
          <div class="topic-description">${t.description}</div>
          <div class="topic-tags">
             ${(t.tags||[]).map(tag=>`<span class="tag">${tag}</span>`).join(' ')}
          </div>
          <div class="topic-stats mt-3">
            <span class="stat-item"><i class="bi bi-chat-left-text"></i> ${t.replies || 0} réponses</span>
            <span class="stat-item"><i class="bi bi-eye"></i> ${t.views || 0} vues</span>
            <span class="stat-item"><i class="bi bi-hand-thumbs-up"></i> ${t.likes || 0} likes</span>
          </div>
        </div>
      </div>
    `;
    topicsContainer.appendChild(card);
  });
}

// Initial rendering
document.addEventListener('DOMContentLoaded', () => {
  renderCategories();
  renderTopics();
});