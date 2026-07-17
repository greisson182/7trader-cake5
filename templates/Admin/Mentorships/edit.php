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
        <div class="d-flex gap-2">
            <?php if ($mentorship->type === 'particular'): ?>
                <a href="/admin/mentorships/instances/<?= $mentorship->id ?>" class="btn btn-outline-primary">
                    <i class="bi bi-collection-play me-2"></i>Gerenciar Alunos
                </a>
            <?php else: ?>
                <a href="/admin/mentorships/videos/<?= $mentorship->id ?>" class="btn btn-outline-primary">
                    <i class="bi bi-collection-play me-2"></i>Gerenciar Vídeos
                </a>
            <?php endif; ?>
            <a href="/admin/mentorships/view/<?= $mentorship->id ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <?= $this->Form->create($mentorship, ['type' => 'file']) ?>
    <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
    <div class="row g-3">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card glass">
                <div class="card-body">
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
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-image me-2"></i>
                        Capa da Mentoria
                    </h6>

                    <div class="thumbnail-preview mb-3">
                        <img id="thumbnailPreview"
                             src="<?= h($mentorship->thumbnail ?? '') ?>"
                             alt="Prévia da capa"
                             class="<?= empty($mentorship->thumbnail) ? 'd-none' : '' ?>">
                        <div id="thumbnailPlaceholder" class="thumbnail-placeholder <?= empty($mentorship->thumbnail) ? '' : 'd-none' ?>">
                            <i class="bi bi-image"></i>
                            <span>Nenhuma imagem definida</span>
                        </div>
                    </div>

                    <label for="thumbnail_upload" class="form-label fw-semibold small">
                        <i class="bi bi-upload me-1"></i>
                        Enviar imagem do computador
                    </label>
                    <input type="file" class="form-control form-control-sm" id="thumbnail_upload" name="thumbnail_upload"
                        accept="image/jpeg,image/png,image/webp,image/gif">
                    <div class="form-text">JPG, PNG, WEBP ou GIF, até 5MB. Proporção 16:9 recomendada (ex: 1280x720px).</div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const preview = document.getElementById('thumbnailPreview');
        const placeholder = document.getElementById('thumbnailPlaceholder');
        const uploadInput = document.getElementById('thumbnail_upload');
        uploadInput.addEventListener('change', function() {
            const uploadedFile = uploadInput.files[0];
            if (!uploadedFile) {
                return;
            }
            preview.src = URL.createObjectURL(uploadedFile);
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        });
    });
</script>

<style>
    .form-control:focus,
    .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .thumbnail-preview {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        border-radius: var(--radius-sm, 8px);
        overflow: hidden;
        background: rgba(255, 255, 255, 0.05);
    }

    .thumbnail-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumbnail-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--bs-gray-500, #adb5bd);
        border: 2px dashed rgba(255, 255, 255, 0.15);
        border-radius: var(--radius-sm, 8px);
    }

    .thumbnail-placeholder i {
        font-size: 2.5rem;
    }

    .thumbnail-placeholder span {
        font-size: 0.85rem;
    }
</style>
