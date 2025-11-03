// QR kod okuma sayfası JavaScript

let users = [];

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});

// Kullanıcıları yükle (Örnek veriler - normalde API'den gelir)
async function loadUsers() {
    const userSelect = document.getElementById('user-select');
    
    // Örnek kullanıcılar (backend'den gelecek)
    users = [
        { id: 1, ad: 'Ahmet', soyad: 'Yılmaz', ogrenci_no: '2021001001' },
        { id: 2, ad: 'Ayşe', soyad: 'Kaya', ogrenci_no: '2021001002' },
        { id: 3, ad: 'Mehmet', soyad: 'Demir', ogrenci_no: '2021001003' }
    ];
    
    userSelect.innerHTML = '<option value="">Öğrenci seçiniz...</option>';
    
    users.forEach(user => {
        const option = document.createElement('option');
        option.value = user.id;
        option.textContent = `${user.ad} ${user.soyad} (${user.ogrenci_no})`;
        userSelect.appendChild(option);
    });
}

// QR kod doğrula ve katılım oluştur
async function verifyQRCode() {
    const userSelect = document.getElementById('user-select');
    const qrCodeInput = document.getElementById('qr-code-input');
    const resultDiv = document.getElementById('scan-result');
    
    const userId = userSelect.value;
    const qrCode = qrCodeInput.value.trim();
    
    if (!userId) {
        showError('Lütfen bir öğrenci seçiniz');
        return;
    }
    
    if (!qrCode) {
        showError('Lütfen QR kodu giriniz');
        return;
    }
    
    try {
        resultDiv.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Doğrulanıyor...</div>';
        resultDiv.classList.remove('hidden');
        
        // QR kodu doğrula
        const qrData = await api.verifyQRCode(qrCode);
        
        if (qrData.gecerli) {
            // Katılım oluştur
            const participation = await api.createParticipation(
                parseInt(userId),
                qrData.etkinlik.id,
                qrCode
            );
            
            // Başarı mesajı göster
            showSuccessModal(qrData.etkinlik);
            
            // Formu temizle
            qrCodeInput.value = '';
            resultDiv.classList.add('hidden');
            
            // Son katılımları güncelle
            addRecentParticipation(qrData.etkinlik, userId);
        }
        
    } catch (error) {
        console.error('Hata:', error);
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i>
                <strong>Hata:</strong> ${error.message}
            </div>
        `;
    }
}

// Başarı modalı göster
function showSuccessModal(event) {
    const modal = document.getElementById('success-modal');
    const message = document.getElementById('success-message');
    
    if (!modal) return;
    
    message.innerHTML = `
        <p><strong>Etkinlik:</strong> ${event.baslik}</p>
        <p><strong>Tür:</strong> ${event.etkinlik_turu}</p>
        <p><strong>Tarih:</strong> ${formatDate(event.baslangic_tarihi)}</p>
        ${event.konum ? `<p><strong>Konum:</strong> ${event.konum}</p>` : ''}
    `;
    
    modal.classList.remove('hidden');
}

// Başarı modalı kapat
function closeSuccessModal() {
    const modal = document.getElementById('success-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Son katılımları göster
function addRecentParticipation(event, userId) {
    const recentList = document.getElementById('recent-list');
    const user = users.find(u => u.id == userId);
    
    if (!user) return;
    
    // Eğer liste boşsa, temizle
    if (recentList.querySelector('.text-muted')) {
        recentList.innerHTML = '';
    }
    
    const item = document.createElement('div');
    item.className = 'event-card';
    item.innerHTML = `
        <div class="event-type ${event.etkinlik_turu.toLowerCase().replace('ı', 'i')}">${event.etkinlik_turu}</div>
        <h4>${event.baslik}</h4>
        <p><strong>Katılımcı:</strong> ${user.ad} ${user.soyad}</p>
        <div class="event-date">
            <i class="fas fa-clock"></i>
            <span>${formatDate(new Date())}</span>
        </div>
    `;
    
    // En üste ekle
    recentList.insertBefore(item, recentList.firstChild);
    
    // Maksimum 5 kayıt göster
    while (recentList.children.length > 5) {
        recentList.removeChild(recentList.lastChild);
    }
}

// Enter tuşu ile QR kod doğrula
document.getElementById('qr-code-input')?.addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        verifyQRCode();
    }
});

