<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWST Dashboard - Space Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            margin-bottom: 30px;
            background: linear-gradient(90deg, #00d4ff, #5d3fd3);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .dashboard-card:hover {
            border-color: var(--neon-blue);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.2);
        }
        
        .card-header-space {
            background: rgba(93, 63, 211, 0.2);
            border-bottom: 1px solid rgba(93, 63, 211, 0.3);
            color: var(--neon-blue);
            font-weight: 600;
            font-family: 'Orbitron', sans-serif;
        }
        
        .jwst-slider {
            position: relative;
        }
        
        .jwst-track {
            display: flex;
            gap: .75rem;
            overflow: auto;
            scroll-snap-type: x mandatory;
            padding: .25rem;
        }
        
        .jwst-item {
            flex: 0 0 180px;
            scroll-snap-align: start;
        }
        
        .jwst-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid rgba(93, 63, 211, 0.3);
            transition: all 0.3s ease;
        }
        
        .jwst-item img:hover {
            border-color: var(--neon-blue);
            transform: scale(1.02);
        }
        
        .jwst-cap {
            font-size: .85rem;
            margin-top: .25rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .jwst-nav {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            z-index: 2;
            background: rgba(93, 63, 211, 0.5);
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .jwst-nav:hover {
            background: rgba(0, 212, 255, 0.7);
            border-color: var(--neon-blue);
        }
        
        .jwst-prev {
            left: -.25rem;
        }
        
        .jwst-next {
            right: -.25rem;
        }
        
        .form-control-space, .form-select-space {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(93, 63, 211, 0.3) !important;
            color: white !important;
        }
        
        .form-control-space::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        
        .form-control-space:focus, .form-select-space:focus {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: var(--neon-blue) !important;
            color: white !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.25) !important;
        }
        
        .form-select-space option {
            background: var(--space-blue) !important;
            color: white !important;
        }
        
        .form-select-space option:hover {
            background: var(--space-purple) !important;
        }
        
        select.form-select-space {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2300d4ff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 16px 12px !important;
        }
        
        .btn-space {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            border: none;
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .btn-space:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
            color: white !important;
        }
        
        .btn-outline-space {
            background: transparent;
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .btn-outline-space:hover {
            border-color: var(--neon-blue);
            background: rgba(0, 212, 255, 0.1);
            color: white !important;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(93, 63, 211, 0.2);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            border-color: var(--neon-blue);
            transform: translateY(-3px);
        }
        
        .info-icon {
            font-size: 2.5rem;
            color: var(--neon-blue);
            margin-bottom: 10px;
        }
        
        .info-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--neon-blue);
            font-family: 'Orbitron', sans-serif;
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
        
        .loading-pulse {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--neon-blue);
            animation: pulse 1.5s infinite;
            margin-right: 10px;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .planet {
                display: none;
            }
            
            .jwst-item {
                flex: 0 0 150px;
            }
            
            .jwst-item img {
                height: 150px;
            }
            
            .info-card {
                padding: 15px;
            }
            
            .info-icon {
                font-size: 2rem;
            }
        }
    </style>
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
                            <i class="fas fa-star me-2"></i>События
                        </a>
                        <a class="nav-link nav-link-space" href="/cms-admin">
                            <i class="fas fa-cogs me-2"></i>CMS
                        </a>
                        <a class="nav-link nav-link-space" href="/dashboard-minimal" style="background: rgba(93, 63, 211, 0.2);">
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
        <div class="container pb-5">
            <h1 class="page-title">JWST Dashboard - Космический телескоп Джеймса Уэбба</h1>
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-satellite-dish"></i>
                        </div>
                        <div class="info-label">Диаметр зеркала</div>
                        <div class="info-value">6.5 м</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div class="info-label">Точка Лагранжа</div>
                        <div class="info-value">L₂</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="info-label">Инструменты</div>
                        <div class="info-value">5</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="dashboard-card h-100">
                        <div class="card-header card-header-space">
                            <i class="fas fa-telescope me-2"></i>JWST — Информация о телескопе
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info bg-dark border-info mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-info fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="alert-heading text-info">Космический телескоп Джеймса Уэбба</h6>
                                        <p class="mb-0" style="color: rgba(255, 255, 255, 0.9);">
                                            JWST — инфракрасный телескоп с составным зеркалом 6,5 м, 
                                            находящийся в точке Лагранжа L₂ системы Солнце—Земля. 
                                            Предназначен для наблюдений в инфракрасном диапазоне с длинами волн от 0,6 до 28 микрометров.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-info mb-3"><i class="fas fa-list me-2"></i>Основные характеристики</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Запуск:</strong> 25 декабря 2021
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Орбита:</strong> Точка L₂ (1.5 млн км от Земли)
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Масса:</strong> 6,500 кг
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Мощность:</strong> 2,000 ватт
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-info mb-3"><i class="fas fa-microscope me-2"></i>Научные инструменты</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-camera text-warning me-2"></i>
                                            <strong>NIRCam</strong> - Ближняя инфракрасная камера
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-eye text-warning me-2"></i>
                                            <strong>NIRSpec</strong> - Ближний инфракрасный спектрограф
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-thermometer text-warning me-2"></i>
                                            <strong>MIRI</strong> - Средний инфракрасный инструмент
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-ruler text-warning me-2"></i>
                                            <strong>NIRISS</strong> - Ближний инфракрасный имитатор щели
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-card h-100">
                        <div class="card-header card-header-space">
                            <i class="fas fa-rocket me-2"></i>Быстрые ссылки
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                <a href="https://webb.nasa.gov" target="_blank" class="btn btn-space">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Официальный сайт NASA JWST
                                </a>
                                <a href="https://webbtelescope.org" target="_blank" class="btn btn-outline-space">
                                    <i class="fas fa-images me-2"></i>
                                    Галерея изображений JWST
                                </a>
                                <button class="btn btn-outline-space" onclick="window.location.reload()">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Обновить данные
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header card-header-space d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-images me-2"></i>JWST — последние изображения
                            </div>
                            <form id="jwstFilter" class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <select class="form-select form-select-sm form-select-space" name="source" id="srcSel">
                                        <option value="jpg" selected>Все JPG</option>
                                        <option value="suffix">По суффиксу</option>
                                        <option value="program">По программе</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <input type="text" class="form-control form-control-sm form-control-space" name="suffix" id="suffixInp" placeholder="_cal / _thumb" style="width:140px;display:none">
                                    <input type="text" class="form-control form-control-sm form-control-space" name="program" id="progInp" placeholder="2734" style="width:110px;display:none">
                                </div>
                                <div class="col-auto">
                                    <select class="form-select form-select-sm form-select-space" name="instrument" style="width:130px">
                                        <option value="">Любой инструмент</option>
                                        <option>NIRCam</option><option>MIRI</option><option>NIRISS</option><option>NIRSpec</option><option>FGS</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select form-select-sm form-select-space" name="perPage" style="width:90px">
                                        <option>12</option><option selected>24</option><option>36</option><option>48</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-space" type="submit">
                                        <i class="fas fa-search me-1"></i>Показать
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="jwst-slider">
                                <button class="btn jwst-nav jwst-prev" type="button" aria-label="Prev">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <div id="jwstTrack" class="jwst-track border rounded"></div>
                                <button class="btn jwst-nav jwst-next" type="button" aria-label="Next">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div id="jwstInfo" class="small text-muted mt-3 text-center"></div>
                        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
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
        
        document.addEventListener('DOMContentLoaded', async function () {
            createStars();
            
            const track = document.getElementById('jwstTrack');
            const info = document.getElementById('jwstInfo');
            const form = document.getElementById('jwstFilter');
            const srcSel = document.getElementById('srcSel');
            const sfxInp = document.getElementById('suffixInp');
            const progInp = document.getElementById('progInp');
            
            function toggleInputs() {
                sfxInp.style.display = (srcSel.value === 'suffix') ? '' : 'none';
                progInp.style.display = (srcSel.value === 'program') ? '' : 'none';
            }
            
            srcSel.addEventListener('change', toggleInputs);
            toggleInputs();
            
            async function loadFeed(qs) {
                track.innerHTML = '<div class="p-5 text-center"><div class="loading-pulse"></div><div class="text-info mt-2">Загрузка изображений JWST...</div></div>';
                info.textContent = '';
                
                try {
                    const url = '/api/jwst/feed?' + new URLSearchParams(qs).toString();
                    const r = await fetch(url);
                    const js = await r.json();
                    
                    track.innerHTML = '';
                    
                    if (js.items && js.items.length > 0) {
                        js.items.forEach(it => {
                            const fig = document.createElement('figure');
                            fig.className = 'jwst-item m-0';
                            fig.innerHTML = `
                                <a href="${it.link || it.url}" target="_blank" rel="noreferrer">
                                    <img loading="lazy" src="${it.url}" alt="JWST изображение" onerror="this.src='https://via.placeholder.com/180x180/0a192f/00d4ff?text=JWST'">
                                </a>
                                <figcaption class="jwst-cap">${(it.caption || '').replaceAll('<', '&lt;').substring(0, 50)}${(it.caption || '').length > 50 ? '...' : ''}</figcaption>`;
                            track.appendChild(fig);
                        });
                        
                        info.textContent = `Источник: ${js.source} · Показано ${js.items.length} изображений`;
                    } else {
                        track.innerHTML = '<div class="p-5 text-center text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Изображения не найдены</div>';
                        info.textContent = 'Нет данных для отображения';
                    }
                } catch (e) {
                    console.error('Ошибка загрузки JWST:', e);
                    track.innerHTML = '<div class="p-5 text-center text-danger"><i class="fas fa-exclamation-circle me-2"></i>Ошибка загрузки галереи</div>';
                }
            }
            
            form.addEventListener('submit', function(ev) {
                ev.preventDefault();
                const fd = new FormData(form);
                const q = Object.fromEntries(fd.entries());
                loadFeed(q);
            });
            
            document.querySelector('.jwst-prev').addEventListener('click', () => track.scrollBy({
                left: -600,
                behavior: 'smooth'
            }));
            
            document.querySelector('.jwst-next').addEventListener('click', () => track.scrollBy({
                left: 600,
                behavior: 'smooth'
            }));
            
            loadFeed({
                source: 'jpg',
                perPage: 24
            });
            
            function handleScrollAnimations() {
                const cards = document.querySelectorAll('.dashboard-card, .info-card');
                const windowHeight = window.innerHeight;
                
                cards.forEach((card, index) => {
                    const cardTop = card.getBoundingClientRect().top;
                    const cardVisible = 100;
                    
                    if (cardTop < windowHeight - cardVisible) {
                        card.style.opacity = "1";
                        card.style.transform = "translateY(0)";
                    }
                });
            }
            
            const cards = document.querySelectorAll('.dashboard-card, .info-card');
            cards.forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
            });
            
            window.addEventListener('scroll', handleScrollAnimations);
            setTimeout(() => {
                handleScrollAnimations();
            }, 300);
        });
    </script>
</body>
</html>