// Yönetim paneli JavaScript

let allAdminEvents = [];

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadAdminEvents();
    loadEventSelectOptions();
    setupForms();
});

// Form event listener'ları ayarla
function setupForms() {
    // Etkinlik oluşturma formu
    document.getElementById('event-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        await createNewEvent();
    });
    
    // QR kod oluşturma formu
    document.getElementById('qr-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        await generateQRCode();
    });
}

// Yönetim paneli etkinliklerini yükle
async function loadAdminEvents() {
    const listContainer = document.getElementById('admin-events-list');
    
    try {
        listContainer.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>';
        
        allAdminEvents = await api.getEvents();
        displayAdminEvents(allAdminEvents);
        
    } catch (error) {
        console.error('Hata:', error);
        listContainer.innerHTML = '<p class="text-center text-danger">Etkinlikler yüklenirken hata oluştu.</p>';
    }
}

// Yönetim etkinliklerini göster
function displayAdminEvents(events) {
    const listContainer = document.getElementById('admin-events-list');
    
    if (events.length === 0) {
        listContainer.innerHTML = '<p class="text-center">Henüz etkinlik oluşturulmamış.</p>';
        return;
    }
    
    listContainer.innerHTML = '';
    
    events.forEach(event => {
        const item = createAdminEventItem(event);
        listContainer.appendChild(item);
    });
}

// Yönetim etkinlik öğesi oluştur
function createAdminEventItem(event) {
    const item = document.createElement('div');
    item.className = 'event-card admin-event-item';
    
    const typeClass = event.etkinlik_turu.toLowerCase().replace('ı', 'i');
    
    item.innerHTML = `
        <div class="event-header">
            <span class="event-type ${typeClass}">${event.etkinlik_turu}</span>
            <div class="event-actions">
                <button onclick="editEvent(${event.id})" class="btn-icon" title="Düzenle">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteEventConfirm(${event.id})" class="btn-icon" title="Sil">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
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
    `;
    
    return item;
}

// Yeni etkinlik oluştur
async function createNewEvent() {
    const title = document.getElementById('event-title').value;
    const description = document.getElementById('event-description').value;
    const type = document.getElementById('event-type').value;
    const location = document.getElementById('event-location').value;
    const startDate = document.getElementById('event-start-date').value;
    const endDate = document.getElementById('event-end-date').value;
    
    if (!title || !type || !startDate) {
        showError('Lütfen zorunlu alanları doldurun');
        return;
    }
    
    const eventData = {
        baslik: title,
        aciklama: description,
        etkinlik_turu: type,
        baslangic_tarihi: startDate,
        bitis_tarihi: endDate || null,
        konum: location,
        aktif: true
    };
    
    try {
        await api.createEvent(eventData);
        showSuccess('Etkinlik başarıyla oluşturuldu');
        
        // Formu temizle
        document.getElementById('event-form').reset();
        
        // Listeyi güncelle
        loadAdminEvents();
        loadEventSelectOptions();
        
    } catch (error) {
        showError('Etkinlik oluşturulurken hata: ' + error.message);
    }
}

// Etkinlik düzenle
async function editEvent(eventId) {
    // Bu fonksiyon genişletilebilir - şimdilik basit bir alert
    alert('Düzenleme özelliği yakında eklenecek. Etkinlik ID: ' + eventId);
}

// Etkinlik silme onayı
async function deleteEventConfirm(eventId) {
    if (confirm('Bu etkinliği silmek istediğinizden emin misiniz?')) {
        try {
            await api.deleteEvent(eventId);
            showSuccess('Etkinlik silindi');
            loadAdminEvents();
            loadEventSelectOptions();
        } catch (error) {
            showError('Etkinlik silinirken hata: ' + error.message);
        }
    }
}

// QR kod oluştur
async function generateQRCode() {
    const eventId = document.getElementById('qr-event-select').value;
    const validity = document.getElementById('qr-validity').value;
    
    if (!eventId) {
        showError('Lütfen bir etkinlik seçin');
        return;
    }
    
    try {
        const qrData = await api.createQRCode(parseInt(eventId), validity || null);
        
        // QR kodu göster
        displayQRCode(qrData);
        showSuccess('QR kod başarıyla oluşturuldu');
        
    } catch (error) {
        showError('QR kod oluşturulurken hata: ' + error.message);
    }
}

