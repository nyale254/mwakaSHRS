// Vaccination Management System JavaScript

class VaccinationManager {
    constructor() {
        this.initEventListeners();
        this.loadVaccinationData();
        this.initModals();
        this.initCharts();
    }

    initEventListeners() {
        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', (e) => {
            this.debounce(this.searchVaccinations.bind(this), 300)(e.target.value);
        });

        // Filter by status
        document.getElementById('statusFilter')?.addEventListener('change', (e) => {
            this.filterByStatus(e.target.value);
        });

        // Filter by date
        document.getElementById('dateFilter')?.addEventListener('change', (e) => {
            this.filterByDate(e.target.value);
        });

        // Add new vaccination button
        document.getElementById('addVaccinationBtn')?.addEventListener('click', () => {
            this.openAddModal();
        });

        // Export buttons
        document.getElementById('exportPdfBtn')?.addEventListener('click', () => {
            this.exportToPDF();
        });

        document.getElementById('exportExcelBtn')?.addEventListener('click', () => {
            this.exportToExcel();
        });

        // Print button
        document.getElementById('printBtn')?.addEventListener('click', () => {
            window.print();
        });
    }

    initModals() {
        // Close modal when clicking on X or outside
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('vaccinationModal').style.display = 'none';
            });
        });

        window.addEventListener('click', (e) => {
            const modal = document.getElementById('vaccinationModal');
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    initCharts() {
        this.loadVaccinationChart();
        this.loadStatusDistribution();
    }

    async loadVaccinationData() {
        this.showLoading();
        try {
            const response = await fetch('api/get_vaccinations.php');
            const data = await response.json();
            
            if (data.success) {
                this.renderVaccinationTable(data.vaccinations);
                this.updateDashboardStats(data.stats);
            } else {
                this.showAlert('Error loading vaccination data', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('Failed to load vaccination data', 'error');
        } finally {
            this.hideLoading();
        }
    }

    renderVaccinationTable(vaccinations) {
        const tbody = document.getElementById('vaccinationTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';
        
        vaccinations.forEach(vax => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${this.escapeHtml(vax.student_name)}</td>
                <td>${this.escapeHtml(vax.vaccine_name)}</td>
                <td>${this.formatDate(vax.due_date)}</td>
                <td>${this.formatDate(vax.administered_date) || '-'}</td>
                <td><span class="status-badge status-${vax.status}">${vax.status}</span></td>
                <td>${this.escapeHtml(vax.administered_by) || '-'}</td>
                <td>
                    <button class="action-btn btn-view" onclick="vaccinationManager.viewDetails(${vax.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn btn-edit" onclick="vaccinationManager.editVaccination(${vax.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn btn-delete" onclick="vaccinationManager.deleteVaccination(${vax.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="action-btn btn-print" onclick="vaccinationManager.printRecord(${vax.id})">
                        <i class="fas fa-print"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    updateDashboardStats(stats) {
        document.getElementById('totalVaccinations').textContent = stats.total || 0;
        document.getElementById('completedVaccinations').textContent = stats.completed || 0;
        document.getElementById('pendingVaccinations').textContent = stats.pending || 0;
        document.getElementById('overdueVaccinations').textContent = stats.overdue || 0;
    }

    async loadVaccinationChart() {
        try {
            const response = await fetch('api/get_vaccination_chart_data.php');
            const data = await response.json();
            
            if (data.success) {
                this.renderVaccinationChart(data.monthlyData);
            }
        } catch (error) {
            console.error('Error loading chart data:', error);
        }
    }

    renderVaccinationChart(monthlyData) {
        const ctx = document.getElementById('vaccinationChart')?.getContext('2d');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Completed Vaccinations',
                    data: monthlyData.map(d => d.completed),
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Scheduled Vaccinations',
                    data: monthlyData.map(d => d.scheduled),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Vaccination Trends'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });
    }

    async loadStatusDistribution() {
        try {
            const response = await fetch('api/get_status_distribution.php');
            const data = await response.json();
            
            if (data.success) {
                this.renderStatusChart(data.distribution);
            }
        } catch (error) {
            console.error('Error loading status distribution:', error);
        }
    }

    renderStatusChart(distribution) {
        const ctx = document.getElementById('statusChart')?.getContext('2d');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Scheduled', 'Overdue'],
                datasets: [{
                    data: [
                        distribution.completed || 0,
                        distribution.pending || 0,
                        distribution.scheduled || 0,
                        distribution.overdue || 0
                    ],
                    backgroundColor: [
                        '#27ae60',
                        '#f39c12',
                        '#3498db',
                        '#e74c3c'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    async searchVaccinations(query) {
        if (query.length < 2) {
            this.loadVaccinationData();
            return;
        }

        try {
            const response = await fetch(`api/search_vaccinations.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderVaccinationTable(data.vaccinations);
            }
        } catch (error) {
            console.error('Error searching:', error);
        }
    }

    async filterByStatus(status) {
        try {
            const response = await fetch(`api/filter_vaccinations.php?status=${status}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderVaccinationTable(data.vaccinations);
            }
        } catch (error) {
            console.error('Error filtering:', error);
        }
    }

    async filterByDate(date) {
        try {
            const response = await fetch(`api/filter_vaccinations.php?date=${date}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderVaccinationTable(data.vaccinations);
            }
        } catch (error) {
            console.error('Error filtering:', error);
        }
    }

    openAddModal() {
        const modal = document.getElementById('vaccinationModal');
        document.getElementById('modalTitle').textContent = 'Add New Vaccination';
        document.getElementById('vaccinationForm').reset();
        document.getElementById('vaccinationId').value = '';
        modal.style.display = 'block';
    }

    async viewDetails(id) {
        try {
            const response = await fetch(`api/get_vaccination_details.php?id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                this.showVaccinationDetails(data.vaccination);
            }
        } catch (error) {
            console.error('Error fetching details:', error);
        }
    }

    async editVaccination(id) {
        try {
            const response = await fetch(`api/get_vaccination.php?id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                this.populateEditForm(data.vaccination);
            }
        } catch (error) {
            console.error('Error fetching vaccination:', error);
        }
    }

    populateEditForm(vaccination) {
        const modal = document.getElementById('vaccinationModal');
        document.getElementById('modalTitle').textContent = 'Edit Vaccination';
        document.getElementById('vaccinationId').value = vaccination.id;
        document.getElementById('studentId').value = vaccination.student_id;
        document.getElementById('vaccineName').value = vaccination.vaccine_name;
        document.getElementById('dueDate').value = vaccination.due_date;
        document.getElementById('administeredDate').value = vaccination.administered_date || '';
        document.getElementById('status').value = vaccination.status;
        document.getElementById('notes').value = vaccination.notes || '';
        
        modal.style.display = 'block';
    }

    async deleteVaccination(id) {
        if (!confirm('Are you sure you want to delete this vaccination record?')) {
            return;
        }

        try {
            const response = await fetch('api/delete_vaccination.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Vaccination record deleted successfully', 'success');
                this.loadVaccinationData();
            } else {
                this.showAlert('Error deleting record', 'error');
            }
        } catch (error) {
            console.error('Error deleting:', error);
            this.showAlert('Failed to delete record', 'error');
        }
    }

    async saveVaccination(formData) {
        try {
            const response = await fetch('api/save_vaccination.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Vaccination record saved successfully', 'success');
                document.getElementById('vaccinationModal').style.display = 'none';
                this.loadVaccinationData();
            } else {
                this.showAlert(data.message || 'Error saving record', 'error');
            }
        } catch (error) {
            console.error('Error saving:', error);
            this.showAlert('Failed to save record', 'error');
        }
    }

    printRecord(id) {
        window.open(`print_vaccination.php?id=${id}`, '_blank');
    }

    exportToPDF() {
        window.open('export_vaccinations_pdf.php', '_blank');
    }

    exportToExcel() {
        window.location.href = 'export_vaccinations_excel.php';
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    formatDate(dateString) {
        if (!dateString) return null;
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    showLoading() {
        const loader = document.getElementById('loadingSpinner');
        if (loader) loader.style.display = 'block';
    }

    hideLoading() {
        const loader = document.getElementById('loadingSpinner');
        if (loader) loader.style.display = 'none';
    }

    showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    showVaccinationDetails(vaccination) {
        // Implementation for showing detailed view
        alert(`Viewing details for: ${vaccination.student_name}`);
    }
}

// Initialize the vaccination manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.vaccinationManager = new VaccinationManager();
});

// Form submission handler
document.getElementById('vaccinationForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    vaccinationManager.saveVaccination(formData);
});