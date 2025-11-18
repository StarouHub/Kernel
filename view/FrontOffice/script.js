// Investment Platform Data Model
const investmentPlatform = {
    // User portfolio data
    portfolio: {
        totalInvested: 125500,
        totalGains: 18750,
        activeProjects: 12,
        investorScore: 4.8,
        monthlyChange: 12.5,
        gainsChange: 8.3,
        projectsChange: 3
    },
    
    // User's active investments
    userInvestments: [
        {
            id: "i1",
            tenderId: "t1",
            projectName: "Assistant IA Intelligent",
            amount: 12500,
            date: "2024-10-15",
            roi: 15.2,
            status: "active",
            sector: "AI",
            thumbnail: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%232563EB' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='12' fill='white' text-anchor='middle' dy='.3em'%3EIA%3C/text%3E%3C/svg%3E"
        },
        {
            id: "i2",
            tenderId: "t2",
            projectName: "Maison Connectée Écologique",
            amount: 20000,
            date: "2024-10-08",
            roi: 22.8,
            status: "completed",
            sector: "IoT",
            thumbnail: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%237C3AED' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='12' fill='white' text-anchor='middle' dy='.3em'%3EIoT%3C/text%3E%3C/svg%3E"
        },
        {
            id: "i3",
            tenderId: "t3",
            projectName: "Plateforme NFT Artistique",
            amount: 15000,
            date: "2024-09-25",
            roi: 8.5,
            status: "active",
            sector: "Blockchain",
            thumbnail: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%23F59E0B' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='12' fill='white' text-anchor='middle' dy='.3em'%3ENFT%3C/text%3E%3C/svg%3E"
        },
        {
            id: "i4",
            tenderId: "t4",
            projectName: "Application de Santé Mobile",
            amount: 8000,
            date: "2024-09-12",
            roi: 5.2,
            status: "pending",
            sector: "Health",
            thumbnail: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%2310B981' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='10' fill='white' text-anchor='middle' dy='.3em'%3EHealth%3C/text%3E%3C/svg%3E"
        }
    ],
    
    // Available investment opportunities (tenders)
    tenders: [
        {
            tenderId: "t1",
            projectName: "Maison Connectée Écologique",
            shortPitch: "IoT low-cost pour économies d'énergie",
            sector: "IoT",
            fundingTarget: 200000,
            raised: 75000,
            minInvestment: 1000,
            maxInvestment: 50000,
            offerType: "Equity",
            expectedROI: "22.8%",
            deadline: "2025-12-15T23:59:59Z",
            status: "open"
        },
        {
            tenderId: "t2",
            projectName: "Assistant IA Intelligent",
            shortPitch: "Agent IA pour PME",
            sector: "AI",
            fundingTarget: 150000,
            raised: 125000,
            minInvestment: 500,
            maxInvestment: 30000,
            offerType: "Convertible",
            expectedROI: "15.2%",
            deadline: "2025-11-30T23:59:59Z",
            status: "open"
        },
        {
            tenderId: "t3",
            projectName: "Plateforme NFT Artistique",
            shortPitch: "Marketplace pour artistes",
            sector: "Blockchain",
            fundingTarget: 100000,
            raised: 100000,
            minInvestment: 200,
            maxInvestment: 10000,
            offerType: "Reward",
            expectedROI: "8.5%",
            deadline: "2025-10-30T23:59:59Z",
            status: "funded"
        },
        {
            tenderId: "t4",
            projectName: "Application de Santé Mobile",
            shortPitch: "Suivi médical personnalisé",
            sector: "Health",
            fundingTarget: 120000,
            raised: 45000,
            minInvestment: 300,
            maxInvestment: 15000,
            offerType: "Equity",
            expectedROI: "12.5%",
            deadline: "2025-11-20T23:59:59Z",
            status: "open"
        }
    ],
    
    // Transaction history
    transactions: [
        {
            id: "tr1",
            date: "2024-11-15",
            type: "investment",
            project: "Assistant IA Intelligent",
            amount: -12500,
            status: "confirmed"
        },
        {
            id: "tr2",
            date: "2024-11-10",
            type: "return",
            project: "Maison Connectée",
            amount: 4560,
            status: "received"
        },
        {
            id: "tr3",
            date: "2024-11-08",
            type: "investment",
            project: "Plateforme NFT",
            amount: -15000,
            status: "confirmed"
        },
        {
            id: "tr4",
            date: "2024-11-03",
            type: "dividend",
            project: "Assistant IA",
            amount: 1200,
            status: "received"
        },
        {
            id: "tr5",
            date: "2024-10-28",
            type: "investment",
            project: "App Santé Mobile",
            amount: -8000,
            status: "pending"
        }
    ],
    
    // Initialize the platform
    init() {
        this.renderStats();
        this.renderActiveInvestments();
        this.renderTenders();
        this.renderTransactionHistory();
        this.renderTopPerformers();
        this.setupEventListeners();
        this.startRealTimeUpdates();
        this.setupFormValidation();
    },
    
    // Render portfolio statistics
    renderStats() {
        document.getElementById('totalInvested').textContent = this.formatCurrency(this.portfolio.totalInvested);
        document.getElementById('totalGains').textContent = this.formatCurrency(this.portfolio.totalGains);
        document.getElementById('activeProjects').textContent = this.portfolio.activeProjects;
        document.getElementById('investorScore').textContent = this.portfolio.investorScore;
        document.getElementById('monthlyChange').textContent = `+${this.portfolio.monthlyChange}% ce mois`;
        document.getElementById('gainsChange').textContent = `+${this.portfolio.gainsChange}%`;
        document.getElementById('projectsChange').textContent = `+${this.portfolio.projectsChange} ce mois`;
    },
    
    // Render active investments
    renderActiveInvestments(filter = 'all') {
        const container = document.getElementById('activeInvestmentsList');
        const countElement = document.getElementById('activeInvestmentsCount');
        
        let filteredInvestments = this.userInvestments;
        
        if (filter === 'active') {
            filteredInvestments = this.userInvestments.filter(inv => inv.status === 'active');
        } else if (filter === 'completed') {
            filteredInvestments = this.userInvestments.filter(inv => inv.status === 'completed');
        } else if (filter === 'profitable') {
            filteredInvestments = this.userInvestments.filter(inv => inv.roi > 10);
        }
        
        countElement.textContent = filteredInvestments.length;
        
        if (filteredInvestments.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:20px; color:#6B7280;">Aucun investissement trouvé</div>';
            return;
        }
        
        container.innerHTML = filteredInvestments.map(investment => `
            <div class="investment-item">
            <img src="${investment.thumbnail}" class="project-thumb" alt="${investment.projectName}">
            <div class="investment-details">
                <div class="project-name">${investment.projectName}</div>
                <div class="investment-meta">
                <span><i class="bi bi-calendar"></i> Investi le ${this.formatDate(investment.date)}</span>
                <span><i class="bi bi-percent"></i> ROI: +${investment.roi}%</span>
                </div>
                <span class="investment-status status-${investment.status}">${this.getStatusText(investment.status)}</span>
            </div>
            <div class="investment-amount">${this.formatCurrency(investment.amount)}</div>
            </div>
        `).join('');
    },
    
    // Render tenders (investment opportunities)
    renderTenders(tendersList = this.tenders) {
        const container = document.getElementById('tendersList');
        const countElement = document.getElementById('tenderCount');
        
        container.innerHTML = '';
        countElement.textContent = tendersList.length;
        
        if (tendersList.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:20px; color:#6B7280;">Aucun appel d\'offres trouvé</div>';
            return;
        }
        
        tendersList.forEach(tender => {
            const progress = Math.round((tender.raised / tender.fundingTarget) * 100);
            const daysLeft = this.calculateDaysLeft(tender.deadline);
            
            const tenderElement = document.createElement('div');
            tenderElement.className = 'investment-item';
            tenderElement.style.justifyContent = 'space-between';
            tenderElement.innerHTML = `
            <div style="display:flex; gap:12px; align-items:center;">
                <div style="width:64px; height:48px; background:linear-gradient(135deg,#2563EB,#7C3AED); border-radius:8px; color:white; display:flex; align-items:center; justify-content:center; font-weight:700;">
                ${tender.sector.slice(0,3).toUpperCase()}
                </div>
                <div style="min-width:200px;">
                <div style="font-weight:600;">${tender.projectName}</div>
                <div style="font-size:13px; color:#6B7280;">${tender.shortPitch}</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${progress}%"></div>
                </div>
                <div style="margin-top:6px; font-size:13px;">
                    <span style="font-weight:700; color:#2563EB;">${this.formatCurrency(tender.raised)}</span>
                    <span style="color:#6B7280;"> / ${this.formatCurrency(tender.fundingTarget)} TND</span>
                    <span style="margin-left:10px;" class="status-${tender.status}">${tender.status === 'open' ? 'Ouvert' : 'Financé'}</span>
                </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="text-align:right; font-weight:700;">Min ${this.formatCurrency(tender.minInvestment)}</div>
                <div style="text-align:right; font-size:13px; color:#6B7280;">Clôture: ${daysLeft} j</div>
                <div>
                <button class="filter-tab" data-id="${tender.tenderId}">Voir</button>
                ${tender.status === 'open' ? `<button class="btn-invest-more" data-invest="${tender.tenderId}" style="margin-left:8px;">Investir</button>` : ''}
                </div>
            </div>
            `;
            container.appendChild(tenderElement);
        });
    },
    
    // Render transaction history
    renderTransactionHistory() {
        const container = document.getElementById('transactionHistoryBody');
        
        container.innerHTML = this.transactions.map(transaction => `
            <tr>
            <td>${this.formatDate(transaction.date)}</td>
            <td>
                <span class="transaction-type type-${transaction.type}">
                <i class="bi ${transaction.type === 'investment' ? 'bi-arrow-up-right' : 'bi-arrow-down-left'}"></i>
                ${this.getTransactionTypeText(transaction.type)}
                </span>
            </td>
            <td>${transaction.project}</td>
            <td style="font-weight: 600; color: ${transaction.amount < 0 ? '#EF4444' : '#10B981'};">${transaction.amount < 0 ? '' : '+'}${this.formatCurrency(Math.abs(transaction.amount))}</td>
            <td><span class="investment-status status-${transaction.status}">${this.getStatusText(transaction.status)}</span></td>
            </tr>
        `).join('');
    },
    
    // Render top performing projects
    renderTopPerformers() {
        const container = document.getElementById('topPerformersList');
        
        // Get top 3 investments by ROI
        const topPerformers = [...this.userInvestments]
            .sort((a, b) => b.roi - a.roi)
            .slice(0, 3);
        
        container.innerHTML = topPerformers.map(investment => `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #E5E7EB;">
            <div>
                <div style="font-weight: 600; font-size: 14px;">${investment.projectName}</div>
                <div style="font-size: 12px; color: #6B7280;">${investment.sector}</div>
            </div>
            <div style="color: #10B981; font-weight: 700;">+${investment.roi}%</div>
            </div>
        `).join('');
        
        // Add a message if no investments
        if (topPerformers.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding:20px; color:#6B7280;">Aucun investissement</div>';
        }
    },
    
    // Setup event listeners
    setupEventListeners() {
        // Tender list interactions
        document.getElementById('tendersList').addEventListener('click', (e) => {
            const viewBtn = e.target.closest('button[data-id]');
            if (viewBtn) {
                const tenderId = viewBtn.getAttribute('data-id');
                const tender = this.tenders.find(t => t.tenderId === tenderId);
                this.showTenderDetails(tender);
            }

            const investBtn = e.target.closest('button[data-invest]');
            if (investBtn) {
                const tenderId = investBtn.getAttribute('data-invest');
                const tender = this.tenders.find(t => t.tenderId === tenderId);
                this.openInvestModal(tender);
            }
        });
        
        // Investment modal interactions
        document.getElementById('cancelInvest').addEventListener('click', () => this.closeInvestModal());
        document.getElementById('confirmInvest').addEventListener('click', () => this.confirmInvestment());
        document.getElementById('investModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) this.closeInvestModal();
        });
        
        // Filter tabs for investments
        document.querySelectorAll('.filter-tabs .filter-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-tabs .filter-tab').forEach(t => t.classList.remove('active'));
                e.target.classList.add('active');
                this.renderActiveInvestments(e.target.getAttribute('data-filter'));
            });
        });
        
        // Tender filtering
        document.getElementById('tenderFilter').addEventListener('change', (e) => {
            const sector = e.target.value;
            const filtered = sector === 'all' ? this.tenders : this.tenders.filter(t => t.sector === sector);
            this.renderTenders(filtered);
        });

        // Sort by close soon
        document.getElementById('sortClose').addEventListener('click', () => {
            const sorted = [...this.tenders].sort((a,b) => new Date(a.deadline) - new Date(b.deadline));
            this.renderTenders(sorted);
            this.showToast("Triers par date de clôture", "success");
        });
        
        // Quick action buttons
        document.getElementById('exportReportBtn').addEventListener('click', () => this.exportPortfolioReport());
        document.getElementById('settingsBtn').addEventListener('click', () => {
            this.showToast("Paramètres ouverts", "success");
        });
        
        // Create tender button
        document.getElementById('createTenderBtn').addEventListener('click', () => this.openCreateTenderModal());
        
        // Create tender modal interactions
        document.getElementById('cancelCreateTender').addEventListener('click', () => this.closeCreateTenderModal());
        document.getElementById('cancelTenderForm').addEventListener('click', () => this.closeCreateTenderModal());
        document.getElementById('createTenderModal').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) this.closeCreateTenderModal();
        });
    },
    
    // Setup form validation
    setupFormValidation() {
        const tenderForm = document.getElementById('tenderForm');
        
        tenderForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (this.validateTenderForm()) {
                this.createNewTender();
            }
        });
        
        // Real-time validation
        const formInputs = tenderForm.querySelectorAll('input, textarea, select');
        formInputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });
    },
    
    // Validate tender form
    validateTenderForm() {
        let isValid = true;
        
        // Project name validation
        const projectName = document.getElementById('projectName');
        if (!projectName.value.trim()) {
            this.showFieldError(projectName, 'Le nom du projet est requis');
            isValid = false;
        } else if (projectName.value.trim().length < 5) {
            this.showFieldError(projectName, 'Le nom doit contenir au moins 5 caractères');
            isValid = false;
        } else {
            this.clearFieldError(projectName);
        }
        
        // Short pitch validation
        const shortPitch = document.getElementById('shortPitch');
        if (!shortPitch.value.trim()) {
            this.showFieldError(shortPitch, 'Le pitch court est requis');
            isValid = false;
        } else if (shortPitch.value.trim().length < 10) {
            this.showFieldError(shortPitch, 'Le pitch doit contenir au moins 10 caractères');
            isValid = false;
        } else {
            this.clearFieldError(shortPitch);
        }
        
        // Description validation
        const projectDescription = document.getElementById('projectDescription');
        if (!projectDescription.value.trim()) {
            this.showFieldError(projectDescription, 'La description est requise');
            isValid = false;
        } else if (projectDescription.value.trim().length < 50) {
            this.showFieldError(projectDescription, 'La description doit contenir au moins 50 caractères');
            isValid = false;
        } else {
            this.clearFieldError(projectDescription);
        }
        
        // Sector validation
        const projectSector = document.getElementById('projectSector');
        if (!projectSector.value) {
            this.showFieldError(projectSector, 'Veuillez sélectionner un secteur');
            isValid = false;
        } else {
            this.clearFieldError(projectSector);
        }
        
        // Offer type validation
        const offerType = document.getElementById('offerType');
        if (!offerType.value) {
            this.showFieldError(offerType, 'Veuillez sélectionner un type d\'offre');
            isValid = false;
        } else {
            this.clearFieldError(offerType);
        }
        
        // Funding target validation
        const fundingTarget = document.getElementById('fundingTarget');
        if (!fundingTarget.value || fundingTarget.value < 1000) {
            this.showFieldError(fundingTarget, 'Le montant cible doit être d\'au moins 1,000 TND');
            isValid = false;
        } else {
            this.clearFieldError(fundingTarget);
        }
        
        // ROI validation
        const expectedROI = document.getElementById('expectedROI');
        if (!expectedROI.value || expectedROI.value < 1 || expectedROI.value > 100) {
            this.showFieldError(expectedROI, 'Le ROI estimé doit être entre 1% et 100%');
            isValid = false;
        } else {
            this.clearFieldError(expectedROI);
        }
        
        // Minimum investment validation
        const minInvestment = document.getElementById('minInvestment');
        if (!minInvestment.value || minInvestment.value < 100) {
            this.showFieldError(minInvestment, 'L\'investissement minimum doit être d\'au moins 100 TND');
            isValid = false;
        } else {
            this.clearFieldError(minInvestment);
        }
        
        // Maximum investment validation (if provided)
        const maxInvestment = document.getElementById('maxInvestment');
        if (maxInvestment.value && maxInvestment.value < minInvestment.value) {
            this.showFieldError(maxInvestment, 'L\'investissement maximum ne peut pas être inférieur au minimum');
            isValid = false;
        } else {
            this.clearFieldError(maxInvestment);
        }
        
        // Deadline validation
        const deadline = document.getElementById('deadline');
        if (!deadline.value) {
            this.showFieldError(deadline, 'La date de clôture est requise');
            isValid = false;
        } else {
            const deadlineDate = new Date(deadline.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (deadlineDate <= today) {
                this.showFieldError(deadline, 'La date de clôture doit être dans le futur');
                isValid = false;
            } else {
                this.clearFieldError(deadline);
            }
        }
        
        return isValid;
    },
    
    // Validate individual field
    validateField(field) {
        const fieldId = field.id;
        
        switch(fieldId) {
            case 'projectName':
                if (!field.value.trim()) {
                    this.showFieldError(field, 'Le nom du projet est requis');
                } else if (field.value.trim().length < 5) {
                    this.showFieldError(field, 'Le nom doit contenir au moins 5 caractères');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'shortPitch':
                if (!field.value.trim()) {
                    this.showFieldError(field, 'Le pitch court est requis');
                } else if (field.value.trim().length < 10) {
                    this.showFieldError(field, 'Le pitch doit contenir au moins 10 caractères');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'projectDescription':
                if (!field.value.trim()) {
                    this.showFieldError(field, 'La description est requise');
                } else if (field.value.trim().length < 50) {
                    this.showFieldError(field, 'La description doit contenir au moins 50 caractères');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'fundingTarget':
                if (!field.value || field.value < 1000) {
                    this.showFieldError(field, 'Le montant cible doit être d\'au moins 1,000 TND');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'expectedROI':
                if (!field.value || field.value < 1 || field.value > 100) {
                    this.showFieldError(field, 'Le ROI estimé doit être entre 1% et 100%');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'minInvestment':
                if (!field.value || field.value < 100) {
                    this.showFieldError(field, 'L\'investissement minimum doit être d\'au moins 100 TND');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'maxInvestment':
                const minInvestment = document.getElementById('minInvestment');
                if (field.value && field.value < minInvestment.value) {
                    this.showFieldError(field, 'L\'investissement maximum ne peut pas être inférieur au minimum');
                } else {
                    this.clearFieldError(field);
                }
                break;
                
            case 'deadline':
                if (!field.value) {
                    this.showFieldError(field, 'La date de clôture est requise');
                } else {
                    const deadlineDate = new Date(field.value);
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
    },
    
    // Show field error
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    },
    
    // Clear field error
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        
        const errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.textContent = '';
        }
    },
    
    // Open investment modal
    openInvestModal(tender) {
        const modal = document.getElementById('investModal');
        const title = document.getElementById('modalTitle');
        const meta = document.getElementById('modalMeta');
        const amountInput = document.getElementById('investAmount');
        const hint = document.getElementById('investHint');
        
        // Set modal content
        title.textContent = `Investir — ${tender.projectName}`;
        meta.innerHTML = `Min ${this.formatCurrency(tender.minInvestment)} • Max ${tender.maxInvestment ? this.formatCurrency(tender.maxInvestment) : '—'} • ${tender.offerType} • Clôture: ${this.formatDate(tender.deadline)}`;
        amountInput.value = tender.minInvestment;
        hint.style.display = 'none';
        
        // Store current tender
        modal.currentTender = tender;
        
        // Show modal
        modal.style.display = 'flex';
    },
    
    // Close investment modal
    closeInvestModal() {
        document.getElementById('investModal').style.display = 'none';
    },
    
    // Confirm investment
    confirmInvestment() {
        const modal = document.getElementById('investModal');
        const amountInput = document.getElementById('investAmount');
        const hint = document.getElementById('investHint');
        const tender = modal.currentTender;
        
        if (!tender) return;
        
        const amount = Number(amountInput.value || 0);
        const errors = this.validateInvestment(amount, tender);
        
        if (errors.length > 0) {
            hint.textContent = errors[0];
            hint.style.display = 'block';
            return;
        }
        
        // Process the investment
        this.processInvestment(tender, amount);
        this.closeInvestModal();
    },
    
    // Process investment - update all data
    processInvestment(tender, amount) {
        // Update tender raised amount
        tender.raised += amount;
        
        // Check if investment already exists for this user
        const existingInvestment = this.userInvestments.find(inv => inv.tenderId === tender.tenderId);
        
        if (existingInvestment) {
            // Update existing investment
            existingInvestment.amount += amount;
        } else {
            // Create new investment
            const newInvestment = {
                id: 'i' + (this.userInvestments.length + 1),
                tenderId: tender.tenderId,
                projectName: tender.projectName,
                amount: amount,
                date: new Date().toISOString().split('T')[0],
                roi: parseFloat(tender.expectedROI),
                status: 'active',
                sector: tender.sector,
                thumbnail: `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='80' viewBox='0 0 100 80'%3E%3Crect fill='%232563EB' width='100' height='80'/%3E%3Ctext x='50%25' y='50%25' font-size='12' fill='white' text-anchor='middle' dy='.3em'%3E${tender.sector.slice(0,3)}%3C/text%3E%3C/svg%3E`
            };
            this.userInvestments.push(newInvestment);
        }
        
        // Add transaction to history
        const newTransaction = {
            id: 'tr' + (this.transactions.length + 1),
            date: new Date().toISOString().split('T')[0],
            type: 'investment',
            project: tender.projectName,
            amount: -amount,
            status: 'confirmed'
        };
        this.transactions.unshift(newTransaction); // Add to beginning
        
        // Update portfolio stats
        this.updatePortfolioStats(amount);
        
        // Re-render all components
        this.renderStats();
        this.renderActiveInvestments();
        this.renderTenders();
        this.renderTransactionHistory();
        this.renderTopPerformers();
        
        // Show success message
        this.showToast(`Investissement réussi: ${this.formatCurrency(amount)} pour ${tender.projectName}`, "success");
    },
    
    // Update portfolio statistics after investment
    updatePortfolioStats(amount) {
        this.portfolio.totalInvested += amount;
        this.portfolio.activeProjects = this.userInvestments.filter(inv => inv.status === 'active').length;
        
        // Simulate gains increase (in reality this would come from actual returns)
        this.portfolio.totalGains += amount * 0.15;
        
        // Update change percentages (simulate small random changes)
        this.portfolio.monthlyChange = (10 + Math.random() * 5).toFixed(1);
        this.portfolio.gainsChange = (5 + Math.random() * 5).toFixed(1);
    },
    
    // Validate investment
    validateInvestment(amount, tender) {
        const errors = [];
        
        if (amount < tender.minInvestment) {
            errors.push(`Le montant est inférieur au minimum (${this.formatCurrency(tender.minInvestment)}).`);
        }
        
        if (tender.maxInvestment && amount > tender.maxInvestment) {
            errors.push(`Le montant dépasse le maximum (${this.formatCurrency(tender.maxInvestment)}).`);
        }
        
        if (amount > (tender.fundingTarget - tender.raised)) {
            errors.push(`Attention: ce montant dépasse l'objectif restant (${this.formatCurrency(tender.fundingTarget - tender.raised)}).`);
        }
        
        return errors;
    },
    
    // Show tender details
    showTenderDetails(tender) {
        const daysLeft = this.calculateDaysLeft(tender.deadline);
        const progress = Math.round((tender.raised / tender.fundingTarget) * 100);
        
        const details = `
            <strong>Projet:</strong> ${tender.projectName}<br>
            <strong>Pitch:</strong> ${tender.shortPitch}<br>
            <strong>Secteur:</strong> ${tender.sector}<br>
            <strong>Type d'offre:</strong> ${tender.offerType}<br>
            <strong>Montant cible:</strong> ${this.formatCurrency(tender.fundingTarget)}<br>
            <strong>Montant levé:</strong> ${this.formatCurrency(tender.raised)} (${progress}%)<br>
            <strong>Investissement:</strong> Min ${this.formatCurrency(tender.minInvestment)} • Max ${tender.maxInvestment ? this.formatCurrency(tender.maxInvestment) : '—'}<br>
            <strong>ROI estimé:</strong> ${tender.expectedROI}<br>
            <strong>Clôture:</strong> ${this.formatDate(tender.deadline)} (${daysLeft} jours restants)
        `;
        
        // Create a modal for details (simplified version)
        alert(details);
    },
    
    // Open create tender modal
    openCreateTenderModal() {
        const modal = document.getElementById('createTenderModal');
        
        // Set default deadline to 30 days from now
        const deadline = new Date();
        deadline.setDate(deadline.getDate() + 30);
        document.getElementById('deadline').valueAsDate = deadline;
        
        // Reset form
        document.getElementById('tenderForm').reset();
        
        // Clear any validation errors
        const formInputs = document.querySelectorAll('#tenderForm input, #tenderForm textarea, #tenderForm select');
        formInputs.forEach(input => {
            this.clearFieldError(input);
        });
        
        // Show modal
        modal.style.display = 'flex';
    },
    
    // Close create tender modal
    closeCreateTenderModal() {
        document.getElementById('createTenderModal').style.display = 'none';
    },
    
    // Create new tender
    createNewTender() {
        // Get form values
        const projectName = document.getElementById('projectName').value;
        const shortPitch = document.getElementById('shortPitch').value;
        const projectDescription = document.getElementById('projectDescription').value;
        const projectSector = document.getElementById('projectSector').value;
        const offerType = document.getElementById('offerType').value;
        const fundingTarget = parseInt(document.getElementById('fundingTarget').value);
        const expectedROI = parseFloat(document.getElementById('expectedROI').value);
        const minInvestment = parseInt(document.getElementById('minInvestment').value);
        const maxInvestment = document.getElementById('maxInvestment').value ? parseInt(document.getElementById('maxInvestment').value) : null;
        const deadline = document.getElementById('deadline').value;
        const projectLogo = document.getElementById('projectLogo').value;
        
        // Create new tender object
        const newTender = {
            tenderId: 't' + (this.tenders.length + 1),
            projectName: projectName,
            shortPitch: shortPitch,
            projectDescription: projectDescription,
            sector: projectSector,
            fundingTarget: fundingTarget,
            raised: 0,
            minInvestment: minInvestment,
            maxInvestment: maxInvestment,
            offerType: offerType,
            expectedROI: expectedROI.toFixed(1) + '%',
            deadline: deadline + 'T23:59:59Z',
            status: 'open',
            projectLogo: projectLogo || null
        };
        
        // Add to tenders array
        this.tenders.push(newTender);
        
        // Close modal
        this.closeCreateTenderModal();
        
        // Re-render tenders
        this.renderTenders();
        
        // Show success message
        this.showToast(`Appel d'offres "${projectName}" créé avec succès!`, "success");
    },
    
    // Start real-time updates for stats
    startRealTimeUpdates() {
        // Update stats every 30 seconds to simulate real-time changes
        setInterval(() => {
            // Simulate small random changes in portfolio value
            const randomChange = (Math.random() - 0.4) * 0.5; // -0.2% to +0.3%
            this.portfolio.totalInvested = Math.max(0, this.portfolio.totalInvested * (1 + randomChange/100));
            
            // Simulate gains changes
            const gainsChange = (Math.random() - 0.3) * 0.8; // -0.24% to +0.56%
            this.portfolio.totalGains = Math.max(0, this.portfolio.totalGains * (1 + gainsChange/100));
            
            // Update change percentages
            this.portfolio.monthlyChange = (10 + Math.random() * 5).toFixed(1);
            this.portfolio.gainsChange = (5 + Math.random() * 5).toFixed(1);
            
            // Update the displayed stats
            this.renderStats();
        }, 30000);
    },
    
    // Export portfolio report
    exportPortfolioReport() {
        const data = {
            totalInvested: this.portfolio.totalInvested,
            totalGains: this.portfolio.totalGains,
            activeProjects: this.portfolio.activeProjects,
            investorScore: this.portfolio.investorScore,
            exportDate: new Date().toLocaleDateString(),
            investments: this.userInvestments,
            transactions: this.transactions.slice(0, 10) // Last 10 transactions
        };
        
        const dataStr = JSON.stringify(data, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        
        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = 'portfolio-kernel.json';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showToast("Rapport exporté avec succès", "success");
    },
    
    // Show toast notification
    showToast(message, type = "success") {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle'}"></i>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    },
    
    // Utility functions
    formatCurrency(amount) {
        return new Intl.NumberFormat('fr-TN', { style: 'currency', currency: 'TND' }).format(amount);
    },
    
    formatDate(dateString) {
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    
    calculateDaysLeft(deadline) {
        const deadlineDate = new Date(deadline);
        const now = new Date();
        const timeDiff = deadlineDate - now;
        return Math.max(0, Math.ceil(timeDiff / (1000 * 60 * 60 * 24)));
    },
    
    getStatusText(status) {
        const statusMap = {
            'active': 'En cours',
            'completed': 'Financé',
            'pending': 'En attente',
            'confirmed': 'Confirmé',
            'received': 'Reçu'
        };
        return statusMap[status] || status;
    },
    
    getTransactionTypeText(type) {
        const typeMap = {
            'investment': 'Investissement',
            'return': 'Retour',
            'dividend': 'Dividende'
        };
        return typeMap[type] || type;
    }
};

// Initialize the platform when the page loads
document.addEventListener('DOMContentLoaded', function() {
    investmentPlatform.init();
});