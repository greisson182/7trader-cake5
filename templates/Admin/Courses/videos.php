<?php
/**
 * @var \App\View\AppView $this
 * @var array $course
 * @var array $videos
 */
?>
<div class="course-videos content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-collection-play me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Gerenciar Vídeos
            </h1>
            <p class="text-muted mb-0">Curso: <?= h($course['title']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/courses/add-video/<?= $course['id'] ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Adicionar Vídeo
            </a>
            <a href="/admin/courses/view/<?= $course['id'] ?>" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>
                Ver Curso
            </a>
            <a href="/admin/courses" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Course Info Bar -->
    <div class="card glass mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($course['thumbnail'])): ?>
                        <img src="<?= h($course['thumbnail']) ?>" 
                             alt="<?= h($course['title']) ?>" 
                             class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        <?php endif; ?>
                        <div>
                            <h5 class="fw-bold mb-1"><?= h($course['title']) ?></h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-<?= $course['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $course['is_active'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                                <span class="badge bg-info"><?= h($course['difficulty']) ?></span>
                                <span class="badge bg-<?= $course['is_free'] ? 'success' : 'warning' ?>">
                                    <?= $course['is_free'] ? 'Gratuito' : 'Pago' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-4">
                        <div class="text-center">
                            <h4 class="text-primary fw-bold mb-0"><?= count($videos) ?></h4>
                            <small class="text-muted">Vídeos</small>
                        </div>
                        <div class="text-center">
                            <h4 class="text-success fw-bold mb-0">
                                <?php 
                                $totalDuration = array_sum(array_column($videos, 'duration_seconds'));
                                echo floor($totalDuration / 60) . 'min';
                                ?>
                            </h4>
                            <small class="text-muted">Duração Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Videos Management -->
    <div class="card glass">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-play-circle me-2"></i>
                Lista de Vídeos
            </h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="reorderVideos()">
                    <i class="bi bi-arrow-up-down me-1"></i>
                    Reordenar
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="bulkActivate()">
                    <i class="bi bi-check-all me-1"></i>
                    Ativar Selecionados
                </button>
            </div>
        </div>
        <div class="card-body p-0">
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th width="80">Ordem</th>
                                <th>Título</th>
                                <th width="100">Duração</th>
                                <th width="100">Tipo</th>
                                <th width="120">Status</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="videos-list">
                            <?php foreach ($videos as $video): ?>
                            <tr data-video-id="<?= $video['id'] ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input video-checkbox" 
                                           value="<?= $video['id'] ?>">
                                </td>
                                <td>
                                    <span class="badge bg-secondary fs-6"><?= h($video['order_position']) ?></span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= h($video['title']) ?></h6>
                                        <?php if (!empty($video['description'])): ?>
                                            <small class="text-muted"><?= h(substr($video['description'], 0, 100)) ?><?= strlen($video['description']) > 100 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <?= floor($video['duration_seconds'] / 60) ?>:<?= str_pad($video['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($video['video_type']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-<?= $video['is_active'] ? 'success' : 'secondary' ?> fs-7">
                                            <?= $video['is_active'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                        <?php if ($video['is_preview']): ?>
                                            <span class="badge bg-warning fs-7">Preview</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/courses/watch-video/<?= h($video['id']) ?>" 
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
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reorder Modal -->
<div class="modal fade" id="reorderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-up-down me-2"></i>
                    Reordenar Vídeos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Arraste os vídeos para reordená-los:</p>
                <div id="sortable-videos" class="list-group">
                    <?php foreach ($videos as $video): ?>
                    <div class="list-group-item d-flex align-items-center" data-video-id="<?= $video['id'] ?>">
                        <i class="bi bi-grip-vertical text-muted me-3" style="cursor: move;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?= h($video['title']) ?></h6>
                            <small class="text-muted">
                                Posição atual: <?= $video['order_position'] ?> | 
                                Duração: <?= floor($video['duration_seconds'] / 60) ?>:<?= str_pad($video['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT) ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveOrder()">Salvar Ordem</button>
            </div>
        </div>
    </div>
</div>

<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.video-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Delete video function
function deleteVideo(videoId) {
    if (confirm('Tem certeza que deseja excluir este vídeo? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/courses/delete-video/' + videoId;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_method';
        csrfInput.value = 'DELETE';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk activate function
function bulkActivate() {
    const selectedVideos = Array.from(document.querySelectorAll('.video-checkbox:checked')).map(cb => cb.value);
    
    if (selectedVideos.length === 0) {
        alert('Selecione pelo menos um vídeo para ativar.');
        return;
    }
    
    if (confirm(`Ativar ${selectedVideos.length} vídeo(s) selecionado(s)?`)) {
        // Implementation for bulk activation
        console.log('Activating videos:', selectedVideos);
    }
}

// Reorder functionality
function reorderVideos() {
    const modal = new bootstrap.Modal(document.getElementById('reorderModal'));
    modal.show();
}

function saveOrder() {
    const videoOrder = Array.from(document.querySelectorAll('#sortable-videos .list-group-item')).map((item, index) => ({
        id: item.dataset.videoId,
        order: index + 1
    }));
    
    console.log('New order:', videoOrder);
    // Implementation for saving order
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('reorderModal'));
    modal.hide();
}

// Make videos sortable (requires jQuery UI or similar library)
// This is a placeholder - you would need to implement actual drag & drop functionality
</script>

<style>
.card {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: var(--bs-gray-700);
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:not(:last-child) {
    margin-right: 2px;
}

#sortable-videos .list-group-item {
    cursor: move;
    border: 1px solid rgba(0,0,0,0.125);
    margin-bottom: 2px;
}

#sortable-videos .list-group-item:hover {
    background-color: var(--bs-light);
}
</style>