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

function normalize(node) {
    const name = node.name || node.body || node.object || node.target || '';
    const type = node.type || node.event_type || node.category || node.kind || '';
    const when = node.time || node.date || node.occursAt || node.peak || node.instant || '';
    const extra = node.details || node.description || node.note || 
                node.magnitude || node.mag || node.altitude || 
                node.phase || node.rate || node.duration || '';
    return { name, type, when, extra };
}

function collect(root) {
    const rows = [];
    (function dfs(x) {
        if (!x || typeof x !== 'object') return;
        if (Array.isArray(x)) { 
            x.forEach(dfs); 
            return; 
        }
        if ((x.type || x.event_type || x.category) && (x.name || x.body || x.object || x.target)) {
            rows.push(normalize(x));
        }
        Object.values(x).forEach(dfs);
    })(root);
    return rows;
}

async function loadEvents(q) {
    const body = document.getElementById('astroBody');
    const raw = document.getElementById('astroRaw');
    const countElement = document.getElementById('eventsCount');
    
    body.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-5">
                <div class="loading-pulse"></div>
                <span class="text-info">Загрузка событий...</span>
            </td>
        </tr>
    `;
    
    try {
        const url = '/api/astronomy/events?' + new URLSearchParams(q).toString();
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
                    <td colspan="5" class="text-center py-5">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h4>Событий не найдено</h4>
                            <p>Для указанных параметров не найдено астрономических событий.</p>
                            <button class="btn btn-astro mt-2" onclick="document.getElementById('astroForm').dispatchEvent(new Event('submit'))">
                                <i class="fas fa-sync-alt me-2"></i>Попробовать снова
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            countElement.textContent = '0';
            return;
        }
        
        countElement.textContent = rows.length;
        
        body.innerHTML = rows.slice(0, 200).map((row, index) => `
            <tr>
                <td><span class="badge bg-dark" style="font-family: 'Orbitron';">${index + 1}</span></td>
                <td>
                    <strong>${row.name || 'Неизвестно'}</strong>
                    ${getBodyIcon(row.name)}
                </td>
                <td>
                    <span class="event-badge ${getEventTypeClass(row.type)}">
                        ${row.type || 'Не указан'}
                    </span>
                </td>
                <td>
                    <code class="text-info">${formatDateTime(row.when) || '—'}</code>
                </td>
                <td>
                    <small class="text-info">${row.extra || '—'}</small>
                </td>
            </tr>
        `).join('');
        
        const tableRows = document.querySelectorAll('#astroBody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = "0";
            row.style.transform = "translateY(20px)";
            row.style.transition = `opacity 0.3s ease ${index * 0.05}s, transform 0.3s ease ${index * 0.05}s`;
            
            setTimeout(() => {
                row.style.opacity = "1";
                row.style.transform = "translateY(0)";
            }, 50);
        });
        
    } catch (error) {
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

function getBodyIcon(bodyName) {
    if (!bodyName) return '';
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
    if (typeLower.includes('eclipse') || typeLower.includes('затмен')) return 'badge-eclipse';
    if (typeLower.includes('sun_event') || typeLower.includes('sunrise') || typeLower.includes('sunset')) 
        return 'badge-sun-event';
    if (typeLower.includes('sun_position') || typeLower.includes('solar_noon')) 
        return 'badge-sun-position';
    if (typeLower.includes('moon') || typeLower.includes('лун') || typeLower.includes('phase') || typeLower.includes('фаза')) 
        return 'badge-moon-phase';
    if (typeLower.includes('transit') || typeLower.includes('транзит')) return 'badge-transit';
    if (typeLower.includes('meteor') || typeLower.includes('метеор')) return 'badge-meteor';
    if (typeLower.includes('flyby') || typeLower.includes('пролёт')) return 'badge-flyby';
    if (typeLower.includes('planet') || typeLower.includes('планет')) return 'badge-planetary';
    return 'badge-secondary';
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

function initAstronomyPage() {
    createStars();
    
    const form = document.getElementById('astroForm');
    
    const defaultParams = {
        lat: form.lat.value,
        lon: form.lon.value,
        days: form.days.value
    };
    loadEvents(defaultParams);
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        const params = Object.fromEntries(formData.entries());
        loadEvents(params);
    });
    
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link-space').forEach(link => {
        if (currentPath.includes('astronomy')) {
            link.style.background = '';
        }
        if (link.getAttribute('href') === '/astronomy') {
            link.style.background = 'rgba(93, 63, 211, 0.2)';
        }
    });
}

document.addEventListener('DOMContentLoaded', initAstronomyPage);