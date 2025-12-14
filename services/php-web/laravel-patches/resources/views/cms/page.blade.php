<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CMS Страница' }} - Astronomy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/space-styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;800&display=swap" rel="stylesheet">
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
        
        .cms-page-container {
            min-height: 100vh;
            color: #fff;
            padding: 120px 0 100px;
            position: relative;
        }
        
        .cms-page-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background: rgba(20, 20, 40, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(93, 63, 211, 0.4);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 1;
        }
        
        .cms-page-header {
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 2px solid rgba(93, 63, 211, 0.5);
            position: relative;
        }
        
        .cms-page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #00d4ff, #5d3fd3);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .breadcrumb-item a {
            color: var(--neon-blue);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .breadcrumb-item a:hover {
            color: white;
            text-shadow: 0 0 10px var(--neon-blue);
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .cms-page-body {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        
        .cms-page-body h1,
        .cms-page-body h2,
        .cms-page-body h3,
        .cms-page-body h4 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            margin-top: 2em;
            margin-bottom: 0.8em;
            text-shadow: 0 0 10px rgba(0, 212, 255, 0.2);
        }
        
        .cms-page-body h1 {
            font-size: 2.5rem;
            background: linear-gradient(90deg, var(--neon-blue), var(--space-purple));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .cms-page-body h2 {
            font-size: 2rem;
            border-bottom: 1px solid rgba(93, 63, 211, 0.3);
            padding-bottom: 10px;
        }
        
        .cms-page-body h3 {
            font-size: 1.5rem;
        }
        
        .cms-page-body p {
            margin-bottom: 1.5em;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .cms-page-body a {
            color: var(--neon-blue);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: all 0.3s ease;
        }
        
        .cms-page-body a:hover {
            color: white;
            border-bottom: 1px solid var(--neon-blue);
            text-shadow: 0 0 8px var(--neon-blue);
        }
        
        .cms-page-body ul,
        .cms-page-body ol {
            margin-bottom: 1.5em;
            padding-left: 1.5em;
        }
        
        .cms-page-body li {
            margin-bottom: 0.5em;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .cms-page-body code {
            background: rgba(0, 0, 0, 0.3);
            color: #90ee90;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        
        .cms-page-body pre {
            background: rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(93, 63, 211, 0.3);
            overflow-x: auto;
            margin-bottom: 1.5em;
        }
        
        .cms-page-body blockquote {
            border-left: 3px solid var(--space-purple);
            padding-left: 20px;
            margin: 2em 0;
            color: rgba(255, 255, 255, 0.8);
            font-style: italic;
            background: rgba(93, 63, 211, 0.1);
            padding: 20px;
            border-radius: 0 10px 10px 0;
        }
        
        .back-to-home {
            display: inline-flex;
            align-items: center;
            margin-top: 40px;
            color: white;
            background: rgba(93, 63, 211, 0.3);
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(93, 63, 211, 0.5);
            font-weight: 500;
        }
        
        .back-to-home:hover {
            background: rgba(93, 63, 211, 0.5);
            color: white;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(93, 63, 211, 0.3);
            border-color: var(--neon-blue);
        }
        
        .back-to-home i {
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        
        .back-to-home:hover i {
            transform: translateX(-5px);
        }
        
        .planet {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #3a1c71, #5d3fd3, #0a192f);
            right: 10%;
            top: 20%;
            z-index: 0;
            opacity: 0.3;
            animation: float 20s infinite ease-in-out;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        .space-footer {
            background: rgba(2, 12, 27, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(93, 63, 211, 0.3);
            padding: 30px 0 20px;
            position: relative;
            z-index: 2;
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
        
        @media (max-width: 768px) {
            .cms-page-content {
                padding: 25px;
                margin: 0 15px;
            }
            
            .cms-page-header h1 {
                font-size: 2rem;
            }
            
            .cms-page-body {
                font-size: 1rem;
            }
            
            .planet {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="stars" id="stars"></div>
    <div class="planet"></div>
    
    <div class="cms-page-container">
        <div class="container">
            <div class="cms-page-content">
                <div class="cms-page-header">
                    <h1>{{ $title ?? 'CMS Страница' }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Главная</a></li>
                            <li class="breadcrumb-item"><a href="/cms-admin">CMS Панель</a></li>
                            <li class="breadcrumb-item active">{{ $title ?? 'Страница' }}</li>
                        </ol>
                    </nav>
                </div>
                
                <div class="cms-page-body">
                    {!! $html ?? '<div class="text-center py-5"><i class="fas fa-file-alt fa-3x mb-3 text-muted"></i><p class="text-muted">Контент отсутствует</p></div>' !!}
                </div>
                
                <div class="text-center">
                    <a href="/cms-admin" class="back-to-home">
                        <i class="fas fa-arrow-left"></i>Вернуться в панель управления
                    </a>
                </div>
            </div>
        </div>
    </div>
    
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function createStars() {
            const starsContainer = document.getElementById('stars');
            const starCount = 200;
            
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                
                const size = Math.random() * 3 + 1;
                const brightness = Math.random() * 0.8 + 0.2;
                
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                star.style.opacity = brightness;
                star.style.animationDelay = `${Math.random() * 5}s`;
                star.style.animationDuration = `${Math.random() * 3 + 2}s`;
                
                starsContainer.appendChild(star);
            }
        }
        
        function animateContent() {
            const content = document.querySelector('.cms-page-content');
            if (content) {
                content.style.opacity = "0";
                content.style.transform = "translateY(30px)";
                content.style.transition = "opacity 0.8s ease, transform 0.8s ease";
                
                setTimeout(() => {
                    content.style.opacity = "1";
                    content.style.transform = "translateY(0)";
                }, 300);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            animateContent();
            
            const headings = document.querySelectorAll('.cms-page-body h1, .cms-page-body h2, .cms-page-body h3');
            headings.forEach((heading, index) => {
                heading.style.opacity = "0";
                heading.style.transform = "translateX(-20px)";
                heading.style.transition = `opacity 0.6s ease ${index * 0.1 + 0.5}s, transform 0.6s ease ${index * 0.1 + 0.5}s`;
                
                setTimeout(() => {
                    heading.style.opacity = "1";
                    heading.style.transform = "translateX(0)";
                }, 600 + index * 100);
            });
            
            const paragraphs = document.querySelectorAll('.cms-page-body p');
            paragraphs.forEach((p, index) => {
                p.style.opacity = "0";
                p.style.transform = "translateY(10px)";
                p.style.transition = `opacity 0.5s ease ${index * 0.05 + 0.8}s, transform 0.5s ease ${index * 0.05 + 0.8}s`;
                
                setTimeout(() => {
                    p.style.opacity = "1";
                    p.style.transform = "translateY(0)";
                }, 800 + index * 50);
            });
        });
    </script>
</body>
</html>