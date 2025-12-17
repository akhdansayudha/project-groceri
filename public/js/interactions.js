document.addEventListener('DOMContentLoaded', () => {

    // 1. INITIALIZE LENIS SMOOTH SCROLL
    // ----------------------------------
    const lenis = new Lenis({
        duration: 1.2,        // Durasi scroll (semakin besar semakin lambat/smooth)
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Easing function
        direction: 'vertical',
        gestureDirection: 'vertical',
        smooth: true,
        mouseMultiplier: 1,   // Sensitivitas scroll mouse
        smoothTouch: false,   // False agar touch device tetap native (lebih responsif)
        touchMultiplier: 2,
    });

    // Request Animation Frame loop untuk Lenis
    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);


    // 2. CUSTOM CURSOR LOGIC
    // ----------------------
    const cursor = document.querySelector('.cursor-dot');

    // Initial State
    cursor.style.opacity = '0';

    document.addEventListener('mousemove', (e) => {
        cursor.style.opacity = '1';

        // Kita update posisi left/top. 
        // Karena di CSS ada 'transition: left ... top ...', 
        // maka pergerakan ini akan otomatis menjadi smooth/laggy.
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    });

    // Hover Interaction
    const interactables = document.querySelectorAll('a, button, input, textarea, .hover-target');

    interactables.forEach(el => {
        el.addEventListener('mouseenter', () => {
            document.body.classList.add('hovering');
        });

        el.addEventListener('mouseleave', () => {
            document.body.classList.remove('hovering');
        });
    });

    document.addEventListener('mouseleave', () => cursor.style.opacity = '0');
    document.addEventListener('mouseenter', () => cursor.style.opacity = '1');


    // 3. ADVANCED SCROLL REVEAL OBSERVER
    // ----------------------------------
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Tambahkan delay sedikit jika ada atribut data-delay
                const delay = entry.target.getAttribute('data-delay') || 0;

                setTimeout(() => {
                    entry.target.classList.add('active');
                }, delay);

                // Optional: Stop observing setelah muncul (agar performa ringan)
                // observer.unobserve(entry.target); 
            }
        });
    }, {
        threshold: 0.15, // Elemen harus masuk 15% baru muncul
        rootMargin: "0px 0px -50px 0px"
    });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});