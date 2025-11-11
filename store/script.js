/**
 * Sistema Simples de Gestão Clínica - JavaScript
 * Interações, validações e efeitos
 */

// Validação de Formulário de Contato
function validateContactForm(form) {
    const nome = form.querySelector('#nome').value.trim();
    const email = form.querySelector('#email').value.trim();
    const mensagem = form.querySelector('#mensagem').value.trim();
    
    if (!nome || !email || !mensagem) {
        showFormMessage(form, 'Por favor, preencha todos os campos obrigatórios.', 'error');
        return false;
    }
    
    if (!isValidEmail(email)) {
        showFormMessage(form, 'Por favor, insira um email válido.', 'error');
        return false;
    }
    
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validação de Login
function validateLoginForm(form) {
    const email = form.querySelector('#email').value.trim();
    const senha = form.querySelector('#senha').value;
    
    if (!email || !senha) {
        showFormMessage(form, 'Por favor, preencha email e senha.', 'error');
        return false;
    }
    
    if (!isValidEmail(email)) {
        showFormMessage(form, 'Por favor, insira um email válido.', 'error');
        return false;
    }
    
    return true;
}

function showFormMessage(form, message, type = 'error') {
    const existing = form.querySelector('.form-message');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.className = 'form-message alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
    div.textContent = message;
    form.prepend(div);
    div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    void toast.offsetWidth;
    toast.classList.add('toast-show');
    setTimeout(() => {
        toast.classList.remove('toast-show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

document.addEventListener('DOMContentLoaded', function() {
    // Animação de entrada
    const elements = document.querySelectorAll('.pain-card, .feature-card, .tech-item');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Validação de formulários
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (form.classList.contains('contact-form') && !validateContactForm(form)) {
                e.preventDefault();
            }
            if (form.classList.contains('login-form') && !validateLoginForm(form)) {
                e.preventDefault();
            }
        });
    });

    // Foco acessível (reforço)
    document.querySelectorAll('a, button, input, textarea').forEach(el => {
        el.addEventListener('focus', () => el.classList.add('focused'));
        el.addEventListener('blur', () => el.classList.remove('focused'));
    });
});

function getCurrentPageFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('page') || 'home';
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copiado!', 'success');
        }).catch(() => {
            showToast('Falha ao copiar.', 'error');
        });
    }
}

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}