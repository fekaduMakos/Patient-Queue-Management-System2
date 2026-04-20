/* =====================================================
   WORLD-CLASS PORTFOLIO — MAIN JAVASCRIPT
   Fekadu Markos | Professional Grade
   ===================================================== */

// ========== THEME TOGGLE ==========
function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    const icon = document.querySelector('#themeToggle i');
    icon.className = next === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

// ========== INIT THEME ==========
(function () {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    document.addEventListener('DOMContentLoaded', () => {
        const icon = document.querySelector('#themeToggle i');
        if (icon) icon.className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    });
})();

// ========== HAMBURGER MENU ==========
function toggleMenu() {
    const links = document.querySelector('.nav-links');
    const btn = document.getElementById('hamburger');
    links.style.display = links.style.display === 'flex' ? 'none' : 'flex';
    links.style.flexDirection = 'column';
    links.style.position = 'absolute';
    links.style.top = '80px';
    links.style.left = '0';
    links.style.right = '0';
    links.style.background = 'var(--navbar-bg)';
    links.style.padding = '1.5rem 5%';
    links.style.borderBottom = '1px solid var(--border)';
    links.style.backdropFilter = 'blur(20px)';
}

// ========== NAVBAR SCROLL ==========
window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }

    // Back to top
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        if (window.scrollY > 400) backToTop.classList.add('show');
        else backToTop.classList.remove('show');
    }

    // Active nav link
    updateActiveLink();
});

// ========== ACTIVE NAV LINK ==========
function updateActiveLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    let current = '';
    sections.forEach(sec => {
        if (window.scrollY >= sec.offsetTop - 120) current = sec.getAttribute('id');
    });
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) link.classList.add('active');
    });
}

// ========== TYPING ANIMATION ==========
const phrases = ['Fekadu Markos', 'a Designer', 'a Creator', 'an Engineer'];
let phraseIndex = 0;
let charIndex = 0;
let isDeleting = false;

function type() {
    const target = document.getElementById('typingText');
    if (!target) return;

    const current = phrases[phraseIndex];
    target.textContent = isDeleting
        ? current.substring(0, charIndex--)
        : current.substring(0, charIndex++);

    const delay = isDeleting ? 60 : 120;

    if (!isDeleting && charIndex === current.length + 1) {
        setTimeout(() => { isDeleting = true; type(); }, 2000);
        return;
    }
    if (isDeleting && charIndex === 0) {
        isDeleting = false;
        phraseIndex = (phraseIndex + 1) % phrases.length;
    }
    setTimeout(type, delay);
}

// ========== COUNTER ANIMATION ==========
function animateCounters() {
    document.querySelectorAll('.counter').forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        let cur = 0;
        const step = Math.ceil(target / 60);
        const interval = setInterval(() => {
            cur = Math.min(cur + step, target);
            counter.textContent = cur;
            if (cur >= target) clearInterval(interval);
        }, 30);
    });
}

// ========== PROGRESS BARS ==========
function animateProgressBars() {
    document.querySelectorAll('.progress-fill').forEach(bar => {
        const w = bar.getAttribute('data-width');
        bar.style.width = w + '%';
    });
}

// ========== AOS (Scroll Reveal) ==========
function initAOS() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = el.getAttribute('data-delay') || 0;
                setTimeout(() => el.classList.add('visible'), parseInt(delay));

                // Trigger special animations
                if (el.classList.contains('stats-container')) animateCounters();
                if (el.closest('.about-content')) animateProgressBars();
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));

    // Also trigger stats when the section is in view
    const statsSection = document.querySelector('.stats-container');
    if (statsSection) {
        const statsObs = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) { animateCounters(); statsObs.disconnect(); }
        }, { threshold: 0.5 });
        statsObs.observe(statsSection);
    }

    // Trigger progress bars in about section
    const aboutSection = document.querySelector('.about-section');
    if (aboutSection) {
        const aboutObs = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) { animateProgressBars(); aboutObs.disconnect(); }
        }, { threshold: 0.3 });
        aboutObs.observe(aboutSection);
    }
}

// ========== PORTFOLIO FILTER ==========
function initPortfolioFilter() {
    const buttons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.portfolio-card');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');
            cards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 50);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => { card.style.display = 'none'; }, 400);
                }
            });
        });
    });
}

// ========== SEND EMAIL ==========
function sendToGmail() {
    const name = document.getElementById('clientName').value;
    const message = document.getElementById('clientMessage').value;
    const email = document.getElementById('clientEmail')?.value || '';
    const service = document.getElementById('clientService')?.value || '';
    const myEmail = "fekemark6@gmail.com";

    if (!name || !message) {
        alert("Please fill in your name and message!");
        return;
    }

    const subject = encodeURIComponent(`Project Inquiry: ${service} from ${name}`);
    const body = encodeURIComponent(`Name: ${name}\nEmail: ${email}\nService: ${service}\n\nMessage:\n${message}`);
    window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=${myEmail}&su=${subject}&body=${body}`, '_blank');
}

// ========== LIGHTBOX & HOVER PREVIEW ==========
function openLightbox(src, caption) {
    const lightbox = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImg');
    const cap = document.getElementById('lightboxCaption');
    if (!lightbox || !img) return;
    lightbox.style.display = "block";
    img.src = src;
    if (cap) cap.textContent = caption;
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    if (lightbox) lightbox.style.display = "none";
}

function initHoverPreview() {
    const cards = document.querySelectorAll('.portfolio-card');
    const preview = document.getElementById('hoverPreview');
    const previewImg = document.getElementById('hoverPreviewImg');

    if (!preview || !previewImg) return;

    cards.forEach(card => {
        const img = card.querySelector('.portfolio-real-img');
        if (!img) return;

        card.addEventListener('mouseenter', () => {
            previewImg.src = img.src;
            preview.style.display = 'block';
            setTimeout(() => preview.classList.add('active'), 10);
        });

        card.addEventListener('mouseleave', () => {
            preview.classList.remove('active');
            setTimeout(() => {
                if (!preview.classList.contains('active')) {
                    preview.style.display = 'none';
                }
            }, 400);
        });
    });
}

// ========== INIT ALL ==========
document.addEventListener('DOMContentLoaded', () => {
    type();
    initAOS();
    initPortfolioFilter();
    initHoverPreview();
});
