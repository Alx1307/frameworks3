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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="planet"></div>
    
    <main class="main-content">
        <div class="container">
            <h1 class="page-title">NASA OSDR - Открытый репозиторий научных данных</h1>
            
            <div class="info-panel mb-4">
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
            
            {{-- Панель фильтров --}}
            <div class="filter-panel mb-4" id="filterControls">
                <form method="GET" action="{{ url()->current() }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-osdr" 
                                   name="search" 
                                   id="searchFilter"
                                   value="{{ request('search') }}"
                                   placeholder="Поиск по названию...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-osdr" name="sort_column" id="sortColumn">
                                <option value="title" {{ request('sort_column') == 'title' ? 'selected' : '' }}>Сортировка по названию</option>
                                <option value="inserted_at" {{ request('sort_column') == 'inserted_at' ? 'selected' : '' }}>Сортировка по дате добавления</option>
                                <option value="id" {{ request('sort_column') == 'id' ? 'selected' : '' }}>Сортировка по ID записи</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-osdr" name="sort_direction" id="sortDirection">
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>По убыванию</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-osdr w-100" id="applyFilters">
                                <i class="fas fa-filter me-1"></i>Применить
                            </button>
                        </div>
                    </div>
                    {{-- Скрытые поля --}}
                    <input type="hidden" name="per_page" value="{{ request('per_page', 50) }}">
                    <input type="hidden" name="page" value="1">
                </form>
            </div>
            
            {{-- Таблица данных --}}
            <div class="osdr-table-container">
                <div class="table-responsive">
                    <table class="table osdr-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>title</th>
                                <th>REST_URL</th>
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
                                <td style="max-width: 300px;">
                                    <div class="text-truncate" title="{{ $row['title'] ?? '' }}">
                                        {{ $row['title'] ?? '—' }}
                                    </div>
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
                                            {{ \Carbon\Carbon::parse($row['inserted_at'])->format('Y-m-d H:i') }}
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
                                <td colspan="8">
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
                                <td colspan="8">
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
            
            {{-- Пагинация и управление --}}
            @if($items->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted" style="color: #aaa;">
                    Показано {{ $items->firstItem() }} - {{ $items->lastItem() }} из {{ $items->total() }}
                </div>
                
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Первая страница --}}
                        <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $items->url(1) }}@if(request()->has('search'))&search={{ request('search') }}@endif @if(request()->has('has_url'))&has_url=1 @endif @if(request()->has('sort_column'))&sort_column={{ request('sort_column') }}@endif @if(request()->has('sort_direction'))&sort_direction={{ request('sort_direction') }}@endif" 
                               aria-label="Первая">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        
                        {{-- Предыдущая страница --}}
                        <li class="page-item {{ $items->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $items->previousPageUrl() }}" aria-label="Предыдущая">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        {{-- Номера страниц --}}
                        @php
                            $start = max(1, $items->currentPage() - 2);
                            $end = min($items->lastPage(), $items->currentPage() + 2);
                        @endphp
                        @for($page = $start; $page <= $end; $page++)
                            <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                                <a class="page-link" 
                                   href="{{ $items->url($page) }}@if(request()->has('search'))&search={{ request('search') }}@endif @if(request()->has('has_url'))&has_url=1 @endif @if(request()->has('sort_column'))&sort_column={{ request('sort_column') }}@endif @if(request()->has('sort_direction'))&sort_direction={{ request('sort_direction') }}@endif">
                                    {{ $page }}
                                </a>
                            </li>
                        @endfor
                        
                        {{-- Следующая страница --}}
                        <li class="page-item {{ !$items->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $items->nextPageUrl() }}" aria-label="Следующая">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        
                        {{-- Последняя страница --}}
                        <li class="page-item {{ !$items->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" 
                               href="{{ $items->url($items->lastPage()) }}@if(request()->has('search'))&search={{ request('search') }}@endif @if(request()->has('has_url'))&has_url=1 @endif @if(request()->has('sort_column'))&sort_column={{ request('sort_column') }}@endif @if(request()->has('sort_direction'))&sort_direction={{ request('sort_direction') }}@endif" 
                               aria-label="Последняя">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                {{-- Выбор количества на странице --}}
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false" style="border-color: #5d3fd3; color: #fff;">
                        {{ request('per_page', 50) }} на странице
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}">25</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}">50</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 100, 'page' => 1]) }}">100</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 200, 'page' => 1]) }}">200</a></li>
                    </ul>
                </div>
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
    
    <script>
    // Авто-отправка формы при изменении фильтров
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchFilter');
        const sortColumn = document.getElementById('sortColumn');
        const sortDirection = document.getElementById('sortDirection');
        const filterHasUrl = document.getElementById('filterHasUrl');
        const form = document.getElementById('filterForm');
        
        let searchTimeout;
        
        // Поиск с задержкой
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.querySelector('input[name="page"]').value = 1;
                    form.submit();
                }, 500);
            });
        }
        
        // Остальные фильтры
        if (sortColumn) {
            sortColumn.addEventListener('change', function() {
                document.querySelector('input[name="page"]').value = 1;
                form.submit();
            });
        }
        
        if (sortDirection) {
            sortDirection.addEventListener('change', function() {
                document.querySelector('input[name="page"]').value = 1;
                form.submit();
            });
        }
        
        if (filterHasUrl) {
            filterHasUrl.addEventListener('change', function() {
                document.querySelector('input[name="page"]').value = 1;
                form.submit();
            });
        }
        
        // Кнопка сброса фильтров (добавьте её в HTML если нужно)
        const resetBtn = document.getElementById('resetFilters');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ url()->current() }}";
            });
        }
    });
    </script>
    
    <style>
    /* Дополнительные стили для таблицы */
    .table th {
        border-top: none;
        font-weight: 600;
        color: #b3b3cc;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(93, 63, 211, 0.1);
    }
    
    #filterControls .form-control:focus,
    #filterControls .form-select:focus {
        border-color: #5d3fd3;
        box-shadow: 0 0 0 0.25rem rgba(93, 63, 211, 0.25);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #5d3fd3;
        border-color: #5d3fd3;
    }
    
    .pagination .page-link {
        color: #5d3fd3;
        background-color: rgba(0, 0, 0, 0.3);
        border-color: rgba(93, 63, 211, 0.3);
    }
    
    .pagination .page-link:hover {
        color: #fff;
        background-color: rgba(93, 63, 211, 0.5);
        border-color: #5d3fd3;
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid rgba(93, 63, 211, 0.2);
    }
    
    .stat-icon {
        font-size: 24px;
        color: #5d3fd3;
    }
    
    .stat-label {
        font-size: 12px;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #fff;
        font-family: 'Orbitron', sans-serif;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    </style>
</body>
</html>