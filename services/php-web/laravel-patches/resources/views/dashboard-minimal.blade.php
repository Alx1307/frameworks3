<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWST Dashboard - Space Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/space-styles.css') }}" rel="stylesheet">
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
                        <div class="info-value">4</div>
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
    <script src="{{ asset('js/jwst-scripts.js') }}"></script>
</body>
</html>