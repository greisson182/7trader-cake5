<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
?>
<div class="settings-page content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-gear-fill me-2 icon-accent"></i>
                <?= h($setting->title) ?: 'Configuração #' . h($setting->id) ?>
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/settings/edit/<?= $setting->id ?>" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="/admin/settings" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-2"></i>Ver Todas
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Geral
                    </h6>
                    <ul class="meta-list">
                        <li><span>Título</span><strong><?= h($setting->title) ?: '—' ?></strong></li>
                        <li><span>Descrição</span><strong><?= h($setting->description) ?: '—' ?></strong></li>
                        <li><span>Imagem</span><strong><?= h($setting->image) ?: '—' ?></strong></li>
                        <li><span>E-mail</span><strong><?= h($setting->email) ?: '—' ?></strong></li>
                        <li><span>Modo Manutenção</span><strong><?= $setting->maintenance ? 'Ligado' : 'Desligado' ?></strong></li>
                    </ul>
                </div>
            </div>

            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-share me-2"></i>
                        SEO &amp; Redes Sociais
                    </h6>
                    <ul class="meta-list">
                        <li><span>Facebook App ID</span><strong><?= h($setting->face_app_id) ?: '—' ?></strong></li>
                        <li><span>Facebook Author</span><strong><?= h($setting->face_author) ?: '—' ?></strong></li>
                        <li><span>Facebook Publisher</span><strong><?= h($setting->face_publisher) ?: '—' ?></strong></li>
                        <li><span>Google Publisher</span><strong><?= h($setting->google_publisher) ?: '—' ?></strong></li>
                        <li><span>Google Author</span><strong><?= h($setting->google_author) ?: '—' ?></strong></li>
                    </ul>
                </div>
            </div>

            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-plug me-2"></i>
                        Integrações
                    </h6>
                    <ul class="meta-list">
                        <li><span>AWS API</span><strong><?= h($setting->aws_api) ?: '—' ?></strong></li>
                        <li><span>URL de Redirecionamento</span><strong><?= h($setting->url_site) ?: '—' ?></strong></li>
                        <li><span>Contador de Acessos</span><strong><?= $this->Number->format($setting->accesses ?? 0) ?></strong></li>
                        <li><span>Redirecionar site</span><strong><?= $setting->redirect_website ? 'Sim' : 'Não' ?></strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-credit-card-2-back me-2"></i>
                        Mercado Pago
                    </h6>
                    <ul class="meta-list">
                        <li><span>Public Key</span><strong><?= h($setting->mp_public_key) ?: '—' ?></strong></li>
                        <li><span>Access Token</span><strong><?= $setting->mp_access_token ? '••••••••' : '—' ?></strong></li>
                        <li><span>Webhook Secret</span><strong><?= $setting->mp_webhook_secret ? '••••••••' : '—' ?></strong></li>
                        <li><span>Sandbox</span><strong><?= $setting->mp_sandbox ? 'Sim' : 'Não' ?></strong></li>
                    </ul>
                </div>
            </div>

            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Sistema
                    </h6>
                    <ul class="meta-list">
                        <li><span>ID</span><strong>#<?= h($setting->id) ?></strong></li>
                        <li><span>Criado em</span><strong><?= h($setting->created) ?: '—' ?></strong></li>
                        <li><span>Modificado em</span><strong><?= h($setting->modified) ?: '—' ?></strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
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

    .meta-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .meta-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.9rem;
    }

    .meta-list li span {
        color: var(--text-muted, #b0b0b0);
    }

    .meta-list li strong {
        color: var(--text-primary, #fff);
        font-weight: 600;
        text-align: right;
    }
</style>
