<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mentorship $mentorship
 * @var array $formData
 */
$csrfToken = $this->request->getAttribute('csrfToken');
$formData = $formData ?? [];
?>
<div class="add-module content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-folder-plus me-2 icon-accent"></i>
                Adicionar Módulo
            </h1>
            <p class="text-muted mb-0">Mentoria: <?= h($mentorship['name']) ?></p>
        </div>
        <a href="/admin/mentorships/videos/<?= h($mentorship['id']) ?>" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card glass">
                <div class="card-body">
                    <form method="post" action="/admin/mentorships/add-module/<?= h($mentorship['id']) ?>">
                        <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

                        <h6 class="section-title">
                            <i class="bi bi-folder me-2"></i>
                            Informações do Módulo
                        </h6>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Título do Módulo *</label>
                            <input type="text" class="form-control" id="title" name="title" required
                                   placeholder="Ex: Módulo 1 - Fundamentos"
                                   value="<?= h($formData['title'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Descreva o que este módulo aborda..."><?= h($formData['description'] ?? '') ?></textarea>
                        </div>

                        <?php $isPostback = !empty($formData); ?>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   <?= (!$isPostback || isset($formData['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_active">Módulo Ativo</label>
                            <div class="form-text">Visível para os alunos</div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-secondary-subtle">
                            <a href="/admin/mentorships/videos/<?= h($mentorship['id']) ?>" class="btn btn-outline-light">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Adicionar Módulo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>Sobre Módulos
                    </h6>
                    <p class="text-muted small mb-0">
                        Módulos agrupam os vídeos da mentoria em seções (ex: "Módulo 1 - Fundamentos",
                        "Módulo 2 - Estratégias"). Depois de criar o módulo, associe os vídeos a ele
                        na tela de adicionar/editar vídeo.
                    </p>
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
</style>
