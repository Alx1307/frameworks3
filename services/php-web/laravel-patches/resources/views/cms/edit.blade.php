<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if(isset($block) && $block)Редактирование блока@elseСоздание блока@endif - Space Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/space-styles.css') }}" rel="stylesheet">
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
        
        .main-content {
            padding: 120px 0 80px;
            position: relative;
            z-index: 1;
        }
        
        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 30px;
            background: linear-gradient(90deg, #00d4ff, #5d3fd3);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
            text-align: left;
        }
        
        .cms-card {
            max-width: 1000px;
            margin: 0 auto;
            padding: 50px;
            background: rgba(20, 20, 40, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(93, 63, 211, 0.4);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            animation: fadeIn 0.8s ease-out;
            min-height: 600px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cms-card-header {
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 2px solid rgba(93, 63, 211, 0.5);
            position: relative;
        }
        
        .cms-card-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--neon-blue);
            text-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
            margin: 0;
            text-align: left;
        }
        
        .cms-slug {
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.3);
            color: #90ee90;
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid rgba(144, 238, 144, 0.3);
            margin-top: 10px;
            display: inline-block;
        }
        
        .form-label {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            text-shadow: 0 0 8px rgba(0, 212, 255, 0.2);
        }
        
        .form-label i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .cms-input {
            background: rgba(10, 25, 47, 0.6) !important;
            border: 2px solid rgba(93, 63, 211, 0.3) !important;
            color: white !important;
            border-radius: 12px !important;
            padding: 15px !important;
            font-size: 1.1rem !important;
            transition: all 0.3s ease !important;
            font-family: 'Segoe UI', sans-serif;
        }
        
        .cms-input:focus {
            background: rgba(10, 25, 47, 0.8) !important;
            border-color: var(--neon-blue) !important;
            box-shadow: 0 0 0 0.3rem rgba(0, 212, 255, 0.15) !important;
            color: white !important;
            transform: translateY(-2px);
        }
        
        .cms-input::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        
        .slug-hint {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 0.95rem;
            margin-top: 8px;
            padding: 8px 12px;
            background: rgba(93, 63, 211, 0.1);
            border-radius: 6px;
            border-left: 3px solid var(--neon-blue);
        }
        
        .slug-hint code {
            background: rgba(0, 0, 0, 0.3);
            color: #90ee90;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        #content {
            min-height: 300px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            font-size: 1.1rem;
        }
        
        .btn-cms {
            border: 2px solid rgba(93, 63, 211, 0.5);
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .btn-cms-secondary {
            background: rgba(108, 117, 125, 0.3);
            border-color: rgba(108, 117, 125, 0.5);
        }
        
        .btn-cms-primary {
            background: linear-gradient(135deg, rgba(93, 63, 211, 0.4), rgba(0, 212, 255, 0.3));
            border-color: var(--neon-blue);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.2);
        }
        
        .btn-cms:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(93, 63, 211, 0.3);
            text-decoration: none;
        }
        
        .btn-cms-secondary:hover {
            background: rgba(108, 117, 125, 0.5);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .btn-cms-primary:hover {
            background: linear-gradient(135deg, rgba(93, 63, 211, 0.6), rgba(0, 212, 255, 0.4));
            border-color: white;
            box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
        }
        
        .btn-cms i {
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        
        .btn-cms:hover i {
            transform: scale(1.1);
        }
        
        .planet {
            position: fixed;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #3a1c71, #5d3fd3, #0a192f);
            right: 10%;
            top: 20%;
            z-index: 0;
            opacity: 0.3;
            animation: float 20s infinite ease-in-out;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        .space-footer {
            background: rgba(2, 12, 27, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(93, 63, 211, 0.3);
            padding: 30px 0 20px;
            position: relative;
            z-index: 2;
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
        
        .footer-copyright {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .alert {
            border-radius: 12px;
            border: 2px solid transparent;
            backdrop-filter: blur(5px);
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.4);
            color: #d4edda;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.4);
            color: #f8d7da;
        }
        
        .text-danger {
            color: #ff6b6b !important;
            font-weight: 500;
            margin-top: 5px;
            padding-left: 5px;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.2rem;
                text-align: center;
            }
            
            .cms-card {
                padding: 30px;
                margin: 0 15px;
                min-height: auto;
            }
            
            .cms-card-title {
                font-size: 1.8rem;
                text-align: center;
            }
            
            .planet {
                display: none;
            }
            
            .btn-cms {
                padding: 10px 20px;
                font-size: 1rem;
                width: 100%;
                margin-bottom: 10px;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 15px;
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
                        <a class="nav-link nav-link-space" href="/cms-admin">
                            <i class="fas fa-cogs me-2"></i>CMS
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="planet"></div>
    
    <main class="main-content">
        <div class="container">
            <h1 class="page-title mb-4">
                @if(isset($block) && $block)
                    Редактирование блока
                @else
                    Создание блока
                @endif
            </h1>
            
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="cms-card">
                        @if(isset($block) && $block)
                            <div class="cms-card-header">
                                <h4 class="cms-card-title">
                                    {{ $block['title'] ?? 'Без названия' }}
                                </h4>
                                <div class="cms-slug">
                                    <i class="fas fa-hashtag me-1"></i>{{ $block['slug'] ?? '' }}
                                </div>
                            </div>
                        @else
                            <div class="cms-card-header">
                                <h4 class="cms-card-title">
                                    <i class="fas fa-plus-circle me-2"></i>Новый блок CMS
                                </h4>
                            </div>
                        @endif
                            
                        <div class="cms-content p-3">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if(isset($block) && $block)
                                <form action="{{ route('cms.update', $block['id']) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                            @else
                                <form action="{{ route('cms.store') }}" method="POST">
                                    @csrf
                            @endif
                                
                                <div class="mb-4">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading"></i>Заголовок блока
                                    </label>
                                    <input type="text" 
                                           class="form-control cms-input" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $block['title'] ?? '') }}" 
                                           placeholder="Введите заголовок блока"
                                           required>
                                    @error('title')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="slug" class="form-label">
                                        <i class="fas fa-hashtag"></i>Slug (идентификатор)
                                    </label>
                                    <input type="text" 
                                           class="form-control cms-input" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug', $block['slug'] ?? '') }}" 
                                           placeholder="my-block-1"
                                           required
                                           pattern="[a-z0-9-_]+" 
                                           title="Только строчные латинские буквы, цифры, дефисы и подчеркивания">
                                    <div class="slug-hint">
                                        <strong>Формат:</strong> Только строчные латинские буквы, цифры, дефисы и подчеркивания
                                        <br><small>Пример: <code>my-block-1</code>, <code>about_us</code>, <code>home-page</code></small>
                                    </div>
                                    @error('slug')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="content" class="form-label">
                                        <i class="fas fa-file-alt"></i>Содержимое
                                    </label>
                                    <textarea class="form-control cms-input" 
                                              id="content" 
                                              name="content" 
                                              rows="10" 
                                              placeholder="Введите HTML-контент блока..."
                                              required>{{ old('content', $block['content'] ?? '') }}</textarea>
                                    <div class="slug-hint mt-2">
                                        <strong>Подсказка:</strong> Можно использовать HTML теги и CSS стили. Пример:
                                        <code>&lt;div class="space-card"&gt;...&lt;/div&gt;</code>
                                    </div>
                                    @error('content')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="/cms-admin" class="btn btn-cms btn-cms-secondary">
                                            <i class="fas fa-arrow-left"></i>Назад к списку
                                        </a>
                                    </div>
                                    
                                    <div>
                                        @if(isset($block) && $block)
                                            <button type="submit" class="btn btn-cms btn-cms-primary">
                                                <i class="fas fa-save"></i>Сохранить изменения
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-cms btn-cms-primary">
                                                <i class="fas fa-plus-circle"></i>Создать блок
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
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
                <div class="footer-copyright">
                    &copy; 2025 Astronomy Dashboard
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function createStars() {
            const starsContainer = document.getElementById('stars');
            const starCount = 200;
            
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                
                const size = Math.random() * 3 + 1;
                const brightness = Math.random() * 0.8 + 0.2;
                
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                star.style.opacity = brightness;
                star.style.animationDelay = `${Math.random() * 5}s`;
                star.style.animationDuration = `${Math.random() * 3 + 2}s`;
                
                starsContainer.appendChild(star);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            
            const textarea = document.getElementById('content');
            if (textarea) {
                textarea.rows = 10;
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
                
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            }
            
            const slugInput = document.getElementById('slug');
            if (slugInput) {
                slugInput.addEventListener('input', function() {
                    this.value = this.value.toLowerCase().replace(/[^a-z0-9-_]/g, '');
                });
            }
            
            const form = document.querySelector('.cms-card');
            if (form) {
                form.style.opacity = "0";
                form.style.transform = "translateY(30px)";
                form.style.transition = "opacity 0.8s ease, transform 0.8s ease";
                
                setTimeout(() => {
                    form.style.opacity = "1";
                    form.style.transform = "translateY(0)";
                }, 300);
            }
            
            const titleInput = document.getElementById('title');
            if (titleInput && slugInput && !slugInput.value) {
                titleInput.addEventListener('blur', function() {
                    if (!slugInput.value) {
                        const title = this.value;
                        const slug = title.toLowerCase()
                            .replace(/[^a-zа-яё0-9\s]/g, '')
                            .replace(/[а-яё]/g, function(ch) {
                                const translit = {
                                    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
                                    'е': 'e', 'ё': 'yo', 'ж': 'zh', 'з': 'z', 'и': 'i',
                                    'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
                                    'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't',
                                    'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch',
                                    'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '',
                                    'э': 'e', 'ю': 'yu', 'я': 'ya'
                                };
                                return translit[ch] || '';
                            })
                            .replace(/\s+/g, '-')
                            .replace(/[^a-z0-9-_]/g, '')
                            .replace(/-+/g, '-')
                            .replace(/^-|-$/g, '');
                        
                        if (slug) {
                            slugInput.value = slug;
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>