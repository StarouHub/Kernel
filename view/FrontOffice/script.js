// C:\xampp\htdocs\projetweb\Kernel\view\Frontoffice\script.js

class InvestmentPlatform {
    constructor() {
        this.currentTender = null;
        this.init();
    }
    
    init() {
        console.log('Initializing Investment Platform...');
        this.renderStats();
        this.renderActiveInvestments();
        this.renderTenders();
        this.renderTransactionHistory();
        this.renderTopPerformers();
        this.renderPortfolioChart();  // <-- ADD THIS LINE
        this.setupEventListeners();
        this.setupFormValidation();
    }
    
    renderStats() {
        console.log('Stats loaded from PHP backend');
    }
    
    renderActiveInvestments(filter = 'all') {
        const container = document.getElementById('activeInvestmentsList');
        const investments = window.investmentsData || [];
        
        let filteredInvestments = investments;
        
        if (filter === 'active') {
            filteredInvestments = investments.filter(inv => inv.status === 'active');
        } else if (filter === 'completed') {
            filteredInvestments = investments.filter(inv => inv.status === 'completed');
        } else if (filter === 'profitable') {
            filteredInvestments = investments.filter(inv => parseFloat(inv.roi) > 10);
        }
        
        document.getElementById('activeInvestmentsCount').textContent = filteredInvestments.length;
        
        if (filteredInvestments.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:20px; color:#6B7280;">Aucun investissement trouvé</div>';
            return;
        }
        
        container.innerHTML = filteredInvestments.map(investment => `
            <div class="investment-item">
                <img src="${investment.thumbnail || this.generateThumbnail(investment.sector)}" class="project-thumb" alt="${investment.projectName || investment.project_name}">
                <div class="investment-details">
                    <div class="project-name">${investment.projectName || investment.project_name}</div>
                    <div class="investment-meta">
                        <span><i class="bi bi-calendar"></i> Investi le ${this.formatDate(investment.date || investment.investment_date)}</span>
                        <span><i class="bi bi-percent"></i> ROI: +${investment.roi}%</span>
                        <span><i class="bi bi-tag"></i> ${investment.sector}</span>
                    </div>
                    <span class="investment-status status-${investment.status}">${investment.statusText || this.getStatusText(investment.status)}</span>
                </div>
                <div class="investment-amount">${this.formatCurrency(investment.amount)}</div>
            </div>
        `).join('');
    }
    
    renderTenders() {
        const container = document.getElementById('tendersList');
        const countElement = document.getElementById('tenderCount');
        const tenders = window.tendersData || [];
        
        container.innerHTML = '';
        countElement.textContent = tenders.length;
        
        if (tenders.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:20px; color:#6B7280;">Aucun appel d\'offres trouvé</div>';
            return;
        }
        
        tenders.forEach(tender => {
            const progress = tender.progress || this.calculateProgress(tender);
            const daysLeft = tender.daysLeft || this.calculateDaysLeft(tender.deadline);
            const projName = tender.projectName || tender.project_name;
            const shortP = tender.shortPitch || tender.short_pitch;
            const minInv = tender.minInvestment || tender.min_investment;
            const fundTarget = tender.fundingTarget || tender.funding_target;
            
            const tenderElement = document.createElement('div');
            tenderElement.className = 'investment-item';
            tenderElement.style.justifyContent = 'space-between';
            tenderElement.innerHTML = `
                <div style="display:flex; gap:12px; align-items:center;">
                    <div style="width:64px; height:48px; background:linear-gradient(135deg,#2563EB,#7C3AED); border-radius:8px; color:white; display:flex; align-items:center; justify-content:center; font-weight:700;">
                        ${tender.sector ? tender.sector.slice(0,3).toUpperCase() : 'PRO'}
                    </div>
                    <div style="min-width:200px;">
                        <div style="font-weight:600;">${projName}</div>
                        <div style="font-size:13px; color:#6B7280;">${shortP || 'No description'}</div>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: ${progress}%"></div>
                        </div>
                        <div style="margin-top:6px; font-size:13px;">
                            <span style="font-weight:700; color:#2563EB;">${this.formatCurrency(tender.raised || 0)}</span>
                            <span style="color:#6B7280;"> / ${this.formatCurrency(fundTarget)} TND</span>
                            <span style="margin-left:10px;" class="status-${tender.status || 'open'}">${tender.status === 'open' ? 'Ouvert' : 'Financé'}</span>
                        </div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="text-align:right; font-weight:700;">Min ${this.formatCurrency(minInv)}</div>
                    <div style="text-align:right; font-size:13px; color:#6B7280;">Clôture: ${daysLeft} j</div>
                    <div>
                        <button class="filter-tab" data-id="${tender.id}" onclick="investmentPlatform.showTenderDetails(${tender.id})">Voir</button>
                        ${(tender.status === 'open' || !tender.status) ? `<button class="btn-invest-more" data-invest="${tender.id}" style="margin-left:8px;">Investir</button>` : ''}
                    </div>
                </div>
            `;
            container.appendChild(tenderElement);
        });
    }
    
