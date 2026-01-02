document.addEventListener('DOMContentLoaded', () => {

    // 1. INITIALIZE LENIS SMOOTH SCROLL
    // ----------------------------------
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        direction: 'vertical',
        gestureDirection: 'vertical',
        smooth: true,
        mouseMultiplier: 1,
        smoothTouch: false,
        touchMultiplier: 2,
    });

    // Request Animation Frame loop untuk Lenis
    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // --- NEW: HANDLE ANCHOR LINKS CLICK (SMOOTH SCROLL) ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');

            // Jika href="#" saja (Scroll Up)
            if (targetId === '#') {
                e.preventDefault();
                lenis.scrollTo(0, {
                    duration: 1.5 // Sedikit lebih lambat agar elegan
                });
            } 
            // Jika href="#id-section" (Anchor Link)
            else {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    lenis.scrollTo(targetElement, {
                        offset: 0, // Bisa diatur misal -100 jika tertutup navbar
                        duration: 1.2
                    });
                }
            }
        });
    });


    // 2. CUSTOM CURSOR LOGIC
    // ----------------------
    const cursor = document.querySelector('.cursor-dot');

    if (cursor) {
        // Initial State
        cursor.style.opacity = '0';

        document.addEventListener('mousemove', (e) => {
            cursor.style.opacity = '1';
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
    }


    // 3. ADVANCED SCROLL REVEAL OBSERVER
    // ----------------------------------
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.getAttribute('data-delay') || 0;
                setTimeout(() => {
                    entry.target.classList.add('active');
                }, delay);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});