/**
 * Clinig — Interações da landing
 */

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showFormMessage(form, message, type) {
    type = type || 'error';
    const existing = form.querySelector('.form-message');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.className = 'form-message alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
    div.textContent = message;
    form.prepend(div);
    div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function validateContactForm(form) {
    const nome = form.querySelector('#nome').value.trim();
    const email = form.querySelector('#email').value.trim();
    const mensagem = form.querySelector('#mensagem').value.trim();

    if (!nome || !email || !mensagem) {
        showFormMessage(form, 'Por favor, preencha todos os campos obrigatórios.');
        return false;
    }
    if (!isValidEmail(email)) {
        showFormMessage(form, 'Por favor, insira um email válido.');
        return false;
    }
    return true;
}

function validateLoginForm(form) {
    const email = form.querySelector('#email').value.trim();
    const senha = form.querySelector('#senha').value;

    if (!email || !senha) {
        showFormMessage(form, 'Por favor, preencha email e senha.');
        return false;
    }
    return true;
}

function showToast(message, type, duration) {
    type = type || 'info';
    duration = duration || 3000;
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    void toast.offsetWidth;
    toast.classList.add('toast-show');
    setTimeout(function () {
        toast.classList.remove('toast-show');
        setTimeout(function () { toast.remove(); }, 300);
    }, duration);
}

function initNavbar() {
    const navbar = document.getElementById('navbar');
    const toggle = document.getElementById('navToggle');
    const menu = document.getElementById('navMenu');

    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 8);
        }, { passive: true });
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            const open = menu.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }
}

function initReveal() {
    const items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    items.forEach(function (el) { observer.observe(el); });
}

document.addEventListener('DOMContentLoaded', function () {
    initNavbar();
    initReveal();

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.classList.contains('contact-form') && !validateContactForm(form)) {
                e.preventDefault();
            }
            if (form.classList.contains('login-form') && !validateLoginForm(form)) {
                e.preventDefault();
            }
        });
    });
});
