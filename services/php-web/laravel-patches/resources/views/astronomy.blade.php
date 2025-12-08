<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Астрономические события - Space Dashboard</title>
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
        
        /* Main content */
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
            text-align: center;
        }
        
        .page-subtitle {
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            margin-bottom: 40px;
            font-size: 1.1rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Карточка событий */
        .events-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .events-header {
            border-bottom: 1px solid rgba(93, 63, 211, 0.3);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .events-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--neon-blue);
            margin: 0;
        }
        
        .form-control-astro {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(93, 63, 211, 0.3);
            color: white;
        }
        
        .form-control-astro:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--neon-blue);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.25);
        }
        
        .btn-astro {
            background: linear-gradient(90deg, var(--space-purple), var(--neon-blue));
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-astro:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
            color: white;
        }
        
        /* Таблица */
        .table-astro {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table-astro thead th {
            background: rgba(93, 63, 211, 0.2);
            color: var(--neon-blue);
            border-bottom: 2px solid rgba(93, 63, 211, 0.3);
            font-weight: 600;
        }
        
        .table-astro tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s ease;
        }
        
        .table-astro tbody tr:hover {
            background: rgba(0, 212, 255, 0.1);
        }
        
        .table-astro tbody td {
            color: rgba(255, 255, 255, 0.9);
            vertical-align: middle;
        }
        
        /* JSON блок */
        .json-panel {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(93, 63, 211, 0.3);
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .json-panel summary {
            color: var(--neon-blue);
            font-weight: 600;
            padding: 10px 15px;
            cursor: pointer;
        }
        
        .json-panel pre {
            background: rgba(0, 0, 0, 0.5);
            color: #90ee90;
            padding: 15px;
            margin: 0;
            font-size: 0.85rem;
            max-height: 400px;
            overflow-y: auto;
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
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;800&display=swap" rel="stylesheet">
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
                        <a class="nav-link nav-link-space" href="/astronomy" style="background: rgba(93, 63, 211, 0.2);">
                            <i class="fas fa-calendar-alt me-2"></i>События
                        </a>
                        <a class="nav-link nav-link-space" href="/cms-admin">
                            <i class="fas fa-cogs me-2"></i>CMS
                        </a>
                        <a class="nav-link nav-link-space" href="/dashboard-minimal">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link nav-link-space active" href="/osdr-new">
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
            <h1 class="page-title">Астрономические события</h1>
            <p class="page-subtitle">
                Получайте информацию о предстоящих затмениях, пролётах спутников, 
                метеорных потоках и других космических явлениях. Данные предоставляются 
                через AstronomyAPI.
            </p>
            
            <!-- Карточка событий -->
            <div class="events-card">
                <div class="events-header">
                    <h5 class="events-title">Фильтр событий</h5>
                    <p class="text-muted mb-0">Укажите координаты и период для получения событий</p>
                </div>
                
                <form id="astroForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-light">Широта (lat)</label>
                        <input type="number" step="0.0001" class="form-control form-control-astro" 
                               name="lat" value="55.7558" placeholder="55.7558">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-light">Долгота (lon)</label>
                        <input type="number" step="0.0001" class="form-control form-control-astro" 
                               name="lon" value="37.6176" placeholder="37.6176">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-light">Период (дней)</label>
                        <input type="number" min="1" max="30" class="form-control form-control-astro" 
                               name="days" value="7" placeholder="7">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-astro w-100" type="submit">
                            <i class="fas fa-search me-2"></i>Показать события
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Таблица событий -->
            <div class="events-card">
                <div class="events-header">
                    <h5 class="events-title">Результаты</h5>
                    <p class="text-muted mb-0">Найдено событий: <span id="eventsCount">0</span></p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-astro">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Небесное тело</th>
                                <th>Тип события</th>
                                <th>Время (UTC)</th>
                                <th>Детали</th>
                            </tr>
                        </thead>
                        <tbody id="astroBody">
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-search me-2"></i>
                                        Укажите параметры и нажмите "Показать события"
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- JSON блок -->
                <div class="json-panel">
                    <details>
                        <summary>Полный JSON ответ</summary>
                        <pre id="astroRaw" class="bg-dark rounded p-3">{"message": "Данные появятся после запроса"}</pre>
                    </details>
                </div>
            </div>
            
            <!-- Информация о данных -->
            <div class="alert alert-info bg-dark border-info">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-info fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="alert-heading text-info">Информация о данных</h6>
                        <p class="mb-0">
                            Данные предоставляются через AstronomyAPI. Включают информацию о: 
                            затмениях (солнечных и лунных), пролётах МКС и других спутников, 
                            метеорных потоках, положениях планет и других астрономических событиях.
                        </p>
                    </div>
                </div>
            </div>
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
                    &copy; 2025 Astronomy Dashboard. Используются данные AstronomyAPI и открытые источники.
                </div>
            </div>
        </div>
    </footer>

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
        
        // Функции для работы с событиями
        function normalize(node){
            const name = node.name || node.body || node.object || node.target || '';
            const type = node.type || node.event_type || node.category || node.kind || '';
            const when = node.time || node.date || node.occursAt || node.peak || node.instant || '';
            const extra = node.magnitude || node.mag || node.altitude || node.note || '';
            return {name, type, when, extra};
        }

        function collect(root){
            const rows = [];
            (function dfs(x){
                if (!x || typeof x !== 'object') return;
                if (Array.isArray(x)) { x.forEach(dfs); return; }
                if ((x.type || x.event_type || x.category) && (x.name || x.body || x.object || x.target)) {
                    rows.push(normalize(x));
                }
                Object.values(x).forEach(dfs);
            })(root);
            return rows;
        }

        async function loadEvents(q){
            const body = document.getElementById('astroBody');
            const raw = document.getElementById('astroRaw');
            const countElement = document.getElementById('eventsCount');
            
            // Показываем загрузку
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="loading-pulse"></div>
                        <span class="text-info">Загрузка событий...</span>
                    </td>
                </tr>
            `;
            
            try {
                const url = '/api/astro/events?' + new URLSearchParams(q).toString();
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                raw.textContent = JSON.stringify(data, null, 2);
                
                const rows = collect(data);
                
                if (!rows.length) {
                    body.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Событий не найдено для указанных параметров
                                </div>
                            </td>
                        </tr>
                    `;
                    countElement.textContent = '0';
                    return;
                }
                
                // Обновляем счетчик
                countElement.textContent = rows.length;
                
                // Форматируем строки таблицы
                body.innerHTML = rows.slice(0, 200).map((row, index) => `
                    <tr>
                        <td><span class="badge bg-primary">${index + 1}</span></td>
                        <td>
                            <strong>${row.name || 'Неизвестно'}</strong>
                            ${row.name ? getBodyIcon(row.name) : ''}
                        </td>
                        <td>
                            <span class="badge ${getEventTypeClass(row.type)}">
                                ${row.type || 'Не указан'}
                            </span>
                        </td>
                        <td>
                            <code class="text-info">${formatDateTime(row.when) || '—'}</code>
                        </td>
                        <td>
                            <small class="text-muted">${row.extra || '—'}</small>
                        </td>
                    </tr>
                `).join('');
                
            } catch(error) {
                console.error('Ошибка загрузки:', error);
                body.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Ошибка загрузки: ${error.message}
                            </div>
                        </td>
                    </tr>
                `;
                countElement.textContent = '0';
            }
        }
        
        // Вспомогательные функции
        function getBodyIcon(bodyName) {
            const bodyLower = bodyName.toLowerCase();
            if (bodyLower.includes('sun') || bodyLower.includes('солн')) return '<i class="fas fa-sun ms-2 text-warning"></i>';
            if (bodyLower.includes('moon') || bodyLower.includes('лун')) return '<i class="fas fa-moon ms-2 text-light"></i>';
            if (bodyLower.includes('mars') || bodyLower.includes('марс')) return '<i class="fas fa-globe-americas ms-2 text-danger"></i>';
            if (bodyLower.includes('jupiter') || bodyLower.includes('юпитер')) return '<i class="fas fa-globe-americas ms-2 text-warning"></i>';
            if (bodyLower.includes('satellite') || bodyLower.includes('спутник')) return '<i class="fas fa-satellite ms-2 text-info"></i>';
            return '<i class="fas fa-star ms-2 text-light"></i>';
        }
        
        function getEventTypeClass(type) {
            const typeLower = (type || '').toLowerCase();
            if (typeLower.includes('eclipse') || typeLower.includes('затмен')) return 'bg-danger';
            if (typeLower.includes('transit') || typeLower.includes('транзит')) return 'bg-warning text-dark';
            if (typeLower.includes('meteor') || typeLower.includes('метеор')) return 'bg-success';
            if (typeLower.includes('flyby') || typeLower.includes('пролёт')) return 'bg-info';
            return 'bg-secondary';
        }
        
        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return '';
            try {
                const date = new Date(dateTimeStr);
                return date.toLocaleString('ru-RU', {
                    timeZone: 'UTC',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }) + ' UTC';
            } catch {
                return dateTimeStr;
            }
        }
        
        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            
            const form = document.getElementById('astroForm');
            
            // Загружаем события по умолчанию при загрузке страницы
            const defaultParams = {
                lat: form.lat.value,
                lon: form.lon.value,
                days: form.days.value
            };
            loadEvents(defaultParams);
            
            // Обработка отправки формы
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(form);
                const params = Object.fromEntries(formData.entries());
                loadEvents(params);
            });
            
            // Анимация для строк таблицы при появлении
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                    }
                });
            }, { threshold: 0.1 });
            
            // Наблюдаем за строками таблицы
            setTimeout(() => {
                document.querySelectorAll('#astroBody tr').forEach(row => {
                    row.style.opacity = "0";
                    row.style.transform = "translateY(10px)";
                    row.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                    observer.observe(row);
                });
            }, 500);
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>