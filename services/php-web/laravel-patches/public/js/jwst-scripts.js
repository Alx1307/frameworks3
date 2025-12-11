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

async function loadJWSTFeed(qs) {
    const track = document.getElementById('jwstTrack');
    const info = document.getElementById('jwstInfo');
    
    track.innerHTML = '<div class="p-5 text-center"><div class="loading-pulse"></div><div class="text-info mt-2">Загрузка изображений JWST...</div></div>';
    info.textContent = '';
    
    try {
        const url = '/api/jwst/feed?' + new URLSearchParams(qs).toString();
        const response = await fetch(url);
        const data = await response.json();
        
        track.innerHTML = '';
        
        if (data.items && data.items.length > 0) {
            data.items.forEach(item => {
                const figure = document.createElement('figure');
                figure.className = 'jwst-item m-0';
                figure.innerHTML = `
                    <a href="${item.link || item.url}" target="_blank" rel="noreferrer">
                        <img loading="lazy" src="${item.url}" alt="JWST изображение" onerror="this.src='https://via.placeholder.com/180x180/0a192f/00d4ff?text=JWST'">
                    </a>
                    <figcaption class="jwst-cap">${(item.caption || '').replaceAll('<', '&lt;').substring(0, 50)}${(item.caption || '').length > 50 ? '...' : ''}</figcaption>`;
                track.appendChild(figure);
            });
            
            info.textContent = `Источник: ${data.source} · Показано ${data.items.length} изображений`;
        } else {
            track.innerHTML = '<div class="p-5 text-center text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Изображения не найдены</div>';
            info.textContent = 'Нет данных для отображения';
        }
    } catch (error) {
        console.error('Ошибка загрузки JWST:', error);
        track.innerHTML = '<div class="p-5 text-center text-danger"><i class="fas fa-exclamation-circle me-2"></i>Ошибка загрузки галереи</div>';
    }
}

function initJWSTPage() {
    createStars();
    
    const track = document.getElementById('jwstTrack');
    const info = document.getElementById('jwstInfo');
    const form = document.getElementById('jwstFilter');
    const srcSel = document.getElementById('srcSel');
    const sfxInp = document.getElementById('suffixInp');
    const progInp = document.getElementById('progInp');
    
    function toggleInputs() {
        sfxInp.style.display = (srcSel.value === 'suffix') ? '' : 'none';
        progInp.style.display = (srcSel.value === 'program') ? '' : 'none';
    }
    
    srcSel.addEventListener('change', toggleInputs);
    toggleInputs();
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        const query = Object.fromEntries(formData.entries());
        loadJWSTFeed(query);
    });
    
    document.querySelector('.jwst-prev').addEventListener('click', () => track.scrollBy({
        left: -600,
        behavior: 'smooth'
    }));
    
    document.querySelector('.jwst-next').addEventListener('click', () => track.scrollBy({
        left: 600,
        behavior: 'smooth'
    }));
    
    loadJWSTFeed({
        source: 'jpg',
        perPage: 24
    });
    
    function handleScrollAnimations() {
        const cards = document.querySelectorAll('.dashboard-card, .info-card');
        const windowHeight = window.innerHeight;
        
        cards.forEach((card, index) => {
            const cardTop = card.getBoundingClientRect().top;
            const cardVisible = 100;
            
            if (cardTop < windowHeight - cardVisible) {
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }
        });
    }
    
    const cards = document.querySelectorAll('.dashboard-card, .info-card');
    cards.forEach(card => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    });
    
    window.addEventListener('scroll', handleScrollAnimations);
    setTimeout(() => {
        handleScrollAnimations();
    }, 300);
}

document.addEventListener('DOMContentLoaded', initJWSTPage);