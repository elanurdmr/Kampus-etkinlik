from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from routers import calendar, qr_system, oneri_sistemi, kutuphane, randevu, bildirimler
import uvicorn

app = FastAPI(title="Akademik Takvim ve QR Sistem API")

# CORS ayarları - Frontend'den gelen isteklere izin vermek için
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Geliştirme için tüm originlere izin
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Router'ları ekle
app.include_router(calendar.router, prefix="/api/calendar", tags=["Akademik Takvim"])
app.include_router(qr_system.router, prefix="/api/qr", tags=["QR Sistem"])
app.include_router(oneri_sistemi.router, tags=["Öneri Sistemi"])
app.include_router(kutuphane.router, prefix="/api/kutuphane", tags=["Kütüphane Rezervasyon"])
app.include_router(randevu.router, prefix="/api/randevu", tags=["Öğretim Üyesi Randevu"])
app.include_router(bildirimler.router, prefix="/api/bildirimler", tags=["Bildirimler"])

@app.get("/")
def read_root():
    return {
        "message": "Akademik Takvim ve QR Sistem API'sine Hoş Geldiniz",
        "version": "1.0.0",
        "endpoints": {
            "akademik_takvim": "/api/calendar",
            "qr_sistem": "/api/qr",
            "oneri_sistemi": "/api/oneri",
            "kutuphane_rezervasyon": "/api/kutuphane",
            "ogretim_uyesi_randevu": "/api/randevu",
            "bildirimler": "/api/bildirimler"
        }
    }

if __name__ == "__main__":
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)

