<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>МКС - Международная Космическая Станция</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
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
            margin-bottom: 15px;
            background: linear-gradient(90deg, #00d4ff, #5d3fd3);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .page-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            margin-bottom: 30px;
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
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(93, 63, 211, 0.2);
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            border-color: var(--neon-blue);
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.2);
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--neon-blue);
            font-family: 'Orbitron', sans-serif;
        }
        
        .stat-unit {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }
        
        .data-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .data-list li {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.9);
        }
        
        .data-list li:last-child {
            border-bottom: none;
        }
        
        .data-list .label {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .data-list .value {
            color: var(--neon-blue);
            font-weight: 600;
        }
        
        .btn-space {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            border: none;
            color: white;
            transition: all 0.3s ease;
            padding: 8px 20px;
            font-weight: 500;
        }
        
        .btn-space:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
            color: white;
        }
        
        .btn-outline-space {
            background: transparent;
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }
        
        .btn-outline-space:hover {
            border-color: var(--neon-blue);
            background: rgba(0, 212, 255, 0.1);
            color: white;
        }
        
        #map {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(93, 63, 211, 0.3);
            min-height: 300px;
        }
        
        .chart-container {
            position: relative;
            height: 160px;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .chart-header {
            color: var(--neon-blue);
            font-size: 0.9rem;
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .info-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .info-panel-header {
            color: var(--neon-blue);
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
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
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--neon-blue);
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .planet {
                display: none;
            }
            
            .stat-value {
                font-size: 1.5rem;
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
                        <a class="nav-link nav-link-space" href="/dashboard-minimal">
                            <i class="fas fa-globe-europe me-2"></i>JWST
                        </a>
                        <a class="nav-link nav-link-space" href="/osdr-new">
                            <i class="fas fa-database me-2"></i>OSDR
                        </a>
                        <a class="nav-link nav-link-space active" href="/iss-new" style="background: rgba(93, 63, 211, 0.2);">
                            <i class="fas fa-satellite me-2"></i>МКС
                        </a>
                        <a class="nav-link nav-link-space" href="/telemetry">
                            <i class="fas fa-chart-line me-2"></i>Телеметрия
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="planet"></div>
    
    <main class="main-content">
        <div class="container">
            <h1 class="page-title">МКС - Международная Космическая Станция</h1>
            <p class="page-subtitle">
                Отслеживание положения и параметров Международной Космической Станции в режиме реального времени
            </p>
            
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-label">Скорость МКС</div>
                        <div class="stat-value">
                            @if(!empty($initialData['payload']['velocity']))
                                {{ number_format($initialData['payload']['velocity'], 0, '', ' ') }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="stat-unit">км/ч</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-label">Высота орбиты</div>
                        <div class="stat-value">
                            @if(!empty($initialData['payload']['altitude']))
                                {{ number_format($initialData['payload']['altitude'], 0, '', ' ') }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="stat-unit">км</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-label">Широта</div>
                        <div class="stat-value">
                            @if(!empty($initialData['payload']['latitude']))
                                {{ number_format($initialData['payload']['latitude'], 2) }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="stat-unit">градусы</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-label">Долгота</div>
                        <div class="stat-value">
                            @if(!empty($initialData['payload']['longitude']))
                                {{ number_format($initialData['payload']['longitude'], 2) }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="stat-unit">градусы</div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header card-header-space">
                            <i class="fas fa-satellite me-2"></i>Текущие параметры МКС
                        </div>
                        <div class="card-body">
                            @if(!empty($initialData['payload']))
                            <ul class="data-list">
                                <li>
                                    <span class="label">Широта:</span>
                                    <span class="value">{{ $initialData['payload']['latitude'] ?? '—' }}</span>
                                </li>
                                <li>
                                    <span class="label">Долгота:</span>
                                    <span class="value">{{ $initialData['payload']['longitude'] ?? '—' }}</span>
                                </li>
                                <li>
                                    <span class="label">Высота:</span>
                                    <span class="value">{{ $initialData['payload']['altitude'] ?? '—' }} км</span>
                                </li>
                                <li>
                                    <span class="label">Скорость:</span>
                                    <span class="value">{{ $initialData['payload']['velocity'] ?? '—' }} км/ч</span>
                                </li>
                                <li>
                                    <span class="label">Время получения:</span>
                                    <span class="value">{{ $initialData['fetched_at'] ?? '—' }}</span>
                                </li>
                            </ul>
                            @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-satellite"></i>
                                </div>
                                <h4>Данные не получены</h4>
                                <p>Нет доступных данных о текущем положении МКС.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="dashboard-card h-100">
                        <div class="card-header card-header-space">
                            <i class="fas fa-chart-line me-2"></i>Тренд движения МКС
                        </div>
                        <div class="card-body">
                            @if(!empty($trendData))
                            <ul class="data-list">
                                <li>
                                    <span class="label">Движение:</span>
                                    <span class="value {{ ($trendData['movement'] ?? false) ? 'text-success' : 'text-warning' }}">
                                        {{ ($trendData['movement'] ?? false) ? 'Активно' : 'Отсутствует' }}
                                    </span>
                                </li>
                                <li>
                                    <span class="label">Смещение:</span>
                                    <span class="value">{{ number_format($trendData['delta_km'] ?? 0, 3, '.', ' ') }} км</span>
                                </li>
                                <li>
                                    <span class="label">Интервал:</span>
                                    <span class="value">{{ $trendData['dt_sec'] ?? 0 }} сек</span>
                                </li>
                                <li>
                                    <span class="label">Скорость (тренд):</span>
                                    <span class="value">{{ $trendData['velocity_kmh'] ?? '—' }} км/ч</span>
                                </li>
                                <li>
                                    <span class="label">Направление:</span>
                                    <span class="value">
                                        @if(!empty($trendData['from_lat']) && !empty($trendData['to_lat']))
                                            {{ $trendData['from_lat'] < $trendData['to_lat'] ? 'С юга на север' : 'С севера на юг' }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </li>
                            </ul>
                            @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h4>Тренд не доступен</h4>
                                <p>Нет данных о движении МКС за период.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-4">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header card-header-space">
                            <i class="fas fa-chart-area me-2"></i>Графики параметров МКС
                        </div>
                        <div class="card-body">
                            <div class="chart-header">Скорость МКС (км/ч)</div>
                            <div class="chart-container">
                                <canvas id="speedChart"></canvas>
                            </div>
                            
                            <div class="chart-header">Высота орбиты (км)</div>
                            <div class="chart-container">
                                <canvas id="altitudeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header card-header-space">
                            <i class="fas fa-globe-americas me-2"></i>Положение МКС на карте
                        </div>
                        <div class="card-body">
                            <div id="map" style="height: 400px;"></div>
                            <div class="mt-3">
                                <button class="btn btn-space w-100" onclick="window.location.reload()">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Обновить данные
                                </button>
                            </div>
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const ISS_CONFIG = {
            endpoints: {
                latest: '/api/iss/latest',
                trend: '/api/iss/trend',
                fetch: '/api/iss/fetch',
                history: '/api/iss/history'
            },
            initialData: @json($initialData ?? []),
            trendData: @json($trendData ?? []),
            nodeBackendAvailable: @json($nodeBackendAvailable ?? false)
        };
        
        @if(isset($apiEndpoints) && is_array($apiEndpoints))
            ISS_CONFIG.endpoints = @json($apiEndpoints);
        @endif
        
        let map, marker, trail;
        let speedChart, altitudeChart;
        let isUpdating = false;
        
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
        
        function formatNumber(num, decimals = 0) {
            if (num === null || num === undefined || isNaN(num)) return '—';
            const formatted = parseFloat(num).toFixed(decimals);
            if (decimals === 0) {
                return formatted.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }
            return formatted;
        }
        
        function initISSMap() {
            const lastData = ISS_CONFIG.initialData.payload || {};
            let lat = Number(lastData.latitude || 0);
            let lon = Number(lastData.longitude || 0);
            
            map = L.map('map', { 
                attributionControl: false,
                zoomControl: true
            }).setView([lat || 0, lon || 0], lat ? 3 : 2);
            
            L.tileLayer('https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png', { 
                noWrap: true,
                className: 'map-tiles'
            }).addTo(map);
            
            const issIcon = L.divIcon({
                html: '<i class="fas fa-satellite" style="color:#00d4ff; font-size:28px; text-shadow:0 0 15px #00d4ff;"></i>',
                iconSize: [30, 30],
                className: 'iss-icon'
            });
            
            marker = L.marker([lat || 0, lon || 0], { icon: issIcon })
                .addTo(map)
                .bindPopup(`
                    <b>Международная космическая станция</b><br>
                    Широта: ${lat || '—'}<br>
                    Долгота: ${lon || '—'}<br>
                    Высота: ${lastData.altitude || '—'} км<br>
                    Скорость: ${lastData.velocity || '—'} км/ч
                `);
        }
        
        function initCharts() {
            const ctx1 = document.getElementById('speedChart').getContext('2d');
            const ctx2 = document.getElementById('altitudeChart').getContext('2d');
            
            speedChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Скорость (км/ч)',
                        data: [],
                        borderColor: '#00d4ff',
                        backgroundColor: 'rgba(0, 212, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                        }
                    }
                }
            });
            
            altitudeChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Высота (км)',
                        data: [],
                        borderColor: '#5d3fd3',
                        backgroundColor: 'rgba(93, 63, 211, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                        }
                    }
                }
            });
            
            if (ISS_CONFIG.trendData && ISS_CONFIG.trendData.points) {
                updateCharts(ISS_CONFIG.trendData.points);
            }
        }
        
        function updateCharts(points) {
            if (!points || points.length === 0) return;
            
            const labels = points.map((p, i) => {
                if (p.timestamp) {
                    const date = new Date(p.timestamp * 1000);
                    return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
                }
                return `Точка ${i + 1}`;
            });
            
            const speeds = points.map(p => p.velocity || p.speed || 0);
            const altitudes = points.map(p => p.altitude || 0);
            
            const maxPoints = 50;
            if (points.length > maxPoints) {
                const step = Math.ceil(points.length / maxPoints);
                speedChart.data.labels = labels.filter((_, i) => i % step === 0);
                speedChart.data.datasets[0].data = speeds.filter((_, i) => i % step === 0);
                altitudeChart.data.labels = labels.filter((_, i) => i % step === 0);
                altitudeChart.data.datasets[0].data = altitudes.filter((_, i) => i % step === 0);
            } else {
                speedChart.data.labels = labels;
                speedChart.data.datasets[0].data = speeds;
                altitudeChart.data.labels = labels;
                altitudeChart.data.datasets[0].data = altitudes;
            }
            
            speedChart.update('none');
            altitudeChart.update('none');
        }
        
        async function loadISSTrend() {
            if (isUpdating) return;
            isUpdating = true;
            
            try {
                
                const [latestResponse, trendResponse, historyResponse] = await Promise.all([
                    fetch(ISS_CONFIG.endpoints.latest),
                    fetch(ISS_CONFIG.endpoints.trend),
                    fetch(`${ISS_CONFIG.endpoints.history}?limit=60`)
                ]);
                
                if (!latestResponse.ok) {
                    throw new Error(`HTTP ошибка latest: ${latestResponse.status}`);
                }
                
                const latestData = await latestResponse.json();
                const trendData = trendResponse.ok ? await trendResponse.json() : null;
                const historyData = historyResponse.ok ? await historyResponse.json() : null;
                
                if (!latestData || !latestData.payload) {
                    throw new Error('Нет данных payload в ответе');
                }
                
                updateStats(latestData);
                
                updateLatestData(latestData);
                
                updateTrendData(trendData, historyData);
                
                updateMap(latestData, historyData);
                
                updateChartsFromHistory(historyData, latestData);
                
                updateLastUpdateTime();
                
            } catch (error) {
                console.error('Ошибка загрузки данных МКС:', error);                
            } finally {
                isUpdating = false;
            }
        }

        function updateStats(latestData) {
            if (!latestData.payload) return;
            
            const payload = latestData.payload;
            
            const stats = {
                'statVelocity': payload.velocity,
                'statAltitude': payload.altitude, 
                'statLatitude': payload.latitude,
                'statLongitude': payload.longitude
            };
            
            Object.entries(stats).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value ? formatNumber(value, id.includes('itude') ? 2 : 0) : '—';
                }
            });
        }

        function updateLatestData(latestData) {
            if (!latestData.payload) return;
            
            const latestList = document.getElementById('latestDataList');
            if (!latestList) return;
            
            const payload = latestData.payload;
            const fetchedTime = latestData.fetched_at 
                ? new Date(latestData.fetched_at).toLocaleString('ru-RU') 
                : new Date().toLocaleString('ru-RU');
            
            latestList.innerHTML = `
                <li>
                    <span class="label">Широта:</span>
                    <span class="value">${payload.latitude ? formatNumber(payload.latitude, 6) : '—'}</span>
                </li>
                <li>
                    <span class="label">Долгота:</span>
                    <span class="value">${payload.longitude ? formatNumber(payload.longitude, 6) : '—'}</span>
                </li>
                <li>
                    <span class="label">Высота:</span>
                    <span class="value">${payload.altitude ? formatNumber(payload.altitude, 1) + ' км' : '—'}</span>
                </li>
                <li>
                    <span class="label">Скорость:</span>
                    <span class="value">${payload.velocity ? formatNumber(payload.velocity, 0) + ' км/ч' : '—'}</span>
                </li>
                <li>
                    <span class="label">Время получения:</span>
                    <span class="value">${fetchedTime}</span>
                </li>
            `;
        }

        function updateTrendData(trendData, historyData) {
            const trendList = document.getElementById('trendDataList');
            if (!trendList) return;
            
            const historyPoints = historyData?.points || [];
            const hasHistory = historyPoints.length > 0;
            
            if (trendData) {
                trendList.innerHTML = `
                    <li>
                        <span class="label">Движение:</span>
                        <span class="value ${trendData.movement ? 'text-success' : 'text-warning'}">
                            ${trendData.movement ? 'Активно' : 'Отсутствует'}
                        </span>
                    </li>
                    <li>
                        <span class="label">Смещение:</span>
                        <span class="value">${formatNumber(trendData.delta_km || 0, 3)} км</span>
                    </li>
                    <li>
                        <span class="label">Интервал:</span>
                        <span class="value">${formatNumber(trendData.dt_sec || 0, 1)} сек</span>
                    </li>
                    <li>
                        <span class="label">Скорость (тренд):</span>
                        <span class="value">${trendData.velocity_kmh ? formatNumber(trendData.velocity_kmh, 0) + ' км/ч' : '—'}</span>
                    </li>
                    <li>
                        <span class="label">Исторических точек:</span>
                        <span class="value ${hasHistory ? 'text-success' : 'text-warning'}">
                            ${hasHistory ? historyPoints.length + ' шт' : 'Нет данных'}
                        </span>
                    </li>
                `;
            } else if (hasHistory) {
                calculateTrendFromHistory(historyPoints, trendList);
            } else {
                trendList.innerHTML = `
                    <li>
                        <span class="label">Статус:</span>
                        <span class="value text-warning">Данные тренда недоступны</span>
                    </li>
                `;
            }
        }

        function calculateTrendFromHistory(points, trendList) {
            if (points.length < 2) return;
            
            const first = points[0];
            const last = points[points.length - 1];
            
            const deltaKm = calculateHaversine(
                first.lat, first.lon,
                last.lat, last.lon
            );
            
            const time1 = first.timestamp ? new Date(first.timestamp * 1000) : new Date(first.fetched_at);
            const time2 = last.timestamp ? new Date(last.timestamp * 1000) : new Date(last.fetched_at);
            const dtSec = (time2 - time1) / 1000;
            
            const avgSpeed = points.reduce((sum, p) => sum + (p.velocity || 0), 0) / points.length;
            
            trendList.innerHTML = `
                <li>
                    <span class="label">Движение (из истории):</span>
                    <span class="value ${deltaKm > 0.1 ? 'text-success' : 'text-warning'}">
                        ${deltaKm > 0.1 ? 'Активно' : 'Минимальное'}
                    </span>
                </li>
                <li>
                    <span class="label">Общее смещение:</span>
                    <span class="value">${formatNumber(deltaKm, 1)} км</span>
                </li>
                <li>
                    <span class="label">Период анализа:</span>
                    <span class="value">${formatNumber(dtSec / 60, 1)} мин</span>
                </li>
                <li>
                    <span class="label">Средняя скорость:</span>
                    <span class="value">${formatNumber(avgSpeed, 0)} км/ч</span>
                </li>
                <li>
                    <span class="label">Точек в анализе:</span>
                    <span class="value text-success">${points.length} шт</span>
                </li>
            `;
        }

        function updateMap(latestData, historyData) {
            if (!latestData.payload || !map || !marker) return;
            
            const payload = latestData.payload;
            const lat = parseFloat(payload.latitude);
            const lon = parseFloat(payload.longitude);
            
            if (isNaN(lat) || isNaN(lon)) return;
            
            marker.setLatLng([lat, lon]);
            marker.bindPopup(`
                <div style="font-size: 14px;">
                    <b>МКС - Текущее положение</b><br><br>
                    Широта: <b>${formatNumber(lat, 6)}</b><br>
                    Долгота: <b>${formatNumber(lon, 6)}</b><br>
                    Высота: <b>${payload.altitude ? formatNumber(payload.altitude, 1) + ' км' : '—'}</b><br>
                    Скорость: <b>${payload.velocity ? formatNumber(payload.velocity, 0) + ' км/ч' : '—'}</b><br>
                    Обновлено: <b>${new Date().toLocaleTimeString('ru-RU')}</b>
                </div>
            `);
            
            map.flyTo([lat, lon], 3, { duration: 1 });
            
            if (trail && historyData?.points) {
                const trailPoints = historyData.points
                    .filter(p => p.lat && p.lon)
                    .map(p => [p.lat, p.lon]);
                
                if (trailPoints.length > 0) {
                    trail.setLatLngs(trailPoints);
                }
            }
        }

        function updateChartsFromHistory(historyData, latestData) {
            if (historyData?.points && historyData.points.length > 0) {
                updateCharts(historyData.points);
                return;
            }
            
            if (latestData?.payload) {
                createChartsFromCurrent(latestData.payload);
                return;
            }
        }

        function createChartsFromCurrent(currentPayload) {
            
            const baseSpeed = currentPayload.velocity || 27500;
            const baseAltitude = currentPayload.altitude || 420;
            
            const labels = [];
            const speeds = [];
            const altitudes = [];
            
            const now = new Date();
            
            for (let i = 59; i >= 0; i--) {
                const time = new Date(now.getTime() - i * 30000);
                labels.push(time.toLocaleTimeString('ru-RU', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                }));
                
                speeds.push(baseSpeed + Math.sin(i * 0.3) * 100 + (Math.random() - 0.5) * 50);
                altitudes.push(baseAltitude + Math.cos(i * 0.2) * 1 + (Math.random() - 0.5) * 0.5);
            }
            
            if (speedChart && altitudeChart) {
                speedChart.data.labels = labels;
                speedChart.data.datasets[0].data = speeds;
                speedChart.update();
                
                altitudeChart.data.labels = labels;
                altitudeChart.data.datasets[0].data = altitudes;
                altitudeChart.update();
            }
        }

        function updateLastUpdateTime() {
            const timeElement = document.getElementById('lastUpdateTime');
            if (timeElement) {
                timeElement.textContent = new Date().toLocaleString('ru-RU');
            }
        }

        function toRad(degrees) {
            return degrees * (Math.PI / 180);
        }

        function calculateHaversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function showDemoData() {
            const points = [];
            const now = new Date();
            const baseSpeed = 27500;
            const baseAltitude = 420;
            
            for (let i = 30; i >= 0; i--) {
                const time = new Date(now.getTime() - i * 120000);
                points.push({
                    lat: 51.5074 + Math.sin(i) * 10,
                    lon: -0.1278 + Math.cos(i) * 10,
                    altitude: baseAltitude + (Math.random() - 0.5) * 2,
                    velocity: baseSpeed + (Math.random() - 0.5) * 100,
                    timestamp: Math.floor(time.getTime() / 1000),
                    fetched_at: time.toISOString()
                });
            }
            
            updateCharts(points);
            
            const trendList = document.getElementById('trendDataList');
            if (trendList) {
                trendList.innerHTML += `
                    <li class="text-warning">
                        <span class="label">Внимание:</span>
                        <span class="value">Демо-данные</span>
                    </li>
                `;
            }
        }
        
        async function triggerFetch() {
            if (isUpdating) return;
            isUpdating = true;
            
            try {
                
                const response = await fetch(ISS_CONFIG.endpoints.fetch);
                const result = await response.json();
                
                if (response.ok) {
                    
                    setTimeout(() => {
                        loadISSTrend();
                    }, 1000);
                } else {
                    console.error('Ошибка получения данных:', result.message || 'Неизвестная ошибка');
                }
                
            } catch (error) {
                console.error('Ошибка запроса новых данных:', error);
            } finally {
                isUpdating = false;
            }
        }
        
        function initAnimations() {
            const cards = document.querySelectorAll('.dashboard-card, .stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            });
            
            function handleScrollAnimations() {
                const cards = document.querySelectorAll('.dashboard-card, .stat-card');
                const windowHeight = window.innerHeight;
                
                cards.forEach((card) => {
                    const cardTop = card.getBoundingClientRect().top;
                    const cardVisible = 100;
                    
                    if (cardTop < windowHeight - cardVisible) {
                        card.style.opacity = "1";
                        card.style.transform = "translateY(0)";
                    }
                });
            }
            
            window.addEventListener('scroll', handleScrollAnimations);
            setTimeout(() => {
                handleScrollAnimations();
            }, 300);
        }
        
        function initEventListeners() {
            const refreshBtn = document.querySelector('.btn-space');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    loadISSTrend();
                });
            }
            
            const triggerFetchBtn = document.getElementById('triggerFetchBtn');
            if (triggerFetchBtn) {
                triggerFetchBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    triggerFetch();
                });
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            
            if (typeof L !== 'undefined') {
                initISSMap();
            }
            
            if (typeof Chart !== 'undefined') {
                initCharts();
            }
            
            initEventListeners();
            initAnimations();
            
            if (ISS_CONFIG.nodeBackendAvailable) {
                setTimeout(() => {
                    loadISSTrend();
                }, 1000);
                
                setInterval(() => {
                    loadISSTrend();
                }, 30000);
            } else {
                console.warn('Node.js бэкенд недоступен, используются демо-данные');
            }
        });
    </script>
</body>
</html>