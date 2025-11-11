/**
 * Sistema Vivenciar - JavaScript
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

// Validação de Email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validação de Formulário de Login
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

// Mostrar mensagem no topo do formulário (inline)
function showFormMessage(form, message, type = 'error') {
    // remover mensagens anteriores
    const existing = form.querySelector('.form-message');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.className = 'form-message alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
    div.textContent = message;
    form.prepend(div);
    // rolar até a mensagem em telas pequenas
    div.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Toast simples para mensagens rápidas
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    // force reflow para animação
    void toast.offsetWidth;
    toast.classList.add('toast-show');
    setTimeout(() => {
        toast.classList.remove('toast-show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Efeito de Scroll Suave para Links Internos
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar efeito de fade-in aos elementos
    const elements = document.querySelectorAll('.pain-card, .feature-card, .tech-item');
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    elements.forEach(function(element) {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
    
    // Validar formulários ao enviar
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Validação básica (o servidor também deve validar)
            if (form.classList.contains('contact-form')) {
                if (!validateContactForm(form)) {
                    e.preventDefault();
                }
            }
            
            if (form.classList.contains('login-form')) {
                if (!validateLoginForm(form)) {
                    e.preventDefault();
                }
            }
        });
    });
    
    // Adicionar classe ativa ao link de navegação atual
    const currentPage = getCurrentPageFromUrl();
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.classList.remove('active');
        if (link.href.includes('page=' + currentPage)) {
            link.classList.add('active');
        }
    });
});

// Obter página atual da URL
function getCurrentPageFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('page') || 'home';
}

// Função para animar números (contador)
function animateCounter(element, target, duration = 2000) {
    let current = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(function() {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Função para copiar texto para clipboard
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copiado para a área de transferência!', 'success');
        }).catch(function(err) {
            console.error('Erro ao copiar:', err);
            showToast('Não foi possível copiar para a área de transferência.', 'error');
        });
    } else {
        // fallback
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showToast('Copiado para a área de transferência!', 'success');
        } catch (err) {
            console.error('Erro ao copiar (fallback):', err);
            showToast('Não foi possível copiar para a área de transferência.', 'error');
        }
        textarea.remove();
    }
}

// Função para mostrar/ocultar senha
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

// Efeito de scroll parallax (opcional)
window.addEventListener('scroll', function() {
    const scrollPosition = window.scrollY;
    const parallaxElements = document.querySelectorAll('.hero-illustration');
    
    parallaxElements.forEach(function(element) {
        element.style.transform = 'translateY(' + (scrollPosition * 0.5) + 'px)';
    });
});

// Log para debug
console.log('Sistema Vivenciar - JavaScript carregado com sucesso');
