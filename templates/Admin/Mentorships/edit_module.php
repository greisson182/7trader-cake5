<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MentorshipModule $module
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="edit-module content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-folder2-open me-2 icon-accent"></i>
                Editar Módulo
            </h1>
            <p class="text-muted mb-0"><?= h($module->title) ?></p>
        </div>
        <a href="/admin/mentorships/videos/<?= h($module->mentorship_id) ?>" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <div class="card glass">
        <div class="card-body">
            <form method="post" action="/admin/mentorships/edit-module/<?= h($module->id) ?>">
                <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

                <h6 class="section-title">
                    <i class="bi bi-folder me-2"></i>
                    Informações do Módulo
                </h6>

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Título do Módulo *</label>
                    <input type="text" class="form-control" id="title" name="title" required
                           value="<?= h($module->title) ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= h($module->description) ?></textarea>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                           <?= !empty($module->is_active) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="is_active">Módulo Ativo</label>
                    <div class="form-text">Visível para os alunos</div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-secondary-subtle">
                    <a href="/admin/mentorships/videos/<?= h($module->mentorship_id) ?>" class="btn btn-outline-light">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Salvar Alterações
                    </button>
                </div>
            </form>
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
