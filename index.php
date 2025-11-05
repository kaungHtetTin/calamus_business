<?php
// Session check to toggle navbar/CTA based on authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = !empty($_SESSION['partner_session_token']);
?>
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
            padding: 0.25rem 0;
            min-height: auto;
        }
        
        .welcome-navbar .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: white !important;
            padding: 0.25rem 0;
        }
        
        .welcome-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            margin: 0 0.125rem;
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
        
        /* Terms Section */
        .terms-section {
            padding: 5rem 0;
            background: white;
        }
        
        .terms-section h2 {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .terms-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        
        .terms-header {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .terms-header h3 {
            color: white;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .terms-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.95rem;
        }
        
        .terms-content h4 {
            color: #4a5568;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .terms-content h4:first-child {
            margin-top: 0;
        }
        
        .terms-content p {
            color: #6c757d;
            line-height: 1.8;
            margin-bottom: 1rem;
            text-align: justify;
        }
        
        .terms-content ul {
            list-style: none;
            padding-left: 0;
        }
        
        .terms-content ul li {
            color: #6c757d;
            line-height: 1.8;
            margin-bottom: 0.75rem;
            padding-left: 1.5rem;
            position: relative;
        }
        
        .terms-content ul li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #4a5568;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .terms-section .container {
            max-width: 900px;
        }
        
        .terms-content a {
            color: #4a5568;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        
        .terms-content a:hover {
            color: #718096;
        }
        
        .terms-content .highlight-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .terms-content .highlight-box h4 {
            color: #856404;
            border-bottom: 2px solid #ffc107;
            margin-top: 0;
        }
        
        .terms-content .highlight-box p {
            color: #856404;
        }
        
        .terms-content .highlight-box ul li {
            color: #856404;
        }
        
        .terms-content .highlight-box ul li::before {
            color: #856404;
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
                <img src="logo.png" alt="Calamus" width="30" height="30" class="me-2">
                Calamus Education
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Welcome</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="partner_login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="partner_register.php">Register</a>
                    </li>
                    <?php endif; ?>
                    
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
                <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                </a>
                <?php else: ?>
                <a href="partner_register.php" class="btn btn-light btn-lg">
                    <i class="fas fa-handshake me-2"></i>Get Started Today
                </a>
                <?php endif; ?>
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
                        <a href="https://www.facebook.com/easykoreancalamus" target="_blank">Click Here</a>

                    </li>
                    <li>
                        <strong>Easy Korean - Tiktok Channel</strong> <br>
                        <a href="https://www.tiktok.com/@ekcalamus?_t=ZS-90dm6eBXj9B" target="_blank">Click Here</a>
                    </li>

                    <li>
                        <strong>Easy English - English For Myanmar</strong> <br>
                        <a href="https://www.facebook.com/easyenglishcalamus" target="_blank">Click Here</a>

                    </li>
                    <li>
                        <strong>Easy English - Tiktok Channel</strong> <br>
                        <a href="https://www.tiktok.com/@freeenglishformyanmar?_t=ZS-90dq5yAFzHQ&_r=1" target="_blank">Click Here</a>
                    </li>

                    <li>
                        <strong>Calamus Education - Youtube Channel</strong> <br>
                        <a href="https://www.youtube.com/@calamuseducationmyanmar5078" target="_blank">Click Here</a>
                    </li>

                    <li>
                        <strong>Email Contact</strong> <br>
                        calamuseducation@gmail.com
                    </li>
                    <li>
                        <strong>Phone Contact/ Viber/ Telegram</strong> <br>
                        09 688683805
                    </li>
                    
                </ul>

            </div>

 


        </div>
    </section>

    <!-- Terms and Conditions Section -->
    <section class="terms-section" id="terms-conditions-section">
        <div class="container">
            <h2>Affiliate Program ဆိုင်ရာ စည်းကမ်းသတ်မှတ်ချက်များ</h2>
            
            <div class="terms-card">
                <div class="terms-header">
                    <h3>Affiliate Program Terms and Conditions</h3>
                    <p>စတင်အသက်ဝင်သည့်နေ့: 11-5-2025 | ကုမ္ပဏီအမည်: Calamus Education | ပရိုဂရမ်အမည်: Affiliate Program</p>
                </div>
                
                <div class="terms-content">
                    <h4>၁။ အကျဉ်းချုပ်</h4>
                    <p>
                        ဤပရိုဂရမ်သည် အတည်ပြုထားသော ကိုယ်စားလှယ်များအား Calamus Education product များကို ၎င်းတို့၏ သီးသန့် referral code များ အသုံးပြု၍ ကြော်ငြာရောင်းချခွင့်ပြုပေးမည်ဖြစ်သည်။ ကိုယ်စားလှယ်၏ code ကို အသုံးပြု၍ ဝယ်ယူသော သုံးစွဲသူများသည် ဒစ်စကောင့် ရရှိမည်ဖြစ်ပြီး၊ ကိုယ်စားလှယ်သည် အတည်ပြုပြီးသော ရောင်းချမှုတစ်ခုစီအတွက် ကော်မရှင် (အကျိုးဆောင်ခ) ရရှိမည်ဖြစ်သည်။
                    </p>
                    <p>
                        ဤပရိုဂရမ်တွင် ပါဝင်သည့် ကိုယ်စားလှယ်များနှင့် သုံးစွဲသူများ အားလုံးတို့သည် ဤစည်းကမ်းသတ်မှတ်ချက်များကို သဘောတူသည်ဟု သတ်မှတ်သည်။
                    </p>

                    <h4>၂။ ကိုယ်စားလှယ် မှတ်ပုံတင်ခြင်း</h4>
                    <ul>
                        <li>ကိုယ်စားလှယ်များသည် အမည်အပြည့်အစုံ၊ ဖုန်းနံပါတ်၊ အီးမေးလ် နှင့် နိုင်ငံသားစိစစ်ရေးကတ်(သို့) ပတ်စ်ပို့ အပါအဝင် တိကျမှန်ကန်သော ကိုယ်ရေးအချက်အလက်များကို အသုံးပြု၍ မှတ်ပုံတင်ရမည်။</li>
                        <li>အတည်ပြုပြီးသော ကိုယ်စားလှယ်တစ်ဦးစီသည် ကုမ္ပဏီမှ ထုတ်ပေးမည့် သီးသန့် referral code တစ်ခု ရရှိမည်ဖြစ်သည်။</li>
                        <li>ကုမ္ပဏီသည် မည်သည့် မှတ်ပုံတင်ခြင်းကိုမဆို ဖြေရှင်းချက်မပေးဘဲ လက်ခံရန် သို့မဟုတ် ငြင်းပယ်ရန် အခွင့်အရေးရှိသည်။</li>
                    </ul>

                    <h4>၃။ ကော်မရှင် (အကျိုးဆောင်ခ) နှင့် ငွေပေးချေမှု</h4>
                    <ul>
                        <li>ကိုယ်စားလှယ်များသည် ၎င်းတို့၏ code ကို အသုံးပြု၍ ပြုလုပ်ခဲ့သော အတည်ပြုပြီး ရောင်းချမှုတိုင်းအတွက် ၁၀% ကော်မရှင် ရရှိမည်ဖြစ်သည်။</li>
                        <li>ရောင်းချမှုကို အတည်ပြုပြီးနောက်၊ သုံးစွဲသူမှ ငွေပြန်အမ်းခိုင်းခြင်း သို့မဟုတ် ပယ်ဖျက်ခြင်း မပြုလုပ်မှသာ ကော်မရှင်ကို တွက်ချက်မည်ဖြစ်သည်။</li>
                        <li>ငွေပေးချေမှုများကို လကုန်တိုင်း WavePay သို့ KBZPay မှတဆင့် ပြုလုပ်ပေးမည်။ (တခြားငွေပေးချေမည့်နည်းလမ်းများလည်းညှိနှိုင်းနိုင်သည်)</li>
                        <li>အနည်းဆုံး ငွေထုတ်ပမာဏမှာ ၁၀,၀၀၀ ကျပ် ဖြစ်သည်။ ဤပမာဏထက် နည်းသော လက်ကျန်ငွေများကို နောက်လသို့ပေါင်းထည့်၍ သတ်မှတ်ငွေထုတ်ပမာဏပြည့်မှထုတ်ပေးသွားမည်ဖြစ်သည်။</li>
                        <li>ကုမ္ပဏီသည် ကြိုတင်အကြောင်းကြားခြင်းဖြင့် ကော်မရှင်နှုန်းထားများကို အချိန်မရွေး ချိန်ညှိခွင့်ရှိသည်။</li>
                    </ul>

                    <h4>၄။ သုံးစွဲသူများအတွက် လျှော့စျေး</h4>
                    <ul>
                        <li>ဝယ်ယူမှုပြုလုပ်စဉ် တရားဝင် referral code ကို အသုံးပြုသော သုံးစွဲသူများသည် ၁၀% လျှော့စျေး ရရှိမည်ဖြစ်သည်။</li>
                        <li>လျှော့စျေးသည် ဝယ်ယူသည့်အချိန်တွင် code ထည့်သွင်းမှသာ အကျုံးဝင်မည်။</li>
                        <li>သုံးစွဲသူသည် Referral code ကို တစ်ခေါက်သာ အသုံးပြုနိုင်မည် ဖြစ်သည်။</li>
                    </ul>

                    <h4>၅။ ရောင်းချမှုများအား အတည်ပြုခြင်း</h4>
                    <p>ရောင်းချမှုတစ်ခုအား အောက်ပါအခြေအနေများနှင့် ပြည့်စုံမှသာ အတည်ပြုပြီးဖြစ်သည်ဟု မှတ်ယူမည်-</p>
                    <ul>
                        <li>ငွေကို အပြည့်အဝ လက်ခံရရှိခြင်း။</li>
                        <li>ငွေလက်ခံရရှိမှုအား ကုမ္ပဏီ၏ စနစ်မှ စစ်မှန်ကြောင်း အတည်ပြုခြင်း။</li>
                    </ul>
                    <p>ကုမ္ပဏီသည် အတည်မပြုရသေးသော သို့မဟုတ် သံသယဖြစ်ဖွယ် မှာယူမှုများအတွက် ကော်မရှင်ပေးချေမှုများကို ရပ်ဆိုင်းထားခြင်း သို့မဟုတ် ငြင်းပယ်ပိုင်ခွင့်ရှိသည်။</p>

                    <div class="highlight-box">
                        <h4>၆။ လိမ်လည်မှု နှင့် အလွဲသုံးစားပြုမှု</h4>
                        <p>ရောင်းချမှု အတုအယောင်များ ပြုလုပ်ရန် ကြိုးပမ်းမှု၊ မိမိဝယ်ယူရန်အတွက် ကုဒ်ထုတ်ခြင်း သို့မဟုတ် စနစ်အား လှည့်ဖြားခြင်းများ ပြုလုပ်ပါက အောက်ပါတို့ကို ဖြစ်စေမည်-</p>
                        <ul>
                            <li>ပရိုဂရမ်မှ ချက်ချင်း ဆိုင်းငံ့ခြင်း သို့မဟုတ် ရပ်စဲခြင်း။</li>
                            <li>မရရှိသေးသော ကော်မရှင်များအားလုံးကို ဆုံးရှုံးခြင်း။</li>
                        </ul>
                        <p>ကိုယ်စားလှယ်များသည် ၎င်းတို့၏ code ကို ကြော်ငြာရန်အတွက် မှားယွင်းသော၊ လှည့်ဖြားသော၊ တရားဉပဒေနှင့်မလွတ်ကင်းသော ကြော်ငြာများ၊ သို့မဟုတ် ကိုယ်ကျင့်တရားနှင့် မညီသော နည်းလမ်းများကို အသုံးမပြုရ။</p>
                        <p>ကုမ္ပဏီသည် ကိုယ်စားလှယ်များ၏ လုပ်ဆောင်မှုအားလုံးကို စစ်ဆေးအတည်ပြုပိုင်ခွင့်ရှိသည်။</p>
                    </div>

                    <h4>၇။ ကိုယ်စားလှယ်၏ တာဝန်များ</h4>
                    <ul>
                        <li>ကိုယ်စားလှယ်များသည် Calamus Education ၏ Product များ ကို ရိုးသားစွာနှင့် တိကျမှန်ကန်စွာ ကြော်ငြာရန် တာဝန်ရှိသည်။</li>
                        <li>ကိုယ်စားလှယ်များသည် ကုမ္ပဏီ၏ဝန်ထမ်း သို့မဟုတ် တရားဝင်မိတ်ဖက်အဖြစ် ကိုယ်စားပြုကြောင်း မပြောဆိုရ။</li>
                        <li>ကိုယ်စားလှယ်များသည် ကုမ္ပဏီ၏ စျေးကွက်ရှာဖွေရေး လမ်းညွှန်ချက်များနှင့် ပြည်တွင်းဥပဒေများအားလုံးကို လိုက်နာရမည်။</li>
                    </ul>

                    <h4>၈။ Active မဖြစ်သောအကောင့်များ</h4>
                    <ul>
                        <li>၃ လကြာသည်အထိ အတည်ပြုပြီး ရောင်းချမှု မရှိသော ကိုယ်စားလှယ်များကို Active မဖြစ်ဟု အမှတ်အသားပြုနိုင်သည်။</li>
                        <li>Active မဖြစ်သော အကောင့်များကို ဖျက်သိမ်းခြင်း သို့မဟုတ် ကုမ္ပဏီ၏ ခွင့်ပြုချက်ဖြင့် ပြန်လည်အသက်သွင်းခြင်း ပြုလုပ်နိုင်သည်။</li>
                    </ul>

                    <h4>၉။ ပြင်ဆင်ခြင်း သို့မဟုတ် ရပ်စဲခြင်း</h4>
                    <ul>
                        <li>ကုမ္ပဏီသည် ကြိုတင်အသိပေးခြင်းမရှိဘဲ အချိန်မရွေး ပရိုဂရမ်ကို ပြင်ဆင်ခြင်း၊ ဆိုင်းငံ့ခြင်း သို့မဟုတ် ရပ်စဲခြင်းများ ပြုလုပ်နိုင်သည်။</li>
                        <li>ပရိုဂရမ် ရပ်စဲပါက၊ အတည်ပြုပြီးသော ရောင်းချမှုများအတွက် ကော်မရှင်များကို ပုံမှန်လုပ်ထုံးလုပ်နည်းများအတိုင်း ဆက်လက်ပေးချေသွားမည်။</li>
                    </ul>

                    <h4>၁၀။ တာဝန်ယူမှု အကန့်အသတ်</h4>
                    <p>ကုမ္ပဏီသည် အောက်ပါတို့အတွက် တာဝန်မရှိပါ-</p>
                    <ul>
                        <li>စနစ်ရပ်တန့်မှု သို့မဟုတ် နည်းပညာပိုင်းဆိုင်ရာ အမှားများကြောင့် ဝင်ငွေဆုံးရှုံးမှုများ။</li>
                        <li>ကိုယ်စားလှယ်၏ အချက်အလက် မှားယွင်းမှုကြောင့် ငွေပေးချေမှု နှောင့်နှေးခြင်း။</li>
                        <li>ကိုယ်စားလှယ်များနှင့် သုံးစွဲသူများကြား အငြင်းပွားမှုများ။</li>
                    </ul>

                    <h4>၁၁။ အငြင်းပွားမှု ဖြေရှင်းခြင်း</h4>
                    <ul>
                        <li>မည်သည့် အငြင်းပွားမှုကိုမဆို ငွေပေးငွေယူ ပြုလုပ်ပြီး ၇ ရက်အတွင်း ကုမ္ပဏီ၏ support team သို့ တိုင်ကြားရမည်။</li>
                        <li>ကော်မရှင်များနှင့် ရောင်းချမှု အတည်ပြုခြင်းဆိုင်ရာ ကိစ္စရပ်အားလုံးတွင် ကုမ္ပဏီ၏ ဆုံးဖြတ်ချက်သည်သာ အတည် ဖြစ်သည်။</li>
                    </ul>

                    <h4>၁၂။ ဆက်သွယ်ရန်</h4>
                    <p>မေးခွန်းများ၊ အကူအညီများ သို့မဟုတ် အငြင်းပွားမှုများ တိုင်ကြားရန်အတွက် ကျေးဇူးပြု၍ ဆက်သွယ်ပါ-</p>
                    <ul>
                        <li>📧 <a href="mailto:calamuseducation@gmail.com">calamuseducation@gmail.com</a></li>
                        <li>📱 Telegram / Viber: <a href="tel:09688683805">09688683805</a></li>
                    </ul>

                    <h4>၁၃။ သဘောတူညီချက်</h4>
                    <p><strong>ဤပရိုဂရမ်တွင် ပါဝင်ခြင်းဖြင့်၊ အထက်တွင်ဖော်ပြထားသော စည်းကမ်းချက်များအားလုံးကို သင်ဖတ်ရှု နားလည်ပြီး သဘောတူကြောင်း အတည်ပြုပါသည်။</strong></p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'layout/welcome_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
