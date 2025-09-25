<?php
/**
 * @var \App\View\AppView $this
 * @var array $course
 * @var array $videos
 */
?>
<div class="courses view content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-eye me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                <?= h($course['title']) ?>
            </h1>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-<?= $course['is_active'] ? 'success' : 'secondary' ?> fs-6">
                    <i class="bi bi-<?= $course['is_active'] ? 'check-circle' : 'x-circle' ?> me-1"></i>
                    <?= $course['is_active'] ? 'Ativo' : 'Inativo' ?>
                </span>
                <span class="badge bg-info fs-6">
                    <i class="bi bi-mortarboard me-1"></i>
                    <?= h($course['difficulty']) ?>
                </span>
                <span class="badge bg-<?= $course['is_free'] ? 'success' : 'warning' ?> fs-6">
                    <i class="bi bi-<?= $course['is_free'] ? 'gift' : 'currency-dollar' ?> me-1"></i>
                    <?= $course['is_free'] ? 'Gratuito' : 'R$ ' . number_format($course['price'], 2, ',', '.') ?>
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-primary">
                <i class="bi bi-collection-play me-2"></i>
                Gerenciar Vídeos
            </a>
            <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>
                Editar
            </a>
            <a href="/admin/courses" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Course Information -->
        <div class="col-lg-8">
            <!-- Course Details Card -->
            <div class="card glass mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Thumbnail -->
                        <?php if (!empty($course['thumbnail'])): ?>
                        <div class="col-md-4">
                            <img src="<?= h($course['thumbnail']) ?>" 
                                 alt="<?= h($course['title']) ?>" 
                                 class="img-fluid rounded shadow-sm"
                                 style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <?php endif; ?>

                        <!-- Course Info -->
                        <div class="col-md-<?= !empty($course['thumbnail']) ? '8' : '12' ?>">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Informações do Curso
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <strong><i class="bi bi-tag me-1"></i> Categoria:</strong><br>
                                    <span class="text-muted"><?= h($course['category']) ?: 'Não informado' ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="bi bi-person me-1"></i> Instrutor:</strong><br>
                                    <span class="text-muted"><?= h($course['instructor']) ?: 'Não informado' ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="bi bi-clock me-1"></i> Duração:</strong><br>
                                    <span class="text-muted">
                                        <?php if ($course['duration_minutes']): ?>
                                            <?= floor($course['duration_minutes'] / 60) ?>h <?= $course['duration_minutes'] % 60 ?>min
                                        <?php else: ?>
                                            Não informado
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="bi bi-sort-numeric-up me-1"></i> Posição:</strong><br>
                                    <span class="text-muted"><?= h($course['order_position']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <?php if (!empty($course['description'])): ?>
                        <div class="col-12">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-text-paragraph me-1"></i>
                                Descrição
                            </h6>
                            <p class="text-muted mb-0"><?= nl2br(h($course['description'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Videos List -->
            <div class="card glass">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-collection-play me-2"></i>
                        Vídeos do Curso (<?= count($videos) ?>)
                    </h5>
                    <a href="/admin/courses/add-video/<?= $course['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>
                        Adicionar Vídeo
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($videos)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-collection-play display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum vídeo cadastrado</h5>
                            <p class="text-muted mb-4">Adicione vídeos para começar a construir seu curso.</p>
                            <a href="/admin/courses/add-video/<?= $course['id'] ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Adicionar Primeiro Vídeo
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($videos as $video): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start the-video">
                                <div class="ms-2 me-auto">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-secondary me-2"><?= h($video['order_position']) ?></span>
                                        <h6 class="fw-bold mb-0"><?= h($video['title']) ?></h6>
                                        <?php if ($video['is_preview']): ?>
                                            <span class="badge bg-info ms-2">Preview</span>
                                        <?php endif; ?>
                                        <?php if (!$video['is_active']): ?>
                                            <span class="badge bg-secondary ms-2">Inativo</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($video['description'])): ?>
                                        <p class="text-muted mb-2 small"><?= h($video['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center gap-3 small text-muted">
                                        <span>
                                            <i class="bi bi-clock me-1"></i>
                                            <?= floor($video['duration_seconds'] / 60) ?>:<?= str_pad($video['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT) ?>
                                        </span>
                                        <span>
                                            <i class="bi bi-play-circle me-1"></i>
                                            <?= ucfirst($video['video_type']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="/admin/courses/watch-video/<?= h($video['id']) ?>" target="_blank" 
                                       class="btn btn-outline-primary btn-sm" title="Assistir">
                                        <i class="bi bi-play"></i>
                                    </a>
                                    <a href="/admin/courses/edit-video/<?= $video['id'] ?>" 
                                       class="btn btn-outline-warning btn-sm" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteVideo(<?= $video['id'] ?>)" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card glass mb-4">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up display-4 text-primary mb-3"></i>
                    <h5 class="fw-bold">Estatísticas</h5>
                    <div class="row g-3 mt-3">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary fw-bold mb-0"><?= count($videos) ?></h4>
                                <small class="text-muted">Vídeos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success fw-bold mb-0">0</h4>
                            <small class="text-muted">Inscritos</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Metadata -->
            <div class="card glass">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-calendar me-1"></i>
                        Informações do Sistema
                    </h6>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ID do Curso:</span>
                            <span class="fw-semibold">#<?= h($course['id']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Criado em:</span>
                            <span class="fw-semibold"><?= date('d/m/Y H:i', strtotime($course['created'])) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Modificado em:</span>
                            <span class="fw-semibold"><?= date('d/m/Y H:i', strtotime($course['modified'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteVideo(videoId) {
    if (confirm('Tem certeza que deseja excluir este vídeo? Esta ação não pode ser desfeita.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/courses/delete-video/' + videoId;
        
        // Add CSRF token if needed
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_method';
        csrfInput.value = 'DELETE';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.card {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.list-group-item {
    border-left: none;
    border-right: none;
    border-top: 1px solid rgba(0,0,0,0.125);
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>