// QR kodu görüntüle
function displayQRCode(qrData) {
    const resultDiv = document.getElementById('qr-result');
    const displayDiv = document.getElementById('qr-display');
    
    if (!resultDiv || !displayDiv) return;
    
    // QR kod string'ini göster
    displayDiv.innerHTML = `
        <div class="qr-code-container">
            <div id="qrcode"></div>
            <div class="qr-info">
                <p><strong>QR Kod:</strong></p>
                <code class="qr-code-text">${qrData.qr_kod}</code>
                <button onclick="copyQRCode('${qrData.qr_kod}')" class="btn-secondary">
                    <i class="fas fa-copy"></i> Kopyala
                </button>
                <p class="mt-3"><strong>Geçerlilik:</strong> ${qrData.gecerlilik_suresi ? formatDate(qrData.gecerlilik_suresi) : 'Etkinlik tarihine kadar'}</p>
            </div>
        </div>
    `;
    
    resultDiv.classList.remove('hidden');
    
    // QR code kütüphanesi ile görsel QR kod oluştur
    if (typeof QRCode !== 'undefined') {
        const qrcodeContainer = document.getElementById('qrcode');
        qrcodeContainer.innerHTML = ''; // Öncekini temizle
        new QRCode(qrcodeContainer, {
            text: qrData.qr_kod,
            width: 256,
            height: 256
        });
    }
}

// QR kodu kopyala
function copyQRCode(qrCode) {
    navigator.clipboard.writeText(qrCode).then(() => {
        showSuccess('QR kod kopyalandı');
    }).catch(err => {
        showError('Kopyalama hatası');
    });
}

// Select kutularına etkinlik seçeneklerini yükle
async function loadEventSelectOptions() {
    try {
        const events = await api.getEvents();
        
        // QR kod oluşturma için
        const qrEventSelect = document.getElementById('qr-event-select');
        if (qrEventSelect) {
            qrEventSelect.innerHTML = '<option value="">Etkinlik seçiniz...</option>';
            events.forEach(event => {
                const option = document.createElement('option');
                option.value = event.id;
                option.textContent = `${event.baslik} (${formatDate(event.baslangic_tarihi)})`;
                qrEventSelect.appendChild(option);
            });
        }
        
        // Katılımcı listesi için
        const participantEventSelect = document.getElementById('participant-event-select');
        if (participantEventSelect) {
            participantEventSelect.innerHTML = '<option value="">Etkinlik seçiniz...</option>';
            events.forEach(event => {
                const option = document.createElement('option');
                option.value = event.id;
                option.textContent = `${event.baslik} (${formatDate(event.baslangic_tarihi)})`;
                participantEventSelect.appendChild(option);
            });
        }
        
    } catch (error) {
        console.error('Etkinlikler yüklenemedi:', error);
    }
}

// Katılımcıları yükle
async function loadParticipants() {
    const eventId = document.getElementById('participant-event-select').value;
    const participantsList = document.getElementById('participants-list');
    
    if (!eventId) {
        participantsList.innerHTML = '<p class="text-muted">Bir etkinlik seçin</p>';
        return;
    }
    
    try {
        participantsList.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>';
        
        const data = await api.getParticipations(eventId);
        const participations = data.katilimlar;
        
        if (participations.length === 0) {
            participantsList.innerHTML = '<p class="text-muted">Henüz katılımcı yok</p>';
            return;
        }
        
        // Katılımcı listesi
        let html = `
            <div class="participants-table">
                <table>
                    <thead>
                        <tr>
                            <th>Ad Soyad</th>
                            <th>Öğrenci No</th>
                            <th>Katılım Tarihi</th>
                            <th>Yöntem</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        participations.forEach(p => {
            html += `
                <tr>
                    <td>${p.ad_soyad}</td>
                    <td>${p.ogrenci_no}</td>
                    <td>${formatDate(p.katilim_tarihi)}</td>
                    <td><span class="badge">${p.katilim_turu}</span></td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
                <p class="mt-3"><strong>Toplam Katılımcı:</strong> ${data.toplam_katilim}</p>
            </div>
        `;
        
        participantsList.innerHTML = html;
        
    } catch (error) {
        participantsList.innerHTML = '<p class="text-danger">Katılımcılar yüklenirken hata oluştu</p>';
    }
}

// Tab değiştirme
function showTab(tabId) {
    // Tüm tab içeriklerini gizle
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Tüm tab butonlarından active sınıfını kaldır
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Seçili tab'ı göster
    document.getElementById(tabId)?.classList.add('active');
    
    // Seçili butona active sınıfı ekle
    event.target.closest('.tab-btn')?.classList.add('active');
}

