// Ana sayfa JavaScript

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadUpcomingEvents();
    startCountdownCheck();
});

// Yaklaşan etkinlikleri yükle
async function loadUpcomingEvents() {
    const container = document.getElementById('events-container');
    const totalEventsEl = document.getElementById('total-events');
    const upcomingCountEl = document.getElementById('upcoming-count');
    
    try {
        container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>';
        
        const data = await api.getUpcomingEvents(7);
        const events = data.etkinlikler;
        
        if (events.length === 0) {
            container.innerHTML = '<p class="text-center">Yaklaşan etkinlik bulunmamaktadır.</p>';
            return;
        }
        
        // İstatistikleri güncelle
        if (totalEventsEl) totalEventsEl.textContent = events.length;
        if (upcomingCountEl) upcomingCountEl.textContent = events.length;
        
        // Etkinlik kartlarını oluştur
        container.innerHTML = '';
        events.forEach(event => {
            const card = createEventCard(event);
            container.appendChild(card);
        });
        
        // Pop-up kontrolü
        checkForPopups(events);
        
    } catch (error) {
        console.error('Hata:', error);
        container.innerHTML = '<p class="text-center text-danger">Etkinlikler yüklenirken hata oluştu.</p>';
    }
}

// Etkinlik kartı oluştur
function createEventCard(event) {
    const card = document.createElement('div');
    card.className = 'event-card';
    card.onclick = () => showEventDetail(event.id);
    
    // Etkinlik türü sınıfı
    const typeClass = event.etkinlik_turu.toLowerCase().replace('ı', 'i');
    
    // Geri sayım
    const countdown = calculateCountdown(event.baslangic_tarihi);
    let countdownHTML = '';
    
    if (!countdown.expired) {
        countdownHTML = `
            <div class="countdown">
                <div class="countdown-time">
                    ${countdown.days}g ${countdown.hours}s ${countdown.minutes}d
                </div>
                <div class="countdown-label">Kalan Süre</div>
            </div>
        `;
    }
    
    card.innerHTML = `
        <span class="event-type ${typeClass}">${event.etkinlik_turu}</span>
        <h4>${event.baslik}</h4>
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
        ${countdownHTML}
    `;
    
    return card;
}

// Etkinlik detayını göster
async function showEventDetail(eventId) {
    try {
        const event = await api.getEventById(eventId);
        alert(`Etkinlik: ${event.baslik}\n\nAçıklama: ${event.aciklama || 'Açıklama yok'}\n\nTarih: ${formatDate(event.baslangic_tarihi)}\n\nKonum: ${event.konum || 'Belirtilmemiş'}`);
    } catch (error) {
        showError('Etkinlik detayları yüklenemedi');
    }
}

// Pop-up'ları kontrol et
function checkForPopups(events) {
    const popupEvents = events.filter(event => event.popup_goster);
    
    if (popupEvents.length > 0) {
        // İlk pop-up gösterilecek etkinliği al
        const event = popupEvents[0];
        showCountdownPopup(event);
    }
}

// Geri sayım pop-up göster
function showCountdownPopup(event) {
    const popup = document.getElementById('countdown-popup');
    const title = document.getElementById('popup-title');
    const message = document.getElementById('popup-message');
    const daysEl = document.getElementById('days-left');
    const hoursEl = document.getElementById('hours-left');
    const minutesEl = document.getElementById('minutes-left');
    
    if (!popup) return;
    
    title.textContent = event.baslik;
    message.textContent = `${event.etkinlik_turu.toUpperCase()} yaklaşıyor!`;
    
    // Geri sayımı güncelle
    function updateCountdown() {
        const countdown = calculateCountdown(event.baslangic_tarihi);
        
        if (countdown.expired) {
            closePopup();
            return;
        }
        
        daysEl.textContent = countdown.days;
        hoursEl.textContent = countdown.hours;
        minutesEl.textContent = countdown.minutes;
    }
    
    updateCountdown();
    popup.classList.remove('hidden');
    
    // Her dakika güncelle
    const interval = setInterval(updateCountdown, 60000);
    
    // Pop-up kapatıldığında interval'i temizle
    popup.dataset.intervalId = interval;
}

// Pop-up kapat
function closePopup() {
    const popup = document.getElementById('countdown-popup');
    if (popup) {
        if (popup.dataset.intervalId) {
            clearInterval(popup.dataset.intervalId);
        }
        popup.classList.add('hidden');
    }
}

// Periyodik geri sayım kontrolü (her 5 dakikada bir)
function startCountdownCheck() {
    setInterval(() => {
        loadUpcomingEvents();
    }, 300000); // 5 dakika
}

