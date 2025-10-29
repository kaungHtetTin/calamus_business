<?php
$pageTitle = 'Contact Us';
ob_start();
?>

<!-- Contact Us Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-envelope me-2"></i>Contact Us</h2>
        <div class="text-muted">
            <small>We're here to help you succeed</small>
        </div>
    </div>

    <div class="row">
        <!-- Contact Information -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6><i class="fas fa-envelope me-2 text-primary"></i>Email Support</h6>
                        <p class="mb-2">
                            <strong>Business Inquiries:</strong><br>
                            <a href="mailto:business@calamuseducation.com" class="text-decoration-none">business@calamuseducation.com</a>
                        </p>
                        <p class="mb-2">
                            <strong>Technical Support:</strong><br>
                            <a href="mailto:kaunghtettin17204@gmail.com" class="text-decoration-none">kaunghtettin17204@gmail.com</a>
                        </p>
                        <p class="mb-0">
                            <strong>General Support:</strong><br>
                            <a href="mailto:calamuseducation@gmail.com" class="text-decoration-none">calamuseducation@gmail.com</a>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6><i class="fas fa-phone me-2 text-primary"></i>Phone Support</h6>
                        <p class="mb-2">
                            <strong>Phone 1:</strong><br>
                            <a href="tel:+959682537158" class="text-decoration-none">09682537158</a>
                        </p>
                        <p class="mb-0">
                            <strong>Phone 2:</strong><br>
                            <a href="tel:+959688683805" class="text-decoration-none">09688683805</a>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i>Office Address</h6>
                        <p class="mb-0">
                            Yangon, Myanmar
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6><i class="fas fa-clock me-2 text-primary"></i>Business Hours</h6>
                        <p class="mb-2">
                            <strong>Monday - Friday:</strong><br>
                            9:00 AM - 6:00 PM (Myanmar Time)
                        </p>
                        <p class="mb-2">
                            <strong>Saturday:</strong><br>
                            10:00 AM - 4:00 PM (Myanmar Time)
                        </p>
                        <p class="mb-0">
                            <strong>Sunday:</strong><br>
                            Closed
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Send us a Message</h5>
                </div>
                <div class="card-body">
                    <form id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="company" class="form-label">Company/Organization</label>
                            <input type="text" class="form-control" id="company" name="company">
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="partnership">Partnership Opportunity</option>
                                <option value="technical">Technical Support</option>
                                <option value="payment">Payment Issues</option>
                                <option value="account">Account Management</option>
                                <option value="feedback">Feedback & Suggestions</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority Level</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low">Low - General inquiry</option>
                                <option value="medium" selected>Medium - Standard support</option>
                                <option value="high">High - Urgent issue</option>
                                <option value="critical">Critical - System down</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" placeholder="Please describe your inquiry in detail..." required></textarea>
                        </div>

                        <!-- <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Subscribe to our newsletter for updates and tips
                                </label>
                            </div>
                        </div> -->

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                <label class="form-check-label" for="privacy">
                                    I agree to the <a href="privacy_policy.php" target="_blank">Privacy Policy</a> and consent to the processing of my personal data *
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" style="color: red;">
                             This feature is not available yet.
                        </div>


                        <!-- <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary me-md-2" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row mt-4">
        <div class="col-lg-12 mb-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How quickly will I receive a response?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We typically respond to all inquiries within 24 hours during business days. Urgent technical issues receive priority attention.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What information should I include in my message?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Please include your partner ID, detailed description of the issue, steps to reproduce (if applicable), and any error messages you've encountered.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Can I schedule a call with support?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, for complex issues, we can schedule a phone call or video conference. Please mention this preference in your message.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- 
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-headset me-2"></i>Support Resources</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-book me-2"></i>Partner Portal User Guide
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-video me-2"></i>Video Tutorials
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-download me-2"></i>Download Resources
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-comments me-2"></i>Community Forum
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-bug me-2"></i>Report a Bug
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-lightbulb me-2"></i>Feature Requests
                        </a>
                    </div>
                </div>
            </div>
        </div> -->
    </div>

    <!-- Social Media & Additional Contact -->
    <!-- <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Connect With Us</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="fab fa-facebook fa-2x text-primary mb-2"></i>
                                <h6>Facebook</h6>
                                <p class="small text-muted">Follow us for updates and community discussions</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Follow</a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="fab fa-twitter fa-2x text-info mb-2"></i>
                                <h6>Twitter</h6>
                                <p class="small text-muted">Get the latest news and announcements</p>
                                <a href="#" class="btn btn-outline-info btn-sm">Follow</a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="fab fa-linkedin fa-2x text-primary mb-2"></i>
                                <h6>LinkedIn</h6>
                                <p class="small text-muted">Connect with our professional network</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Connect</a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="fab fa-youtube fa-2x text-danger mb-2"></i>
                                <h6>YouTube</h6>
                                <p class="small text-muted">Watch tutorials and product demos</p>
                                <a href="#" class="btn btn-outline-danger btn-sm">Subscribe</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Show success message (in a real implementation, this would send to a server)
    alert('Thank you for your message! We will get back to you within 24 hours.');
    
    // Reset form
    this.reset();
});

function resetForm() {
    document.getElementById('contactForm').reset();
}
</script>

<?php
$content = ob_get_clean();
include 'layout/public_layout.php';
?>
