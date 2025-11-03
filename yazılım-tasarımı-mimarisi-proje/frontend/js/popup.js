// Pop-up yönetimi için JavaScript

// Pop-up gösterme fonksiyonu
function showPopup(event) {
    const popup = document.getElementById('countdown-popup');
    if (!popup) return;
    
    const title = document.getElementById('popup-title');
    const message = document.getElementById('popup-message');
    const daysEl = document.getElementById('days-left');
    const hoursEl = document.getElementById('hours-left');
    const minutesEl = document.getElementById('minutes-left');
    
    // Etkinlik bilgilerini doldur
    title.textContent = event.baslik || 'Etkinlik Yaklaşıyor!';
    message.textContent = `${event.etkinlik_turu || 'Etkinlik'} için hazır olun!`;
    
    // Geri sayımı hesapla ve göster
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
    
    // İlk güncelleme
    updateCountdown();
    
    // Pop-up'ı göster
    popup.classList.remove('hidden');
    
    // Her dakika güncelle
    const intervalId = setInterval(updateCountdown, 60000);
    popup.dataset.intervalId = intervalId;
}

// Pop-up kapatma fonksiyonu
function closePopup() {
    const popup = document.getElementById('countdown-popup');
    if (!popup) return;
    
    // Interval'i temizle
    if (popup.dataset.intervalId) {
        clearInterval(Number(popup.dataset.intervalId));
        delete popup.dataset.intervalId;
    }
    
    // Pop-up'ı gizle
    popup.classList.add('hidden');
}

// Otomatik pop-up kontrolü
function checkAndShowPopups() {
    // LocalStorage'dan son gösterim zamanını al
    const lastShownTime = localStorage.getItem('lastPopupShown');
    const now = Date.now();
    
    // 1 saatten önce gösterildiyse tekrar gösterme
    if (lastShownTime && (now - parseInt(lastShownTime)) < 3600000) {
        return;
    }
    
    // Yaklaşan etkinlikleri kontrol et
    api.getUpcomingEvents(1).then(data => {
        const events = data.etkinlikler;
        
        // Bir gün içinde başlayacak etkinlikleri filtrele
        const urgentEvents = events.filter(event => event.popup_goster);
        
        if (urgentEvents.length > 0) {
            // İlk etkinlik için pop-up göster
            showPopup(urgentEvents[0]);
            
            // Gösterim zamanını kaydet
            localStorage.setItem('lastPopupShown', now.toString());
        }
    }).catch(error => {
        console.error('Pop-up kontrolü sırasında hata:', error);
    });
}

// Sayfa yüklendiğinde pop-up kontrolü yap
window.addEventListener('load', () => {
    // 2 saniye sonra kontrol et (sayfa tam yüklendikten sonra)
    setTimeout(checkAndShowPopups, 2000);
});

// ESC tuşu ile pop-up kapatma
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closePopup();
    }
});

// Pop-up dışına tıklandığında kapat
document.getElementById('countdown-popup')?.addEventListener('click', (event) => {
    if (event.target.id === 'countdown-popup') {
        closePopup();
    }
});

