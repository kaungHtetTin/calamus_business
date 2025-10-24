<?php
require_once 'affiliate_tracker.php';

// Initialize affiliate tracker
$tracker = new AffiliateTracker();

// Get affiliate code from URL
$affiliateCode = $_GET['ref'] ?? '';

if (empty($affiliateCode)) {
    // Redirect to homepage if no affiliate code
    header('Location: index.php');
    exit;
}

// Validate affiliate link
$validation = $tracker->validateAffiliateLink($affiliateCode);

if (!$validation['valid']) {
    // Redirect to homepage if invalid affiliate link
    header('Location: index.php');
    exit;
}

$affiliateLink = $validation['link'];

// Track the click
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer = $_SERVER['HTTP_REFERER'] ?? '';

$trackingResult = $tracker->trackClick($affiliateCode, $ipAddress, $userAgent, $referrer);

if ($trackingResult['success']) {
    $redirectUrl = $trackingResult['redirect_url'];
} else {
    $redirectUrl = 'index.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Language Learning Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .cta-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
        .language-badge {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4 mb-4">Master Languages with Expert Teachers</h1>
                    <p class="lead mb-4">Join thousands of students learning English, Chinese, Japanese, Korean, and Russian with our comprehensive online courses.</p>
                    
                    <!-- Language Badges -->
                    <div class="mb-4">
                        <span class="language-badge">English</span>
                        <span class="language-badge">Chinese</span>
                        <span class="language-badge">Japanese</span>
                        <span class="language-badge">Korean</span>
                        <span class="language-badge">Russian</span>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?php echo $redirectUrl; ?>" class="btn btn-light btn-lg px-4 me-md-2">
                            <i class="fas fa-play me-2"></i>Start Learning Now
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 mb-4">Why Choose Our Platform?</h2>
                    <p class="lead">Experience the most effective way to learn languages online</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Expert Teachers</h5>
                            <p class="card-text">Learn from certified native speakers with years of teaching experience.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <i class="fas fa-video fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Interactive Lessons</h5>
                            <p class="card-text">Engage with high-quality video lessons, quizzes, and interactive exercises.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Learn Anywhere</h5>
                            <p class="card-text">Access your courses on any device, anytime, anywhere with our mobile app.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <i class="fas fa-certificate fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Certificates</h5>
                            <p class="card-text">Earn recognized certificates upon course completion to boost your career.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Community</h5>
                            <p class="card-text">Join a vibrant community of language learners and practice together.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">24/7 Support</h5>
                            <p class="card-text">Get help whenever you need it with our dedicated support team.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="h2 text-primary mb-2">50,000+</div>
                    <div class="text-muted">Active Students</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="h2 text-primary mb-2">200+</div>
                    <div class="text-muted">Expert Teachers</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="h2 text-primary mb-2">1,000+</div>
                    <div class="text-muted">Video Lessons</div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="h2 text-primary mb-2">5</div>
                    <div class="text-muted">Languages</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 mb-4">Ready to Start Your Language Journey?</h2>
                    <p class="lead mb-4">Join thousands of successful language learners and unlock new opportunities.</p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary btn-lg px-4 me-md-2">
                            <i class="fas fa-rocket me-2"></i>Get Started Free
                        </a>
                        <a href="courses.php?ref=<?php echo $affiliateCode; ?>" class="btn btn-outline-primary btn-lg px-4">
                            <i class="fas fa-book me-2"></i>Browse Courses
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            30-day money-back guarantee • Cancel anytime
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Language Learning Platform</h5>
                    <p class="text-muted">Master languages with expert teachers and interactive lessons.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="mb-2">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                    </div>
                    <small class="text-muted">© 2024 Language Learning Platform. All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto-redirect after 10 seconds -->
    <script>
        let countdown = 10;
        const countdownElement = document.createElement('div');
        countdownElement.className = 'position-fixed bottom-0 end-0 m-3 bg-primary text-white p-2 rounded';
        countdownElement.innerHTML = `Redirecting in <span id="countdown">${countdown}</span> seconds...`;
        document.body.appendChild(countdownElement);
        
        const timer = setInterval(() => {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '<?php echo $redirectUrl; ?>';
            }
        }, 1000);
    </script>
</body>
</html>
