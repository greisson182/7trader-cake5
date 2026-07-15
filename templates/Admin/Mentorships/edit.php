<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mentorship $mentorship
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="mentorships edit content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                <i class="bi bi-pencil-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Editar Mentoria
            </h1>
            <p class="text-muted mb-0"><?= h($mentorship->name) ?></p>
        </div>
        <a href="/admin/mentorships/view/<?= $mentorship->id ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <div class="card glass">
        <div class="card-body">
            <?= $this->Form->create($mentorship) ?>
            <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label fw-semibold">Nome da Mentoria *</label>
                    <input type="text" class="form-control" id="name" name="name" required
                        value="<?= h($mentorship->name) ?>">
                </div>

                <div class="col-md-4">
                    <label for="type" class="form-label fw-semibold">Tipo *</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="grupo" <?= $mentorship->type === 'grupo' ? 'selected' : '' ?>>Em grupo</option>
                        <option value="particular" <?= $mentorship->type === 'particular' ? 'selected' : '' ?>>Particular</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= h($mentorship->description) ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="price" class="form-label fw-semibold">Preço (R$) *</label>
                    <input type="number" class="form-control" id="price" name="price"
                        min="0" step="0.01" required value="<?= h($mentorship->price) ?>">
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                            <?= $mentorship->is_active ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_active">
                            Mentoria Ativa
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
                <a href="/admin/mentorships/view/<?= $mentorship->id ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-2"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>
                    Salvar Alterações
                </button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
