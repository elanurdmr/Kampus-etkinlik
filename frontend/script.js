document.addEventListener("DOMContentLoaded", () => {
  // === Slider ===
  const slides = document.querySelectorAll(".slide-item");
  let currentSlide = 0;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.style.display = i === index ? "block" : "none";
    });
  }

  function nextSlide() {
    if (slides.length === 0) return;
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  function prevSlide() {
    if (slides.length === 0) return;
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
  }

  if (slides.length > 0) {
    slides[0].style.display = "block";
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);
  }

  // === Popup Katılım Formu + Backend entegrasyonu ===
  const popup = document.getElementById("popup");
  const closeBtn = document.querySelector(".close");
  const onaylaBtn = document.getElementById("onayla");
  const eventNameEl = document.getElementById("event-name");
  let seciliEtkinlik = null;

  const KAYIT_API_URL = "http://localhost:8000/api/qr/kayit-olustur";

  document.querySelectorAll(".katil-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      seciliEtkinlik = btn.getAttribute("data-event");
      eventNameEl.textContent = "Etkinlik: " + seciliEtkinlik;
      if (popup) popup.style.display = "flex";
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      popup.style.display = "none";
    });
  }

  window.onclick = function (e) {
    if (e.target === popup) popup.style.display = "none";
  };

  if (onaylaBtn) {
    onaylaBtn.addEventListener("click", async () => {
      const ad = document.getElementById("ad").value.trim();
      const soyad = document.getElementById("soyad").value.trim();
      const email = document.getElementById("email").value.trim();
      const telefon = document.getElementById("telefon").value.trim();

      if (!ad || !soyad || !email || !telefon) {
        alert("<?= t('Lütfen tüm alanları doldurun!', 'Please fill in all fields!') ?>");
        return;
      }

      try {
        const payload = {
          first_name: ad,
          last_name: soyad,
          email: email,
          phone: telefon,
          event_name: seciliEtkinlik || (eventNameEl.textContent || "").replace("Etkinlik: ", ""),
        };

        const res = await fetch(KAYIT_API_URL, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(payload),
        });

        if (res.ok) {
          alert(`${ad} ${soyad}, <?= t('katılımınız başarıyla alındı ve puanınıza eklendi!', 'your participation has been recorded and points added!') ?> 🎉`);
          popup.style.display = "none";
        } else {
          const data = await res.json().catch(() => ({}));
          alert("<?= t('Katılım kaydedilirken hata oluştu:', 'An error occurred while saving participation:') ?> " + (data.detail || res.statusText));
        }
      } catch (err) {
        console.error("Katılım isteği hatası:", err);
        alert("<?= t('Sunucuya bağlanılamadı. Lütfen daha sonra tekrar deneyin.', 'Cannot reach the server. Please try again later.') ?>");
      }
    });
  }
});
