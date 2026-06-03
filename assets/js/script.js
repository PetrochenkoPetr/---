/* =============================================
   script.js — Слайдер, валидация, микроанимации
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    /* ========== SLIDER ========== */
    const wrapper = document.getElementById('sliderWrapper');
    if (wrapper) {
        const slides  = wrapper.querySelectorAll('.slider-slide');
        const prevBtn = document.getElementById('sliderPrev');
        const nextBtn = document.getElementById('sliderNext');
        const dots    = document.getElementById('sliderDots');
        let current   = 0;
        let interval  = null;
        const total   = slides.length;

        if (total > 1 && dots) {
            for (let i = 0; i < total; i++) {
                const dot = document.createElement('button');
                dot.className = 'slider-dot' + (i === 0 ? ' active' : '');
                dot.dataset.index = i;
                dot.addEventListener('click', function () {
                    goTo(parseInt(this.dataset.index));
                });
                dots.appendChild(dot);
            }
        }

        function goTo(index) {
            if (index < 0) index = total - 1;
            if (index >= total) index = 0;
            current = index;
            wrapper.style.transform = 'translateX(-' + (current * 100) + '%)';
            document.querySelectorAll('.slider-dot').forEach((d, i) => {
                d.classList.toggle('active', i === current);
            });
        }

        function nextSlide() { goTo(current + 1); }
        function prevSlide() { goTo(current - 1); }

        function startAuto() {
            stopAuto();
            interval = setInterval(nextSlide, 3000);
        }

        function stopAuto() {
            if (interval) { clearInterval(interval); interval = null; }
        }

        if (prevBtn) prevBtn.addEventListener('click', function () { prevSlide(); startAuto(); });
        if (nextBtn) nextBtn.addEventListener('click', function () { nextSlide(); startAuto(); });

        /* Pause on hover */
        const container = wrapper.closest('.slider-container');
        if (container) {
            container.addEventListener('mouseenter', stopAuto);
            container.addEventListener('mouseleave', startAuto);
        }

        startAuto();
    }

    /* ========== LIVE VALIDATION ========== */
    document.querySelectorAll('input, select, textarea').forEach(function (el) {
        el.addEventListener('blur', function () {
            validateField(this);
        });
        el.addEventListener('input', function () {
            if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
                validateField(this);
            }
        });
    });

    function validateField(el) {
        const feedback = el.parentElement.querySelector('.invalid-feedback');
        if (!feedback) return;

        let error = '';
        const val = el.value.trim();

        if (el.name === 'login') {
            if (val.length < 6) error = 'Логин должен содержать минимум 6 символов';
            else if (!/^[a-zA-Z0-9]+$/.test(val)) error = 'Только латинские буквы и цифры';
        } else if (el.name === 'password') {
            if (val.length < 8) error = 'Пароль должен быть минимум 8 символов';
        } else if (el.name === 'full_name') {
            if (val === '') error = 'Укажите ФИО';
        } else if (el.name === 'phone') {
            if (val === '') error = 'Укажите телефон';
        } else if (el.name === 'email') {
            if (val === '') error = 'Укажите e-mail';
        } else if (el.name === 'start_date') {
            if (!/^\d{2}\.\d{2}\.\d{4}$/.test(val)) {
                error = 'Формат: ДД.ММ.ГГГГ';
            } else {
                const [d, m, y] = val.split('.');
                const date = new Date(y, m - 1, d);
                if (date.getDate() !== parseInt(d) || date.getMonth() + 1 !== parseInt(m)) {
                    error = 'Некорректная дата';
                }
            }
        }

        if (error) {
            el.classList.add('is-invalid');
            el.classList.remove('is-valid');
            feedback.textContent = error;
        } else if (val !== '') {
            el.classList.remove('is-invalid');
            el.classList.add('is-valid');
            feedback.textContent = '';
        } else {
            el.classList.remove('is-invalid', 'is-valid');
            feedback.textContent = '';
        }
    }

    /* ========== MICRO-ANIMATIONS ========== */
    document.querySelectorAll('.btn, .card, .nav-link, .page-link').forEach(function (el) {
        el.addEventListener('mouseenter', function () {
            this.style.transition = 'all 0.2s ease';
        });
    });

    /* Smooth alert dismiss animation */
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            if (alert.classList.contains('alert-dismissible')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function () { alert.remove(); }, 500);
            }
        }, 5000);
    });

});
