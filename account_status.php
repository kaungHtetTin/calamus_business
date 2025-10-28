<?php
$pageTitle = 'Account Status';
include 'layout/header.php';
?>

<style>
	/* Google Console-like minimal styling */
	.status-container { background: #fafafa; }
	.google-card { background: #fff; border: 1px solid #e8eaed; border-radius: 8px; }
	.google-card .card-header { padding: 16px 20px; border-bottom: 1px solid #e8eaed; }
	.google-card .card-body { padding: 20px; }
	.status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
	.status-item { display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 1px solid #e8eaed; border-radius: 8px; background: #fff; position: relative; }
	.status-meta { color: #5f6368; font-size: 12px; margin-top: 4px; }
	.chip { border-radius: 16px; padding: 4px 10px; font-size: 12px; font-weight: 500; border: 1px solid #dadce0; background: #f8f9fa; color: #202124; }
	.chip-success { background: #e6f4ea; color: #137333; border-color: #c7e5cc; }
	.chip-danger { background: #fce8e6; color: #c5221f; border-color: #f4c7c3; }
	.chip-warning { background: #fef7e0; color: #b06000; border-color: #f7d8a8; }
	.chip-info { background: #f8f9fa; color: #202124; }
	.icon-pill { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: #f1f3f4; color: #5f6368; margin-right: 12px; }
	.status-left { display: flex; align-items: center; }
	.section-title { font-size: 16px; font-weight: 500; color: #202124; margin: 0; }
	.section-subtitle { font-size: 13px; color: #5f6368; margin: 4px 0 0 0; }
	.helper { font-size: 13px; color: #5f6368; }
	.status-item .google-btn { font-size: 12px;
		 color: #202124; border-radius: 4px; padding: 3px; 
		position: absolute; right: 2px; top: 2px;}
	.status-item .google-btn:hover { background: #f8f9fa; }
	.header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }

	@media (max-width: 768px) { .status-grid { grid-template-columns: 1fr; } }
</style>

<div class="status-container">
	<div class="google-card mb-3">
		<div class="card-header">
			<div class="header-row">
				<div>
					<h5 class="section-title">Account status</h5>
					<p class="section-subtitle">These 5 items are required to receive payments</p>
				</div>
				<div class="helper">Updated just now</div>
			</div>
		</div>
		<div class="card-body">
			<?php
			// Compute statuses
			$emailVerified = !empty($currentPartner['email_verified']) ? 1 : 0;
			$accountVerified = !empty($currentPartner['account_verified']) ? 1 : 0;
			$isActive = (isset($currentPartner['status']) && $currentPartner['status'] === 'active') ? 'active' : 'inactive';
			$personalInfoComplete = (!empty($currentPartner['address']) && !empty($currentPartner['city']) && !empty($currentPartner['state']) && !empty($currentPartner['national_id_card_number'])) ? 1 : 0;
			$paymentMethodsManager = new PaymentMethodsManager();
			$partnerPaymentMethods = $paymentMethodsManager->getPartnerPaymentMethods($currentPartner['id']);
			$hasPaymentMethod = !empty($partnerPaymentMethods) ? 1 : 0;
			?>

			<div class="status-grid">
				<div class="status-item">
					<div class="status-left">
						<span class="icon-pill"><i class="fas fa-envelope"></i></span>
						<div>
							<div>Email verified</div>
							<div class="status-meta">Your email must be verified</div>
						</div>
					</div>
					<div class="actions">
						<span class="chip <?php echo $emailVerified ? 'chip-success' : 'chip-danger'; ?>"><?php echo $emailVerified ? 'Verified' : 'Not Verified'; ?></span>
					</div>
				</div>

				<div class="status-item">
					<div class="status-left">
						<span class="icon-pill"><i class="fas fa-credit-card"></i></span>
						<div>
							<div>Payment method</div>
							<div class="status-meta">Add at least one method</div>
						</div>
					</div>
					<div class="actions">
						<span class="chip <?php echo $hasPaymentMethod ? 'chip-success' : 'chip-danger'; ?>"><?php echo $hasPaymentMethod ? 'added' : 'missing'; ?></span>
						
					</div>
					<a href="partner_payment_methods.php" class="google-btn ms-2"><i class="fas fa-edit"></i></a>
				</div>

				<div class="status-item">
					<div class="status-left">
						<span class="icon-pill"><i class="fas fa-id-card"></i></span>
						<div>
							<div>Personal information</div>
							<div class="status-meta">Address and national ID</div>
						</div>
					</div>
					<div class="actions">
						<span class="chip <?php echo $personalInfoComplete ? 'chip-success' : 'chip-danger'; ?>"><?php echo $personalInfoComplete ? 'complete' : 'incomplete'; ?></span>
						
					</div>
					<a href="profile.php" class="google-btn ms-2"><i class="fas fa-edit"></i></a>
				</div>

				<div class="status-item">
					<div class="status-left">
						<span class="icon-pill"><i class="fas fa-shield-check"></i></span>
						<div>
							<div>Account verified</div>
							<div class="status-meta">Admin will review your info</div>
						</div>
					</div>
					<div class="actions">
						<span class="chip <?php echo $accountVerified ? 'chip-success' : 'chip-warning'; ?>"><?php echo $accountVerified ? 'verified' : 'in review'; ?></span>
					</div>
				</div>

				<div class="status-item">
					<div class="status-left">
						<span class="icon-pill"><i class="fas fa-user-check"></i></span>
						<div>
							<div>Account status</div>
							<div class="status-meta">Your account must be active</div>
						</div>
					</div>
					<div class="actions">
						<span class="chip <?php echo $isActive === 'active' ? 'chip-success' : 'chip-danger'; ?>"><?php echo $isActive; ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="google-card">
		<div class="card-body">
			<div class="helper"><i class="fas fa-info-circle me-2"></i>To receive payments, ensure all items above are satisfied.</div>
		</div>
	</div>
</div>

<?php include 'layout/footer.php'; ?>

