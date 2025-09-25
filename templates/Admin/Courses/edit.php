<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="courses edit content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-pencil-square me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Editar Curso
            </h1>
            <p class="text-muted mb-0">Modificar informações do curso: <?= h($course['title']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/courses/view/<?= $course['id'] ?>" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>
                Visualizar
            </a>
            <a href="/admin/courses" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card glass">
        <div class="card-body">
            <?= $this->Form->create($course) ?>
            <div class="row g-4">
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
                        value="<?= h($course['title']) ?>" placeholder="Ex: Fundamentos do Trading">
                </div>

                <div class="col-md-4">
                    <label for="difficulty" class="form-label fw-semibold">
                        <i class="bi bi-mortarboard me-1"></i>
                        Nível de Dificuldade
                    </label>
                    <select class="form-select" id="difficulty" name="difficulty">
                        <option value="Iniciante" <?= $course['difficulty'] === 'Iniciante' ? 'selected' : '' ?>>Iniciante</option>
                        <option value="Intermediário" <?= $course['difficulty'] === 'Intermediário' ? 'selected' : '' ?>>Intermediário</option>
                        <option value="Avançado" <?= $course['difficulty'] === 'Avançado' ? 'selected' : '' ?>>Avançado</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">
                        <i class="bi bi-text-paragraph me-1"></i>
                        Descrição
                    </label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        placeholder="Descreva o conteúdo e objetivos do curso..."><?= h($course['description']) ?></textarea>
                </div>

                <!-- Course Details -->
                <div class="col-12 mt-5">
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
                        value="<?= h($course['category']) ?>" placeholder="Ex: Trading Básico, Análise Técnica">
                </div>

                <div class="col-md-6">
                    <label for="instructor" class="form-label fw-semibold">
                        <i class="bi bi-person me-1"></i>
                        Instrutor
                    </label>
                    <input type="text" class="form-control" id="instructor" name="instructor"
                        value="<?= h($course['instructor']) ?>" placeholder="Nome do instrutor">
                </div>

                <div class="col-md-6">
                    <label for="duration_minutes" class="form-label fw-semibold">
                        <i class="bi bi-clock me-1"></i>
                        Duração Estimada (minutos)
                    </label>
                    <input type="number" class="form-control" id="duration_minutes" name="duration_minutes"
                        min="0" value="<?= h($course['duration_minutes']) ?>" placeholder="Ex: 120">
                </div>

                <div class="col-md-6">
                    <label for="thumbnail" class="form-label fw-semibold">
                        <i class="bi bi-image me-1"></i>
                        URL da Thumbnail
                    </label>
                    <input type="url" class="form-control" id="thumbnail" name="thumbnail"
                        value="<?= h($course['thumbnail']) ?>" placeholder="https://exemplo.com/imagem.jpg">
                </div>

                <!-- Pricing -->
                <div class="col-12 mt-5">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Preço e Acesso
                    </h5>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_free" name="is_free"
                            <?= $course['is_free'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_free">
                            <i class="bi bi-gift me-1"></i>
                            Curso Gratuito
                        </label>
                    </div>
                </div>

                <div class="col-md-6" id="price-field" style="display: <?= $course['is_free'] ? 'none' : 'block' ?>;">
                    <label for="price" class="form-label fw-semibold">
                        <i class="bi bi-currency-dollar me-1"></i>
                        Preço (R$)
                    </label>
                    <input type="number" class="form-control" id="price" name="price"
                        min="0" step="0.01" value="<?= h($course['price']) ?>" placeholder="0.00">
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                            <?= $course['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_active">
                            <i class="bi bi-toggle-on me-1"></i>
                            Curso Ativo
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="order_position" class="form-label fw-semibold">
                        <i class="bi bi-sort-numeric-up me-1"></i>
                        Posição na Ordenação
                    </label>
                    <input type="number" class="form-control" id="order_position" name="order_position"
                        min="0" value="<?= h($course['order_position']) ?>" placeholder="0">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                <a href="/admin/courses" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-2"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>
                    Salvar Alterações
                </button>
            </div>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <!-- Course Statistics -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card glass">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle display-4 text-primary mb-3"></i>
                    <h5 class="fw-bold">Vídeos do Curso</h5>
                    <p class="text-muted mb-3">Total de vídeos cadastrados</p>
                    <h3 class="text-primary fw-bold"><?= isset($course['video_count']) ? $course['video_count'] : '0' ?></h3>
                    <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-collection-play me-1"></i>
                        Gerenciar Vídeos
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card glass">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-success mb-3"></i>
                    <h5 class="fw-bold">Estudantes Inscritos</h5>
                    <p class="text-muted mb-3">Total de inscrições</p>
                    <h3 class="text-success fw-bold"><?= isset($course['enrollment_count']) ? $course['enrollment_count'] : '0' ?></h3>
                </div>
            </div>
        </div>
    </div>
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
    });
</script>

<style>
    .form-label {
        color: var(--bs-gray-700);
    }

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
</style>