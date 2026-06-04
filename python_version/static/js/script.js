/* =============================================
   script.js — Слайдер, валидация, микроанимации
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    /* ========== PAGE LOADER ========== */
    const loader = document.getElementById('pageLoader');
    if (loader) {
        setTimeout(function () { loader.classList.add('hidden'); }, 300);
    }

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
        const parent = el.closest('.mb-3') || el.parentElement;
        let feedback = parent.querySelector('.invalid-feedback');
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

    /* ========== DATE MASK (ДД.ММ.ГГГГ) ========== */
    const dateInput = document.getElementById('startDate');
    if (dateInput) {
        dateInput.addEventListener('input', function (e) {
            let val = this.value.replace(/\D/g, '');
            if (val.length > 2) val = val.substring(0, 2) + '.' + val.substring(2);
            if (val.length > 5) val = val.substring(0, 5) + '.' + val.substring(5, 9);
            this.value = val;
        });
        dateInput.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && (this.value.endsWith('.') || this.value.length === 3 || this.value.length === 6)) {
                this.value = this.value.slice(0, -1);
                e.preventDefault();
            }
        });
    }

    /* ========== PASSWORD STRENGTH ========== */
    const pwdInput = document.getElementById('regPassword');
    const strengthContainer = document.getElementById('passwordStrength');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    if (pwdInput && strengthContainer && strengthFill && strengthText) {
        pwdInput.addEventListener('input', function () {
            const val = this.value;
            if (val.length === 0) {
                strengthContainer.classList.remove('visible');
                return;
            }
            strengthContainer.classList.add('visible');

            let score = 0;
            if (val.length >= 8) score += 25;
            if (val.length >= 12) score += 15;
            if (/[a-z]/.test(val)) score += 15;
            if (/[A-Z]/.test(val)) score += 15;
            if (/[0-9]/.test(val)) score += 15;
            if (/[^a-zA-Z0-9]/.test(val)) score += 15;

            strengthFill.style.width = Math.min(100, score) + '%';

            if (score < 30) {
                strengthFill.style.background = '#dc3545';
                strengthText.textContent = 'Слабый';
                strengthText.style.color = '#dc3545';
            } else if (score < 60) {
                strengthFill.style.background = '#ffc107';
                strengthText.textContent = 'Средний';
                strengthText.style.color = '#ffc107';
            } else if (score < 80) {
                strengthFill.style.background = '#0d6efd';
                strengthText.textContent = 'Хороший';
                strengthText.style.color = '#0d6efd';
            } else {
                strengthFill.style.background = '#198754';
                strengthText.textContent = 'Надёжный';
                strengthText.style.color = '#198754';
            }
        });
    }

    /* ========== BUTTON LOADING STATE ========== */
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) btn.classList.add('btn-loading');
        });
    });

    /* ========== SMOOTH ALERT DISMISS ========== */
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            if (alert.classList.contains('alert-dismissible')) {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function () { alert.remove(); }, 500);
            }
        }, 5000);
    });

    /* ========== COUNTER ANIMATION ========== */
    document.querySelectorAll('.animate-count').forEach(function (el) {
        const target = parseInt(el.dataset.count) || 0;
        let current = 0;
        const step = Math.ceil(target / 30);
        const timer = setInterval(function () {
            current += step;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = current;
        }, 40);
    });

    /* ========== TOOLTIP INIT ========== */
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });

});