    // UPDATED: Better transaction history rendering
    renderTransactionHistory() {
        const container = document.getElementById('transactionHistoryBody');
        
        // Null check - prevent crash if element doesn't exist
        if (!container) {
            console.warn('Transaction history container not found');
            return;
        }
        
        const transactions = window.transactionsData || [];
        
        console.log('Rendering transactions:', transactions.length); // Debug
        
        if (transactions.length === 0) {
            container.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:20px; color:#6B7280;">Aucune transaction trouvée</td></tr>';
            return;
        }
        
        container.innerHTML = transactions.map(transaction => {
            // Use formatted data if available from PHP
            const date = transaction.formatted_date || this.formatDate(transaction.transaction_date);
            const amountColor = transaction.amount_color || (transaction.amount < 0 ? '#EF4444' : '#10B981');
            const amountSign = transaction.amount_sign || (transaction.amount < 0 ? '-' : '+');
            const formattedAmount = transaction.formatted_amount || this.formatCurrency(Math.abs(transaction.amount));
            const typeText = transaction.type_text || this.getTransactionTypeText(transaction.type);
            const statusText = transaction.status_text || this.getStatusText(transaction.status);
            
            return `
                <tr>
                    <td>${date}</td>
                    <td>
                        <span class="transaction-type type-${transaction.type}">
                            <i class="bi bi-${transaction.type === 'investment' ? 'arrow-up' : 'arrow-down'}"></i>
                            ${typeText}
                        </span>
                    </td>
                    <td>${transaction.project}</td>
                    <td style="color: ${amountColor}; font-weight:600;">
                        ${amountSign}${formattedAmount}
                    </td>
                    <td><span class="investment-status status-${transaction.status}">${statusText}</span></td>
                </tr>
            `;
        }).join('');
    }
    
    renderTopPerformers() {
        const container = document.getElementById('topPerformersList');
        const investments = window.investmentsData || [];
        
        const topPerformers = investments
            .filter(inv => inv.status === 'active')
            .sort((a, b) => parseFloat(b.roi) - parseFloat(a.roi))
            .slice(0, 3);
        
        if (topPerformers.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:20px; color:#6B7280;">Aucun investissement actif</div>';
            return;
        }
        
        container.innerHTML = topPerformers.map(investment => `
            <div style="background: var(--light-bg); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h6 style="font-weight: 600; color: var(--dark-color); margin: 0; flex: 1;">${investment.projectName || investment.project_name}</h6>
                    <span style="background: #10B981; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                        +${investment.roi}%
                    </span>
                </div>
                <div style="font-size: 14px; color: #6B7280;">
                    Investi: ${this.formatCurrency(investment.amount)}
                </div>
                <div style="font-size: 12px; color: #6B7280; margin-top: 5px;">
                    ${investment.sector} • ${this.formatDate(investment.date || investment.investment_date)}
                </div>
            </div>
        `).join('');
    }
    
