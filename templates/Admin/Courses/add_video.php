<?php
/**
 * @var \App\View\AppView $this
 * @var array $course
 */
?>
<div class="add-video content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-plus-circle-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
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

    <!-- Form Card -->
    <div class="card glass">
        <div class="card-body">
            <form method="post" action="/admin/courses/add-video/<?= $course['id'] ?>">
                <div class="row g-4">
                    <!-- Video Information -->
                    <div class="col-12">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-play-circle me-2"></i>
                            Informações do Vídeo
                        </h5>
                    </div>

                    <div class="col-md-8">
                        <label for="title" class="form-label fw-semibold">
                            <i class="bi bi-type me-1"></i>
                            Título do Vídeo *
                        </label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               placeholder="Ex: Introdução ao Trading">
                    </div>

                    <div class="col-md-4">
                        <label for="order_position" class="form-label fw-semibold">
                            <i class="bi bi-sort-numeric-up me-1"></i>
                            Posição na Ordem
                        </label>
                        <input type="number" class="form-control" id="order_position" name="order_position" 
                               min="1" placeholder="1">
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">
                            <i class="bi bi-text-paragraph me-1"></i>
                            Descrição
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Descreva o conteúdo do vídeo..."></textarea>
                    </div>

                    <!-- Video Source -->
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-link me-2"></i>
                            Fonte do Vídeo
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="video_type" class="form-label fw-semibold">
                            <i class="bi bi-camera-video me-1"></i>
                            Tipo de Vídeo
                        </label>
                        <select class="form-select" id="video_type" name="video_type" required>
                            <option value="">Selecione o tipo</option>
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="direct">Link Direto</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="duration_seconds" class="form-label fw-semibold">
                            <i class="bi bi-clock me-1"></i>
                            Duração (segundos)
                        </label>
                        <input type="number" class="form-control" id="duration_seconds" name="duration_seconds" 
                               min="0" placeholder="Ex: 300">
                    </div>

                    <div class="col-12">
                        <label for="video_url" class="form-label fw-semibold">
                            <i class="bi bi-link-45deg me-1"></i>
                            URL do Vídeo *
                        </label>
                        <input type="url" class="form-control" id="video_url" name="video_url" required 
                               placeholder="https://www.youtube.com/watch?v=...">
                        <div class="form-text">
                            <strong>Exemplos:</strong><br>
                            • YouTube: https://www.youtube.com/watch?v=VIDEO_ID<br>
                            • Vimeo: https://vimeo.com/VIDEO_ID<br>
                            • Link Direto: https://exemplo.com/video.mp4
                        </div>
                    </div>

                    <!-- Video Preview -->
                    <div class="col-12" id="video-preview" style="display: none;">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-eye me-1"></i>
                            Pré-visualização
                        </h6>
                        <div class="ratio ratio-16x9">
                            <div id="preview-container" class="bg-light d-flex align-items-center justify-content-center">
                                <span class="text-muted">Pré-visualização do vídeo aparecerá aqui</span>
                            </div>
                        </div>
                    </div>

                    <!-- Video Settings -->
                    <div class="col-12 mt-5">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-gear me-2"></i>
                            Configurações
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_preview" name="is_preview">
                            <label class="form-check-label fw-semibold" for="is_preview">
                                <i class="bi bi-eye me-1"></i>
                                Vídeo de Preview
                            </label>
                            <div class="form-text">Permite visualização gratuita mesmo em cursos pagos</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label fw-semibold" for="is_active">
                                <i class="bi bi-toggle-on me-1"></i>
                                Vídeo Ativo
                            </label>
                            <div class="form-text">Vídeo visível para os estudantes</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                    <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Adicionar Vídeo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoUrlInput = document.getElementById('video_url');
    const videoTypeSelect = document.getElementById('video_type');
    const previewContainer = document.getElementById('preview-container');
    const videoPreview = document.getElementById('video-preview');

    // Auto-detect video type based on URL
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
        }
    });

    // Generate video preview
    function generatePreview(url, type) {
        let embedHtml = '';

        switch (type) {
            case 'youtube':
                const youtubeId = extractYouTubeId(url);
                if (youtubeId) {
                    embedHtml = `<iframe src="https://www.youtube.com/embed/${youtubeId}" 
                                        frameborder="0" allowfullscreen></iframe>`;
                }
                break;

            case 'vimeo':
                const vimeoId = extractVimeoId(url);
                if (vimeoId) {
                    embedHtml = `<iframe src="https://player.vimeo.com/video/${vimeoId}" 
                                        frameborder="0" allowfullscreen></iframe>`;
                }
                break;

            case 'direct':
                embedHtml = `<video controls style="width: 100%; height: 100%;">
                                <source src="${url}" type="video/mp4">
                                Seu navegador não suporta o elemento de vídeo.
                            </video>`;
                break;
        }

        if (embedHtml) {
            previewContainer.innerHTML = embedHtml;
            videoPreview.style.display = 'block';
        } else {
            videoPreview.style.display = 'none';
        }
    }

    // Extract YouTube video ID
    function extractYouTubeId(url) {
        const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[7].length === 11) ? match[7] : null;
    }

    // Extract Vimeo video ID
    function extractVimeoId(url) {
        const regExp = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
        const match = url.match(regExp);
        return match ? match[1] : null;
    }

    // Duration helper
    const durationInput = document.getElementById('duration_seconds');
    durationInput.addEventListener('input', function() {
        const seconds = parseInt(this.value);
        if (seconds > 0) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            const formattedTime = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            
            // Show formatted time as help text
            let helpText = this.parentNode.querySelector('.duration-help');
            if (!helpText) {
                helpText = document.createElement('div');
                helpText.className = 'form-text duration-help';
                this.parentNode.appendChild(helpText);
            }
            helpText.textContent = `Duração formatada: ${formattedTime}`;
        }
    });
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
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

#video-preview {
    border: 2px dashed var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    background-color: var(--bs-light);
}

.ratio iframe,
.ratio video {
    border-radius: 0.375rem;
}
</style>