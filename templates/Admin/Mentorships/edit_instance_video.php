<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MentorshipVideo $video
 * @var array $modules
 */
$csrfToken = $this->request->getAttribute('csrfToken');
$durationMinutes = !empty($video['duration_seconds']) ? intdiv((int)$video['duration_seconds'], 60) : '';
$durationSecondsRemainder = !empty($video['duration_seconds']) ? (int)$video['duration_seconds'] % 60 : '';
$modules = $modules ?? [];
?>
<div class="add-video content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-pencil-square me-2 icon-accent"></i>
                Editar Vídeo
            </h1>
            <p class="text-muted mb-0"><?= h($video['title']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/mentorships/watch-video/<?= $video['id'] ?>" class="btn btn-outline-success" target="_blank">
                <i class="bi bi-play-fill me-2"></i>
                Assistir
            </a>
            <a href="/admin/mentorships/instance-videos/<?= $video['instance_id'] ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <form method="post">
        <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
        <div class="row g-3">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card glass">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="bi bi-play-circle me-2"></i>
                            Informações do Vídeo
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label for="title" class="form-label fw-semibold">Título do Vídeo *</label>
                                <input type="text" class="form-control" id="title" name="title" required
                                       value="<?= h($video['title']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Duração</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="duration_minutes" name="duration_minutes"
                                           min="0" placeholder="Min" value="<?= h($durationMinutes) ?>">
                                    <span class="input-group-text">min</span>
                                    <input type="number" class="form-control" id="duration_seconds" name="duration_seconds"
                                           min="0" max="59" placeholder="Seg" value="<?= h($durationSecondsRemainder) ?>">
                                    <span class="input-group-text">seg</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="module_id" class="form-label fw-semibold">Módulo</label>
                                <select class="form-select" id="module_id" name="module_id">
                                    <option value="">Sem módulo</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?= h($module->id) ?>" <?= (int)$video['module_id'] === $module->id ? 'selected' : '' ?>>
                                            <?= h($module->title) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Descrição</label>
                                <textarea class="form-control" id="description" name="description" rows="2"><?= h($video['description']) ?></textarea>
                            </div>
                        </div>

                        <h6 class="section-title">
                            <i class="bi bi-link-45deg me-2"></i>
                            Fonte do Vídeo
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="video_type" class="form-label fw-semibold">Tipo</label>
                                <select class="form-select" id="video_type" name="video_type">
                                    <option value="youtube" <?= $video['video_type'] === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                    <option value="vimeo" <?= $video['video_type'] === 'vimeo' ? 'selected' : '' ?>>Vimeo</option>
                                    <option value="embed" <?= $video['video_type'] === 'embed' ? 'selected' : '' ?>>Embed (iframe)</option>
                                    <option value="direct" <?= $video['video_type'] === 'direct' ? 'selected' : '' ?>>Link Direto</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="video_url" class="form-label fw-semibold">URL do Vídeo *</label>
                                <input type="url" class="form-control" id="video_url" name="video_url" required
                                       value="<?= h($video['video_url']) ?>">
                            </div>
                        </div>

                        <h6 class="section-title">
                            <i class="bi bi-gear me-2"></i>
                            Configurações
                        </h6>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   <?= ($video['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="is_active">Vídeo Ativo</label>
                            <div class="form-text">Visível para o aluno</div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                            <a href="/admin/mentorships/instance-videos/<?= $video['instance_id'] ?>" class="btn btn-outline-secondary">
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
                <div class="card glass mb-3">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="bi bi-eye me-2"></i>
                            Pré-visualização
                        </h6>
                        <div class="ratio ratio-16x9 video-preview-box">
                            <div id="preview-container" class="video-preview-placeholder">
                                <i class="bi bi-film"></i>
                                <span>Preview não disponível</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card glass">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="bi bi-info-circle me-2"></i>
                            Informações
                        </h6>
                        <ul class="meta-list">
                            <li>
                                <span>ID do Vídeo</span>
                                <strong>#<?= h($video['id']) ?></strong>
                            </li>
                            <li>
                                <span>Posição</span>
                                <strong><?= h($video['order_position'] ?? 'Não definida') ?></strong>
                            </li>
                            <li>
                                <span>Status</span>
                                <?php if ($video['is_active']): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </li>
                            <?php if (!empty($video['created'])): ?>
                                <li>
                                    <span>Criado em</span>
                                    <strong><?= h($video['created']->format('d/m/Y H:i')) ?></strong>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($video['modified'])): ?>
                                <li>
                                    <span>Atualizado em</span>
                                    <strong><?= h($video['modified']->format('d/m/Y H:i')) ?></strong>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoUrlInput = document.getElementById('video_url');
    const videoTypeSelect = document.getElementById('video_type');
    const previewContainer = document.getElementById('preview-container');

    function resetPreview() {
        previewContainer.className = 'video-preview-placeholder';
        previewContainer.innerHTML = '<i class="bi bi-film"></i><span>Preview não disponível</span>';
    }

    function extractYouTubeId(url) {
        const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[7].length === 11) ? match[7] : null;
    }

    function extractVimeoId(url) {
        const regExp = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
        const match = url.match(regExp);
        return match ? match[1] : null;
    }

    function updatePreview() {
        const url = videoUrlInput.value;
        const type = videoTypeSelect.value;
        let embedHtml = '';

        if (!url) {
            resetPreview();
            return;
        }

        if (type === 'youtube' || url.includes('youtube.com') || url.includes('youtu.be')) {
            const youtubeId = extractYouTubeId(url);
            if (youtubeId) {
                embedHtml = `<iframe src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen></iframe>`;
            }
        } else if (type === 'vimeo' || url.includes('vimeo.com')) {
            const vimeoId = extractVimeoId(url);
            if (vimeoId) {
                embedHtml = `<iframe src="https://player.vimeo.com/video/${vimeoId}" frameborder="0" allowfullscreen></iframe>`;
            }
        } else if (type === 'embed') {
            embedHtml = `<iframe src="${url}" frameborder="0" allowfullscreen></iframe>`;
        } else if (url.match(/\.(mp4|webm|ogg)$/i)) {
            embedHtml = `<video controls style="width: 100%; height: 100%;"><source src="${url}" type="video/mp4"></video>`;
        }

        if (embedHtml) {
            previewContainer.className = '';
            previewContainer.innerHTML = embedHtml;
        } else {
            resetPreview();
        }
    }

    videoUrlInput.addEventListener('input', updatePreview);
    videoTypeSelect.addEventListener('change', updatePreview);
    updatePreview();
});
</script>

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

    .form-control:focus,
    .form-select:focus {
        border-color: #00ff88;
        box-shadow: 0 0 0 0.2rem rgba(0, 255, 136, 0.2);
    }

    .form-check-input:checked {
        background-color: #00ff88;
        border-color: #00ff88;
    }

    .video-preview-box {
        border-radius: var(--radius-sm, 8px);
        overflow: hidden;
    }

    .video-preview-box iframe,
    .video-preview-box video {
        width: 100%;
        height: 100%;
    }

    .video-preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.03);
        border: 2px dashed var(--glass-border, rgba(0, 255, 136, 0.2));
        color: var(--text-muted, #b0b0b0);
    }

    .video-preview-placeholder i {
        font-size: 2rem;
    }

    .video-preview-placeholder span {
        font-size: 0.8rem;
    }

    .meta-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .meta-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.85rem;
    }

    .meta-list li span {
        color: var(--text-muted, #b0b0b0);
    }

    .meta-list li strong {
        color: var(--text-primary, #fff);
        font-weight: 600;
    }
</style>
