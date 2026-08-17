document.addEventListener('DOMContentLoaded', () => {
    // Mobile navigation
    const hamburger = document.getElementById('hamburger');
    const nav = document.getElementById('mainNav');
    if (hamburger && nav) {
        hamburger.addEventListener('click', () => {
            const open = nav.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', open);
            hamburger.innerHTML = open ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-bars"></i>';
        });
        nav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
            nav.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }));
    }

    // Password visibility
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            button.innerHTML = input.type === 'password'
                ? '<i class="fa-regular fa-eye"></i>'
                : '<i class="fa-regular fa-eye-slash"></i>';
        });
    });

    // Simple client-side validation
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', event => {
            const password = form.querySelector('[name="password"]');
            const confirm = form.querySelector('[name="confirm_password"]');
            if (password && password.value.length < 8) {
                event.preventDefault();
                alert('Password must contain at least 8 characters.');
                password.focus();
                return;
            }
            if (password && confirm && password.value !== confirm.value) {
                event.preventDefault();
                alert('Passwords do not match.');
                confirm.focus();
            }
        });
    });

    // Image preview
    document.querySelectorAll('[data-image-preview]').forEach(input => {
        input.addEventListener('change', () => {
            const preview = document.getElementById('imagePreview');
            if (!preview || !input.files[0]) return;
            const file = input.files[0];
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!allowed.includes(file.type)) {
                alert('Please select a JPG, PNG or WEBP image.');
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Image must be 5 MB or less.');
                input.value = '';
                return;
            }
            preview.src = URL.createObjectURL(file);
            preview.classList.add('visible');
        });
    });

    // Journal search: instant client-side filtering for visible cards
    const journalSearch = document.getElementById('journalSearch');
    const cards = [...document.querySelectorAll('.journal-card')];
    const moodFilter = document.getElementById('moodFilter');
    function filterCards() {
        if (!cards.length) return;
        const query = (journalSearch?.value || '').toLowerCase().trim();
        const mood = moodFilter?.value || '';
        cards.forEach(card => {
            const text = card.dataset.search || '';
            const matchesText = !query || text.includes(query);
            const matchesMood = !mood || card.dataset.mood === mood;
            card.style.display = matchesText && matchesMood ? '' : 'none';
        });
    }
    journalSearch?.addEventListener('input', filterCards);
    moodFilter?.addEventListener('change', filterCards);

    // Delete confirmation
    document.querySelectorAll('[data-delete]').forEach(link => {
        link.addEventListener('click', event => {
            if (!confirm('Are you sure you want to remove this memory?')) event.preventDefault();
        });
    });

    // Read entry modal
    const readModal = document.getElementById('readModal');
    document.querySelectorAll('.read-entry').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('readTitle').textContent = button.dataset.title;
            document.getElementById('readMood').textContent = button.dataset.mood;
            document.getElementById('readDate').textContent = button.dataset.date;
            document.getElementById('readContent').textContent = button.dataset.content;
            openModal(readModal);
        });
    });

    // Memory lightbox
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxTitle = document.getElementById('lightboxTitle');
    document.querySelectorAll('.lightbox-trigger').forEach(button => {
        button.addEventListener('click', () => {
            lightboxImage.src = button.dataset.image;
            lightboxImage.alt = button.dataset.title;
            lightboxTitle.textContent = button.dataset.title;
            openModal(lightbox);
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', () => closeModal(button.closest('.modal')));
    });
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) closeModal(modal);
        });
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') document.querySelectorAll('.modal.open').forEach(closeModal);
    });

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.modal.open')) document.body.style.overflow = '';
    }

    // Smooth-scroll links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', event => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Navbar scroll effect
    const appHeader = document.getElementById('appHeader');
    window.addEventListener('scroll', () => {
        if (appHeader) appHeader.style.boxShadow = window.scrollY > 10 ? '0 8px 25px rgba(24,36,59,.06)' : 'none';
        const top = document.getElementById('scrollTopBtn');
        if (top) top.classList.toggle('show', window.scrollY > 450);
    });

    // Scroll-to-top
    document.getElementById('scrollTopBtn')?.addEventListener('click', () => window.scrollTo({top: 0, behavior: 'smooth'}));

    // Entrance animations
    const revealItems = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        revealItems.forEach(item => observer.observe(item));
    } else {
        revealItems.forEach(item => item.classList.add('visible'));
    }

    // Auto-remove flash notification
    const flash = document.querySelector('.flash');
    if (flash) setTimeout(() => flash.remove(), 5000);
});
document.querySelectorAll('.input-wrap input').forEach(input => {
    const icon = input.parentElement.querySelector(':scope > i');

    if (!icon) return;

    function updateIcon() {
        if (input.value.trim() !== '') {
            icon.style.display = 'none';
        } else {
            icon.style.display = '';
        }
    }

    input.addEventListener('input', updateIcon);
    updateIcon();
});
/* =========================================================
   EUNOIA DARK / NIGHT THEME
   ========================================================= */

(function () {

    const themeToggle = document.getElementById('themeToggle');

    // Apply saved theme immediately
    const savedTheme = localStorage.getItem('eunoia-theme');

    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-mode');
    }

    if (!themeToggle) return;

    themeToggle.addEventListener('click', function () {

        document.documentElement.classList.toggle('dark-mode');

        const isDark = document.documentElement.classList.contains('dark-mode');

        localStorage.setItem(
            'eunoia-theme',
            isDark ? 'dark' : 'light'
        );

        updateThemeIcon();
    });

    function updateThemeIcon() {

        const icon = themeToggle.querySelector('i');

        if (!icon) return;

        const isDark =
            document.documentElement.classList.contains('dark-mode');

        if (isDark) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    }

    updateThemeIcon();

})();