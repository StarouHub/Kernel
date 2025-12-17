// assets/js/priority.js
/**
 * Priority Manager - Module JavaScript
 * Gère les interactions AJAX pour la gestion des priorités
 */

class PriorityManagerJS {
    constructor() {
        this.baseUrl = window.location.origin + window.location.pathname;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.init();
    }

    init() {
        console.log('Priority Manager JS initialisé');
        this.bindEvents();
        this.checkCriticalReclamations();
        this.startAutoRefresh();
    }

    /**
     * Lie les événements DOM
     */
    bindEvents() {
        // Boutons d'escalade de priorité
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-escalate-priority]')) {
                const button = e.target.closest('[data-escalate-priority]');
                const reclamationId = button.getAttribute('data-escalate-priority');
                const currentPriority = button.getAttribute('data-current-priority');
                this.escalatePriority(reclamationId, currentPriority);
            }

            // Bouton d'analyse IA améliorée
            if (e.target.closest('[data-ai-analyze]')) {
                const button = e.target.closest('[data-ai-analyze]');
                const reclamationId = button.getAttribute('data-ai-analyze');
                this.analyzeWithAI(reclamationId);
            }

            // Bouton de mise à jour de priorité
            if (e.target.closest('[data-update-priority]')) {
                const button = e.target.closest('[data-update-priority]');
                const reclamationId = button.getAttribute('data-update-priority');
                this.updatePriority(reclamationId);
            }
        });

        // Filtre par priorité
        const priorityFilter = document.getElementById('filterPriority');
        if (priorityFilter) {
            priorityFilter.addEventListener('change', (e) => {
                this.applyPriorityFilter(e.target.value);
            });
        }

        // Auto-refresh toggle
        const autoRefreshToggle = document.getElementById('toggleAutoRefresh');
        if (autoRefreshToggle) {
            autoRefreshToggle.addEventListener('change', (e) => {
                this.toggleAutoRefresh(e.target.checked);
            });
        }

        // Analyse en batch
        const batchAnalyzeBtn = document.getElementById('batchAnalyzeBtn');
        if (batchAnalyzeBtn) {
            batchAnalyzeBtn.addEventListener('click', () => {
                this.analyzeBatch();
            });
        }

        // Test d'analyse
        const testAnalyzeForm = document.getElementById('testAnalyzeForm');
        if (testAnalyzeForm) {
            testAnalyzeForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.testPriorityAnalysis();
            });
        }
    }

    /**
     * Escalade une priorité
     */
    async escalatePriority(reclamationId, currentPriority) {
        if (!confirm(`Escalader la priorité de "${currentPriority}" vers le niveau supérieur ?`)) {
            return;
        }

        const nextPriority = this.getNextPriority(currentPriority);
        
        try {
            const response = await fetch('../../controller/ReclamationController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': this.csrfToken
                },
                body: JSON.stringify({
                    action: 'escalate_priority',
                    reclamation_id: reclamationId,
                    new_priority: nextPriority
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('success', 'Priorité escaladée avec succès !');
                this.updatePriorityBadge(reclamationId, nextPriority);
                this.logActivity(`Priorité escaladée: ${currentPriority} → ${nextPriority}`);
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error escalating priority:', error);
            this.showNotification('error', 'Erreur lors de l\'escalade de priorité');
        }
    }

    /**
     * Analyse avec IA améliorée
     */
    async analyzeWithAI(reclamationId) {
        const button = document.querySelector(`[data-ai-analyze="${reclamationId}"]`);
        const originalText = button?.innerHTML;
        
        if (button) {
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Analyse en cours...';
            button.disabled = true;
        }

        try {
            const response = await fetch('../../controller/ReclamationController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': this.csrfToken
                },
                body: JSON.stringify({
                    action: 'analyze_with_ai',
                    reclamation_id: reclamationId
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showAIResultModal(result.analysis, reclamationId);
                this.logActivity(`Analyse IA effectuée pour la réclamation #${reclamationId}`);
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error analyzing with AI:', error);
            this.showNotification('error', 'Erreur lors de l\'analyse IA');
        } finally {
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    }

    /**
     * Met à jour la priorité via PriorityManager
     */
    async updatePriority(reclamationId) {
        try {
            const response = await fetch(`../../prioritymanager.php?action=update_priority&id=${reclamationId}`);
            const result = await response.json();

            if (result.success) {
                this.showNotification('success', result.message);
                if (result.analysis) {
                    this.updatePriorityDisplay(reclamationId, result.analysis);
                }
                this.logActivity(`Priorité mise à jour via PriorityManager pour #${reclamationId}`);
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error updating priority:', error);
            this.showNotification('error', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Analyse en batch des réclamations
     */
    async analyzeBatch() {
        const button = document.getElementById('batchAnalyzeBtn');
        const originalText = button?.innerHTML;
        
        if (button) {
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Analyse en cours...';
            button.disabled = true;
        }

        try {
            const response = await fetch('../../prioritymanager.php?action=analyze_batch');
            const result = await response.json();

            if (result.success) {
                this.showBatchResults(result);
                this.updateBatchStats(result);
                this.logActivity(`Analyse batch effectuée: ${result.total_analyzed} réclamations analysées`);
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error in batch analysis:', error);
            this.showNotification('error', 'Erreur lors de l\'analyse batch');
        } finally {
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    }

    /**
     * Test d'analyse de priorité
     */
    async testPriorityAnalysis() {
        const title = document.getElementById('testTitle')?.value;
        const description = document.getElementById('testDescription')?.value;

        if (!title || !description) {
            this.showNotification('warning', 'Veuillez remplir le titre et la description');
            return;
        }

        try {
            const response = await fetch('../../prioritymanager.php?action=test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}`
            });

            const result = await response.json();

            if (result.success) {
                this.displayTestResult(result.analysis);
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error testing priority analysis:', error);
            this.showNotification('error', 'Erreur lors du test d\'analyse');
        }
    }

    /**
     * Vérifie les réclamations critiques
     */
    async checkCriticalReclamations() {
        try {
            const response = await fetch('../../controller/ReclamationController.php?action=get_critical_reclamations');
            const result = await response.json();

            if (result.success && result.critical_count > 0) {
                this.showCriticalAlert(result.critical_count, result.reclamations);
            }
        } catch (error) {
            console.error('Error checking critical reclamations:', error);
        }
    }

    /**
     * Applique un filtre par priorité
     */
    applyPriorityFilter(priority) {
        const rows = document.querySelectorAll('tr[data-priority]');
        rows.forEach(row => {
            if (!priority || row.getAttribute('data-priority') === priority) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Mettre à jour le compteur
        const visibleCount = document.querySelectorAll('tr[data-priority]:not([style*="display: none"])').length;
        this.updateCounter(visibleCount);
        
        this.logActivity(`Filtre appliqué: ${priority || 'Toutes les priorités'}`);
    }

    /**
     * Affiche les résultats de l'analyse batch
     */
    showBatchResults(data) {
        const modal = this.createModal('Résultats de l\'analyse batch', 'batch-results-modal');
        
        let html = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> ${data.total_analyzed} réclamation(s) analysée(s)
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3>${data.suggested_updates}</h3>
                            <p class="text-muted">Mises à jour suggérées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3>${data.actual_updates}</h3>
                            <p class="text-muted">Mises à jour appliquées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3>${Math.round((data.suggested_updates / data.total_analyzed) * 100)}%</h3>
                            <p class="text-muted">Taux de modification</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (data.results && data.results.length > 0) {
            html += `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Priorité actuelle</th>
                                <th>Priorité suggérée</th>
                                <th>Score</th>
                                <th>Confiance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.results.forEach(result => {
                const shouldUpdate = result.should_update && result.confidence > 0.7;
                html += `
                    <tr class="${shouldUpdate ? 'table-warning' : ''}">
                        <td>#${result.id}</td>
                        <td><span class="badge bg-${this.getPriorityColor(result.current_priority)}">
                            ${result.current_priority}
                        </span></td>
                        <td><span class="badge bg-${this.getPriorityColor(result.suggested_priority)}">
                            ${result.suggested_priority}
                        </span></td>
                        <td>${result.score}%</td>
                        <td>${Math.round(result.confidence * 100)}%</td>
                        <td>
                            ${shouldUpdate ? 
                                `<button class="btn btn-sm btn-success" onclick="priorityManager.applyPriorityUpdate(${result.id})">
                                    <i class="bi bi-check"></i> Appliquer
                                </button>` :
                                '<span class="text-muted">Aucun changement</span>'
                            }
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }

        modal.innerHTML += html;
        document.body.appendChild(modal);
        bootstrap.Modal.getInstance(modal)?.show();
    }

    /**
     * Applique une mise à jour de priorité
     */
    async applyPriorityUpdate(reclamationId) {
        try {
            const response = await fetch(`../../prioritymanager.php?action=update_priority&id=${reclamationId}`);
            const result = await response.json();

            if (result.success) {
                this.showNotification('success', `Priorité mise à jour pour #${reclamationId}`);
                // Mettre à jour la ligne dans le tableau
                this.updateRowPriority(reclamationId, result.analysis);
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error applying priority update:', error);
            this.showNotification('error', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Affiche le résultat du test
     */
    displayTestResult(analysis) {
        const modal = this.createModal('Résultat de l\'analyse de priorité', 'test-result-modal');
        
        const priorityColor = this.getPriorityColor(analysis.priority);
        const confidencePercent = Math.round(analysis.confidence * 100);
        
        modal.innerHTML = `
            <div class="text-center mb-4">
                <div class="priority-badge-large badge bg-${priorityColor} p-3 mb-3">
                    <h2 class="mb-0">${analysis.priority.toUpperCase()}</h2>
                </div>
                
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar bg-${priorityColor}" role="progressbar" 
                         style="width: ${analysis.score}%" aria-valuenow="${analysis.score}" 
                         aria-valuemin="0" aria-valuemax="100">
                        <strong>${analysis.score}%</strong>
                    </div>
                </div>
                
                <p class="lead">${analysis.reason}</p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="bi bi-shield-check"></i> Confiance</h6>
                            <h3>${confidencePercent}%</h3>
                            <small class="text-muted">Fiabilité de l'analyse</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6><i class="bi bi-emoji-${analysis.sentiment_score < 0 ? 'frown' : 'smile'}"></i> Sentiment</h6>
                            <h3 class="${analysis.sentiment_score < 0 ? 'text-danger' : 'text-success'}">
                                ${analysis.sentiment_score}
                            </h3>
                            <small class="text-muted">Ton du message</small>
                        </div>
                    </div>
                </div>
            </div>
            
            ${analysis.keywords && analysis.keywords.length > 0 ? `
                <h5><i class="bi bi-search"></i> Mots-clés détectés</h5>
                <div class="mb-4">
                    ${analysis.keywords.map(keyword => 
                        `<span class="badge bg-secondary me-1 mb-1">${keyword}</span>`
                    ).join('')}
                </div>
            ` : ''}
            
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Détails de l'analyse</h6>
                </div>
                <div class="card-body">
                    <pre class="mb-0" style="font-size: 0.8rem;">${JSON.stringify(analysis.analysis_details, null, 2)}</pre>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        bootstrap.Modal.getInstance(modal)?.show();
    }

    /**
     * Affiche une alerte pour les réclamations critiques
     */
    showCriticalAlert(count, reclamations = []) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            animation: pulse 2s infinite;
        `;
        
        let html = `
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <h5><i class="bi bi-exclamation-triangle"></i> ${count} Réclamation(s) critique(s) !</h5>
            <p class="mb-2">Action immédiate requise</p>
        `;
        
        if (reclamations.length > 0) {
            html += '<ul class="mb-0">';
            reclamations.slice(0, 3).forEach(r => {
                html += `<li><a href="detailadmin.php?id=${r.id}" class="alert-link">#${r.id}: ${r.titre.substring(0, 50)}...</a></li>`;
            });
            if (reclamations.length > 3) {
                html += `<li>... et ${reclamations.length - 3} autres</li>`;
            }
            html += '</ul>';
        }
        
        html += `
            <div class="mt-2">
                <a href="gestionreclamations.php?filter_priority=critique" class="btn btn-sm btn-outline-light">
                    Voir toutes les critiques
                </a>
            </div>
        `;
        
        alert.innerHTML = html;
        document.body.appendChild(alert);
        
        // Auto-dismiss après 10 secondes
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 10000);
    }

    /**
     * Affiche les résultats de l'analyse IA
     */
    showAIResultModal(analysis, reclamationId) {
        const modal = this.createModal('Analyse IA détaillée', 'ai-analysis-modal');
        
        modal.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-robot"></i> Analyse IA pour la réclamation #${reclamationId}
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6>Priorité suggérée</h6>
                            <h3 class="text-${this.getPriorityColor(analysis.final_priority.priority)}">
                                ${analysis.final_priority.priority.toUpperCase()}
                            </h3>
                            <small class="text-muted">Score: ${analysis.final_priority.score}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6>Synergie IA/PM</h6>
                            <h3>${Math.round(analysis.final_priority.synergy_score * 100)}%</h3>
                            <small class="text-muted">Accord entre les systèmes</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5><i class="bi bi-lightbulb"></i> Suggestions</h5>
            <ul>
                ${analysis.suggestions.map(s => `<li>${s}</li>`).join('')}
            </ul>
            
            <div class="mt-4">
                <button class="btn btn-primary" onclick="priorityManager.applyAIResult(${reclamationId}, '${analysis.final_priority.priority}')">
                    <i class="bi bi-check-circle"></i> Appliquer cette priorité
                </button>
            </div>
        `;
        
        document.body.appendChild(modal);
        bootstrap.Modal.getInstance(modal)?.show();
    }

    /**
     * Applique le résultat de l'analyse IA
     */
    async applyAIResult(reclamationId, priority) {
        try {
            const response = await fetch('../../controller/ReclamationController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': this.csrfToken
                },
                body: JSON.stringify({
                    action: 'update_priorite',
                    id: reclamationId,
                    priorite: priority
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('success', 'Priorité mise à jour avec l\'analyse IA');
                this.updatePriorityBadge(reclamationId, priority);
                bootstrap.Modal.getInstance(document.getElementById('ai-analysis-modal'))?.hide();
            } else {
                this.showNotification('error', 'Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Error applying AI result:', error);
            this.showNotification('error', 'Erreur lors de l\'application');
        }
    }

    /**
     * Met à jour l'affichage d'une priorité
     */
    updatePriorityDisplay(reclamationId, analysis) {
        // Mettre à jour le badge
        const badge = document.querySelector(`[data-priority-badge="${reclamationId}"]`);
        if (badge) {
            const priorityClass = `priority-${analysis.priority}`;
            badge.className = `badge ${priorityClass} priority-badge`;
            badge.innerHTML = `
                ${analysis.priority}
                <span class="priority-score-badge">${analysis.score}%</span>
            `;
        }

        // Mettre à jour la raison
        const reasonElement = document.querySelector(`[data-priority-reason="${reclamationId}"]`);
        if (reasonElement && analysis.reason) {
            reasonElement.textContent = analysis.reason;
            reasonElement.title = analysis.reason;
        }

        // Mettre à jour l'attribut data-priority
        const row = document.querySelector(`tr[data-reclamation-id="${reclamationId}"]`);
        if (row) {
            row.setAttribute('data-priority', analysis.priority);
        }

        // Animation de mise à jour
        this.animateUpdate(reclamationId);
    }

    /**
     * Met à jour un badge de priorité
     */
    updatePriorityBadge(reclamationId, newPriority) {
        const badge = document.querySelector(`[data-priority-badge="${reclamationId}"]`);
        if (badge) {
            const priorityClass = `priority-${newPriority}`;
            badge.className = `badge ${priorityClass} priority-badge`;
            badge.textContent = newPriority;
            badge.setAttribute('data-current-priority', newPriority);
        }

        // Mettre à jour le bouton d'escalade
        const escalateButton = document.querySelector(`[data-escalate-priority="${reclamationId}"]`);
        if (escalateButton) {
            escalateButton.setAttribute('data-current-priority', newPriority);
            if (newPriority === 'critique') {
                escalateButton.style.display = 'none';
            }
        }

        this.animateUpdate(reclamationId);
    }

    /**
     * Met à jour une ligne de tableau
     */
    updateRowPriority(reclamationId, analysis) {
        const row = document.querySelector(`tr[data-reclamation-id="${reclamationId}"]`);
        if (!row) return;

        // Mettre à jour les cellules
        const priorityCell = row.querySelector('.priority-cell');
        if (priorityCell) {
            priorityCell.innerHTML = `
                <span class="badge priority-${analysis.priority} priority-badge">
                    ${analysis.priority}
                    <span class="priority-score-badge">${analysis.score}%</span>
                </span>
                <small class="text-muted d-block mt-1">
                    <i class="bi bi-info-circle"></i> ${analysis.reason.substring(0, 30)}...
                </small>
            `;
        }

        this.animateUpdate(reclamationId);
    }

    /**
     * Animation de mise à jour
     */
    animateUpdate(reclamationId) {
        const element = document.querySelector(`[data-reclamation-id="${reclamationId}"]`);
        if (element) {
            element.classList.add('priority-updated');
            setTimeout(() => {
                element.classList.remove('priority-updated');
            }, 2000);
        }
    }

    /**
     * Met à jour les statistiques batch
     */
    updateBatchStats(data) {
        const statsElement = document.getElementById('batchStats');
        if (statsElement) {
            statsElement.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3>${data.total_analyzed}</h3>
                                <p class="text-muted">Analysées</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3>${data.suggested_updates}</h3>
                                <p class="text-muted">Modifications suggérées</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3>${data.actual_updates}</h3>
                                <p class="text-muted">Modifications appliquées</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Met à jour le compteur
     */
    updateCounter(count) {
        const counter = document.getElementById('filteredCount');
        if (counter) {
            counter.textContent = count;
        }
    }

    /**
     * Active/désactive l'auto-refresh
     */
    toggleAutoRefresh(enabled) {
        if (enabled) {
            this.autoRefreshInterval = setInterval(() => {
                this.checkCriticalReclamations();
                this.refreshPriorityStats();
            }, 30000); // Toutes les 30 secondes
            this.showNotification('info', 'Auto-refresh activé (30s)');
        } else {
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
                this.autoRefreshInterval = null;
                this.showNotification('info', 'Auto-refresh désactivé');
            }
        }
    }

    /**
     * Rafraîchit les statistiques de priorité
     */
    async refreshPriorityStats() {
        try {
            const response = await fetch('../../controller/ReclamationController.php?action=get_priority_stats');
            const result = await response.json();

            if (result.success) {
                this.updatePriorityStatsDisplay(result.stats);
            }
        } catch (error) {
            console.error('Error refreshing priority stats:', error);
        }
    }

    /**
     * Met à jour l'affichage des statistiques
     */
    updatePriorityStatsDisplay(stats) {
        const container = document.getElementById('priorityStatsContainer');
        if (!container) return;

        let html = '<div class="row">';
        
        stats.forEach(stat => {
            const color = this.getPriorityColor(stat.priorite);
            const percentage = stat.percentage || 0;
            
            html += `
                <div class="col-md-3 mb-3">
                    <div class="card border-${color}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-${color} mb-1">${stat.priorite}</h6>
                                    <h3 class="mb-0">${stat.total}</h3>
                                </div>
                                <i class="bi bi-${this.getPriorityIcon(stat.priorite)} text-${color}" style="font-size: 2rem;"></i>
                            </div>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-${color}" style="width: ${percentage}%"></div>
                            </div>
                            <small class="text-muted">${percentage}% du total</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    /**
     * Démarre l'auto-refresh
     */
    startAutoRefresh() {
        // Démarrer après 1 minute, puis toutes les 5 minutes
        setTimeout(() => {
            this.refreshPriorityStats();
            this.autoRefreshInterval = setInterval(() => {
                this.refreshPriorityStats();
            }, 300000); // Toutes les 5 minutes
        }, 60000);
    }

    /**
     * Crée une modal Bootstrap
     */
    createModal(title, id) {
        // Supprimer les modales existantes avec le même ID
        const existingModal = document.getElementById(id);
        if (existingModal) {
            existingModal.remove();
        }

        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = id;
        modal.tabIndex = -1;
        modal.setAttribute('aria-labelledby', `${id}Label`);
        modal.setAttribute('aria-hidden', 'true');
        
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${id}Label">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Le contenu sera ajouté dynamiquement -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        `;
        
        return modal;
    }

    /**
     * Affiche une notification
     */
    showNotification(type, message) {
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${this.getNotificationIcon(type)} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    /**
     * Crée un conteneur pour les toasts
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        document.body.appendChild(container);
        return container;
    }

    /**
     * Journalise une activité
     */
    logActivity(message) {
        console.log(`[PriorityManager] ${message}`);
        
        // Envoyer au serveur si nécessaire
        if (typeof logActivity === 'function') {
            logActivity('priority_manager', { message: message });
        }
    }

    /**
     * Obtient la priorité suivante dans la hiérarchie
     */
    getNextPriority(currentPriority) {
        const hierarchy = {
            'basse': 'normale',
            'normale': 'haute', 
            'haute': 'critique',
            'critique': 'critique'
        };
        return hierarchy[currentPriority] || currentPriority;
    }

    /**
     * Obtient la couleur Bootstrap pour une priorité
     */
    getPriorityColor(priority) {
        switch (priority) {
            case 'critique': return 'danger';
            case 'haute': return 'warning';
            case 'normale': return 'primary';
            case 'basse': return 'secondary';
            default: return 'secondary';
        }
    }

    /**
     * Obtient l'icône pour une priorité
     */
    getPriorityIcon(priority) {
        switch (priority) {
            case 'critique': return 'exclamation-triangle';
            case 'haute': return 'exclamation-circle';
            case 'normale': return 'clock';
            case 'basse': return 'check-circle';
            default: return 'question-circle';
        }
    }

    /**
     * Obtient l'icône pour une notification
     */
    getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'check-circle';
            case 'error': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            case 'info': return 'info-circle';
            default: return 'bell';
        }
    }
}

// Initialiser le Priority Manager
document.addEventListener('DOMContentLoaded', function() {
    window.priorityManager = new PriorityManagerJS();
    
    // Ajouter les styles CSS
    const styles = `
        .priority-updated {
            animation: priorityUpdate 2s ease;
        }
        
        @keyframes priorityUpdate {
            0% { background-color: transparent; }
            50% { background-color: rgba(13, 110, 253, 0.1); }
            100% { background-color: transparent; }
        }
        
        .priority-badge-large {
            font-size: 1.2rem;
            min-width: 150px;
            display: inline-block;
        }
        
        .priority-score-badge {
            font-size: 0.7rem;
            padding: 0.1rem 0.3rem;
            border-radius: 10px;
            background: rgba(0,0,0,0.2);
            margin-left: 5px;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = styles;
    document.head.appendChild(styleSheet);
});

// Exposer pour utilisation globale
window.PriorityManagerJS = PriorityManagerJS;