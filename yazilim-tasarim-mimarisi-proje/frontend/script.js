let currentSlide = 0;
const slides = document.querySelectorAll(".slide-item");

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.style.display = i === index ? "block" : "none";
  });
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  showSlide(currentSlide);
}


document.addEventListener("DOMContentLoaded", () => {
  if (slides.length > 0) {
    slides[0].style.display = "block";
  }
  document.getElementById("nextBtn").addEventListener("click", nextSlide);
  document.getElementById("prevBtn").addEventListener("click", prevSlide);
});
// === Popup Katılım Formu ===
const popup = document.getElementById("popup");
const closeBtn = document.querySelector(".close");
const onaylaBtn = document.getElementById("onayla");
const eventName = document.getElementById("event-name");

document.querySelectorAll(".katil-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const etkinlik = btn.getAttribute("data-event");
    eventName.textContent = "Etkinlik: " + etkinlik;
    popup.style.display = "flex";
  });
});

closeBtn.addEventListener("click", () => {
  popup.style.display = "none";
});

window.onclick = function(e) {
  if (e.target === popup) popup.style.display = "none";
};

onaylaBtn.addEventListener("click", () => {
  const ad = document.getElementById("ad").value.trim();
  const soyad = document.getElementById("soyad").value.trim();
  const email = document.getElementById("email").value.trim();
  const telefon = document.getElementById("telefon").value.trim();

  if (!ad || !soyad || !email || !telefon) {
    alert("Lütfen tüm alanları doldurun!");
    return;
  }

  alert(`${ad} ${soyad}, katılımınız başarıyla alındı! 🎉`);
  popup.style.display = "none";
});
document.addEventListener("DOMContentLoaded", () => {
  const popup = document.getElementById("popup");
  const closeBtn = document.querySelector(".close");
  const onaylaBtn = document.getElementById("onayla");
  const eventName = document.getElementById("event-name");

  document.querySelectorAll(".katil-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const etkinlik = btn.getAttribute("data-event");
      eventName.textContent = "Etkinlik: " + etkinlik;
      popup.style.display = "flex";
    });
  });

  closeBtn.addEventListener("click", () => {
    popup.style.display = "none";
  });

  window.onclick = function(e) {
    if (e.target === popup) popup.style.display = "none";
  };

  onaylaBtn.addEventListener("click", () => {
    const ad = document.getElementById("ad").value.trim();
    const soyad = document.getElementById("soyad").value.trim();
    const email = document.getElementById("email").value.trim();
    const telefon = document.getElementById("telefon").value.trim();

    if (!ad || !soyad || !email || !telefon) {
      alert("Lütfen tüm alanları doldurun!");
      return;
    }

    alert(`${ad} ${soyad}, katılımınız başarıyla alındı! 🎉`);
    popup.style.display = "none";
  });
});
