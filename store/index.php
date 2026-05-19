<?php
/**
 * Clinig — Gestão clínica multidisciplinar
 * Arquivo único com todas as páginas (Home, Contato, Acesso)
 */

// Configurações
define('APP_NAME', 'Clinig');
define('APP_DESCRIPTION', 'Clinig — plataforma de gestão clínica para clínicas multidisciplinares. Fonoaudiologia, psicologia, fisioterapia e mais. Integração SUS, prontuário e BPA.');
define('CONTACT_EMAIL', 'eliabepaz.work@gmail.com');
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// Função auxiliar para sanitizar entrada
function sanitizeInput(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Função auxiliar para validar email
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Envia notificação de contato para o e-mail da clínica
function sendContactEmail(string $nome, string $email, string $telefone, string $mensagem, string $assunto): bool {
    $host = preg_replace('/[^a-zA-Z0-9.-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
    $body = "Nome: {$nome}\r\n";
    $body .= "E-mail: {$email}\r\n";
    $body .= "Telefone: " . ($telefone !== '' ? $telefone : '(não informado)') . "\r\n\r\n";
    $body .= "Mensagem:\r\n{$mensagem}\r\n";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: Clinig <noreply@{$host}>\r\n";
    $headers .= "Reply-To: {$nome} <{$email}>\r\n";

    return @mail(CONTACT_EMAIL, $assunto, $body, $headers);
}

// Obter página atual
$page = isset($_GET['page']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['page']) : 'home';
$validPages = ['home', 'contato', 'acesso'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// Inicializar variáveis de formulário
$nome = $email = $telefone = $mensagem = '';

// Mensagens
$successMessage = '';
$errorMessage = '';
// Processar formulário de contato (páginas contato e acesso)
if (($page === 'contato' || $page === 'acesso') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizeInput($_POST['nome'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $telefone = sanitizeInput($_POST['telefone'] ?? '');
    $mensagem = sanitizeInput($_POST['mensagem'] ?? '');

    if (empty($nome) || empty($email) || empty($mensagem)) {
        $errorMessage = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (!isValidEmail($email)) {
        $errorMessage = 'Por favor, insira um e-mail válido.';
    } else {
        $assunto = $page === 'acesso'
            ? '[Clinig] Solicitação de acesso ao sistema'
            : '[Clinig] Mensagem pelo site';

        if (sendContactEmail($nome, $email, $telefone, $mensagem, $assunto)) {
            $successMessage = $page === 'acesso'
                ? 'Solicitação enviada! Em breve entraremos em contato com os dados de acesso.'
                : 'Obrigado pelo contato! Em breve retornaremos sua mensagem.';
            $nome = $email = $telefone = $mensagem = '';
        } else {
            $errorMessage = 'Não foi possível enviar agora. Escreva diretamente para ' . CONTACT_EMAIL;
        }
    }
}

// Textos das páginas de contato
$contactConfig = [
    'contato' => [
        'heroTitle' => 'Entre em Contato',
        'heroSubtitle' => 'Agende uma demonstração gratuita e veja o Clinig na prática',
        'formTitle' => 'Envie sua mensagem',
        'messagePlaceholder' => 'Conte sobre sua clínica e como podemos ajudar...',
        'submitLabel' => 'Enviar mensagem',
        'benefitsTitle' => 'Por que nos contatar?',
        'benefits' => [
            'Demonstração gratuita do sistema',
            'Consultoria sobre implementação',
            'Suporte técnico especializado',
            'Planos customizados para sua clínica',
        ],
    ],
    'acesso' => [
        'heroTitle' => 'Acessar o Sistema',
        'heroSubtitle' => 'Solicite credenciais de acesso ou fale com nossa equipe — responderemos em ' . CONTACT_EMAIL,
        'formTitle' => 'Solicitar acesso',
        'messagePlaceholder' => 'Informe o nome da clínica, especialidades atendidas e quantos usuários precisam de acesso...',
        'submitLabel' => 'Enviar solicitação de acesso',
        'benefitsTitle' => 'O que você recebe',
        'benefits' => [
            'Análise da necessidade da sua clínica',
            'Credenciais ou demonstração personalizada',
            'Suporte na implantação',
            'Resposta pelo e-mail ' . CONTACT_EMAIL,
        ],
    ],
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= APP_DESCRIPTION ?>">
    <meta property="og:title" content="Clinig — Gestão Multidisciplinar">
    <meta property="og:description" content="<?= APP_DESCRIPTION ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL ?>">
    <!-- Adicione um logo real aqui posteriormente -->
    <!-- <meta property="og:image" content="<?= BASE_URL ?>/assets/logo-og.png"> -->

    <title><?= $page === 'acesso' ? 'Acessar Sistema — Clinig' : ($page === 'contato' ? 'Contato — Clinig' : 'Clinig — Gestão para Clínicas Multidisciplinares') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar" id="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="?page=home" class="logo">
                    <span class="logo-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </span>
                    <span class="logo-text">Cli<span>nig</span></span>
                </a>
                <button type="button" class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="navMenu" aria-label="Abrir menu">
                    <span></span><span></span><span></span>
                </button>
                <nav class="navbar-menu" id="navMenu">
                    <ul class="nav-list">
                        <li><a href="?page=home" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">Início</a></li>
                        <li><a href="?page=home#recursos" class="nav-link">Recursos</a></li>
                        <li><a href="?page=contato" class="nav-link <?= $page === 'contato' ? 'active' : '' ?>">Contato</a></li>
                        <li><a href="?page=acesso" class="btn btn-primary <?= $page === 'acesso' ? 'active' : '' ?>">Acessar Sistema</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <?php if ($page === 'home'): ?>
            <?php require __DIR__ . '/includes/home.php'; ?>
        <?php elseif ($page === 'contato' || $page === 'acesso'): ?>
            <?php
            $cfg = $contactConfig[$page];
            $contactHeroTitle = $cfg['heroTitle'];
            $contactHeroSubtitle = $cfg['heroSubtitle'];
            $contactFormTitle = $cfg['formTitle'];
            $contactMessagePlaceholder = $cfg['messagePlaceholder'];
            $contactSubmitLabel = $cfg['submitLabel'];
            $contactBenefitsTitle = $cfg['benefitsTitle'];
            $contactBenefits = $cfg['benefits'];
            require __DIR__ . '/includes/contact-page.php';
            ?>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <a href="?page=home" class="logo">
                        <span class="logo-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </span>
                        <span class="logo-text">Cli<span>nig</span></span>
                    </a>
                    <p class="footer-desc">Gestão clínica integrada para clínicas multidisciplinares — prontuário, SUS e equipes em uma única plataforma.</p>
                </div>
                <div>
                    <h4 class="footer-subtitle">Navegação</h4>
                    <ul class="footer-links">
                        <li><a href="?page=home">Início</a></li>
                        <li><a href="?page=home#recursos">Recursos</a></li>
                        <li><a href="?page=home#especialidades">Especialidades</a></li>
                        <li><a href="?page=contato">Contato</a></li>
                        <li><a href="?page=acesso">Acessar Sistema</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-subtitle">Contato</h4>
                    <p class="footer-text">
                        <a href="mailto:<?= CONTACT_EMAIL ?>"><?= CONTACT_EMAIL ?></a><br>
                        Recife, PE — Brasil<br>
                        Seg–Sex, 9h–18h
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Clinig. Todos os direitos reservados.</p>
                <p>Desenvolvido para o cuidado multidisciplinar no Brasil.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>