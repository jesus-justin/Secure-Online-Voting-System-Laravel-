<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
    }

    /* ===== HERO SECTION ===== */
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        min-height: 750px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.15), transparent);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -50%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1), transparent);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite reverse;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(30px); }
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
    }

    .hero-content h1 {
        font-size: 4rem;
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 25px;
        text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        animation: slideDown 0.8s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-content p {
        font-size: 1.25rem;
        line-height: 1.8;
        margin-bottom: 40px;
        opacity: 0.95;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 0.8s ease-out 0.2s backwards;
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp 0.8s ease-out 0.4s backwards;
    }

    .btn-primary-custom {
        background: white;
        color: #667eea;
        padding: 16px 40px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 45px rgba(0,0,0,0.3);
        background: #f0f0f0;
    }

    .btn-secondary-custom {
        background: transparent;
        color: white;
        padding: 16px 40px;
        font-size: 1.1rem;
        font-weight: 700;
        border: 2.5px solid white;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary-custom:hover {
        background: white;
        color: #667eea;
        transform: translateY(-5px);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== FEATURES SECTION ===== */
    .features-section {
        padding: 120px 0;
        background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
    }

    .section-header {
        text-align: center;
        margin-bottom: 80px;
    }

    .section-header h2 {
        font-size: 3rem;
        font-weight: 900;
        color: #1a1a1a;
        margin-bottom: 20px;
    }

    .section-header .underline {
        width: 80px;
        height: 5px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        margin: 20px auto;
        border-radius: 3px;
    }

    .section-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 600px;
        margin: 20px auto 0;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 40px;
    }

    .feature-item {
        background: white;
        padding: 50px 30px;
        border-radius: 15px;
        text-align: center;
        transition: all 0.4s ease;
        border: 2px solid transparent;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        animation: slideUp 0.6s ease-out backwards;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .feature-item:nth-child(1) { animation-delay: 0.1s; }
    .feature-item:nth-child(2) { animation-delay: 0.2s; }
    .feature-item:nth-child(3) { animation-delay: 0.3s; }
    .feature-item:nth-child(4) { animation-delay: 0.4s; }
    .feature-item:nth-child(5) { animation-delay: 0.5s; }
    .feature-item:nth-child(6) { animation-delay: 0.6s; }

    .feature-item:hover {
        transform: translateY(-15px);
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.2);
        border-color: #667eea;
    }

    .feature-icon {
        font-size: 3.5rem;
        margin-bottom: 25px;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .feature-item:hover .feature-icon {
        transform: scale(1.15);
    }

    .icon-lock { color: #667eea; }
    .icon-check { color: #28a745; }
    .icon-eye { color: #ffc107; }
    .icon-chart { color: #17a2b8; }
    .icon-shield { color: #dc3545; }
    .icon-bot { color: #6c757d; }

    .feature-item h3 {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .feature-item p {
        color: #666;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    /* ===== HOW IT WORKS ===== */
    .how-it-works {
        padding: 120px 0;
        background: white;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 50px;
        margin-top: 80px;
    }

    .step-card {
        text-align: center;
        animation: slideUp 0.6s ease-out backwards;
    }

    .step-card:nth-child(1) { animation-delay: 0.1s; }
    .step-card:nth-child(2) { animation-delay: 0.2s; }
    .step-card:nth-child(3) { animation-delay: 0.3s; }
    .step-card:nth-child(4) { animation-delay: 0.4s; }

    .step-number {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 2.5rem;
        font-weight: 900;
        color: white;
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }

    .step-card:hover .step-number {
        transform: scale(1.1);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.4);
    }

    .step-card h3 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 15px;
    }

    .step-card p {
        color: #666;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    /* ===== SECURITY SECTION ===== */
    .security-section {
        padding: 120px 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .security-section::before {
        content: '';
        position: absolute;
        top: -200px;
        right: -200px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1), transparent);
        border-radius: 50%;
    }

    .security-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 40px;
        position: relative;
        z-index: 2;
        margin-top: 80px;
    }

    .security-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        padding: 40px;
        border-radius: 15px;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
        animation: slideUp 0.6s ease-out backwards;
    }

    .security-card:nth-child(1) { animation-delay: 0.1s; }
    .security-card:nth-child(2) { animation-delay: 0.2s; }
    .security-card:nth-child(3) { animation-delay: 0.3s; }
    .security-card:nth-child(4) { animation-delay: 0.4s; }
    .security-card:nth-child(5) { animation-delay: 0.5s; }
    .security-card:nth-child(6) { animation-delay: 0.6s; }

    .security-card:hover {
        background: rgba(255,255,255,0.15);
        transform: translateY(-10px);
        border-color: rgba(255,255,255,0.4);
    }

    .security-card i {
        font-size: 2.5rem;
        margin-bottom: 20px;
        display: block;
    }

    .security-card h3 {
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .security-card p {
        opacity: 0.9;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* ===== FAQ SECTION ===== */
    .faq-section {
        padding: 120px 0;
        background: #f8f9fa;
    }

    .faq-container {
        max-width: 800px;
        margin: 80px auto 0;
    }

    .accordion-item {
        margin-bottom: 20px;
        border: none;
        animation: slideUp 0.6s ease-out backwards;
    }

    .accordion-item:nth-child(1) { animation-delay: 0.1s; }
    .accordion-item:nth-child(2) { animation-delay: 0.2s; }
    .accordion-item:nth-child(3) { animation-delay: 0.3s; }
    .accordion-item:nth-child(4) { animation-delay: 0.4s; }
    .accordion-item:nth-child(5) { animation-delay: 0.5s; }

    .accordion-button {
        background: white;
        color: #1a1a1a;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 10px;
        border: 2px solid #e8e8e8;
        padding: 20px 25px;
        transition: all 0.3s ease;
    }

    .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: transparent;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .accordion-button:hover {
        transform: translateX(5px);
    }

    .accordion-body {
        background: white;
        color: #555;
        border: 2px solid #e8e8e8;
        border-top: none;
        border-radius: 0 0 10px 10px;
        padding: 25px;
        line-height: 1.8;
    }

    /* ===== CTA SECTION ===== */
    .cta-section {
        padding: 120px 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1), transparent);
        border-radius: 50%;
    }

    .cta-content {
        position: relative;
        z-index: 2;
    }

    .cta-section h2 {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 20px;
        animation: slideDown 0.8s ease-out;
    }

    .cta-section p {
        font-size: 1.2rem;
        margin-bottom: 50px;
        opacity: 0.95;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 0.8s ease-out 0.2s backwards;
    }

    .cta-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp 0.8s ease-out 0.4s backwards;
    }

    /* ===== FOOTER ===== */
    .footer-section {
        background: #1a1a1a;
        color: #fff;
        padding: 80px 0 30px;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 50px;
    }

    .footer-col h5 {
        font-weight: 800;
        margin-bottom: 25px;
        font-size: 1.1rem;
        color: #fff;
    }

    .footer-col ul {
        list-style: none;
    }

    .footer-col li {
        margin-bottom: 12px;
    }

    .footer-col a {
        color: #aaa;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .footer-col a:hover {
        color: #667eea;
        padding-left: 5px;
    }

    .footer-bottom {
        border-top: 1px solid #333;
        padding-top: 30px;
        text-align: center;
        color: #aaa;
        font-size: 0.9rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.5rem;
        }

        .hero-content p {
            font-size: 1rem;
        }

        .section-header h2 {
            font-size: 2rem;
        }

        .cta-section h2 {
            font-size: 2rem;
        }

        .hero-buttons, .cta-buttons {
            flex-direction: column;
            align-items: center;
        }

        .btn-primary-custom, .btn-secondary-custom {
            width: 100%;
            max-width: 300px;
        }
    }
</style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="http://localhost/Secure-Online-Voting-System-Laravel-/public/">
            <i class="bi bi-shield-check"></i> Secure Voting
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="http://localhost/Secure-Online-Voting-System-Laravel-/public/login">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/Secure-Online-Voting-System-Laravel-/public/register">Register</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ===== HERO SECTION ===== -->
<section class="hero-section">
    <div class="hero-content">
        <h1><i class="bi bi-shield-check"></i> Secure Your Vote</h1>
        <p>Experience the future of voting with military-grade encryption, tamper-proof records, and complete anonymity. Your voice matters—keep it safe.</p>
        <div class="hero-buttons">
            <a href="http://localhost/Secure-Online-Voting-System-Laravel-/public/register" class="btn-primary-custom">
                <i class="bi bi-person-plus"></i> Create Account
            </a>
            <a href="http://localhost/Secure-Online-Voting-System-Laravel-/public/login" class="btn-secondary-custom">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
        </div>
    </div>
</section>

<!-- ===== FEATURES SECTION ===== -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose Us?</h2>
            <div class="underline"></div>
            <p>Industry-leading security features designed to protect democratic voting</p>
        </div>

        <div class="features-grid">
            <div class="feature-item">
                <i class="bi bi-shield-lock feature-icon icon-lock"></i>
                <h3>Military-Grade Encryption</h3>
                <p>AES-256-CBC encryption ensures your vote is protected with the highest security standards available.</p>
            </div>

            <div class="feature-item">
                <i class="bi bi-check-circle feature-icon icon-check"></i>
                <h3>One Vote Per Person</h3>
                <p>Advanced verification mechanisms ensure you vote only once, preventing duplicate votes and fraud.</p>
            </div>

            <div class="feature-item">
                <i class="bi bi-incognito feature-icon icon-eye"></i>
                <h3>Complete Anonymity</h3>
                <p>Your vote is separated from your identity, ensuring your privacy is completely protected.</p>
            </div>

            <div class="feature-item">
                <i class="bi bi-graph-up feature-icon icon-chart"></i>
                <h3>Real-Time Results</h3>
                <p>View election results instantly as votes are cast and verified in real-time.</p>
            </div>

            <div class="feature-item">
                <i class="bi bi-search feature-icon icon-shield"></i>
                <h3>Tampering Detection</h3>
                <p>SHA-256 hashing detects any attempt to tamper with votes and provides cryptographic proof.</p>
            </div>

            <div class="feature-item">
                <i class="bi bi-robot feature-icon icon-bot"></i>
                <h3>Bot Protection</h3>
                <p>Google reCAPTCHA v3 prevents automated attacks and bot voting attempts.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section class="how-it-works">
    <div class="container">
        <div class="section-header">
            <h2>How It Works</h2>
            <div class="underline"></div>
            <p>Simple, secure, and transparent voting process</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Register & Verify</h3>
                <p>Create your account and wait for admin verification to ensure legitimacy.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Select Candidate</h3>
                <p>Choose your preferred candidate from the available election options.</p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Encrypt & Submit</h3>
                <p>Your vote is encrypted with military-grade security and submitted safely.</p>
            </div>

            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Verify Results</h3>
                <p>View real-time results with complete transparency and accountability.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECURITY SECTION ===== -->
<section class="security-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 style="color: white; margin-bottom: 20px;">Our Security Stack</h2>
            <p style="color: rgba(255,255,255,0.9); max-width: 600px; margin: 0 auto;">Comprehensive security measures to protect democracy</p>
        </div>

        <div class="security-grid">
            <div class="security-card">
                <i class="bi bi-shield-check"></i>
                <h3>End-to-End Encryption</h3>
                <p>AES-256-CBC encryption protects vote data from entry to storage and retrieval.</p>
            </div>

            <div class="security-card">
                <i class="bi bi-fingerprint"></i>
                <h3>Device Fingerprinting</h3>
                <p>Unique device identification prevents multiple votes from the same device.</p>
            </div>

            <div class="security-card">
                <i class="bi bi-hash"></i>
                <h3>SHA-256 Hashing</h3>
                <p>Cryptographic hashing creates tamper-proof vote records with digital signatures.</p>
            </div>

            <div class="security-card">
                <i class="bi bi-geo-alt"></i>
                <h3>IP Validation</h3>
                <p>IP address tracking logs and validates voting locations and suspicious access.</p>
            </div>

            <div class="security-card">
                <i class="bi bi-robot"></i>
                <h3>reCAPTCHA v3</h3>
                <p>Advanced bot detection protects against automated attacks and fraud attempts.</p>
            </div>

            <div class="security-card">
                <i class="bi bi-file-text"></i>
                <h3>Audit Logs</h3>
                <p>Complete voting history with timestamps for accountability and verification.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== FAQ SECTION ===== -->
<section class="faq-section">
    <div class="container">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
            <div class="underline"></div>
            <p>Everything you need to know about secure voting</p>
        </div>

        <div class="faq-container">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Is my vote really anonymous?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Absolutely! Your vote is completely separated from your identity. We store your encrypted vote independently from your voting record, ensuring complete anonymity while maintaining accountability. No one can connect your identity to your vote choice.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            How can I verify my vote wasn't tampered with?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Each vote is hashed using SHA-256, creating a unique fingerprint. Any tampering is immediately detected by comparing stored hashes. You can verify the integrity of votes through our transparent audit logs and cryptographic proofs.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            What happens after I register?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            After registration, an administrator verifies your account to ensure legitimacy and prevent fraud. Once verified, you'll receive confirmation and can immediately participate in all active elections with full access.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Can I vote multiple times?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            No. Our system uses device fingerprinting, voting tokens, and advanced verification to ensure you can only vote once per election. Multiple vote attempts are automatically blocked and logged for security.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            How secure is the system really?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We use military-grade encryption (AES-256-CBC), secure hashing (SHA-256), device fingerprinting, IP validation, reCAPTCHA v3, and multiple layers of validation. Every vote is audited and logged with cryptographic proofs for complete transparency and security verification.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Ready to Vote Securely?</h2>
        <p>Join thousands of voters using the most secure and transparent voting system available today.</p>
        <div class="cta-buttons">
            <a href="http://localhost/Secure-Online-Voting-System-Laravel-/public/register" class="btn-primary-custom">
                <i class="bi bi-person-plus"></i> Create Account Now
            </a>
            <a href="http://localhost/Secure-Online-Voting-System-Laravel-/public/login" class="btn-secondary-custom">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer-section">
    <div class="container">
        <div class="footer-content">
            <div class="footer-col">
                <h5><i class="bi bi-shield-check"></i> Secure Online Voting</h5>
                <p style="color: #aaa; line-height: 1.7;">Empowering democracy through secure, transparent, and anonymous voting technology.</p>
            </div>

            <div class="footer-col">
                <h5>Product</h5>
                <ul>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Security</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Documentation</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Company</h5>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>Legal</h5>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Security Policy</a></li>
                    <li><a href="#">Compliance</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 Secure Online Voting System. All rights reserved. Building trust through technology and transparency.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
