<!-- Footer Content -->
<footer class="footer bg-light border-top mt-5">
    <div class="container py-4">
        <div class="row">
            <!-- Quick Access Links -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="fw-bold mb-3 text-primary">
                    <i class="fas fa-rocket me-2"></i>Quick Access
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="dashboard.php" class="text-decoration-none text-muted">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="earning_history.php" class="text-decoration-none text-muted">
                            <i class="fas fa-chart-line me-2"></i>Earning History
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="partner_payment_methods.php" class="text-decoration-none text-muted">
                            <i class="fas fa-mobile-alt me-2"></i>Payment Methods
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="profile.php" class="text-decoration-none text-muted">
                            <i class="fas fa-user me-2"></i>Profile Settings
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Company Information -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="fw-bold mb-3 text-primary">
                    <i class="fas fa-building me-2"></i>Company
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="https://www.calamuseducation.com/calamus/about_us.php" class="text-decoration-none text-muted">
                            <i class="fas fa-info-circle me-2"></i>About Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="https://www.calamuseducation.com/app-portfolio/easy-english.php" class="text-decoration-none text-muted">
                            <i class="fas fa-mobile-alt"></i> Easy English
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="https://www.calamuseducation.com/app-portfolio/easy-korean.php" class="text-decoration-none text-muted">
                            <i class="fas fa-mobile-alt"></i> English Korean
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="https://www.calamuseducation.com" class="text-decoration-none text-muted" data-bs-toggle="modal">
                            <i class="fas fa-globe me-2"></i>Learning Website
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support & Help -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="fw-bold mb-3 text-primary">
                    <i class="fas fa-life-ring me-2"></i>Support
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="help.php" class="text-decoration-none text-muted">
                            <i class="fas fa-question-circle me-2"></i>Help Center
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#faqModal">
                            <i class="fas fa-question me-2"></i>FAQ
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#reportModal">
                            <i class="fas fa-flag me-2"></i>Report Issue
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal & Contact -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="fw-bold mb-3 text-primary">
                    <i class="fas fa-gavel me-2"></i>Legal & Contact
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="terms_conditions.php" class="text-decoration-none text-muted">
                            <i class="fas fa-file-contract me-2"></i>Terms & Conditions
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="privacy_policy.php" class="text-decoration-none text-muted">
                            <i class="fas fa-shield-alt me-2"></i>Privacy Policy
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="cookie_policy.php" class="text-decoration-none text-muted">
                            <i class="fas fa-cookie-bite me-2"></i>Cookie Policy
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="contact_us.php" class="text-decoration-none text-muted">
                            <i class="fas fa-phone me-2"></i>Contact Us
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Social Media & Additional Links -->
        <div class="row border-top pt-4 mt-4">
            <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center flex-wrap">
                  
                </div>
            </div>
            <div class="col-md-6 text-md-end text-center">
                <p class="mb-0 text-muted">
                    <i class="fas fa-copyright me-1"></i>
                    <?php echo date('Y'); ?> Partner Portal. All rights reserved.
                </p>
                <small class="text-muted">Powered by Calamus Business Solutions</small>
            </div>
        </div>
    </div>
</footer>


<!-- FAQ Modal -->
<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question me-2"></i>Frequently Asked Questions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How are my earnings calculated?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Your earnings are calculated based on completed transactions and your commission rate. Each transaction generates a commission based on the agreed percentage.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                When will I receive my payments?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Payments are processed within 3-5 business days after transactions are completed. You can track payment status in your earning history.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                How do I update my payment information?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can update your payment methods in the "Payment Methods" section of your dashboard. Changes take effect immediately for new transactions.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What if I have a problem with a transaction?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Contact our support team immediately. We'll investigate the issue and work to resolve it as quickly as possible.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Additional Modals (simplified) -->
<div class="modal fade" id="aboutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">About Us</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>We are a leading partner platform connecting businesses with opportunities to grow their revenue through strategic partnerships.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Found a bug or issue? Please report it to our technical team for immediate attention.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="socialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Social Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Follow us on social media for updates, tips, and community discussions.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>