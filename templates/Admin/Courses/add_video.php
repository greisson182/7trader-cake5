<?php
/**
 * @var \App\View\AppView $this
 * @var array $course
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="add-video content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-plus-circle-fill me-2 icon-accent"></i>
                Adicionar Vídeo
            </h1>
            <p class="text-muted mb-0">Curso: <?= h($course['title']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-info">
                <i class="bi bi-collection-play me-2"></i>
                Ver Vídeos
            </a>
            <a href="/admin/courses/view/<?= $course['id'] ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <form method="post" action="/admin/courses/add-video/<?= $course['id'] ?>">
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
                                       placeholder="Ex: Introdução ao Trading">
                            </div>
                            <div class="col-md-4">
                                <label for="order_position" class="form-label fw-semibold">Posição</label>
                                <input type="number" class="form-control" id="order_position" name="order_position"
                                       min="1" placeholder="1">
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Descrição</label>
                                <textarea class="form-control" id="description" name="description" rows="2"
                                          placeholder="Descreva o conteúdo do vídeo..."></textarea>
                            </div>
                        </div>

                        <h6 class="section-title">
                            <i class="bi bi-link-45deg me-2"></i>
                            Fonte do Vídeo
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="video_type" class="form-label fw-semibold">Tipo de Vídeo</label>
                                <select class="form-select" id="video_type" name="video_type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="vimeo">Vimeo</option>
                                    <option value="direct">Link Direto</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="duration_seconds" class="form-label fw-semibold">Duração (segundos)</label>
                                <input type="number" class="form-control" id="duration_seconds" name="duration_seconds"
                                       min="0" placeholder="Ex: 300">
                            </div>
                            <div class="col-12">
                                <label for="video_url" class="form-label fw-semibold">URL do Vídeo *</label>
                                <input type="url" class="form-control" id="video_url" name="video_url" required
                                       placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                        </div>

                        <h6 class="section-title">
                            <i class="bi bi-gear me-2"></i>
                            Configurações
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_preview" name="is_preview">
                                    <label class="form-check-label fw-semibold" for="is_preview">Vídeo de Preview</label>
                                    <div class="form-text">Acesso gratuito mesmo em cursos pagos</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">Vídeo Ativo</label>
                                    <div class="form-text">Visível para os estudantes</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                            <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Adicionar Vídeo
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
                                <span>Cole uma URL para ver o preview</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card glass">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="bi bi-info-circle me-2"></i>
                            Como preencher a URL
                        </h6>
                        <ul class="url-examples">
                            <li>
                                <i class="bi bi-youtube"></i>
                                <div>
                                    <strong>YouTube</strong>
                                    <span>youtube.com/watch?v=VIDEO_ID</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-vimeo"></i>
                                <div>
                                    <strong>Vimeo</strong>
                                    <span>vimeo.com/VIDEO_ID</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-link-45deg"></i>
                                <div>
                                    <strong>Link Direto</strong>
                                    <span>exemplo.com/video.mp4</span>
                                </div>
                            </li>
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

    videoUrlInput.addEventListener('input', function() {
        const url = this.value;
        let detectedType = '';

        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            detectedType = 'youtube';
        } else if (url.includes('vimeo.com')) {
            detectedType = 'vimeo';
        } else if (url.match(/\.(mp4|webm|ogg)$/i)) {
            detectedType = 'direct';
        }

        if (detectedType) {
            videoTypeSelect.value = detectedType;
            generatePreview(url, detectedType);
        } else if (!url) {
            resetPreview();
        }
    });

    function resetPreview() {
        previewContainer.className = 'video-preview-placeholder';
        previewContainer.innerHTML = '<i class="bi bi-film"></i><span>Cole uma URL para ver o preview</span>';
    }

    function generatePreview(url, type) {
        let embedHtml = '';

        switch (type) {
            case 'youtube':
                const youtubeId = extractYouTubeId(url);
                if (youtubeId) {
                    embedHtml = `<iframe src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen></iframe>`;
                }
                break;
            case 'vimeo':
                const vimeoId = extractVimeoId(url);
                if (vimeoId) {
                    embedHtml = `<iframe src="https://player.vimeo.com/video/${vimeoId}" frameborder="0" allowfullscreen></iframe>`;
                }
                break;
            case 'direct':
                embedHtml = `<video controls style="width: 100%; height: 100%;"><source src="${url}" type="video/mp4"></video>`;
                break;
        }

        if (embedHtml) {
            previewContainer.className = '';
            previewContainer.innerHTML = embedHtml;
        } else {
            resetPreview();
        }
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

    const durationInput = document.getElementById('duration_seconds');
    durationInput.addEventListener('input', function() {
        const seconds = parseInt(this.value);
        let helpText = this.parentNode.querySelector('.duration-help');
        if (seconds > 0) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            const formattedTime = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            if (!helpText) {
                helpText = document.createElement('div');
                helpText.className = 'form-text duration-help';
                this.parentNode.appendChild(helpText);
            }
            helpText.textContent = `Duração formatada: ${formattedTime}`;
        } else if (helpText) {
            helpText.remove();
        }
    });
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

    .url-examples {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .url-examples li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .url-examples li i {
        color: #00ff88;
        font-size: 1.1rem;
        margin-top: 0.15rem;
    }

    .url-examples li div {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .url-examples li strong {
        font-size: 0.85rem;
        color: var(--text-primary, #fff);
    }

    .url-examples li span {
        font-size: 0.75rem;
        color: var(--text-muted, #b0b0b0);
        word-break: break-all;
    }
</style>
