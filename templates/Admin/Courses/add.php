<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Course $course
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="courses add content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                <i class="bi bi-plus-circle-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Novo Curso
            </h1>
            <p class="text-muted mb-0">Criar um novo curso em vídeo</p>
        </div>
        <a href="/admin/courses" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <?= $this->Form->create($course, ['type' => 'file']) ?>
    <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
    <div class="row g-3">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card glass">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Informações Básicas
                            </h5>
                        </div>

                        <div class="col-md-8">
                            <label for="title" class="form-label fw-semibold">
                                <i class="bi bi-type me-1"></i>
                                Título do Curso *
                            </label>
                            <input type="text" class="form-control" id="title" name="title" required
                                placeholder="Ex: Fundamentos do Trading">
                        </div>

                        <div class="col-md-4">
                            <label for="difficulty" class="form-label fw-semibold">
                                <i class="bi bi-mortarboard me-1"></i>
                                Nível de Dificuldade
                            </label>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="Iniciante" selected>Iniciante</option>
                                <option value="Intermediário">Intermediário</option>
                                <option value="Avançado">Avançado</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i>
                                Descrição
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                placeholder="Descreva o conteúdo e objetivos do curso..."></textarea>
                        </div>

                        <!-- Course Details -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-gear me-2"></i>
                                Detalhes do Curso
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i>
                                Categoria
                            </label>
                            <input type="text" class="form-control" id="category" name="category"
                                placeholder="Ex: Trading Básico, Análise Técnica">
                        </div>

                        <div class="col-md-6">
                            <label for="instructor" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>
                                Instrutor
                            </label>
                            <input type="text" class="form-control" id="instructor" name="instructor"
                                placeholder="Nome do instrutor">
                        </div>

                        <!-- Pricing -->
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-currency-dollar me-2"></i>
                                Preço e Acesso
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" checked>
                                <label class="form-check-label fw-semibold" for="is_free">
                                    <i class="bi bi-gift me-1"></i>
                                    Curso Gratuito
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6" id="price-field" style="display: none;">
                            <label for="price" class="form-label fw-semibold">
                                <i class="bi bi-currency-dollar me-1"></i>
                                Preço (R$)
                            </label>
                            <input type="number" class="form-control" id="price" name="price"
                                min="0" step="0.01" placeholder="0.00">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="bi bi-toggle-on me-1"></i>
                                    Curso Ativo
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
                        <a href="/admin/courses" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Criar Curso
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-image me-2"></i>
                        Imagem do Curso
                    </h6>

                    <div class="thumbnail-preview mb-3">
                        <img id="thumbnailPreview" src="" alt="Prévia da imagem do curso" class="d-none">
                        <div id="thumbnailPlaceholder" class="thumbnail-placeholder">
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
        const isFreeCheckbox = document.getElementById('is_free');
        const priceField = document.getElementById('price-field');
        const priceInput = document.getElementById('price');

        function togglePriceField() {
            if (isFreeCheckbox.checked) {
                priceField.style.display = 'none';
                priceInput.value = '0.00';
                priceInput.required = false;
            } else {
                priceField.style.display = 'block';
                priceInput.required = true;
            }
        }

        isFreeCheckbox.addEventListener('change', togglePriceField);
        togglePriceField(); // Initial call

        // Preview local imediato ao escolher o arquivo
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

    .card {
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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
