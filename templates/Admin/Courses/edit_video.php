<?php
$title = 'Editar Vídeo';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-video me-2"></i>Editar Vídeo
                </h1>
                <div>
                    <a href="/admin/courses/watch-video/<?= $video['id'] ?>" class="btn btn-success me-2" target="_blank">
                        <i class="fas fa-play me-2"></i>Assistir Vídeo
                    </a>
                    <a href="/admin/courses/videos/<?= $video['course_id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Informações do Vídeo
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($video['title'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="video_url" class="form-label">URL do Vídeo *</label>
                                <input type="url" class="form-control" id="video_url" name="video_url" 
                                       value="<?= htmlspecialchars($video['video_url'] ?? '') ?>" required>
                                <div class="form-text">Cole aqui o link do YouTube, Vimeo ou outro serviço</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="video_type" class="form-label">Tipo</label>
                                <select class="form-select" id="video_type" name="video_type">
                                    <option value="youtube" <?= ($video['video_type'] ?? '') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                    <option value="vimeo" <?= ($video['video_type'] ?? '') === 'vimeo' ? 'selected' : '' ?>>Vimeo</option>
                                    <option value="other" <?= ($video['video_type'] ?? '') === 'other' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($video['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">Duração (minutos)</label>
                                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" 
                                       value="<?= isset($video['duration_seconds']) ? round($video['duration_seconds'] / 60) : '' ?>" min="1">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           <?= ($video['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Vídeo ativo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                                <a href="/admin/courses/videos/<?= $video['course_id'] ?>" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Informações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID do Vídeo:</strong><br>
                        <span class="text-muted"><?= $video['id'] ?></span>
                    </div>
                    
                    <?php if (!empty($video['created'])): ?>
                    <div class="mb-3">
                        <strong>Criado em:</strong><br>
                        <span class="text-muted"><?= date('d/m/Y H:i', strtotime($video['created'])) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($video['modified']) && $video['modified'] !== $video['created']): ?>
                    <div class="mb-3">
                        <strong>Última atualização:</strong><br>
                        <span class="text-muted"><?= date('d/m/Y H:i', strtotime($video['modified'])) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <strong>Posição:</strong><br>
                        <span class="text-muted"><?= $video['order_position'] ?? 'Não definida' ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <?php if ($video['is_active']): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($video['video_url'])): ?>
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-play me-2"></i>Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
                        <?php if (strpos($video['video_url'], 'youtube.com') !== false || strpos($video['video_url'], 'youtu.be') !== false): ?>
                            <?php
                            $videoId = '';
                            if (strpos($video['video_url'], 'youtu.be') !== false) {
                                $videoId = substr($video['video_url'], strrpos($video['video_url'], '/') + 1);
                            } elseif (strpos($video['video_url'], 'youtube.com') !== false) {
                                parse_str(parse_url($video['video_url'], PHP_URL_QUERY), $params);
                                $videoId = $params['v'] ?? '';
                            }
                            ?>
                            <?php if ($videoId): ?>
                                <iframe src="https://www.youtube.com/embed/<?= $videoId ?>" 
                                        frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light">
                                    <span class="text-muted">Preview não disponível</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light">
                                <span class="text-muted">Preview disponível apenas para YouTube</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
}

.text-primary {
    color: #4e73df !important;
}

.badge {
    font-size: 0.75rem;
}
</style>