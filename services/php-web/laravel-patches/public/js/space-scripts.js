function createStars() {
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
}

function initScrollAnimations(selector = '.dashboard-card, .cms-card, .stat-card, .feature-card, .info-card') {
    const elements = document.querySelectorAll(selector);
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
            }
        });
    }, { threshold: 0.1 });
    
    elements.forEach((element, index) => {
        element.style.opacity = "0";
        element.style.transform = "translateY(20px)";
        element.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(element);
    });
}

function highlightActiveNav() {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link-space').forEach(link => {
        const linkPath = link.getAttribute('href');
        link.style.background = '';
        
        const isActive = currentPath === linkPath || 
                        (currentPath.includes(linkPath.replace('/', '')) && linkPath !== '/') ||
                        link.getAttribute('style')?.includes('background');
        
        if (isActive) {
            link.style.background = 'rgba(93, 63, 211, 0.2)';
        }
    });
}

function addButtonHoverEffects() {
    document.querySelectorAll('.btn-space, .btn-astro, .btn-osdr, .btn-cms, .dashboard-btn').forEach(button => {
        button.addEventListener('mouseenter', function() {
            if (this.classList.contains('dashboard-btn')) {
                this.style.transform = 'translateY(-5px) scale(1.05)';
            } else {
                this.style.transform = 'translateY(-2px)';
            }
        });
        
        button.addEventListener('mouseleave', function() {
            if (this.classList.contains('dashboard-btn')) {
                this.style.transform = 'translateY(0) scale(1)';
            } else {
                this.style.transform = 'translateY(0)';
            }
        });
    });
}

function handleWelcomeScrollAnimations() {
    const featureCards = document.querySelectorAll('.feature-card');
    const windowHeight = window.innerHeight;
    
    featureCards.forEach((card) => {
        const cardTop = card.getBoundingClientRect().top;
        const cardVisible = 150;
        
        if (cardTop < windowHeight - cardVisible) {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }
    });
}

function initWelcomePage() {
    const featureCards = document.querySelectorAll('.feature-card');
    if (featureCards.length === 0) return;
    
    featureCards.forEach(card => {
        card.style.opacity = "0";
        card.style.transform = "translateY(30px)";
        card.style.transition = "opacity 0.6s ease, transform 0.6s ease, border-color 0.3s ease, box-shadow 0.3s ease";
    });
    
    setTimeout(() => {
        handleWelcomeScrollAnimations();
    }, 300);
    
    window.addEventListener('scroll', handleWelcomeScrollAnimations);
    
    const dashboardBtn = document.querySelector('.dashboard-btn');
    if (dashboardBtn) {
        dashboardBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        dashboardBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
        
        if (dashboardBtn.classList.contains('btn')) {
            dashboardBtn.classList.remove('btn');
        }
    }
}

function detectAndInitPage() {
    const currentPath = window.location.pathname;
    
    createStars();
    highlightActiveNav();
    addButtonHoverEffects();
    
    setTimeout(() => {
        initScrollAnimations();
    }, 300);
    
    if (currentPath === '/' || currentPath === '/welcome') {
        initWelcomePage();
    }
    
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const faLink = document.createElement('link');
        faLink.rel = 'stylesheet';
        faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(faLink);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    detectAndInitPage();
});