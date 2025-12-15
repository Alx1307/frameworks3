<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NASA OSDR - Space Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/space-styles.css') }}" rel="stylesheet">
    <script src="{{ asset('js/osdr-scripts.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="stars" id="stars">
        <script>
            (function() {
                const starsContainer = document.getElementById('stars');
                if (!starsContainer) return;
                
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
            })();
        </script>
    </div>
    
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
                        <a class="nav-link nav-link-space active" href="/osdr-new" style="background: rgba(93, 63, 211, 0.2);">
                            <i class="fas fa-database me-2"></i>OSDR
                        </a>
                        <a class="nav-link nav-link-space" href="/iss-new">
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
            <h1 class="page-title">NASA OSDR - Открытый репозиторий научных данных</h1>
            <p class="page-subtitle">
                Просмотр данных из NASA Open Science Data Repository
            </p>
            
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
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="https://osdr.nasa.gov" target="_blank" class="btn btn-osdr">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Официальный сайт OSDR
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="filter-panel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-osdr" 
                               placeholder="Поиск по названию или ID..." id="searchInput">
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
            
            <div class="osdr-table-container">
                <div class="table-responsive">
                    <table class="table osdr-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>title</th>
                                <th>rest_url</th>
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
                                <td>
                                    @if(!empty($row['inserted_at']))
                                        <span class="timestamp" title="{{ $row['inserted_at'] }}">
                                            {{ \Carbon\Carbon::parse($row['inserted_at'])->format('Y-m-d H:i') }} UTC
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-outline-osdr btn-sm" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                                        <i class="fas fa-code me-1"></i>JSON
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                                <td colspan="5">
                                    <div class="json-block">
                                        <button class="json-toggle" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}" 
                                                aria-expanded="false">
                                            <span>Ссылка на полный JSON (нажмите чтобы скрыть)</span>
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <pre>{{ json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
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
</body>
</html>