    setupEventListeners() {
        document.getElementById('tendersList').addEventListener('click', (e) => {
            const investBtn = e.target.closest('button[data-invest]');
            if (investBtn) {
                const tenderId = investBtn.getAttribute('data-invest');
                const tender = window.tendersData.find(t => t.id == tenderId);
                if (tender) {
                    this.openInvestModal(tender);
                }
            }
        });
        
        document.getElementById('cancelInvest').addEventListener('click', () => this.closeInvestModal());
        document.getElementById('confirmInvest').addEventListener('click', () => this.confirmInvestment());
        document.getElementById('investModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) this.closeInvestModal();
        });
        
        document.getElementById('investAmount').addEventListener('input', (e) => {
            this.validateInvestmentAmountRealTime(e.target.value);
        });
        
        document.querySelectorAll('.filter-tabs .filter-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-tabs .filter-tab').forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                this.renderActiveInvestments(e.target.getAttribute('data-filter'));
            });
        });
        
        document.getElementById('tenderFilter').addEventListener('change', (e) => {
            this.filterTendersBySector(e.target.value);
        });

        document.getElementById('sortClose').addEventListener('click', () => {
            this.sortTendersByDeadline();
        });
        
        // NEW: Refresh transactions button
        document.getElementById('refreshTransactionsBtn').addEventListener('click', () => {
            this.refreshTransactionHistory();
        });
        
        document.getElementById('clearTransactionsBtn').addEventListener('click', () => {
            this.clearTransactionHistory();
        });
        
        document.getElementById('exportReportBtn').addEventListener('click', () => this.exportPortfolioReport());
        document.getElementById('settingsBtn').addEventListener('click', () => {
            this.showToast("Paramètres ouverts", "success");
        });
        
        document.getElementById('createTenderBtn').addEventListener('click', () => this.openCreateTenderModal());
        document.getElementById('cancelCreateTender').addEventListener('click', () => this.closeCreateTenderModal());
        document.getElementById('cancelTenderForm').addEventListener('click', () => this.closeCreateTenderModal());
        document.getElementById('createTenderModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) this.closeCreateTenderModal();
        });
        
        document.getElementById('tenderForm').addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.validateTenderForm()) {
                this.createNewTender();
            }
        });
    }
    
    setupFormValidation() {
        const formInputs = document.querySelectorAll('#tenderForm input, #tenderForm textarea, #tenderForm select');
        formInputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }
    
    // NEW: Refresh transaction history from server
    refreshTransactionHistory() {
        this.showToast("Actualisation de l'historique...", "warning");
        
        fetch('../../controller/controller.php?ajax=get_transactions')
            .then(response => response.json())
            .then(transactions => {
                window.transactionsData = transactions;
                this.renderTransactionHistory();
                this.showToast("Historique actualisé", "success");
            })
            .catch(error => {
                console.error('Error:', error);
                this.showToast("Erreur d'actualisation", "error");
            });
    }
    
    clearTransactionHistory() {
        if (!confirm('Êtes-vous sûr de vouloir effacer tout l\'historique des transactions ?')) {
            return;
        }
        
        this.showToast("Suppression en cours...", "warning");
        
        fetch('../../controller/controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=clear_transactions'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                window.transactionsData = [];
                this.renderTransactionHistory();
                this.showToast("Historique effacé avec succès", "success");
            } else {
                this.showToast("Erreur: " + result.error, "error");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showToast("Erreur de connexion", "error");
        });
    }
    
    filterTendersBySector(sector) {
        fetch(`../../controller/controller.php?ajax=get_tenders&sector=${sector}`)
            .then(response => response.json())
            .then(tenders => {
                window.tendersData = tenders;
                this.renderTenders();
                this.showToast(`Filtré par secteur: ${sector}`, "success");
            })
            .catch(error => {
                console.error('Error:', error);
                this.showToast("Erreur de filtrage", "error");
            });
    }
    
    sortTendersByDeadline() {
        fetch('../../controller/controller.php?ajax=get_tenders&sort=deadline')
            .then(response => response.json())
            .then(tenders => {
                window.tendersData = tenders;
                this.renderTenders();
                this.showToast("Triés par date de clôture", "success");
            })
            .catch(error => {
                console.error('Error:', error);
                this.showToast("Erreur de tri", "error");
            });
    }
    
    validateTenderForm() {
        let isValid = true;
        const fields = [
            { id: 'projectName', minLength: 5, message: 'Le nom du projet doit contenir au moins 5 caractères' },
            { id: 'shortPitch', minLength: 10, message: 'Le pitch court doit contenir au moins 10 caractères' },
            { id: 'projectSector', required: true, message: 'Veuillez sélectionner un secteur' },
            { id: 'offerType', required: true, message: 'Veuillez sélectionner un type d\'offre' },
            { id: 'fundingTarget', minValue: 1000, message: 'Le montant cible doit être d\'au moins 1,000 TND' },
            { id: 'expectedROI', minValue: 1, maxValue: 100, message: 'Le ROI doit être entre 1% et 100%' },
            { id: 'minInvestment', minValue: 100, message: 'L\'investissement minimum doit être d\'au moins 100 TND' },
            { id: 'deadline', futureDate: true, message: 'La date de clôture doit être dans le futur' }
        ];
        
        fields.forEach(field => {
            const element = document.getElementById(field.id);
            const value = element.value.trim();
            
            if (field.required && !value) {
                this.showFieldError(element, field.message);
                isValid = false;
            } else if (field.minLength && value.length < field.minLength) {
                this.showFieldError(element, field.message);
                isValid = false;
            } else if (field.minValue && parseFloat(value) < field.minValue) {
                this.showFieldError(element, field.message);
                isValid = false;
            } else if (field.maxValue && parseFloat(value) > field.maxValue) {
                this.showFieldError(element, field.message);
                isValid = false;
            } else if (field.futureDate) {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate <= today) {
                    this.showFieldError(element, field.message);
                    isValid = false;
                } else {
                    this.clearFieldError(element);
                }
            } else {
                this.clearFieldError(element);
            }
        });
        
        const maxInvestment = document.getElementById('maxInvestment');
        const minInvestment = document.getElementById('minInvestment');
        if (maxInvestment.value && parseFloat(maxInvestment.value) < parseFloat(minInvestment.value)) {
            this.showFieldError(maxInvestment, 'L\'investissement maximum ne peut pas être inférieur au minimum');
            isValid = false;
        }
        
        return isValid;
    }
    
    validateField(field) {
        const fieldId = field.id;
        const value = field.value.trim();
        
        switch(fieldId) {
            case 'projectName':
                if (!value || value.length < 5) {
                    this.showFieldError(field, 'Le nom doit contenir au moins 5 caractères');
                } else {
                    this.clearFieldError(field);
                }
                break;
            case 'shortPitch':
                if (!value || value.length < 10) {
                    this.showFieldError(field, 'Le pitch doit contenir au moins 10 caractères');
                } else {
                    this.clearFieldError(field);
                }
                break;
            case 'fundingTarget':
                if (!value || parseFloat(value) < 1000) {
                    this.showFieldError(field, 'Le montant cible doit être d\'au moins 1,000 TND');
                } else {
                    this.clearFieldError(field);
                }
                break;
            case 'expectedROI':
                if (!value || parseFloat(value) < 1 || parseFloat(value) > 100) {
                    this.showFieldError(field, 'Le ROI estimé doit être entre 1% et 100%');
                } else {
                    this.clearFieldError(field);
                }
                break;
            case 'minInvestment':
                if (!value || parseFloat(value) < 100) {
                    this.showFieldError(field, 'L\'investissement minimum doit être d\'au moins 100 TND');
                } else {
                    this.clearFieldError(field);
                }
                break;
            case 'deadline':
                if (!value) {
                    this.showFieldError(field, 'La date de clôture est requise');
                } else {
                    const deadlineDate = new Date(value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (deadlineDate <= today) {
                        this.showFieldError(field, 'La date de clôture doit être dans le futur');
                    } else {
                        this.clearFieldError(field);
                    }
                }
                break;
        }
    }
    
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        let errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }
    
    openInvestModal(tender) {
        const modal = document.getElementById('investModal');
        const title = document.getElementById('modalTitle');
        const meta = document.getElementById('modalMeta');
        const amountInput = document.getElementById('investAmount');
        const hint = document.getElementById('investHint');
        
        const projName = tender.projectName || tender.project_name;
        const minInv = tender.minInvestment || tender.min_investment;
        const maxInv = tender.maxInvestment || tender.max_investment;
        const fundTarget = tender.fundingTarget || tender.funding_target;
        const offerT = tender.offerType || tender.offer_type;
        
        title.textContent = `Investir — ${projName}`;
        meta.innerHTML = `Min ${this.formatCurrency(minInv)} • Max ${maxInv ? this.formatCurrency(maxInv) : '—'} • ${offerT} • Clôture: ${this.formatDate(tender.deadline)}`;
        amountInput.value = minInv;
        amountInput.min = minInv;
        amountInput.max = maxInv || fundTarget - (tender.raised || 0);
        hint.style.display = 'none';
        
        this.currentTender = {
            id: tender.id,
            projectName: projName,
            minInvestment: minInv,
            maxInvestment: maxInv,
            fundingTarget: fundTarget,
            raised: tender.raised || 0,
            expectedROI: tender.expectedROI || tender.expected_roi,
            sector: tender.sector,
            deadline: tender.deadline
        };
        
        modal.style.display = 'flex';
    }
    
    closeInvestModal() {
        document.getElementById('investModal').style.display = 'none';
        this.currentTender = null;
    }
    
    validateInvestmentAmountRealTime(amount) {
        const hint = document.getElementById('investHint');
        if (!this.currentTender) return;
        
        const errors = this.validateInvestment(Number(amount), this.currentTender);
        
        if (errors.length > 0) {
            hint.textContent = errors[0];
            hint.style.display = 'block';
        } else {
            hint.style.display = 'none';
        }
    }
        confirmInvestment() {
        const amountInput = document.getElementById('investAmount');
        const amount = parseFloat(amountInput.value);
        
        if (!amount || amount <= 0) {
            this.showToast("Veuillez entrer un montant valide", "error");
            return;
        }
        
        const tender = this.currentTender;
        if (!tender) {
            this.showToast("Erreur: Aucun projet sélectionné", "error");
            return;
        }
        
        // Validate investment
        const errors = this.validateInvestment(amount, tender);
        if (errors.length > 0) {
            document.getElementById('investHint').style.display = 'block';
            document.getElementById('investHint').textContent = errors[0];
            return;
        }
        
        // Close modal and redirect to payment page
        this.closeInvestModal();
        
        const projectName = tender.project_name || tender.projectName || 'Projet';
        const tenderId = tender.id;
        
        window.location.href = `payment.php?tender_id=${tenderId}&amount=${amount}&project=${encodeURIComponent(projectName)}`;
    }
    
    
    // UPDATED: Better investment processing with transaction update
    processInvestment(tender, amount) {
        const formData = new FormData();
        formData.append('action', 'create_investment');
        formData.append('tenderId', tender.id);
        formData.append('projectName', tender.projectName);
        formData.append('amount', amount);
        formData.append('roi', tender.expectedROI);
        formData.append('sector', tender.sector);
        
        this.showToast("Traitement de l'investissement...", "warning");
        
        fetch('../../controller/controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                this.showToast(`✓ ${result.message} (${this.formatCurrency(amount)})`, "success");
                
                // Update local data immediately
                this.addTransactionToHistory({
                    transaction_date: new Date().toISOString().split('T')[0],
                    type: 'investment',
                    project: result.projectName || tender.projectName,
                    amount: -amount,
                    status: 'confirmed',
                    formatted_date: 'Aujourd\'hui',
                    formatted_amount: this.formatCurrency(amount),
                    amount_color: '#EF4444',
                    amount_sign: '-',
                    type_text: 'Investissement',
                    status_text: 'Confirmé'
                });
                
                // Also update tenders data
                this.updateTenderAfterInvestment(tender.id, amount);
                
                // Update investments data
                this.refreshAllData();
                
                // Show success message
                setTimeout(() => {
                    this.showToast("Transaction ajoutée à l'historique", "success");
                }, 1000);
                
            } else {
                const errorMsg = result.errors ? result.errors.join(', ') : result.error;
                this.showToast("✗ " + errorMsg, "error");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showToast("✗ Erreur de connexion au serveur", "error");
        });
    }
    
    // NEW: Add transaction to history immediately
    addTransactionToHistory(transaction) {
        if (!window.transactionsData) window.transactionsData = [];
        
        // Add to beginning of array (most recent first)
        window.transactionsData.unshift(transaction);
        
        // Keep only last 20 transactions
        if (window.transactionsData.length > 20) {
            window.transactionsData = window.transactionsData.slice(0, 20);
        }
        
        // Re-render transaction history
        this.renderTransactionHistory();
    }
    
    // NEW: Update tender data after investment
    updateTenderAfterInvestment(tenderId, amount) {
        const tenderIndex = window.tendersData.findIndex(t => t.id == tenderId);
        if (tenderIndex !== -1) {
            window.tendersData[tenderIndex].raised += amount;
            
            // Update progress
            const progress = this.calculateProgress(window.tendersData[tenderIndex]);
            window.tendersData[tenderIndex].progress = progress;
            
            // Re-render tenders
            this.renderTenders();
        }
    }
    
    // NEW: Refresh all data from server
    refreshAllData() {
        // Refresh transactions
        this.refreshTransactionHistory();
        
        // Refresh tenders
        fetch('../../controller/controller.php?ajax=get_tenders')
            .then(response => response.json())
            .then(tenders => {
                window.tendersData = tenders;
                this.renderTenders();
            });
        
        // Refresh investments
        fetch('../../controller/controller.php?ajax=get_investments')
            .then(response => response.json())
            .then(investments => {
                window.investmentsData = investments;
                this.renderActiveInvestments();
                this.renderTopPerformers();
            });
    }
    
    validateInvestment(amount, tender) {
        const errors = [];
        
        if (amount < tender.minInvestment) {
            errors.push(`Le montant est inférieur au minimum (${this.formatCurrency(tender.minInvestment)}).`);
        }
        
        if (tender.maxInvestment && amount > tender.maxInvestment) {
            errors.push(`Le montant dépasse le maximum (${this.formatCurrency(tender.maxInvestment)}).`);
        }
        
        const remaining = tender.fundingTarget - (tender.raised || 0);
        if (amount > remaining) {
            errors.push(`Ce montant dépasse l'objectif restant (${this.formatCurrency(remaining)}).`);
        }
        
        if (amount <= 0) {
            errors.push(`Le montant doit être positif.`);
        }
        
        return errors;
    }
    
    showTenderDetails(tenderId) {
        const tender = window.tendersData.find(t => t.id == tenderId);
        if (!tender) {
            this.showToast("Détails du projet non disponibles", "error");
            return;
        }
        
        const daysLeft = tender.daysLeft || this.calculateDaysLeft(tender.deadline);
        const progress = tender.progress || this.calculateProgress(tender);
        const projName = tender.projectName || tender.project_name;
        const shortP = tender.shortPitch || tender.short_pitch;
        const minInv = tender.minInvestment || tender.min_investment;
        const maxInv = tender.maxInvestment || tender.max_investment;
        const fundTarget = tender.fundingTarget || tender.funding_target;
        const expROI = tender.expectedROI || tender.expected_roi;
        const offerT = tender.offerType || tender.offer_type;
        
        const details = `Projet: ${projName}\nPitch: ${shortP || 'Non spécifié'}\nSecteur: ${tender.sector}\nType d'offre: ${offerT}\nMontant cible: ${this.formatCurrency(fundTarget)}\nMontant levé: ${this.formatCurrency(tender.raised || 0)} (${progress}%)\nInvestissement: Min ${this.formatCurrency(minInv)} • Max ${maxInv ? this.formatCurrency(maxInv) : '—'}\nROI estimé: ${expROI}%\nClôture: ${this.formatDate(tender.deadline)} (${daysLeft} jours restants)`;
        
        alert("Détails du Projet:\n\n" + details);
    }
    
    openCreateTenderModal() {
        const modal = document.getElementById('createTenderModal');
        const deadline = new Date();
        deadline.setDate(deadline.getDate() + 30);
        document.getElementById('deadline').valueAsDate = deadline;
        document.getElementById('tenderForm').reset();
        
        const formInputs = document.querySelectorAll('#tenderForm input, #tenderForm textarea, #tenderForm select');
        formInputs.forEach(input => this.clearFieldError(input));
        
        modal.style.display = 'flex';
    }
    
    closeCreateTenderModal() {
        document.getElementById('createTenderModal').style.display = 'none';
    }
    
    createNewTender() {
        const projectName = document.getElementById('projectName').value;
        const shortPitch = document.getElementById('shortPitch').value;
        const projectSector = document.getElementById('projectSector').value;
        const offerType = document.getElementById('offerType').value;
        const fundingTarget = parseInt(document.getElementById('fundingTarget').value);
        const expectedROI = parseFloat(document.getElementById('expectedROI').value);
        const minInvestment = parseInt(document.getElementById('minInvestment').value);
        const maxInvestment = document.getElementById('maxInvestment').value ? parseInt(document.getElementById('maxInvestment').value) : null;
        const deadline = document.getElementById('deadline').value;
        
        this.showToast("Création de l'appel d'offres...", "warning");
        
        const formData = new FormData();
        formData.append('action', 'create_tender');
        formData.append('projectName', projectName);
        formData.append('shortPitch', shortPitch);
        formData.append('sector', projectSector);
        formData.append('fundingTarget', fundingTarget);
        formData.append('minInvestment', minInvestment);
        formData.append('maxInvestment', maxInvestment || '');
        formData.append('offerType', offerType);
        formData.append('expectedROI', expectedROI);
        formData.append('deadline', deadline);
        
        fetch('../../controller/controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                this.showToast(`✓ ${result.message}`, "success");
                this.closeCreateTenderModal();
                
                // Add transaction for creating tender
                this.addTransactionToHistory({
                    transaction_date: new Date().toISOString().split('T')[0],
                    type: 'investment',
                    project: projectName,
                    amount: 0,
                    status: 'confirmed',
                    formatted_date: 'Aujourd\'hui',
                    formatted_amount: '0 TND',
                    amount_color: '#6B7280',
                    amount_sign: '',
                    type_text: 'Création',
                    status_text: 'Confirmé'
                });
                
                // Refresh data
                this.refreshAllData();
                
            } else {
                const errorMsg = result.errors ? Object.values(result.errors).join(', ') : result.error;
                this.showToast("✗ " + errorMsg, "error");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showToast("✗ Erreur de connexion au serveur", "error");
        });
    }
    
    exportPortfolioReport() {
        this.showToast("Rapport exporté avec succès", "success");
    }
    
    showToast(message, type = "success") {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle'}"></i>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }
    
    formatCurrency(amount) {
        return new Intl.NumberFormat('fr-TN', { 
            style: 'currency', 
            currency: 'TND',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }
    
    formatDate(dateString) {
        if (!dateString) return 'Date inconnue';
        
        // If already formatted by PHP, return as is
        if (dateString.includes('/')) return dateString;
        
        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        if (date.toDateString() === today.toDateString()) {
            return 'Aujourd\'hui';
        } else if (date.toDateString() === yesterday.toDateString()) {
            return 'Hier';
        } else {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('fr-FR', options);
        }
    }
    
    calculateDaysLeft(deadline) {
        if (!deadline) return 0;
        const deadlineDate = new Date(deadline);
        const now = new Date();
        const timeDiff = deadlineDate - now;
        return Math.max(0, Math.ceil(timeDiff / (1000 * 60 * 60 * 24)));
    }
    
    calculateProgress(tender) {
        const fundTarget = tender.fundingTarget || tender.funding_target;
        if (!fundTarget || fundTarget === 0) return 0;
        const raised = tender.raised || 0;
        return Math.min(100, Math.round((raised / fundTarget) * 100));
    }
    
    getStatusText(status) {
        const statusMap = {
            'active': 'En cours',
            'completed': 'Financé',
            'pending': 'En attente',
            'confirmed': 'Confirmé',
            'received': 'Reçu',
            'open': 'Ouvert',
            'closed': 'Fermé'
        };
        return statusMap[status] || status;
    }
    
    getTransactionTypeText(type) {
        const typeMap = {
            'investment': 'Investissement',
            'return': 'Retour',
            'withdrawal': 'Retrait'
        };
        return typeMap[type] || type;
    }
    
    generateThumbnail(sector) {
        const sectorCode = sector ? sector.slice(0,3).toUpperCase() : 'PRO';
        return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%232563EB' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='12' fill='white' text-anchor='middle' dy='.3em'%3E${sectorCode}%3C/text%3E%3C/svg%3E`;
    }
    
    renderPortfolioChart() {
        const canvas = document.getElementById('portfolioChart');
        if (!canvas) {
            console.warn('Portfolio chart canvas not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        const investments = window.investmentsData || [];
        
        // Group investments by month
        const monthlyData = {};
        const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        // Initialize last 6 months with 0
        const now = new Date();
        for (let i = 5; i >= 0; i--) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            const key = `${months[date.getMonth()]} ${date.getFullYear()}`;
            monthlyData[key] = 0;
        }
        
        // Sum investments by month
        investments.forEach(inv => {
            const date = new Date(inv.investment_date || inv.date);
            const key = `${months[date.getMonth()]} ${date.getFullYear()}`;
            if (monthlyData.hasOwnProperty(key)) {
                monthlyData[key] += parseFloat(inv.amount) || 0;
            }
        });
        
        // Calculate cumulative totals
        const labels = Object.keys(monthlyData);
        const rawData = Object.values(monthlyData);
        let cumulative = 0;
        const cumulativeData = rawData.map(val => {
            cumulative += val;
            return cumulative;
        });
        
        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.3)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Investi (TND)',
                    data: cumulativeData,
                    borderColor: '#2563EB',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('fr-TN', {
                                    style: 'currency',
                                    currency: 'TND',
                                    minimumFractionDigits: 0
                                }).format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6B7280'
                        }
                    },
                    y: {
                        grid: {
                            color: '#E5E7EB'
                        },
                        ticks: {
                            color: '#6B7280',
                            callback: function(value) {
                                return value.toLocaleString('fr-TN') + ' TND';
                            }
                        }
                    }
                }
            }
        });
    }
}

let investmentPlatform;
document.addEventListener('DOMContentLoaded', function() {
    investmentPlatform = new InvestmentPlatform();
});