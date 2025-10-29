<?php
require_once 'classes/autoload.php';

$pageTitle = 'Mobile Money';
include 'layout/header.php';


$paymentMethodsManager = new PaymentMethodsManager();
$paymentMethods = $paymentMethodsManager->getPartnerPaymentMethods($currentPartner['id']);

?>

<div class="content-section">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-mobile-alt me-2"></i>Mobile Money Accounts
            </h4>
            <p class="text-muted mb-0">Manage your mobile money accounts for receiving commissions</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
            <i class="fas fa-plus me-2"></i>Add Mobile Money Account
        </button>
    </div>

    <!-- Payment Methods Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Your Mobile Money Accounts
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($paymentMethods)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Mobile Money Accounts</h5>
                    <p class="text-muted">You haven't added any mobile money accounts yet.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
                        <i class="fas fa-plus me-2"></i>Add Your First Mobile Money Account
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mobile Money Service</th>
                                <th>Account Name</th>
                                <th>Phone Number</th>
                                <th>Added Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paymentMethods as $method): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-mobile-alt me-1"></i>
                                            <?php echo htmlspecialchars($method['payment_method']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($method['account_name']); ?></td>
                                    <td>
                                        <code><?php echo htmlspecialchars($method['account_number']); ?></code>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($method['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editPaymentMethod(<?php echo htmlspecialchars(json_encode($method)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deletePaymentMethod(<?php echo $method['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Payment Method Modal -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentMethodModalLabel">
                    <i class="fas fa-plus me-2"></i>Add Mobile Money Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentMethodForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Mobile Money Service</label>
                        <select class="form-select" id="paymentMethod" name="payment_method" required>
                            <option value="">Select Mobile Money Service</option>
                            <option value="KBZ Pay">KBZ Pay</option>
                            <option value="Wave Pay">Wave Pay</option>
                            <option value="AYA Pay">AYA Pay</option>
                            <option value="Mytel Pay">Mytel Pay</option>
                        </select>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Choose your mobile money service provider
                            </small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="accountName" class="form-label">Account Name</label>
                        <input type="text" class="form-control" id="accountName" name="account_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="accountNumber" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="accountNumber" name="account_number" placeholder="e.g.,09123456789" required>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter your mobile money phone number
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Mobile Money Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Payment Method Modal -->
<div class="modal fade" id="editPaymentMethodModal" tabindex="-1" aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentMethodModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Mobile Money Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPaymentMethodForm">
                <input type="hidden" id="editPaymentMethodId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editPaymentMethod" class="form-label">Mobile Money Service</label>
                        <select class="form-select" id="editPaymentMethod" name="payment_method" required>
                            <option value="">Select Mobile Money Service</option>
                            <option value="KBZ Pay">KBZ Pay</option>
                            <option value="Wave Pay">Wave Pay</option>
                            <option value="AYA Pay">AYA Pay</option>
                            <option value="Mytel Pay">Mytel Pay</option>
                        </select>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Choose your mobile money service provider
                            </small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editAccountName" class="form-label">Account Name</label>
                        <input type="text" class="form-control" id="editAccountName" name="account_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAccountNumber" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="editAccountNumber" name="account_number" placeholder="e.g., 09123456789" required>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter your mobile money phone number
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Mobile Money Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/payment_methods.js"></script>
<?php include 'layout/footer.php'; ?>
