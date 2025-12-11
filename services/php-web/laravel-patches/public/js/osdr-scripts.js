function enhanceStars() {
    const starsContainer = document.getElementById('stars');
    if (!starsContainer || starsContainer.children.length > 0) return;
    
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

function initializeTableAnimations() {
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
}

function setupJsonToggles() {
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
}

function setupSearch() {
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
}

function setupFilterButton() {
    const filterBtn = document.getElementById('filterBtn');
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            alert('Фильтрация будет реализована в следующей версии');
        });
    }
}

function highlightActiveNavLink() {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link-space').forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath === currentPath || 
            (currentPath.startsWith('/osdr') && linkPath === '/osdr-new')) {
            link.style.background = 'rgba(93, 63, 211, 0.2)';
        }
    });
}

function animatePageElements() {
    document.querySelectorAll('.info-panel, .filter-panel, .osdr-table-container').forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });
}

function initializeOSDRPage() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enhanceStars);
    } else {
        enhanceStars();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        enhanceStars();
        initializeTableAnimations();
        setupJsonToggles();
        setupSearch();
        setupFilterButton();
        highlightActiveNavLink();
        animatePageElements();
    });
}

initializeOSDRPage();