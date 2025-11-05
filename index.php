<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/app.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        /* Navbar Styles */
        .welcome-navbar {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .welcome-navbar .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white !important;
        }
        
        .welcome-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .welcome-navbar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
        }
        
        .welcome-navbar .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }
        
        .welcome-navbar .btn-outline-light:hover {
            background-color: white;
            color: #4a5568;
        }
        
        .welcome-navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .welcome-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Cover Section */
        .cover-section {
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
        }
        
        .cover-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 85, 104, 0.85) 0%, rgba(113, 128, 150, 0.85) 100%);
        }
        
        .cover-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            padding: 2rem;
            max-width: 800px;
        }
        
        .cover-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .cover-content p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        .cover-content .btn {
            padding: 0.75rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        /* Introduction Section */
        .intro-section {
            padding: 5rem 0;
            background: white;
        }
        
        .intro-section h2 {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .intro-section p {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        /* Myanmar Section */
        .myanmar-section {
            padding: 5rem 0;
            background: #f8f9fa;
        }
        
        .myanmar-section h2 {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        
        .feature-card .icon-wrapper {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .feature-card .icon-wrapper i {
            font-size: 1.5rem;
            color: white;
        }
        
        .feature-card h4 {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        
        .feature-card p {
            color: #6c757d;
            line-height: 1.6;
            margin: 0;
        }
        
        .feature-card ul {
            list-style: none;
            padding: 0;
            margin-top: 1.5rem;
            margin-bottom: 0;
        }
        
        .feature-card ul li {
            margin-bottom: 0.75rem;
            padding-left: 1.5rem;
            position: relative;
        }
        
        .feature-card ul li::before {
            content: '→';
            position: absolute;
            left: 0;
            color: #4a5568;
            font-size: 1rem;
            font-weight: bold;
            top: 0;
        }
        
        .feature-card ul li a {
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .feature-card ul li a:hover {
            color: #718096;
            transform: translateX(5px);
        }
        
        .myanmar-section .container {
            max-width: 800px;
        }
        
        /* Footer */
        .welcome-footer {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            color: white;
            padding: 3rem 0 1.5rem 0;
        }
        
        .welcome-footer h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .welcome-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .welcome-footer a:hover {
            color: white;
        }
        
        .welcome-footer ul {
            list-style: none;
            padding: 0;
        }
        
        .welcome-footer ul li {
            margin-bottom: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .cover-content h1 {
                font-size: 2.5rem;
            }
            
            .cover-content p {
                font-size: 1.1rem;
            }
            
            .intro-section {
                padding: 3rem 0;
            }
            
            .myanmar-section {
                padding: 3rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg welcome-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="logo.png" alt="Calamus" width="40" height="40" class="me-2">
                Calamus Education
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="partner_login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="partner_register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cover Section -->
    <section class="cover-section">
        <div class="container">
            <div class="cover-content">
                <h1>Join Our Partner Program</h1>
                <p>Unlock unlimited earning potential by becoming a Calamus Education partner. Help students achieve their language learning goals while building your own business.</p>
                <a href="partner_register.php" class="btn btn-light btn-lg">
                    <i class="fas fa-handshake me-2"></i>Get Started Today
                </a>
            </div>
        </div>
    </section>

    <!-- Myanmar Section -->
    <section class="myanmar-section">
        <div class="container">
            <h2>Affiliate Program အကြောင်း</h2>
            
            <div class="feature-card">
                <div class="icon-wrapper">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h4>Calamus Education ရဲ့ Affiliate Program ဆိုတာ ဘာလဲ</h4>
                <p>
                    Calamus Education ရဲ့ Affiliate Program သည် ကျောင်းသားများအား ဘာသာစကား သင်ယူဖို့ 
                    ကူညီရင်း ဝင်ငွေရရှိစေနိုင်သော အခွင့်အလမ်းတစ်ခုဖြစ်ပါတယ်။ Calamus Education ရဲ့ သင်တန်းများကို Facebook, Tiktok, Youtube အစရှိသည့် 
                    Social Media Platform များပေါ်ရှိ သင်၏ကိုယ်ပိုင် Channel, Page, Account များတွင် ပြန်လည်ကြောငြာ ပေးခြင်းဖြင့် 
                    ဝင်ငွေရရှိနိုင်မည့် အစီအစဉ်တစ်ခုဖြစ်ပါသည်။
                </p>
            </div>
            
            <div class="feature-card">
                <div class="icon-wrapper">
                    <i class="fas fa-box"></i>
                </div>
                <h4>ဘယ်ဟာတွေကို Affiliate Program နဲ့ ကြော်ငြာပေးရမှာလဲ</h4>
                <p>
                    သင့်အနေနဲ့ Calamus Education ရဲ့ အရည်အသွေးမြင့် သင်တန်းများကို ပြန်လည်ကြော်ငြာ ပေးရမည်ဖြစ်ပါသည်။ လက်ရှိအချိန်တွင် Calamus Education
                    တွင် English နှင့် Korean ဘာသာစကားသင်တန်းများကို ဖွင့်လစ််ထားရှိပါသည်။ သင်တန်းများနှင့် သင်ကြားရေး platform များ 
                    သင်တန်းဝင်ကြေးများကို အောက်ဖော်ပြပါ link များတွင် ဝင်ရောက်လေ့လာနိုင်ပါသည်။
                </p>
                <ul>
                    <li><a href="">English ဘာသာစကားသင်တန်းများ</a></li>
                    <li><a href="">Korean ဘာသာစကားသင်တန်းများ</a></li>
                </ul>
            </div>
            
            <div class="feature-card">
                <div class="icon-wrapper">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4>လုပ်ငန်းအစီအစဉ်</h4>
                <p>
                   အောက်ပါအတိုင်းဖြစ်ပါသည် ...
                </p>
                <ul>
                    <li>
                       <strong> Step 1</strong>: Calamus Education ၏ Business Partner အဖြစ် ဝင်ရောက် မှတ်ပုံတင်ရမည်။
                        <a href="partner_register.php">ဝင်ရောက်မှတ်ပုံတင်ရန် နှိပ်ပါ</a>
                    </li>
                    <li>
                        <strong> Step 2</strong>: မှတ်ပုံတင်ခြင်းလုပ်ငန်းပြီးဆုံးပါက ကိုယ်ပိုင် unique partner code ရရှိမှာဖြစ်ပါတယ်။
                    </li>
                    <li>
                        <strong> Step 3</strong>: သင်၏ကိုယ်ပိုင် Social Media Platform များတွင် Calamus Education ရဲ့ သင်တန်းများကို ပြန်လည်ကြောငြာ 
                        ရောင်းချပေးရမည်ဖြစ်ပါသည်။
                    </li>
                    <li>
                        <strong> Step 4</strong>: သင်ထံသို့ သင်တန်းတက်ရောက်ရန် ဆက်သွယ်မေးမြန်းလာသူများအား သင်၏ ကိုယ်ပိုင် Private Code နှင့် အတူအတူ
                        Calamus Education ၏ သို့ စေလွှတ်ဆက်သွယ်၍ သင်တန်းတက်ရောက်ရန် ကူညီပေးရမည်ဖြစ်ပါသည်။ 
                    </li>
                    <li>
                        <strong> Step 5</strong>: အဆိုပါ သင်တန်းတက်ရောက်လိုသူသည် Calamus Education ရဲ့ သင်တန်းများကို တက်ရောက်ရန် 
                        သင်တန်းကြေးပေးပြီးပြီးပါက သင်သည် commission ရရှိနိုင်မည်ဖြစ်ပါသည်။ သင်တန်းမတက်ရောက်ဖြစ်သော 
                        သူများအတွက် commission မရရှိနိုင်ပါ။
                    </li>

                </ul>
            </div>

            <div class="feature-card">
                <div class="icon-wrapper">
                    <i class="fas fa-gift"></i>
                </div>
                <h4>ဘယ်လိုအကျိုးကျေးဇူးရရှိနိုင်မလဲရရှိနိုင်မလဲ</h4>

                <ul>
                    <li>
                        <strong>သင်ကိုယ်တိုင်</strong> : Calamus Education ရဲ့ သင်တန်းများကို ပြန်လည်ကြောငြာ ရောင်းချပေးခြင်းဖြင့် 
                        commission ရရှိနိုင်မည်ဖြစ်ပါသည်။
                    </li>
                    <li>
                        <strong>သင်တန်းတက်ရောက်လိုသူများ</strong> သင်၏ Refer Code (Private Code) ဖြင့် လာရောက် သင်တန်း အပ်နံသူများသည်
                       သင်တန်းကြေး၏ 10 %  discount ရရှိနိုင်မည်ဖြစ်ပါသည်။
                    </li>
                </ul>
            </div>

             <div class="feature-card">
                <div class="icon-wrapper">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4>Calamus Education သို့ ဆက်သွယ်ရန်နည်းလမ်းများ</h4>
                <ul>
                    <li>
                        <strong>Easy Korean - Korean For Myanmar</strong> <br>
                        <a href="https://www.facebook.com/easykoreancalamus" target="_blank">https://www.facebook.com/easykoreancalamus</a>

                    </li>
                    <li>
                        <strong>Easy Korean - Tiktok Channel</strong> <br>
                        <a href="https://www.tiktok.com/@ekcalamus?_t=ZS-90dm6eBXj9B" target="_blank">https://www.tiktok.com/@ekcalamus?_t=ZS-90dm6eBXj9B</a>
                    </li>

                    <li>
                        <strong>Easy English - English For Myanmar</strong> <br>
                        <a href="https://www.facebook.com/easyenglishcalamus" target="_blank">https://www.facebook.com/easyenglishcalamus</a>

                    </li>
                    <li>
                        <strong>Easy English - Tiktok Channel</strong> <br>
                        <a href="https://www.tiktok.com/@freeenglishformyanmar?_t=ZS-90dq5yAFzHQ&_r=1" target="_blank">https://www.tiktok.com/@freeenglishformyanmar?_t=ZS-90dq5yAFzHQ&_r=1</a>
                    </li>

                    <li>
                        <strong>Calamus Education - Youtube Channel</strong> <br>
                        <a href="https://www.youtube.com/@calamuseducationmyanmar5078" target="_blank">https://www.youtube.com/@calamuseducationmyanmar5078</a>
                    </li>

                    <li>
                        <strong>Email Contact</strong> <br>
                        calamuseducation@gmail.com
                    </li>
                    <li>
                        <strong>Phone Contact</strong> <br>
                        09 688683805
                    </li>
                    
                </ul>

            </div>

 


        </div>
    </section>

    <!-- Footer -->
    <footer class="welcome-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>
                        <img src="logo.png" alt="Calamus" width="30" height="30" class="me-2">
                        Calamus Education
                    </h5>
                    <p class="text-white-50 small">Empowering language learning through quality education.</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Company</h5>
                    <ul>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#careers">Careers</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Legal</h5>
                    <ul>
                        <li><a href="#terms">Terms of Service</a></li>
                        <li><a href="#privacy">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Connect</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-white-50 small">&copy; <?php echo date('Y'); ?> Calamus Education. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
