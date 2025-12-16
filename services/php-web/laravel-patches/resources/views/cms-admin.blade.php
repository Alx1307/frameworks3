<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление CMS - Space Dashboard</title>
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
                        <a class="nav-link nav-link-space" href="/astronomy">
                            <i class="fas fa-star me-2"></i>События
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
            
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('cms.create') }}" class="btn btn-cms btn-cms-primary">
                    <i class="fas fa-plus-circle me-2"></i>Создать новый блок
                </a>
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
                                        <i class="fas fa-file-alt me-2"></i>{{ data_get($block, 'title', 'Без названия') }}
                                    </h4>
                                    <div class="cms-slug">
                                        <i class="fas fa-hashtag me-1"></i>{{ data_get($block, 'slug', '') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="cms-content">
                                <div class="cms-content-preview">
                                    @php
                                        $content = data_get($block, 'content', '');
                                        $preview = strip_tags($content);
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
                                        Обновлен: {{ date('d.m.Y H:i', strtotime(data_get($block, 'updated_at', now()))) }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="cms-actions">
                                <a href="/page/{{ data_get($block, 'slug', '') }}" class="btn btn-cms btn-cms-view" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Просмотр страницы
                                </a>
                                <a href="{{ route('cms.edit', data_get($block, 'id')) }}" class="btn btn-cms btn-cms-edit">
                                    <i class="fas fa-edit me-2"></i>Редактировать
                                </a>
                                
                                <form action="{{ route('cms.destroy', data_get($block, 'id')) }}" method="POST" class="d-inline" onsubmit="return confirm('Удалить этот блок?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-cms btn-cms-delete">
                                        <i class="fas fa-trash me-2"></i>Удалить
                                    </button>
                                </form>
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
                            <a href="/cms-admin/create" class="btn btn-cms btn-cms-view mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Создать первый блок
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
    
    <footer class="space-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">Astronomy Dashboard</div>
                <div class="footer-tagline">
                    <i class="fas fa-rocket me-2"></i>
                    Исследуйте космос вместе с нами
                </div>
                <div class="footer-copyright">
                    &copy; 2025 Astronomy Dashboard. Используются данные NASA и открытые API.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/cms-scripts.js') }}"></script>
</body>
</html>