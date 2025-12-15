<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Телеметрия космических систем - Space Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --space-blue: #0a192f;
            --space-dark: #020c1b;
            --space-purple: #5d3fd3;
            --neon-blue: #00d4ff;
            --star-glow: #ffd700;
            --telemetry-green: #00ff88;
            --telemetry-yellow: #ffcc00;
            --telemetry-red: #ff4757;
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
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
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
        
        .telemetry-stat-card {
            background: linear-gradient(135deg, rgba(18, 23, 42, 0.9), rgba(30, 35, 55, 0.9));
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 10px;
            padding: 1.2rem;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .telemetry-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(93, 63, 211, 0.2);
        }
        
        .stat-icon {
            background: rgba(93, 63, 211, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
        
        .form-control-astro {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: white;
            border-radius: 8px;
        }
        
        .form-control-astro:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--neon-blue);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.1);
        }
        
        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }
        
        .chart-header {
            color: var(--neon-blue);
            font-size: 0.9rem;
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .telemetry-table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(93, 63, 211, 0.05);
            --bs-table-striped-color: #fff;
            --bs-table-active-bg: rgba(93, 63, 211, 0.1);
            --bs-table-active-color: #fff;
            --bs-table-hover-bg: rgba(93, 63, 211, 0.1);
            --bs-table-hover-color: #fff;
            color: #fff;
            border-color: rgba(93, 63, 211, 0.2);
        }
        
        .telemetry-table thead th {
            background: rgba(93, 63, 211, 0.2);
            color: #fff;
            font-weight: 600;
            border-bottom: 2px solid rgba(93, 63, 211, 0.3);
            padding: 1rem;
        }
        
        .telemetry-table tbody td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
            border-color: rgba(93, 63, 211, 0.1);
        }
        
        .telemetry-value {
            font-family: 'Orbitron', monospace;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-normal { background: rgba(0, 255, 136, 0.2); color: var(--telemetry-green); }
        .status-warning { background: rgba(255, 204, 0, 0.2); color: var(--telemetry-yellow); }
        .status-critical { background: rgba(255, 71, 87, 0.2); color: var(--telemetry-red); }
        
        .sensor-badge {
            background: rgba(93, 63, 211, 0.2);
            color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(93, 63, 211, 0.3);
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
        
        .voltage-good { color: var(--telemetry-green); }
        .voltage-warning { color: var(--telemetry-yellow); }
        .voltage-critical { color: var(--telemetry-red); }
        
        .temp-normal { color: var(--telemetry-green); }
        .temp-warning { color: var(--telemetry-yellow); }
        .temp-critical { color: var(--telemetry-red); }
        
        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .pagination-btn {
            background: rgba(93, 63, 211, 0.2);
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .pagination-btn:hover:not(:disabled) {
            background: rgba(93, 63, 211, 0.4);
            border-color: var(--neon-blue);
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .refresh-btn {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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
            
            .telemetry-table {
                font-size: 0.8rem;
            }
            
            .telemetry-table thead th,
            .telemetry-table tbody td {
                padding: 0.6rem;
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
                        <a class="nav-link nav-link-space" href="/iss-new">
                            <i class="fas fa-satellite me-2"></i>МКС
                        </a>
                        <a class="nav-link nav-link-space active" href="/telemetry" style="background: rgba(93, 63, 211, 0.2);">
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
            <h1 class="page-title">Телеметрия космических систем</h1>
            <p class="page-subtitle">
                Мониторинг состояния космических аппаратов в реальном времени. 
                Отслеживание напряжения, температуры и статуса операционных систем.
            </p>
            
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="telemetry-stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="fas fa-bolt text-warning"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stat-label">Среднее напряжение</div>
                                <div class="stat-value" id="avgVoltage">0.00</div>
                                <div class="stat-unit">Вольт</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="telemetry-stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="fas fa-thermometer-half text-danger"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stat-label">Средняя температура</div>
                                <div class="stat-value" id="avgTemp">0.00</div>
                                <div class="stat-unit">°C</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="telemetry-stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="fas fa-database text-success"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stat-label">Всего записей</div>
                                <div class="stat-value" id="totalRecords">0</div>
                                <div class="stat-unit">записей</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="telemetry-stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stat-label">Последнее обновление</div>
                                <div class="stat-value" id="lastUpdate">-</div>
                                <div class="stat-unit"><button class="btn btn-sm btn-outline-space p-1" onclick="refreshAllData()">Обновить</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header card-header-space d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-line me-2"></i>График напряжения
                            </div>
                            <select class="form-select form-select-sm w-auto" id="voltageTimeRange" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(93, 63, 211, 0.3);">
                                <option value="1">Последний час</option>
                                <option value="6">6 часов</option>
                                <option value="24" selected>24 часа</option>
                                <option value="168">7 дней</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="voltageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header card-header-space d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-area me-2"></i>График температуры
                            </div>
                            <select class="form-select form-select-sm w-auto" id="tempTimeRange" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(93, 63, 211, 0.3);">
                                <option value="1">Последний час</option>
                                <option value="6">6 часов</option>
                                <option value="24" selected>24 часа</option>
                                <option value="168">7 дней</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="temperatureChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card mb-4">
                <div class="card-header card-header-space d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-2"></i>Исторические данные телеметрии
                    </div>
                    <button class="btn btn-space btn-sm" onclick="refreshTableData()">
                        <i class="fas fa-sync-alt me-1"></i> Обновить
                    </button>
                </div>
                
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">ID сенсора</label>
                            <input type="text" class="form-control form-control-astro" 
                                   id="sensorId" placeholder="SENSOR_0017">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Дата с</label>
                            <input type="date" class="form-control form-control-astro" 
                                   id="dateFrom">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Дата по</label>
                            <input type="date" class="form-control form-control-astro" 
                                   id="dateTo">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Статус</label>
                            <select class="form-control form-control-astro" id="statusFilter">
                                <option value="">Все</option>
                                <option value="normal">Норма</option>
                                <option value="warning">Предупреждение</option>
                                <option value="critical">Критично</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-space w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Применить фильтры
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table telemetry-table">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Время записи</th>
                                    <th>ID сенсора</th>
                                    <th>Напряжение (V)</th>
                                    <th>Температура (°C)</th>
                                    <th>Статус</th>
                                    <th>Исходный файл</th>
                                </tr>
                            </thead>
                            <tbody id="telemetryBody">
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Загрузка...</span>
                                        </div>
                                        <p class="mt-2 text-info">Загрузка данных телеметрии...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination-controls">
                        <button class="pagination-btn" id="prevPage" onclick="prevPage()" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="text-light mx-3">
                            Страница <span id="currentPage">1</span> из <span id="totalPages">1</span>
                        </span>
                        <button class="pagination-btn" id="nextPage" onclick="nextPage()" disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <span class="text-light ms-3">
                            Показано <span id="shownCount">0</span> из <span id="totalCount">0</span> записей
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="info-panel">
                <div class="info-panel-header">
                    <i class="fas fa-info-circle"></i>
                    Информация о данных телеметрии
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="data-list">
                            <li>
                                <span class="label">Диапазон напряжения:</span>
                                <span class="value voltage-good">3.2 - 12.6 V</span>
                            </li>
                            <li>
                                <span class="label">Критическое напряжение:</span>
                                <span class="value voltage-critical">&lt; 5.0 V</span>
                            </li>
                            <li>
                                <span class="label">Диапазон температуры:</span>
                                <span class="value temp-good">-50 - 80 °C</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="data-list">
                            <li>
                                <span class="label">Критическая температура:</span>
                                <span class="value temp-critical">&gt; 60 °C</span>
                            </li>
                            <li>
                                <span class="label">Период генерации:</span>
                                <span class="value">5 минут</span>
                            </li>
                            <li>
                                <span class="label">Источник данных:</span>
                                <span class="value">TelemetryGenerator</span>
                            </li>
                        </ul>
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
                    &copy; 2025 Astronomy Dashboard. Система телеметрии космических аппаратов.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const TELEMETRY_CONFIG = {
            endpoints: {
                data: '/api/telemetry/data',
                stats: '/api/telemetry/stats',
                realtime: '/api/telemetry/realtime'
            },
            pageSize: 10,
            currentPage: 1,
            totalRecords: 0,
            filters: {},
            charts: {},
            updateInterval: 30000
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            initCharts();
            initFilters();
            loadStats();
            loadTableData();
            
            const now = new Date();
            const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
            
            document.getElementById('dateFrom').value = weekAgo.toISOString().split('T')[0];
            document.getElementById('dateTo').value = now.toISOString().split('T')[0];
            
            setInterval(() => {
                loadStats();
                updateCharts();
            }, TELEMETRY_CONFIG.updateInterval);
        });
        
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
        
        function initCharts() {
            const voltageCtx = document.getElementById('voltageChart').getContext('2d');
            const tempCtx = document.getElementById('temperatureChart').getContext('2d');
            
            TELEMETRY_CONFIG.charts.voltage = new Chart(voltageCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Напряжение (V)',
                        data: [],
                        borderColor: '#00ff88',
                        backgroundColor: 'rgba(0, 255, 136, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.7)' },
                            suggestedMin: 3,
                            suggestedMax: 13
                        }
                    }
                }
            });
            
            TELEMETRY_CONFIG.charts.temperature = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Температура (°C)',
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
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: 'rgba(255, 255, 255, 0.7)' },
                            suggestedMin: -60,
                            suggestedMax: 90
                        }
                    }
                }
            });
            
            updateCharts();
        }
        
        function initFilters() {
            document.getElementById('voltageTimeRange').addEventListener('change', () => updateCharts());
            document.getElementById('tempTimeRange').addEventListener('change', () => updateCharts());
        }
        
        async function loadStats() {
            try {
                const response = await fetch(TELEMETRY_CONFIG.endpoints.stats);
                const data = await response.json();
                
                if (data.success) {
                    updateStatsUI(data.stats);
                }
            } catch (error) {
                console.error('Ошибка загрузки статистики:', error);
            }
        }
        
        function updateStatsUI(stats) {
            document.getElementById('avgVoltage').textContent = 
                parseFloat(stats.avg_voltage || 0).toFixed(2);
            document.getElementById('avgTemp').textContent = 
                parseFloat(stats.avg_temperature || 0).toFixed(2);
            document.getElementById('totalRecords').textContent = 
                (stats.total_records || 0).toLocaleString();
            
            const lastUpdate = new Date(stats.last_record || new Date()).toLocaleString('ru-RU');
            document.getElementById('lastUpdate').textContent = lastUpdate.split(',')[0];
        }
        
        async function updateCharts() {
            try {
                const voltageRange = document.getElementById('voltageTimeRange').value;
                const tempRange = document.getElementById('tempTimeRange').value;
                
                const response = await fetch(TELEMETRY_CONFIG.endpoints.realtime);
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    updateChartWithData(data.data.slice(0, 50));
                }
            } catch (error) {
                console.error('Ошибка обновления графиков:', error);
                generateDemoCharts();
            }
        }
        
        function updateChartWithData(data) {
            const labels = data.map(item => {
                const date = new Date(item.recorded_at);
                return date.toLocaleTimeString('ru-RU', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            }).reverse();
            
            const voltages = data.map(item => parseFloat(item.voltage || 0)).reverse();
            const temps = data.map(item => parseFloat(item.temperature || 0)).reverse();
            
            TELEMETRY_CONFIG.charts.voltage.data.labels = labels;
            TELEMETRY_CONFIG.charts.voltage.data.datasets[0].data = voltages;
            TELEMETRY_CONFIG.charts.voltage.update();
            
            TELEMETRY_CONFIG.charts.temperature.data.labels = labels;
            TELEMETRY_CONFIG.charts.temperature.data.datasets[0].data = temps;
            TELEMETRY_CONFIG.charts.temperature.update();
        }
        
        function generateDemoCharts() {
            const labels = [];
            const voltages = [];
            const temps = [];
            
            const now = new Date();
            
            for (let i = 59; i >= 0; i--) {
                const time = new Date(now.getTime() - i * 60000);
                labels.push(time.toLocaleTimeString('ru-RU', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                }));
                
                voltages.push(7.5 + Math.sin(i * 0.1) * 2 + (Math.random() - 0.5) * 0.5);
                temps.push(25 + Math.cos(i * 0.05) * 10 + (Math.random() - 0.5) * 2);
            }
            
            TELEMETRY_CONFIG.charts.voltage.data.labels = labels;
            TELEMETRY_CONFIG.charts.voltage.data.datasets[0].data = voltages;
            TELEMETRY_CONFIG.charts.voltage.update();
            
            TELEMETRY_CONFIG.charts.temperature.data.labels = labels;
            TELEMETRY_CONFIG.charts.temperature.data.datasets[0].data = temps;
            TELEMETRY_CONFIG.charts.temperature.update();
        }
        
        async function loadTableData() {
            const tbody = document.getElementById('telemetryBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                        <p class="mt-2 text-info">Загрузка данных телеметрии...</p>
                    </td>
                </tr>
            `;
            
            try {
                const offset = (TELEMETRY_CONFIG.currentPage - 1) * TELEMETRY_CONFIG.pageSize;
                let url = `${TELEMETRY_CONFIG.endpoints.data}?limit=${TELEMETRY_CONFIG.pageSize}&offset=${offset}`;
                
                Object.entries(TELEMETRY_CONFIG.filters).forEach(([key, value]) => {
                    if (value) {
                        url += `&${key}=${encodeURIComponent(value)}`;
                    }
                });
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success) {
                    TELEMETRY_CONFIG.totalRecords = data.total;
                    renderTable(data.data);
                    updatePagination();
                } else {
                    throw new Error(data.message || 'Ошибка загрузки данных');
                }
            } catch (error) {
                console.error('Ошибка загрузки таблицы:', error);
                renderDemoTable();
            }
        }
        
        function renderTable(data) {
            const tbody = document.getElementById('telemetryBody');
            
            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-5 text-info">
                            <i class="fas fa-search me-2"></i>
                            Нет данных по выбранным фильтрам
                        </td>
                    </tr>
                `;
                updateCounters(0);
                return;
            }
            
            let html = '';
            const startNum = (TELEMETRY_CONFIG.currentPage - 1) * TELEMETRY_CONFIG.pageSize + 1;
            
            data.forEach((item, index) => {
                const rowNum = startNum + index;
                const recordedAt = new Date(item.recorded_at).toLocaleString('ru-RU');
                
                const voltage = parseFloat(item.voltage || item.voltage_value || 0);
                const temperature = parseFloat(item.temperature || item.temp_value || 0);
                
                let status = 'normal';
                let statusText = 'Норма';
                let statusClass = 'status-normal';
                
                if (voltage < 5 || temperature > 60) {
                    status = 'critical';
                    statusText = 'Критично';
                    statusClass = 'status-critical';
                } else if (voltage < 6 || temperature > 50) {
                    status = 'warning';
                    statusText = 'Предупреждение';
                    statusClass = 'status-warning';
                }
                
                const voltageClass = voltage < 5 ? 'voltage-critical' : 
                                   voltage < 6 ? 'voltage-warning' : 'voltage-good';
                const tempClass = temperature > 60 ? 'temp-critical' : 
                                 temperature > 50 ? 'temp-warning' : 'temp-normal';
                
                html += `
                    <tr>
                        <td class="text-light">${rowNum}</td>
                        <td class="text-light">${recordedAt}</td>
                        <td>
                            <span class="badge sensor-badge">${item.sensor_id || 'N/A'}</span>
                        </td>
                        <td>
                            <span class="telemetry-value ${voltageClass}">
                                ${voltage.toFixed(2)} V
                            </span>
                        </td>
                        <td>
                            <span class="telemetry-value ${tempClass}">
                                ${temperature.toFixed(2)} °C
                            </span>
                        </td>
                        <td>
                            <span class="status-badge ${statusClass}">${statusText}</span>
                        </td>
                        <td class="text-light small">${item.source_file || '-'}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            updateCounters(data.length);
        }
        
        function renderDemoTable() {
            const tbody = document.getElementById('telemetryBody');
            const demoData = [];
            const now = new Date();
            
            for (let i = 0; i < TELEMETRY_CONFIG.pageSize; i++) {
                const voltage = 3.2 + Math.random() * 9.4;
                const temp = -50 + Math.random() * 130;
                const sensorId = 'SENSOR_' + String(Math.floor(Math.random() * 1000) + 1).padStart(4, '0');
                
                demoData.push({
                    recorded_at: new Date(now.getTime() - i * 5 * 60000).toISOString(),
                    voltage: voltage.toFixed(2),
                    temperature: temp.toFixed(2),
                    sensor_id: sensorId,
                    source_file: `telemetry_${now.toISOString().split('T')[0].replace(/-/g, '')}_${String(i).padStart(6, '0')}.csv`
                });
            }
            
            renderTable(demoData);
            TELEMETRY_CONFIG.totalRecords = 1000;
            updatePagination();
            
            const firstRow = tbody.querySelector('tr');
            if (firstRow) {
                firstRow.insertAdjacentHTML('afterend', `
                    <tr>
                        <td colspan="7" class="text-center text-warning small">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Используются демо-данные (БД недоступна)
                        </td>
                    </tr>
                `);
            }
        }
        
        function updateCounters(shown) {
            document.getElementById('shownCount').textContent = shown;
            document.getElementById('totalCount').textContent = 
                TELEMETRY_CONFIG.totalRecords.toLocaleString();
            
            const totalPages = Math.ceil(TELEMETRY_CONFIG.totalRecords / TELEMETRY_CONFIG.pageSize);
            document.getElementById('currentPage').textContent = TELEMETRY_CONFIG.currentPage;
            document.getElementById('totalPages').textContent = totalPages;
        }
        
        function updatePagination() {
            const totalPages = Math.ceil(TELEMETRY_CONFIG.totalRecords / TELEMETRY_CONFIG.pageSize);
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            
            prevBtn.disabled = TELEMETRY_CONFIG.currentPage <= 1;
            nextBtn.disabled = TELEMETRY_CONFIG.currentPage >= totalPages;
        }
        
        function applyFilters() {
            TELEMETRY_CONFIG.filters = {
                sensor_id: document.getElementById('sensorId').value.trim(),
                date_from: document.getElementById('dateFrom').value,
                date_to: document.getElementById('dateTo').value
            };
            
            const statusFilter = document.getElementById('statusFilter').value;
            if (statusFilter) {
                TELEMETRY_CONFIG.filters.status = statusFilter;
            }
            
            TELEMETRY_CONFIG.currentPage = 1;
            loadTableData();
        }
        
        function prevPage() {
            if (TELEMETRY_CONFIG.currentPage > 1) {
                TELEMETRY_CONFIG.currentPage--;
                loadTableData();
            }
        }
        
        function nextPage() {
            const totalPages = Math.ceil(TELEMETRY_CONFIG.totalRecords / TELEMETRY_CONFIG.pageSize);
            if (TELEMETRY_CONFIG.currentPage < totalPages) {
                TELEMETRY_CONFIG.currentPage++;
                loadTableData();
            }
        }
        
        function refreshTableData() {
            const btn = document.querySelector('button[onclick="refreshTableData()"]');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Обновление';
            btn.disabled = true;
            
            loadTableData();
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 1000);
        }
        
        function refreshAllData() {
            loadStats();
            updateCharts();
            refreshTableData();
            
            const updateText = document.getElementById('lastUpdate');
            const originalText = updateText.textContent;
            updateText.textContent = 'Обновление...';
            updateText.style.color = '#00ff88';
            
            setTimeout(() => {
                updateText.style.color = '';
                const now = new Date();
                updateText.textContent = now.toLocaleTimeString('ru-RU', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            }, 2000);
        }
        
        function formatNumber(num, decimals = 0) {
            if (num === null || num === undefined || isNaN(num)) return '—';
            return parseFloat(num).toFixed(decimals);
        }
        
        function initAnimations() {
            const cards = document.querySelectorAll('.dashboard-card, .telemetry-stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            });
            
            function handleScrollAnimations() {
                const cards = document.querySelectorAll('.dashboard-card, .telemetry-stat-card');
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
        
        setTimeout(initAnimations, 500);
    </script>
</body>
</html>