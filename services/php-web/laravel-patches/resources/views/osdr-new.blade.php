<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NASA OSDR - Space Dashboard</title>
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
            --table-bg: rgba(15, 30, 50, 0.8);
            --table-row: rgba(25, 45, 70, 0.5);
            --table-row-alt: rgba(20, 40, 65, 0.5);
        }
        
        body {
            background: linear-gradient(135deg, var(--space-dark) 0%, var(--space-blue) 50%, #1a1a2e 100%);
            color: #e0e0e0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Звёздное небо */
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
        
        /* Header */
        .space-header {
            background: rgba(10, 25, 47, 0.95);
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
        
        /* Main content */
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
        
        /* Информационная панель */
        .info-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
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
        
        /* Таблица OSDR - переделана с тёмным фоном */
        .osdr-table-container {
            background: rgba(15, 30, 50, 0.85) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(93, 63, 211, 0.3) !important;
            border-radius: 15px !important;
            overflow: hidden !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
        }
        
        .osdr-table {
            margin: 0 !important;
            color: #ffffff !important;
            background-color: transparent !important;
            border-collapse: collapse !important;
            width: 100% !important;
            table-layout: fixed !important;
        }
        
        .osdr-table thead {
            background: rgba(93, 63, 211, 0.2) !important;
        }
        
        .osdr-table thead th {
            color: #00d4ff !important;
            border-bottom: 2px solid rgba(93, 63, 211, 0.4) !important;
            font-weight: 600 !important;
            font-family: 'Orbitron', sans-serif !important;
            padding: 15px !important;
            background-color: rgba(93, 63, 211, 0.15) !important;
            text-align: left !important;
        }
        
        /* Колонки одинаковой ширины */
        .osdr-table th:nth-child(1) { width: 60px; } /* # */
        .osdr-table th:nth-child(2) { width: 120px; } /* dataset_id */
        .osdr-table th:nth-child(3) { width: 120px; } /* title */
        .osdr-table th:nth-child(4) { width: 120px; } /* REST_URL */
        .osdr-table th:nth-child(5) { width: 120px; } /* updated_at */
        .osdr-table th:nth-child(6) { width: 250px; } /* inserted_at */
        .osdr-table th:nth-child(7) { width: 100px; } /* raw */
        
        .osdr-table tbody tr {
            background-color: rgba(25, 45, 70, 0.6) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        .osdr-table tbody tr:nth-child(even) {
            background-color: rgba(20, 40, 65, 0.6) !important;
        }
        
        .osdr-table tbody tr:hover {
            background-color: rgba(0, 212, 255, 0.2) !important;
        }
        
        .osdr-table tbody td {
            color: #ffffff !important;
            vertical-align: middle !important;
            padding: 12px 15px !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
        }

        .table {
            --bs-table-bg: transparent !important;
            --bs-table-striped-bg: rgba(25, 45, 70, 0.6) !important;
            --bs-table-striped-color: #ffffff !important;
            --bs-table-active-bg: rgba(0, 212, 255, 0.2) !important;
            --bs-table-active-color: #ffffff !important;
            --bs-table-hover-bg: rgba(0, 212, 255, 0.2) !important;
            --bs-table-hover-color: #ffffff !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Специально для ячейки title - многострочный текст */
        .osdr-table tbody td:nth-child(3) {
            white-space: normal;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.4;
        }
        
        /* Кнопки */
        .btn-osdr {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            border: none;
            color: white;
            transition: all 0.3s ease;
            padding: 8px 20px;
            font-weight: 500;
        }
        
        .btn-osdr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
            color: white;
        }
        
        .btn-outline-osdr {
            background: transparent;
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        
        .btn-outline-osdr:hover {
            border-color: var(--neon-blue);
            background: rgba(0, 212, 255, 0.1);
            color: white;
        }
        
        /* Ссылки */
        .osdr-link {
            color: var(--neon-blue);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .osdr-link:hover {
            color: white;
            text-decoration: underline;
        }
        
        /* JSON блок */
        .json-block {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 10px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .json-block pre {
            background: rgba(0, 0, 0, 0.6);
            color: #90ee90;
            padding: 15px;
            margin: 0;
            font-size: 0.85rem;
            max-height: 300px;
            overflow-y: auto;
            border-radius: 0 0 10px 10px;
        }
        
        .json-toggle {
            background: rgba(93, 63, 211, 0.2);
            color: var(--neon-blue);
            border: none;
            width: 100%;
            padding: 10px 15px;
            text-align: left;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .json-toggle:hover {
            background: rgba(93, 63, 211, 0.3);
        }
        
        .json-toggle[aria-expanded="true"] {
            background: rgba(0, 212, 255, 0.2);
        }
        
        /* Пустое состояние */
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
        
        /* Фильтры и поиск */
        .filter-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .form-control-osdr, .form-select-osdr {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: white;
        }
        
        .form-control-osdr::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-control-osdr:focus, .form-select-osdr:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--neon-blue);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.25);
        }
        
        .form-select-osdr option {
            background: var(--space-blue);
            color: white;
        }
        
        /* Статус */
        .items-count {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(93, 63, 211, 0.2);
            color: var(--neon-blue);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            border: 1px solid rgba(93, 63, 211, 0.3);
        }
        
        /* Пагинация */
        .osdr-pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .page-link-osdr {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: rgba(255, 255, 255, 0.8);
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .page-link-osdr:hover, .page-link-osdr.active {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            border-color: var(--neon-blue);
            color: white;
        }
        
        /* Footer */
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
        
        /* Планета анимация */
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
        
        /* Анимация загрузки */
        .loading-pulse {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--neon-blue);
            animation: pulse 1.5s infinite;
            margin-right: 10px;
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .planet {
                display: none;
            }
            
            .osdr-table {
                font-size: 0.85rem;
            }
            
            .osdr-table thead th,
            .osdr-table tbody td {
                padding: 8px 5px;
            }
            
            .filter-panel .row {
                flex-direction: column;
                gap: 10px;
            }
            
            /* Для мобильных делаем таблицу с прокруткой */
            .osdr-table-container {
                border-radius: 10px;
            }
            
            .osdr-table th:nth-child(1) { width: 50px; }
            .osdr-table th:nth-child(2) { width: 100px; }
            .osdr-table th:nth-child(3) { width: 150px; }
            .osdr-table th:nth-child(4) { width: 80px; }
            .osdr-table th:nth-child(5) { width: 100px; }
            .osdr-table th:nth-child(6) { width: 100px; }
            .osdr-table th:nth-child(7) { width: 80px; }
        }
    </style>
</head>
<body>
    <!-- Звёздное небо -->
    <div class="stars" id="stars"></div>
    
    <!-- Header -->
    <header class="space-header py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Логотип -->
                <div class="logo-container">
                    <a href="/" class="text-decoration-none d-flex align-items-center gap-3">
                        <div class="logo-icon">
                            <i class="fas fa-satellite"></i>
                        </div>
                        <div class="logo-text">Astronomy</div>
                    </a>
                </div>
                
                <!-- Навигация -->
                <nav class="navbar navbar-expand-lg p-0">
                    <div class="navbar-nav">
                        <a class="nav-link nav-link-space" href="/">
                            <i class="fas fa-home me-2"></i>Главная
                        </a>
                        <a class="nav-link nav-link-space" href="/astronomy">
                            <i class="fas fa-calendar-alt me-2"></i>События
                        </a>
                        <a class="nav-link nav-link-space" href="/cms-admin">
                            <i class="fas fa-cogs me-2"></i>CMS
                        </a>
                        <a class="nav-link nav-link-space" href="/dashboard-minimal">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link nav-link-space active" href="/osdr-new" style="background: rgba(93, 63, 211, 0.2);">
                            <i class="fas fa-database me-2"></i>OSDR
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Планета для фона -->
    <div class="planet"></div>
    
    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <h1 class="page-title">NASA OSDR - Открытый репозиторий научных данных</h1>
            <p class="page-subtitle">
                Просмотр и исследование данных из NASA Open Science Data Repository
            </p>
            
            <!-- Информационная панель -->
            <div class="info-panel">
                <div class="info-panel-header">
                    <i class="fas fa-info-circle"></i>
                    Информация о NASA OSDR
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 15px;">
                            NASA Open Science Data Repository (OSDR) предоставляет доступ к научным данным, 
                            полученным в ходе космических миссий и исследований. Здесь вы можете найти наборы данных 
                            по различным направлениям: биология в космосе, физика микрогравитации, материаловедение и другие.
                        </p>
                        <div class="d-flex align-items-center gap-3">
                            <span class="items-count">
                                <i class="fas fa-table me-1"></i>
                                Найдено наборов: {{ count($items ?? []) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="https://osdr.nasa.gov" target="_blank" class="btn btn-osdr">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Официальный сайт OSDR
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Фильтры и поиск -->
            <div class="filter-panel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-osdr" placeholder="Поиск по названию или ID..." id="searchInput">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select form-select-osdr" id="sortSelect">
                            <option selected>Сортировка по дате</option>
                            <option>Сначала новые</option>
                            <option>Сначала старые</option>
                            <option>По названию (А-Я)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-osdr w-100" id="filterBtn">
                            <i class="fas fa-filter me-1"></i>Фильтр
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Таблица данных OSDR -->
            <div class="osdr-table-container">
                <div class="table-responsive">
                    <table class="table osdr-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>dataset_id</th>
                                <th>title</th>
                                <th>REST_URL</th>
                                <th>updated_at</th>
                                <th>inserted_at</th>
                                <th>raw</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $row)
                            <tr>
                                <td>
                                    <span class="badge bg-dark" style="font-family: 'Orbitron';">{{ $row['id'] ?? '—' }}</span>
                                </td>
                                <td>
                                    <code style="color: var(--neon-blue); font-size: 0.9rem;">
                                        {{ $row['dataset_id'] ?? '—' }}
                                    </code>
                                </td>
                                <td>
                                    {{ $row['title'] ?? '—' }}
                                </td>
                                <td>
                                    @if(!empty($row['rest_url']))
                                        <a href="{{ $row['rest_url'] }}" target="_blank" class="osdr-link" title="Открыть в OSDR">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span class="d-none d-md-inline">открыть</span>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $row['updated_at'] ?? '—' }}</td>
                                <td>{{ $row['inserted_at'] ?? '—' }}</td>
                                <td>
                                    <button class="btn btn-outline-osdr btn-sm" data-bs-toggle="collapse" data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                                        <i class="fas fa-code me-1"></i>JSON
                                    </button>
                                </td>
                            </tr>
                            <!-- Скрытый блок с JSON -->
                            <tr class="collapse" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                                <td colspan="7">
                                    <div class="json-block">
                                        <button class="json-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}" aria-expanded="false">
                                            <span>Полный JSON (нажмите чтобы скрыть)</span>
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <pre>{{ json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-database"></i>
                                        </div>
                                        <h4>Данные не найдены</h4>
                                        <p>Нет доступных наборов данных в OSDR репозитории.</p>
                                        <button class="btn btn-osdr mt-2" onclick="window.location.reload()">
                                            <i class="fas fa-sync-alt me-2"></i>Обновить данные
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Пагинация -->
            @if(isset($items) && count($items) > 0)
            <div class="osdr-pagination">
                <a href="#" class="page-link-osdr">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="#" class="page-link-osdr active">1</a>
                <a href="#" class="page-link-osdr">2</a>
                <a href="#" class="page-link-osdr">3</a>
                <span class="text-muted mx-2">...</span>
                <a href="#" class="page-link-osdr">10</a>
                <a href="#" class="page-link-osdr">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            @endif
        </div>
    </main>
    
    <!-- Footer -->
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

    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Создание звёздного неба
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
        
        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            
            // Анимация строк таблицы при появлении
            const tableRows = document.querySelectorAll('.osdr-table tbody tr:not(.collapse)');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                    }
                });
            }, { threshold: 0.1 });
            
            tableRows.forEach((row, index) => {
                if (!row.classList.contains('collapse')) {
                    row.style.opacity = "0";
                    row.style.transform = "translateY(20px)";
                    row.style.transition = `opacity 0.3s ease ${index * 0.05}s, transform 0.3s ease ${index * 0.05}s`;
                    observer.observe(row);
                }
            });
            
            // Обработка JSON кнопок
            document.querySelectorAll('.json-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (this.getAttribute('aria-expanded') === 'true') {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                        this.querySelector('span').textContent = 'Полный JSON (нажмите чтобы показать)';
                    } else {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                        this.querySelector('span').textContent = 'Полный JSON (нажмите чтобы скрыть)';
                    }
                });
            });
            
            // Обработка поиска
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('.osdr-table tbody tr:not(.collapse)');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm) || searchTerm === '') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
            
            // Обработка фильтров
            const filterBtn = document.getElementById('filterBtn');
            if (filterBtn) {
                filterBtn.addEventListener('click', function() {
                    // Здесь можно добавить логику фильтрации
                    alert('Фильтрация будет реализована в следующей версии');
                });
            }
            
            // Подсветка активного пункта меню
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link-space').forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath === currentPath || 
                    (currentPath.startsWith('/osdr') && linkPath === '/osdr-new')) {
                    link.style.background = 'rgba(93, 63, 211, 0.2)';
                }
            });
            
            // Добавляем обработку ошибок JSON данных
            try {
                // Проверяем, есть ли данные
                const itemCount = {{ count($items ?? []) }};
                if (itemCount === 0) {
                    console.log('Нет данных для отображения');
                }
            } catch (error) {
                console.error('Ошибка при обработке данных:', error);
            }
        });
        
        // Функция для обновления данных
        function refreshData() {
            const refreshBtn = document.querySelector('.btn-osdr .fa-sync-alt');
            if (refreshBtn) {
                refreshBtn.closest('button').disabled = true;
                refreshBtn.classList.add('fa-spin');
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }
    </script>
</body>
</html>