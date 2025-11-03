// Takvim sayfası JavaScript

let allEvents = [];
let currentFilter = '';

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadAllEvents();
});

// Tüm etkinlikleri yükle
async function loadAllEvents() {
    const listContainer = document.getElementById('events-list');
    
    try {
        listContainer.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>';
        
        allEvents = await api.getEvents(currentFilter);
        displayEvents(allEvents);
        
    } catch (error) {
        console.error('Hata:', error);
        listContainer.innerHTML = '<p class="text-center text-danger">Etkinlikler yüklenirken hata oluştu.</p>';
    }
}

// Etkinlikleri göster
function displayEvents(events) {
    const listContainer = document.getElementById('events-list');
    
    if (events.length === 0) {
        listContainer.innerHTML = '<p class="text-center">Etkinlik bulunmamaktadır.</p>';
        return;
    }
    
    // Tarihe göre sırala
    events.sort((a, b) => new Date(a.baslangic_tarihi) - new Date(b.baslangic_tarihi));
    
    listContainer.innerHTML = '';
    
    events.forEach(event => {
        const eventItem = createEventListItem(event);
        listContainer.appendChild(eventItem);
    });
}

// Liste öğesi oluştur
function createEventListItem(event) {
    const item = document.createElement('div');
    item.className = 'event-card';
    item.onclick = () => showEventDetailModal(event);
    
    const typeClass = event.etkinlik_turu.toLowerCase().replace('ı', 'i');
    const countdown = calculateCountdown(event.baslangic_tarihi);
    
    let statusHTML = '';
    if (!countdown.expired) {
        statusHTML = `
            <div class="countdown">
                <div class="countdown-time">
                    ${countdown.days} gün ${countdown.hours} saat
                </div>
                <div class="countdown-label">Kalan Süre</div>
            </div>
        `;
    } else {
        statusHTML = '<div class="expired-badge">Geçmiş</div>';
    }
    
    item.innerHTML = `
        <span class="event-type ${typeClass}">${event.etkinlik_turu}</span>
        <h4>${event.baslik}</h4>
        <p>${event.aciklama || ''}</p>
        <div class="event-date">
            <i class="fas fa-calendar"></i>
            <span>${formatDate(event.baslangic_tarihi)}</span>
        </div>
        ${event.konum ? `
            <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>${event.konum}</span>
            </div>
        ` : ''}
        ${statusHTML}
    `;
    
    return item;
}

// Etkinlik detay modalı göster
function showEventDetailModal(event) {
    const modal = document.getElementById('event-detail-modal');
    const title = document.getElementById('event-detail-title');
    const content = document.getElementById('event-detail-content');
    
    if (!modal) return;
    
    title.textContent = event.baslik;
    
    const countdown = calculateCountdown(event.baslangic_tarihi);
    let countdownHTML = '';
    
    if (!countdown.expired) {
        countdownHTML = `
            <div class="countdown-display">
                <div class="countdown-item">
                    <span class="countdown-number">${countdown.days}</span>
                    <span class="countdown-label">Gün</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number">${countdown.hours}</span>
                    <span class="countdown-label">Saat</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number">${countdown.minutes}</span>
                    <span class="countdown-label">Dakika</span>
                </div>
            </div>
        `;
    }
    
    content.innerHTML = `
        <div class="event-type ${event.etkinlik_turu.toLowerCase().replace('ı', 'i')}">${event.etkinlik_turu}</div>
        <p><strong>Açıklama:</strong> ${event.aciklama || 'Açıklama bulunmamaktadır.'}</p>
        <p><strong>Başlangıç:</strong> ${formatDate(event.baslangic_tarihi)}</p>
        ${event.bitis_tarihi ? `<p><strong>Bitiş:</strong> ${formatDate(event.bitis_tarihi)}</p>` : ''}
        ${event.konum ? `<p><strong>Konum:</strong> ${event.konum}</p>` : ''}
        ${countdownHTML}
    `;
    
    modal.classList.remove('hidden');
}

// Modal kapat
function closeEventDetail() {
    const modal = document.getElementById('event-detail-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Etkinlikleri filtrele
function filterEvents() {
    const filter = document.getElementById('event-type-filter').value;
    currentFilter = filter;
    loadAllEvents();
}

// ESC tuşu ile modal kapatma
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeEventDetail();
    }
});

