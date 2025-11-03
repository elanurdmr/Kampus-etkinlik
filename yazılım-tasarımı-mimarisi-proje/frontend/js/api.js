// API Base URL
const API_BASE_URL = 'http://localhost:8000/api';

// API Helper Functions
const api = {
    // Akademik Takvim API'leri
    async getEvents(eventType = '') {
        const url = eventType 
            ? `${API_BASE_URL}/calendar/etkinlikler?etkinlik_turu=${eventType}`
            : `${API_BASE_URL}/calendar/etkinlikler`;
        
        const response = await fetch(url);
        if (!response.ok) throw new Error('Etkinlikler yüklenemedi');
        return await response.json();
    },

    async getUpcomingEvents(days = 7) {
        const response = await fetch(`${API_BASE_URL}/calendar/yaklasan-etkinlikler?gun_sayisi=${days}`);
        if (!response.ok) throw new Error('Yaklaşan etkinlikler yüklenemedi');
        return await response.json();
    },

    async getEventById(eventId) {
        const response = await fetch(`${API_BASE_URL}/calendar/etkinlik/${eventId}`);
        if (!response.ok) throw new Error('Etkinlik bulunamadı');
        return await response.json();
    },

    async createEvent(eventData) {
        const response = await fetch(`${API_BASE_URL}/calendar/etkinlik`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(eventData)
        });
        
        if (!response.ok) throw new Error('Etkinlik oluşturulamadı');
        return await response.json();
    },

    async updateEvent(eventId, eventData) {
        const response = await fetch(`${API_BASE_URL}/calendar/etkinlik/${eventId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(eventData)
        });
        
        if (!response.ok) throw new Error('Etkinlik güncellenemedi');
        return await response.json();
    },

    async deleteEvent(eventId) {
        const response = await fetch(`${API_BASE_URL}/calendar/etkinlik/${eventId}`, {
            method: 'DELETE'
        });
        
        if (!response.ok) throw new Error('Etkinlik silinemedi');
    },

    // QR Sistem API'leri
    async createQRCode(eventId, validity = null) {
        const data = { etkinlik_id: eventId };
        if (validity) {
            data.gecerlilik_suresi = validity;
        }

        const response = await fetch(`${API_BASE_URL}/qr/qr-kod-olustur`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) throw new Error('QR kod oluşturulamadı');
        return await response.json();
    },

    async verifyQRCode(qrCode) {
        const response = await fetch(`${API_BASE_URL}/qr/qr-dogrula`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ qr_kod: qrCode })
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.detail || 'QR kod doğrulanamadı');
        }
        return await response.json();
    },

    async createParticipation(userId, eventId, qrCode) {
        const response = await fetch(`${API_BASE_URL}/qr/katilim-olustur`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                kullanici_id: userId,
                etkinlik_id: eventId,
                qr_kod: qrCode
            })
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.detail || 'Katılım oluşturulamadı');
        }
        return await response.json();
    },

    async getParticipations(eventId) {
        const response = await fetch(`${API_BASE_URL}/qr/katilimlar/${eventId}`);
        if (!response.ok) throw new Error('Katılımlar yüklenemedi');
        return await response.json();
    },

    async createUser(userData) {
        const response = await fetch(`${API_BASE_URL}/qr/kullanici`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData)
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.detail || 'Kullanıcı oluşturulamadı');
        }
        return await response.json();
    }
};

// Tarih formatlama fonksiyonu
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    };
    return date.toLocaleDateString('tr-TR', options);
}

// Geri sayım hesaplama fonksiyonu
function calculateCountdown(targetDate) {
    const now = new Date();
    const target = new Date(targetDate);
    const diff = target - now;
    
    if (diff <= 0) {
        return { days: 0, hours: 0, minutes: 0, expired: true };
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    return { days, hours, minutes, expired: false };
}

// Hata gösterme fonksiyonu
function showError(message) {
    alert('Hata: ' + message);
}

// Başarı mesajı gösterme fonksiyonu
function showSuccess(message) {
    const toast = document.getElementById('toast-notification');
    if (toast) {
        const messageEl = document.getElementById('toast-message');
        if (messageEl) messageEl.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
}

