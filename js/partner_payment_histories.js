// Partner Payment Histories JavaScript
class PartnerPaymentHistories {
    constructor() {
        this.currentOffset = 0;
        this.currentLimit = 20;
        this.currentFilters = {
            status: '',
            startDate: '',
            endDate: ''
        };
        this.isLoading = false;
        this.hasMore = true;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadPaymentHistories();
    }
    
    bindEvents() {
        // Filter events
        document.getElementById('applyFilters').addEventListener('click', () => this.applyFilters());
        document.getElementById('clearFilters').addEventListener('click', () => this.clearFilters());
        document.getElementById('periodFilter').addEventListener('change', (e) => this.handlePeriodChange(e.target.value));
        
        // Load more
        document.getElementById('loadMoreBtn').addEventListener('click', () => this.loadMore());
        
        // Status update confirmation
        document.getElementById('confirmStatusUpdate').addEventListener('click', () => this.confirmStatusUpdate());
    }
    
    async loadPaymentHistories(reset = false) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        
        if (reset) {
            this.currentOffset = 0;
            this.hasMore = true;
        }
        
        try {
            const response = await this.makeRequest('get_payment_histories', {
                status: this.currentFilters.status,
                limit: this.currentLimit,
                offset: this.currentOffset,
                start_date: this.currentFilters.startDate,
                end_date: this.currentFilters.endDate
            });
            
            if (response.success) {
                if (reset) {
                    this.updateTable(response.data);
                } else {
                    this.appendToTable(response.data);
                }
                
                this.hasMore = response.has_more;
                this.currentOffset += this.currentLimit;
                
                // Update load more button
                this.updateLoadMoreButton();
                
                // Update stats
                this.updateStats();
            } else {
                this.showAlert('Error loading payment histories: ' + response.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('An error occurred while loading payment histories', 'danger');
        } finally {
            this.isLoading = false;
        }
    }
    
    async updateStats() {
        try {
            const response = await this.makeRequest('get_payment_stats', {
                status: this.currentFilters.status,
                start_date: this.currentFilters.startDate,
                end_date: this.currentFilters.endDate
            });
            
            if (response.success) {
                const stats = response.stats;
                document.getElementById('totalReceived').textContent = '$' + stats.total_received.toFixed(2);
                document.getElementById('totalPending').textContent = '$' + stats.total_pending.toFixed(2);
                document.getElementById('totalRejected').textContent = '$' + stats.total_rejected.toFixed(2);
                document.getElementById('totalPayments').textContent = stats.total_payments.toLocaleString();
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        }
    }
    
    updateTable(payments) {
        const tbody = document.getElementById('paymentHistoriesTableBody');
        
        if (payments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Payment History</h5>
                        <p class="text-muted">No payments found matching your criteria.</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = payments.map(payment => this.createPaymentRow(payment)).join('');
    }
    
    appendToTable(payments) {
        const tbody = document.getElementById('paymentHistoriesTableBody');
        
        // Remove "no data" row if it exists
        const noDataRow = tbody.querySelector('td[colspan="6"]');
        if (noDataRow) {
            noDataRow.parentElement.remove();
        }
        
        payments.forEach(payment => {
            tbody.insertAdjacentHTML('beforeend', this.createPaymentRow(payment));
        });
    }
    
    createPaymentRow(payment) {
        const statusClass = this.getStatusClass(payment.status);
        const statusIcon = this.getStatusIcon(payment.status);
        const statusText = this.getStatusText(payment.status);
        const paymentIcon = payment.payment_method === 'Bank Transfer' ? 'university' : 'credit-card';
        
        return `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <i class="fas fa-${paymentIcon} text-primary"></i>
                        </div>
                        <div>
                            <strong>${this.escapeHtml(payment.payment_method)}</strong>
                            <br>
                            <small class="text-muted">
                                ID: #${payment.id}
                            </small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>
                        <strong>${this.escapeHtml(payment.account_name)}</strong>
                        <br>
                        <small class="text-muted">
                            ${this.escapeHtml(payment.account_number)}
                        </small>
                    </div>
                </td>
                <td>
                    <div class="text-success fw-bold">
                        $${parseFloat(payment.amount).toFixed(2)}
                    </div>
                </td>
                <td>
                    <span class="badge ${statusClass}">
                        <i class="${statusIcon} me-1"></i>
                        ${statusText}
                    </span>
                </td>
                <td>
                    <div>
                        ${this.formatDate(payment.created_at)}
                        <br>
                        <small class="text-muted">
                            ${this.formatTime(payment.created_at)}
                        </small>
                    </div>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="paymentHistories.viewPaymentDetails(${payment.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${payment.status === 'pending' ? `
                            <button type="button" class="btn btn-sm btn-success" onclick="paymentHistories.updatePaymentStatus(${payment.id}, 'received')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="paymentHistories.updatePaymentStatus(${payment.id}, 'rejected')">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }
    
    getStatusClass(status) {
        switch(status) {
            case 'received': return 'bg-success';
            case 'pending': return 'bg-warning';
            case 'rejected': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
    
    getStatusIcon(status) {
        switch(status) {
            case 'received': return 'fas fa-check-circle';
            case 'pending': return 'fas fa-clock';
            case 'rejected': return 'fas fa-times-circle';
            default: return 'fas fa-question-circle';
        }
    }
    
    getStatusText(status) {
        switch(status) {
            case 'received': return 'Received';
            case 'pending': return 'Pending';
            case 'rejected': return 'Rejected';
            default: return 'Unknown';
        }
    }
    
    applyFilters() {
        this.currentFilters.status = document.getElementById('statusFilter').value;
        this.currentFilters.startDate = document.getElementById('startDate').value;
        this.currentFilters.endDate = document.getElementById('endDate').value;
        
        this.loadPaymentHistories(true);
    }
    
    clearFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('periodFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('customDateRange').style.display = 'none';
        
        this.currentFilters = {
            status: '',
            startDate: '',
            endDate: ''
        };
        
        this.loadPaymentHistories(true);
    }
    
    handlePeriodChange(period) {
        const customDateRange = document.getElementById('customDateRange');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        if (period === 'custom') {
            customDateRange.style.display = 'block';
            return;
        }
        
        customDateRange.style.display = 'none';
        
        const today = new Date();
        let start, end;
        
        switch(period) {
            case 'today':
                start = end = today;
                break;
            case 'week':
                start = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                end = today;
                break;
            case 'month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = today;
                break;
            case 'quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                start = new Date(today.getFullYear(), quarter * 3, 1);
                end = today;
                break;
            case 'year':
                start = new Date(today.getFullYear(), 0, 1);
                end = today;
                break;
            default:
                start = end = null;
        }
        
        if (start && end) {
            startDate.value = this.formatDateForInput(start);
            endDate.value = this.formatDateForInput(end);
        } else {
            startDate.value = '';
            endDate.value = '';
        }
    }
    
    loadMore() {
        if (!this.hasMore || this.isLoading) return;
        this.loadPaymentHistories(false);
    }
    
    updateLoadMoreButton() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (!this.hasMore) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'inline-block';
        }
    }
    
    async viewPaymentDetails(paymentId) {
        try {
            const response = await this.makeRequest('get_payment_details', {
                payment_id: paymentId
            });
            
            if (response.success) {
                const payment = response.payment;
                const modalContent = document.getElementById('paymentDetailsContent');
                
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Payment ID:</strong></td>
                                    <td>#${payment.id}</td>
                                </tr>
                                <tr>
                                    <td><strong>Method:</strong></td>
                                    <td>${this.escapeHtml(payment.payment_method)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td class="text-success fw-bold">$${parseFloat(payment.amount).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge ${this.getStatusClass(payment.status)}">
                                            <i class="${this.getStatusIcon(payment.status)} me-1"></i>
                                            ${this.getStatusText(payment.status)}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Account Name:</strong></td>
                                    <td>${this.escapeHtml(payment.account_name)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Account Number:</strong></td>
                                    <td>${this.escapeHtml(payment.account_number)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Timeline</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>${this.formatDateTime(payment.created_at)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>${this.formatDateTime(payment.updated_at)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    ${payment.transaction_screenshot ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Transaction Screenshot</h6>
                                <img src="uploads/transaction_screenshots/${payment.transaction_screenshot}" 
                                     class="img-fluid rounded" 
                                     alt="Transaction Screenshot"
                                     style="max-height: 300px;">
                            </div>
                        </div>
                    ` : ''}
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
                modal.show();
            } else {
                this.showAlert('Error loading payment details: ' + response.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('An error occurred while loading payment details', 'danger');
        }
    }
    
    updatePaymentStatus(paymentId, status) {
        this.currentPaymentId = paymentId;
        this.newStatus = status;
        
        const statusText = status === 'received' ? 'Received' : 'Rejected';
        document.getElementById('newStatusText').textContent = statusText;
        
        const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        modal.show();
    }
    
    async confirmStatusUpdate() {
        try {
            const response = await this.makeRequest('update_payment_status', {
                payment_id: this.currentPaymentId,
                status: this.newStatus
            });
            
            if (response.success) {
                this.showAlert('Payment status updated successfully', 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
                modal.hide();
                
                // Reload payment histories
                this.loadPaymentHistories(true);
            } else {
                this.showAlert('Error updating payment status: ' + response.message, 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('An error occurred while updating payment status', 'danger');
        }
    }
    
    async makeRequest(action, data = {}) {
        const sessionToken = localStorage.getItem('partner_session_token');
        
        const response = await fetch('api/partner_payment_histories.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': sessionToken
            },
            body: JSON.stringify({
                action: action,
                session_token: sessionToken,
                ...data
            })
        });
        
        return await response.json();
    }
    
    showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at top of content section
        const contentSection = document.querySelector('.content-section');
        contentSection.insertBefore(alertDiv, contentSection.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
    
    formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    formatDateForInput(date) {
        return date.toISOString().split('T')[0];
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global functions for onclick handlers
function viewPaymentDetails(paymentId) {
    paymentHistories.viewPaymentDetails(paymentId);
}

function updatePaymentStatus(paymentId, status) {
    paymentHistories.updatePaymentStatus(paymentId, status);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.paymentHistories = new PartnerPaymentHistories();
});
