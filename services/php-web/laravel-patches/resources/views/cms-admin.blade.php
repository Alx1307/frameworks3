<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление CMS - Space Dashboard</title>
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
        
        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #00d4ff, #5d3fd3);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-align: left;
        }
        
        .page-subtitle {
            color: rgba(255, 255, 255, 0.8);
            text-align: left;
            margin-bottom: 40px;
            font-size: 1.1rem;
            max-width: 800px;
            margin-left: 0;
            margin-right: auto;
        }
        
        .cms-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .cms-card:hover {
            border-color: var(--neon-blue);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.2);
            transform: translateY(-5px);
        }
        
        .cms-card-header {
            border-bottom: 1px solid rgba(93, 63, 211, 0.3);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .cms-card-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--neon-blue);
            margin: 0;
            text-align: left;
        }
        
        .cms-slug {
            color: rgba(255, 255, 255, 0.6);
            font-family: monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            text-align: left;
        }
        
        .cms-content {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-height: 300px;
            overflow-y: auto;
            text-align: left;
        }
        
        .cms-content-preview {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            text-align: left;
        }
        
        .cms-content-preview img {
            max-width: 100%;
            border-radius: 8px;
            margin: 10px 0;
        }
        
        .cms-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            text-align: left;
        }
        
        .cms-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: left;
        }
        
        .cms-status-active {
            background: rgba(0, 255, 0, 0.1);
            color: #90ee90;
            border: 1px solid rgba(0, 255, 0, 0.3);
        }
        
        .cms-status-inactive {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(255, 0, 0, 0.3);
        }
        
        .cms-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            text-align: left;
        }
        
        .btn-cms {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: left;
        }
        
        .btn-cms-view {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            color: white;
            border: none;
        }
        
        .btn-cms-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
            color: white;
        }
        
        .btn-cms-edit {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .btn-cms-edit:hover {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            transform: translateY(-2px);
        }
        
        .cms-empty {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .cms-empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--neon-blue);
            opacity: 0.5;
        }
        
        .info-panel {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .info-panel-header {
            color: var(--neon-blue);
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }
        
        .section-title {
            color: var(--neon-blue);
            margin-bottom: 25px;
            text-align: left;
            font-size: 1.5rem;
            font-weight: 600;
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
            .page-title {
                font-size: 2rem;
            }
            
            .planet {
                display: none;
            }
            
            .cms-actions {
                flex-direction: column;
            }
            
            .cms-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .page-subtitle {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.3rem;
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
                        <a class="nav-link nav-link-space" href="/">
                            <i class="fas fa-home me-2"></i>Главная
                        </a>
                        <a class="nav-link nav-link-space" href="/astronomy">
                            <i class="fas fa-calendar-alt me-2"></i>События
                        </a>
                        <a class="nav-link nav-link-space" href="/cms-admin" style="background: rgba(93, 63, 211, 0.2);">
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
            <h1 class="page-title">Управление CMS</h1>
            <p class="page-subtitle">
                Просмотр и управление контентными блоками системы. Здесь отображаются 
                все активные блоки содержимого, которые можно использовать на различных страницах.
            </p>
            
            <div class="info-panel">
                <div class="info-panel-header">
                    <i class="fas fa-info-circle"></i>
                    Информация о CMS
                </div>
                <p class="mb-0" style="color: rgba(255, 255, 255, 0.8);">
                    Система управления контентом позволяет хранить и отображать HTML-блоки 
                    на различных страницах. Каждый блок имеет уникальный идентификатор (slug), 
                    по которому его можно вставить в любую страницу.
                </p>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <h3 class="section-title">
                        <i class="fas fa-th-list me-2"></i>Активные блоки
                        <span class="badge bg-primary ms-2">{{ count($blocks ?? []) }}</span>
                    </h3>
                    
                    @if(isset($blocks) && count($blocks) > 0)
                        @foreach($blocks as $block)
                        <div class="cms-card">
                            <div class="cms-card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="cms-card-title">
                                        <i class="fas fa-file-alt me-2"></i>{{ $block->title ?? 'Без названия' }}
                                    </h4>
                                    <div class="cms-slug">
                                        <i class="fas fa-hashtag me-1"></i>{{ $block->slug }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="cms-content">
                                <div class="cms-content-preview">
                                    @php
                                        $preview = strip_tags($block->content);
                                        $preview = strlen($preview) > 300 ? substr($preview, 0, 300) . '...' : $preview;
                                        echo htmlspecialchars($preview);
                                    @endphp
                                </div>
                            </div>
                            
                            <div class="cms-meta">
                                <div>
                                    <span class="cms-status cms-status-active">
                                        <i class="fas fa-check-circle me-1"></i>Активен
                                    </span>
                                </div>
                                <div>
                                    <small>
                                        <i class="far fa-clock me-1"></i>
                                        Обновлен: {{ date('d.m.Y H:i', strtotime($block->updated_at)) }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="cms-actions">
                                <a href="/page/{{ $block->slug }}" class="btn btn-cms btn-cms-view" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>Просмотр страницы
                                </a>
                                <a href="#" class="btn btn-cms btn-cms-edit" onclick="alert('Редактирование временно недоступно')">
                                    <i class="fas fa-edit"></i>Редактировать
                                </a>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="cms-card cms-empty">
                            <div class="cms-empty-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h4>Нет активных блоков</h4>
                            <p>В системе CMS пока нет созданных контентных блоков.</p>
                            <a href="#" class="btn btn-cms btn-cms-view mt-3" onclick="alert('Создание блока временно недоступно')">
                                <i class="fas fa-plus-circle me-2"></i>Создать новый блок
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="cms-card mt-5">
                <div class="cms-card-header">
                    <h4 class="cms-card-title">
                        <i class="fas fa-tachometer-alt me-2"></i>CMS блок с dashboard
                    </h4>
                    <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">
                        Этот блок отображается на главной панели управления (dashboard)
                    </p>
                </div>
                
                <div class="cms-content">
                    <div class="cms-content-preview">
                        @php
                            try {
                                $dashboardBlock = DB::selectOne("SELECT content FROM cms_blocks WHERE slug='dashboard_experiment' AND is_active = TRUE LIMIT 1");
                                if ($dashboardBlock) {
                                    echo $dashboardBlock->content;
                                } else {
                                    echo '<div class="text-muted">Блок "dashboard_experiment" не найден или неактивен</div>';
                                }
                            } catch (\Throwable $e) {
                                echo '<div class="text-danger">Ошибка БД: ' . htmlspecialchars($e->getMessage()) . '</div>';
                            }
                        @endphp
                    </div>
                </div>
                
                <div class="cms-meta">
                    <div>
                        <span class="cms-slug">
                            <i class="fas fa-hashtag me-1"></i>dashboard_experiment
                        </span>
                    </div>
                    <div>
                        <a href="/dashboard" class="text-decoration-none" style="color: var(--neon-blue);">
                            <i class="fas fa-external-link-alt me-1"></i>На dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="info-panel mt-4">
                <div class="info-panel-header">
                    <i class="fas fa-database"></i>
                    Техническая информация
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="color: var(--neon-blue); text-align: left;">Как использовать блоки:</h6>
                        <ul style="color: rgba(255, 255, 255, 0.8); text-align: left;">
                            <li>Блоки хранятся в таблице <code>cms_blocks</code></li>
                            <li>Каждый блок имеет уникальный <code>slug</code></li>
                            <li>Для вставки блока используйте маршрут <code>/page/{slug}</code></li>
                            <li>Блоки отображаются "как есть" (HTML поддерживается)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color: var(--neon-blue); text-align: left;">Пример кода:</h6>
                        <pre style="background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px; color: #90ee90; font-size: 0.9rem; text-align: left;">
&lt;!-- Вставка CMS блока в Blade --&gt;
@php
    try {
        $block = DB::selectOne("SELECT content FROM cms_blocks WHERE slug='имя_блока'");
        echo $block ? $block->content : 'Блок не найден';
    } catch (\Exception $e) {
        echo '<div class="text-warning">Таблица cms_blocks не существует в базе данных</div>';
    }
@endphp </pre>
                    </div>
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
        
        function animateCardsOnScroll() {
            const cards = document.querySelectorAll('.cms-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(card);
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            
            setTimeout(() => {
                animateCardsOnScroll();
            }, 300);
            
            document.querySelectorAll('.btn-cms').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId !== '#') {
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });
            
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link-space').forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath === currentPath || 
                    (currentPath.startsWith('/cms') && linkPath === '/cms-admin')) {
                    link.style.background = 'rgba(93, 63, 211, 0.2)';
                }
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>