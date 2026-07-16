<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="settings-page content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-gear-fill me-2 icon-accent"></i>
                Nova Configuração
            </h1>
            <p class="text-muted mb-0">Dados gerais, SEO, integrações e pagamento</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/settings" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-2"></i>Ver Todas
            </a>
        </div>
    </div>

    <?= $this->Form->create($setting) ?>
    <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
    <div class="row g-3">
        <div class="col-lg-8">
            <!-- Geral -->
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Geral
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label fw-semibold">Título do Site</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-semibold">E-mail de Contato</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="2" maxlength="255"></textarea>
                        </div>
                        <div class="col-md-8">
                            <label for="image" class="form-label fw-semibold">Imagem (URL)</label>
                            <input type="text" class="form-control" id="image" name="image" placeholder="/adm/images/logo.png">
                        </div>
                        <div class="col-md-4">
                            <label for="maintenance" class="form-label fw-semibold">Modo Manutenção</label>
                            <select class="form-select" id="maintenance" name="maintenance">
                                <option value="0" selected>Desligado</option>
                                <option value="1">Ligado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO & Redes Sociais -->
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-share me-2"></i>
                        SEO &amp; Redes Sociais
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="face_app_id" class="form-label fw-semibold">Facebook App ID</label>
                            <input type="text" class="form-control" id="face_app_id" name="face_app_id">
                        </div>
                        <div class="col-md-6">
                            <label for="face_author" class="form-label fw-semibold">Facebook Author</label>
                            <input type="text" class="form-control" id="face_author" name="face_author">
                        </div>
                        <div class="col-md-6">
                            <label for="face_publisher" class="form-label fw-semibold">Facebook Publisher</label>
                            <input type="text" class="form-control" id="face_publisher" name="face_publisher">
                        </div>
                        <div class="col-md-6">
                            <label for="google_publisher" class="form-label fw-semibold">Google Publisher</label>
                            <input type="text" class="form-control" id="google_publisher" name="google_publisher">
                        </div>
                        <div class="col-md-6">
                            <label for="google_author" class="form-label fw-semibold">Google Author</label>
                            <input type="text" class="form-control" id="google_author" name="google_author">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integrações -->
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-plug me-2"></i>
                        Integrações
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="aws_api" class="form-label fw-semibold">AWS API (URL)</label>
                            <input type="text" class="form-control" id="aws_api" name="aws_api">
                        </div>
                        <div class="col-md-6">
                            <label for="url_site" class="form-label fw-semibold">URL de Redirecionamento</label>
                            <input type="text" class="form-control" id="url_site" name="url_site">
                        </div>
                        <div class="col-md-6">
                            <label for="accesses" class="form-label fw-semibold">Contador de Acessos</label>
                            <input type="number" class="form-control" id="accesses" name="accesses" min="0" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="redirect_website" name="redirect_website">
                                <label class="form-check-label fw-semibold" for="redirect_website">
                                    Redirecionar site
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Mercado Pago -->
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-credit-card-2-back me-2"></i>
                        Mercado Pago
                    </h6>

                    <div class="mb-3">
                        <label for="mp_access_token" class="form-label fw-semibold small">Access Token</label>
                        <input type="password" class="form-control form-control-sm" id="mp_access_token" name="mp_access_token" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="mp_public_key" class="form-label fw-semibold small">Public Key</label>
                        <input type="text" class="form-control form-control-sm" id="mp_public_key" name="mp_public_key">
                    </div>

                    <div class="mb-3">
                        <label for="mp_webhook_secret" class="form-label fw-semibold small">Webhook Secret</label>
                        <input type="password" class="form-control form-control-sm" id="mp_webhook_secret" name="mp_webhook_secret" autocomplete="off">
                    </div>

                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="mp_sandbox" name="mp_sandbox" checked>
                        <label class="form-check-label fw-semibold" for="mp_sandbox">
                            Modo sandbox (teste)
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>
                    Criar Configuração
                </button>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-primary, #fff);
        margin-bottom: var(--space-md, 1rem);
        padding-bottom: var(--space-sm, 0.5rem);
        border-bottom: 1px solid var(--glass-border, rgba(0, 255, 136, 0.2));
    }

    .section-title i {
        color: #00ff88;
    }
</style>
