document.addEventListener("DOMContentLoaded", () => {

    /* ================== SLIDER ================== */
    let currentSlide = 0;
    const slides = document.querySelectorAll(".slide-item");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");

    function showSlide(index) {
        if (!slides.length) return;
        slides.forEach((slide, i) => {
            slide.style.display = i === index ? "block" : "none";
        });
    }

    function nextSlide() {
        if (!slides.length) return;
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    function prevSlide() {
        if (!slides.length) return;
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }

    if (slides.length > 0) showSlide(0);
    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);



    /* ================== POPUP KATILIM FORMU ================== */
    const popup = document.getElementById("popup");
    const closeBtn = document.querySelector(".close");
    const onaylaBtn = document.getElementById("onayla");
    const eventName = document.getElementById("event-name");

    if (popup && closeBtn && onaylaBtn && eventName) {
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

        window.addEventListener("click", (e) => {
            if (e.target === popup) popup.style.display = "none";
        });

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
    }



    /* ================== HAMBURGER MENÜ ================== */
    const menuBtn = document.getElementById("menuBtn");
    const sideMenu = document.getElementById("sideMenu");
    const closeMenu = document.getElementById("closeMenu");

    // Sadece header olan sayfalarda çalışsın
    if (menuBtn && sideMenu && closeMenu) {

        // Sayfa açılınca menü otomatik açık olmasın
        sideMenu.classList.remove("open");
        document.body.classList.remove("menu-open");

        // Hamburger → Aç
        menuBtn.addEventListener("click", () => {
            sideMenu.classList.add("open");
            document.body.classList.add("menu-open");
        });

        // X → Kapat
        closeMenu.addEventListener("click", () => {
            sideMenu.classList.remove("open");
            document.body.classList.remove("menu-open");
        });

        // Menüdeki linklerden birine tıklanınca da kapat
        sideMenu.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => {
                sideMenu.classList.remove("open");
                document.body.classList.remove("menu-open");
            });
        });
    }

});

