/**
 * Sistema Vivenciar - JavaScript
 * Validações e Interações
 */

// ============================================
// Validação de Formulários
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Validar formulário de contato
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            if (!validateContactForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Validar formulário de login
    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Validar formulário de acompanhamento
    const acompanhamentoForm = document.querySelector('.acompanhamento-form');
    if (acompanhamentoForm) {
        acompanhamentoForm.addEventListener('submit', function(e) {
            if (!validateAcompanhamentoForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Adicionar animações ao scroll
    addScrollAnimations();
    
    // Destacar link ativo na navegação
    highlightActiveNav();
});

// Validar formulário de contato
function validateContactForm() {
    const nome = document.getElementById('nome');
    const email = document.getElementById('email');
    const mensagem = document.getElementById('mensagem');
    
    let isValid = true;
    
    // Validar nome
    if (!nome || nome.value.trim() === '') {
        showFieldError(nome, 'Nome é obrigatório');
        isValid = false;
    } else if (nome.value.trim().length < 3) {
        showFieldError(nome, 'Nome deve ter pelo menos 3 caracteres');
        isValid = false;
    } else {
        clearFieldError(nome);
    }
    
    // Validar email
    if (!email || email.value.trim() === '') {
        showFieldError(email, 'Email é obrigatório');
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        showFieldError(email, 'Email inválido');
        isValid = false;
    } else {
        clearFieldError(email);
    }
    
    // Validar mensagem
    if (!mensagem || mensagem.value.trim() === '') {
        showFieldError(mensagem, 'Mensagem é obrigatória');
        isValid = false;
    } else if (mensagem.value.trim().length < 10) {
        showFieldError(mensagem, 'Mensagem deve ter pelo menos 10 caracteres');
        isValid = false;
    } else {
        clearFieldError(mensagem);
    }
    
    return isValid;
}

// Validar formulário de login
function validateLoginForm() {
    const email = document.getElementById('email');
    const senha = document.getElementById('senha');
    
    let isValid = true;
    
    // Validar email
    if (!email || email.value.trim() === '') {
        showFieldError(email, 'Email é obrigatório');
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        showFieldError(email, 'Email inválido');
        isValid = false;
    } else {
        clearFieldError(email);
    }
    
    // Validar senha
    if (!senha || senha.value === '') {
        showFieldError(senha, 'Senha é obrigatória');
        isValid = false;
    } else if (senha.value.length < 6) {
        showFieldError(senha, 'Senha deve ter pelo menos 6 caracteres');
        isValid = false;
    } else {
        clearFieldError(senha);
    }
    
    return isValid;
}

// Validar formulário de acompanhamento
function validateAcompanhamentoForm() {
    const codigo = document.getElementById('codigo');
    const dataNascimento = document.getElementById('data_nascimento');
    
    let isValid = true;
    
    // Validar código
    if (!codigo || codigo.value.trim() === '') {
        showFieldError(codigo, 'Código de inscrição é obrigatório');
        isValid = false;
    } else {
        clearFieldError(codigo);
    }
    
    // Validar data de nascimento
    if (!dataNascimento || dataNascimento.value === '') {
        showFieldError(dataNascimento, 'Data de nascimento é obrigatória');
        isValid = false;
    } else {
        clearFieldError(dataNascimento);
    }
    
    return isValid;
}

// Validar email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Mostrar erro no campo
function showFieldError(field, message) {
    if (!field) return;
    
    // Remover erro anterior se existir
    clearFieldError(field);
    
    // Adicionar classe de erro
    field.classList.add('field-error');
    field.style.borderColor = '#ef4444';
    
    // Criar elemento de mensagem de erro
    const errorMsg = document.createElement('small');
    errorMsg.className = 'field-error-msg';
    errorMsg.style.color = '#ef4444';
    errorMsg.style.fontSize = '0.875rem';
    errorMsg.style.marginTop = '0.25rem';
    errorMsg.textContent = message;
    
    // Inserir após o campo
    field.parentNode.insertBefore(errorMsg, field.nextSibling);
}

// Limpar erro do campo
function clearFieldError(field) {
    if (!field) return;
    
    field.classList.remove('field-error');
    field.style.borderColor = '';
    
    // Remover mensagem de erro se existir
    const errorMsg = field.parentNode.querySelector('.field-error-msg');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// ============================================
// Animações ao Scroll
// ============================================

function addScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observar cards e seções
    const elementsToObserve = document.querySelectorAll(
        '.pain-card, .feature-card, .tech-item, .benefit-item, .info-card'
    );
    
    elementsToObserve.forEach(element => {
        observer.observe(element);
    });
}

// Adicionar estilos para animação
const style = document.createElement('style');
style.textContent = `
    .pain-card, .feature-card, .tech-item, .benefit-item, .info-card {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    
    .fade-in {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
    
    .field-error {
        border-color: #ef4444 !important;
    }
`;
document.head.appendChild(style);

// ============================================
// Navegação Ativa
// ============================================

function highlightActiveNav() {
    const currentPage = getCurrentPage();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        const href = link.getAttribute('href');
        if (href.includes(`page=${currentPage}`) || (currentPage === 'home' && href === '/?')) {
            link.classList.add('active');
        }
    });
}

function getCurrentPage() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('page') || 'home';
}

// ============================================
// Utilitários
// ============================================

// Formatar telefone
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length > 0) {
        if (value.length <= 2) {
            value = `(${value}`;
        } else if (value.length <= 6) {
            value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
        } else if (value.length <= 10) {
            value = `(${value.slice(0, 2)}) ${value.slice(2, 6)}-${value.slice(6)}`;
        } else {
            value = `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7, 11)}`;
        }
    }
    
    input.value = value;
}

// Adicionar máscara de telefone
const phoneInputs = document.querySelectorAll('input[type="tel"]');
phoneInputs.forEach(input => {
    input.addEventListener('input', function() {
        formatPhoneNumber(this);
    });
});

// ============================================
// Efeitos Visuais
// ============================================

// Adicionar efeito de ripple nos botões
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
});

// Adicionar estilos para ripple
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyle);

// ============================================
// Smooth Scroll
// ============================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ============================================
// Feedback Visual
// ============================================

// Mostrar mensagem de sucesso
function showSuccessMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success';
    alert.textContent = message;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.maxWidth = '400px';
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Mostrar mensagem de erro
function showErrorMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-error';
    alert.textContent = message;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.maxWidth = '400px';
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

console.log('Sistema Vivenciar - JavaScript carregado com sucesso!');
