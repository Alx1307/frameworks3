<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Dashboard - Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --space-blue: #0a192f;
            --space-dark: #020c1b;
            --space-purple: #5d3fd3;
            --neon-blue: #00d4ff;
            --star-glow: #ffd700;
        }
        
        body {
            background: linear-gradient(135deg, var(--space-dark) 0%, var(--space-blue) 50%, #1a1a2e 100%);
            color: white;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: twinkle 5s infinite;
        }
        
        @keyframes twinkle {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }
        
        .space-header {
            background: rgba(10, 25, 47, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(93, 63, 211, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            color: var(--neon-blue);
            font-size: 2rem;
            filter: drop-shadow(0 0 8px var(--neon-blue));
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--neon-blue), var(--space-purple));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        }
        
        .nav-link-space {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            margin: 0 10px;
            padding: 8px 16px !important;
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link-space:hover {
            color: white !important;
            background: rgba(93, 63, 211, 0.2);
            transform: translateY(-2px);
        }
        
        .nav-link-space::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-blue), var(--space-purple));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link-space:hover::after {
            width: 80%;
        }
        
        .main-content {
            padding-top: 120px;
            padding-bottom: 100px;
        }
        
        .hero-section {
            text-align: center;
            padding: 60px 0;
            position: relative;
        }
        
        .hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 30px;
            background: linear-gradient(90deg, #00d4ff, #5d3fd3, #ff00ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientShift 8s infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 700px;
            margin: 0 auto 50px;
            line-height: 1.6;
        }
        
        .dashboard-btn {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            border: none;
            color: white;
            padding: 18px 45px;
            font-size: 1.3rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(93, 63, 211, 0.4);
            z-index: 1;
        }
        
        .dashboard-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.6);
            color: white;
        }
        
        .dashboard-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
            z-index: -1;
        }
        
        .dashboard-btn:hover::before {
            left: 100%;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 80px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.2);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--neon-blue);
            box-shadow: 0 15px 35px rgba(0, 212, 255, 0.2);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--neon-blue);
            filter: drop-shadow(0 0 10px var(--neon-blue));
        }
        
        .feature-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: white;
        }
        
        .feature-desc {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }
        
        .space-footer {
            background: rgba(2, 12, 27, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(93, 63, 211, 0.3);
            padding: 30px 0 20px;
            position: relative;
        }
        
        .footer-content {
            text-align: center;
        }
        
        .footer-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--neon-blue), var(--space-purple));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
        }
        
        .footer-tagline {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-bottom: 20px;
        }
        
        .footer-copyright {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .planet {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #3a1c71, #5d3fd3, #0a192f);
            right: 10%;
            top: 20%;
            z-index: -1;
            opacity: 0.3;
            animation: float 20s infinite ease-in-out;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .planet {
                display: none;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="stars" id="stars"></div>
    
    <header class="space-header py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo-container">
                    <a href="/" class="text-decoration-none d-flex align-items-center gap-3">
                        <div class="logo-icon">
                            <i class="fas fa-satellite"></i>
                        </div>
                        <div class="logo-text">Astronomy</div>
                    </a>
                </div>
                
                <nav class="navbar navbar-expand-lg p-0">
                    <div class="navbar-nav">
                        <a class="nav-link nav-link-space" href="/" style="background: rgba(93, 63, 211, 0.2);">
                            <i class="fas fa-home me-2"></i>Главная
                        </a>
                        <a class="nav-link nav-link-space" href="/astronomy">
                            <i class="fas fa-star me-2"></i>События
                        </a>
                        <a class="nav-link nav-link-space" href="/cms-admin">
                            <i class="fas fa-cogs me-2"></i>CMS
                        </a>
                        <a class="nav-link nav-link-space" href="/dashboard-minimal">
                            <i class="fas fa-globe-europe me-2"></i>JWST
                        </a>
                        <a class="nav-link nav-link-space" href="/osdr-new">
                            <i class="fas fa-database me-2"></i>OSDR
                        </a>
                        <a class="nav-link nav-link-space active" href="/iss-new">
                            <i class="fas fa-satellite me-2"></i>МКС
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="planet"></div>
    
    <main class="main-content">
        <div class="container">
            <section class="hero-section">
                <h1 class="hero-title">SPACE DASHBOARD</h1>
                <p class="hero-subtitle">
                    Мониторинг Международной космической станции в реальном времени, 
                    галерея снимков телескопа Джеймса Уэбба и астрономические события. 
                    Погрузитесь в мир космических данных и исследований.
                </p>
                
                <a href="/dashboard" class="btn dashboard-btn">
                    <i class="fas fa-rocket me-2"></i>Посмотреть исходный сайт
                </a>
            </section>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-satellite"></i>
                    </div>
                    <h3 class="feature-title">МКС в реальном времени</h3>
                    <p class="feature-desc">
                        Отслеживайте положение, скорость и высоту Международной 
                        космической станции на интерактивной карте.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe-europe"></i> 
                    </div>
                    <h3 class="feature-title">JWST Галерея</h3>
                    <p class="feature-desc">
                        Исследуйте последние снимки космоса, сделанные 
                        телескопом Джеймса Уэбба с фильтрами поиска.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="feature-title">Астрономические события</h3>
                    <p class="feature-desc">
                        Получайте информацию о предстоящих затмениях, 
                        пролётах спутников и других космических явлениях.
                    </p>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="space-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">Astronomy Dashboard</div>
                <p class="footer-tagline">
                    <i class="fas fa-rocket me-2"></i>
                    Исследуйте космос вместе с нами
                </p>
                <div class="footer-copyright">
                    &copy; 2025 Astronomy Dashboard. Используются данные NASA и открытые API.
                </div>
            </div>
        </div>
    </footer>

    <script>
        function createStars() {
            const starsContainer = document.getElementById('stars');
            const starCount = 150;
            
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                
                const size = Math.random() * 3 + 1;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                
                star.style.animationDelay = `${Math.random() * 5}s`;
                
                starsContainer.appendChild(star);
            }
        }
        
        function handleScrollAnimations() {
            const featureCards = document.querySelectorAll('.feature-card');
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            
            featureCards.forEach((card, index) => {
                const cardTop = card.getBoundingClientRect().top;
                const cardVisible = 150;
                
                if (cardTop < windowHeight - cardVisible) {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            handleScrollAnimations();
            
            window.addEventListener('scroll', handleScrollAnimations);
            
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(30px)";
                card.style.transition = "opacity 0.6s ease, transform 0.6s ease, border-color 0.3s ease, box-shadow 0.3s ease";
            });
            
            setTimeout(() => {
                handleScrollAnimations();
            }, 300);
            
            if (!document.querySelector('link[href*="font-awesome"]')) {
                const faLink = document.createElement('link');
                faLink.rel = 'stylesheet';
                faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
                document.head.appendChild(faLink);
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>