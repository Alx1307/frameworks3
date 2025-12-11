<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Астрономические события - Space Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/space-styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <a class="nav-link nav-link-space" href="/astronomy" style="background: rgba(93, 63, 211, 0.2);">
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
            <h1 class="page-title">Астрономические события</h1>
            <p class="page-subtitle">
                Получайте информацию о предстоящих затмениях, пролётах спутников, 
                метеорных потоках и других космических явлениях. Данные предоставляются 
                через AstronomyAPI и альтернативные API.
            </p>
            
            <div class="events-card">
                <div class="events-header">
                    <h5 class="events-title">Фильтр событий</h5>
                    <p class="text-info mb-0">Укажите координаты и период для получения событий</p>
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
            
            <div class="events-card">
                <div class="events-header">
                    <h5 class="events-title">Результаты</h5>
                    <p class="text-info mb-0">Найдено событий: <span id="eventsCount">0</span></p>
                </div>
                
                <div class="astro-table-container">
                    <div class="table-responsive">
                        <table class="table astro-table">
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
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-info">
                                            <i class="fas fa-search me-2"></i>
                                            Укажите параметры и нажмите "Показать события"
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="json-panel">
                    <details>
                        <summary>Полный JSON ответ</summary>
                        <pre id="astroRaw" class="bg-dark rounded p-3">{"message": "Данные появятся после запроса"}</pre>
                    </details>
                </div>
            </div>
            
            <div class="alert alert-info bg-dark border-info">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-info fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="alert-heading text-info">Информация о данных</h6>
                        <p class="mb-0">
                            Данные предоставляются через AstronomyAPI и Sunrise & Sunset Times for Any Location. Включают информацию о: 
                            затмениях (солнечных и лунных), пролётах МКС и других спутников, 
                            метеорных потоках, положениях планет и других астрономических событиях.
                        </p>
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
                    &copy; 2025 Astronomy Dashboard. Используются данные AstronomyAPI и открытые источники.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/astronomy-scripts.js') }}"></script>
</body>
</html>