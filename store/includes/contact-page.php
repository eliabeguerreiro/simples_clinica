<?php
/**
 * Partial de contato/acesso — incluído por index.php após definir as variáveis abaixo.
 *
 * @var string $contactHeroTitle
 * @var string $contactHeroSubtitle
 * @var string $contactFormTitle
 * @var string $contactMessagePlaceholder
 * @var string $contactSubmitLabel
 * @var string $contactBenefitsTitle
 * @var array<int, string> $contactBenefits
 * @var string $nome
 * @var string $email
 * @var string $telefone
 * @var string $mensagem
 * @var string $successMessage
 * @var string $errorMessage
 */
?>
            <section class="contact-hero">
                <div class="container">
                    <h1 class="page-title"><?= htmlspecialchars($contactHeroTitle) ?></h1>
                    <p class="page-subtitle"><?= htmlspecialchars($contactHeroSubtitle) ?></p>
                </div>
            </section>

            <section class="contact-section">
                <div class="container">
                    <div class="contact-grid">
                        <div class="contact-form-container">
                            <h2 class="contact-form-title"><?= htmlspecialchars($contactFormTitle) ?></h2>
                            <?php if (!empty($successMessage)): ?>
                                <div class="alert alert-success"><?= $successMessage ?></div>
                            <?php endif; ?>
                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert alert-error"><?= $errorMessage ?></div>
                            <?php endif; ?>
                            <form method="POST" class="contact-form">
                                <div class="form-group">
                                    <label for="nome" class="form-label">Nome completo *</label>
                                    <input type="text" id="nome" name="nome" class="form-input" value="<?= $nome ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Seu e-mail *</label>
                                    <input type="email" id="email" name="email" class="form-input" value="<?= $email ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                                    <input type="tel" id="telefone" name="telefone" class="form-input" value="<?= $telefone ?>" placeholder="(00) 90000-0000">
                                </div>
                                <div class="form-group">
                                    <label for="mensagem" class="form-label">Mensagem *</label>
                                    <textarea id="mensagem" name="mensagem" class="form-textarea" rows="6" required placeholder="<?= htmlspecialchars($contactMessagePlaceholder) ?>"><?= $mensagem ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block"><?= htmlspecialchars($contactSubmitLabel) ?></button>
                            </form>
                        </div>
                        <div class="contact-info-container">
                            <h2 class="contact-info-title">Fale conosco</h2>
                            <div class="contact-info-item">
                                <div class="contact-info-icon" aria-hidden="true">📧</div>
                                <div class="contact-info-content">
                                    <h4 class="contact-info-label">E-mail</h4>
                                    <p class="contact-info-text">
                                        <a href="mailto:<?= CONTACT_EMAIL ?>"><?= CONTACT_EMAIL ?></a>
                                    </p>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon" aria-hidden="true">📍</div>
                                <div class="contact-info-content">
                                    <h4 class="contact-info-label">Localização</h4>
                                    <p class="contact-info-text">Recife, PE — Brasil</p>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon" aria-hidden="true">⏰</div>
                                <div class="contact-info-content">
                                    <h4 class="contact-info-label">Horário</h4>
                                    <p class="contact-info-text">
                                        Segunda a sexta: 9h – 18h<br>
                                        Sábado: 9h – 13h
                                    </p>
                                </div>
                            </div>
                            <div class="contact-benefits">
                                <h4 class="contact-benefits-title"><?= htmlspecialchars($contactBenefitsTitle) ?></h4>
                                <ul class="contact-benefits-list">
                                    <?php foreach ($contactBenefits as $benefit): ?>
                                        <li><?= htmlspecialchars($benefit) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
