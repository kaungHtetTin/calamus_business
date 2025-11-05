<?php
$pageTitle = 'Terms & Conditions';
ob_start();
?>

<!-- Terms & Conditions Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-contract me-2"></i>Terms & Conditions</h2>
        <div class="text-muted">
            <small>Last updated: <?php echo date('F j, Y'); ?></small>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="mb-4">
                        <h4>Affiliate Program Terms and Conditions</h4>
                        <p><strong>Effective date:</strong> 11-5-2025</p>
                        <p><strong>Company:</strong> Calamus Education</p>
                        <p><strong>Program:</strong> Affiliate Program</p>
                    </div>

                    <div class="mb-4">
                        <h4>1. Summary</h4>
                        <p>This program allows approved affiliates to promote Calamus Education products using their unique referral codes. Customers who purchase with a valid code receive a discount, and the affiliate earns a commission on each confirmed sale.</p>
                        <p>All affiliates and customers participating in the program are deemed to have accepted these terms and conditions.</p>
                    </div>

                    <div class="mb-4">
                        <h4>2. Affiliate Registration</h4>
                        <ul>
                            <li>Affiliates must register with accurate personal information including full name, phone number, email, and NRC/passport.</li>
                            <li>Each approved affiliate will receive a unique referral code issued by the company.</li>
                            <li>The company reserves the right to accept or reject any registration without obligation to provide reasons.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>3. Commission and Payments</h4>
                        <ul>
                            <li>Affiliates earn a 10% commission for each confirmed sale made using their code.</li>
                            <li>Commissions are calculated only after the sale is confirmed and no refund or cancellation is requested by the customer.</li>
                            <li>Payments are made monthly via WavePay or KBZPay (other methods can be arranged).</li>
                            <li>The minimum payout amount is 10,000 MMK. Balances below this threshold will roll over to the next month until the minimum is met.</li>
                            <li>The company may adjust commission rates at any time with prior notice.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>4. Customer Discounts</h4>
                        <ul>
                            <li>Customers using a valid referral code at checkout receive a 10% discount.</li>
                            <li>Discounts apply only when the code is entered at the time of purchase.</li>
                            <li>Each customer may use a referral code only once.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>5. Sale Confirmation</h4>
                        <p>A sale is considered confirmed only when all of the following conditions are met:</p>
                        <ul>
                            <li>Full payment has been received.</li>
                            <li>Payment has been verified as valid by the company’s system.</li>
                        </ul>
                        <p>The company may suspend or deny commission payments for unverified or suspicious orders.</p>
                    </div>

                    <div class="mb-4">
                        <h4>6. Fraud and Abuse</h4>
                        <p>Attempting fake sales, self-purchases with codes, or manipulating the system may result in:</p>
                        <ul>
                            <li>Immediate suspension or termination from the program.</li>
                            <li>Forfeiture of all unpaid commissions.</li>
                        </ul>
                        <p>Affiliates must not use misleading, deceptive, illegal, or unethical advertising methods to promote their codes.</p>
                        <p>The company reserves the right to review and verify all affiliate activities.</p>
                    </div>

                    <div class="mb-4">
                        <h4>7. Affiliate Responsibilities</h4>
                        <ul>
                            <li>Promote Calamus Education products honestly and accurately.</li>
                            <li>Do not represent yourself as company staff or an official partner beyond the affiliate relationship.</li>
                            <li>Follow all marketing guidelines and comply with applicable local laws.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>8. Inactive Accounts</h4>
                        <ul>
                            <li>Affiliates with no confirmed sales for 3 months may be marked as inactive.</li>
                            <li>Inactive accounts may be removed or reactivated at the company’s discretion.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>9. Modifications or Suspension</h4>
                        <ul>
                            <li>The company may modify, pause, or terminate the program at any time without prior notice.</li>
                            <li>Upon termination, commissions for confirmed sales will be paid according to standard procedures.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>10. Limitation of Liability</h4>
                        <ul>
                            <li>The company is not responsible for lost earnings due to system downtime or technical issues.</li>
                            <li>Delays caused by incorrect affiliate payment information are not the company’s responsibility.</li>
                            <li>Disputes between affiliates and customers are outside the company’s liability.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>11. Dispute Resolution</h4>
                        <ul>
                            <li>Any dispute must be submitted to the support team within 7 days after payment processing.</li>
                            <li>For commission and sale confirmation matters, the company’s decision is final.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>12. Contact</h4>
                        <ul>
                            <li>Email: calamuseducation@gmail.com</li>
                            <li>Telegram / Viber: 09688683805</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Agreement</h6>
                        <p class="mb-0">By participating in this program, you confirm that you have read, understood, and agreed to all terms above.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout/public_layout.php';
?>
