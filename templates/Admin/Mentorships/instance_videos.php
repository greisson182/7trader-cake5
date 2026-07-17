<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MentorshipInstance $instance
 * @var array $videos
 * @var array $modules
 * @var array $ungroupedVideos
 */
$modules = $modules ?? [];
$ungroupedVideos = $ungroupedVideos ?? $videos;
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="mentorship-videos content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                <i class="bi bi-collection-play me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Gerenciar Vídeos
            </h1>
            <p class="text-muted mb-0">
                Mentoria Particular: <?= h($instance->mentorship->name) ?> ·
                Aluno: <strong><?= h($instance->student->name ?? 'Aluno removido') ?></strong>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/mentorships/add-instance-module/<?= $instance->id ?>" class="btn btn-outline-light">
                <i class="bi bi-folder-plus me-2"></i>
                Adicionar Módulo
            </a>
            <a href="/admin/mentorships/add-instance-video/<?= $instance->id ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Adicionar Vídeo
            </a>
            <a href="/admin/mentorships/instances/<?= $instance->mentorship_id ?>" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Instance Info Bar -->
    <div class="card glass mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-1"><?= h($instance->student->name ?? 'Aluno removido') ?></h5>
                    <span class="badge bg-warning text-dark">Mentoria Particular</span>
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
                                $totalDurationMinutes = floor($totalDuration / 60);
                                $totalDurationHours = floor($totalDurationMinutes / 60);
                                echo $totalDurationHours > 0
                                    ? $totalDurationHours . 'h ' . ($totalDurationMinutes % 60) . 'min'
                                    : $totalDurationMinutes . 'min';
                                ?>
                            </h4>
                            <small class="text-muted">Duração Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Renderiza a tabela de vídeos de um módulo (ou dos vídeos sem módulo).
    $renderVideoTable = function (array $moduleVideos, $moduleIdAttr) {
        ?>
        <div class="table-responsive admin-table">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="30"></th>
                        <th width="50">
                            <input type="checkbox" class="form-check-input video-select-all">
                        </th>
                        <th width="80">Ordem</th>
                        <th>Título</th>
                        <th width="100">Duração</th>
                        <th width="100">Tipo</th>
                        <th width="120">Status</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody class="video-sortable" data-module-id="<?= h((string)$moduleIdAttr) ?>">
                    <?php if (empty($moduleVideos)): ?>
                    <tr class="video-drop-placeholder">
                        <td colspan="8" class="text-center text-muted py-3">Arraste vídeos para cá</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($moduleVideos as $video): ?>
                    <tr data-video-id="<?= $video['id'] ?>">
                        <td class="drag-handle text-muted" title="Arrastar">
                            <i class="bi bi-grip-vertical"></i>
                        </td>
                        <td>
                            <input type="checkbox" class="form-check-input video-checkbox"
                                   value="<?= $video['id'] ?>">
                        </td>
                        <td>
                            <span class="badge bg-secondary fs-6 order-badge"><?= h($video['order_position']) ?></span>
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
                            <?php
                            $vHours = floor($video['duration_seconds'] / 3600);
                            $vMinutes = floor(($video['duration_seconds'] % 3600) / 60);
                            $vSeconds = $video['duration_seconds'] % 60;
                            ?>
                            <span class="text-muted">
                                <?= $vHours > 0 ? sprintf('%d:%02d:%02d', $vHours, $vMinutes, $vSeconds) : sprintf('%d:%02d', $vMinutes, $vSeconds) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= ucfirst($video['video_type']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $video['is_active'] ? 'success' : 'secondary' ?> fs-7">
                                <?= $video['is_active'] ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/admin/mentorships/watch-video/<?= h($video['id']) ?>"
                                   class="btn btn-outline-primary btn-sm" title="Assistir">
                                    <i class="bi bi-play"></i>
                                </a>
                                <a href="/admin/mentorships/edit-instance-video/<?= $video['id'] ?>"
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
        <?php
    };
    ?>

    <?php if (empty($videos)): ?>
        <!-- Nenhum vídeo nesta instância -->
        <div class="card glass">
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="bi bi-collection-play display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum vídeo cadastrado</h5>
                    <p class="text-muted mb-4">Suba as gravações das sessões deste aluno conforme forem acontecendo.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="/admin/mentorships/add-instance-module/<?= $instance->id ?>" class="btn btn-outline-light">
                            <i class="bi bi-folder-plus me-2"></i>
                            Adicionar Módulo
                        </a>
                        <a href="/admin/mentorships/add-instance-video/<?= $instance->id ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Adicionar Primeiro Vídeo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Módulos -->
        <?php foreach ($modules as $module): ?>
            <div class="card glass mb-3">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="flex-grow-1">
                            <h6 class="section-title mb-0">
                                <i class="bi bi-folder2-open me-2"></i>
                                <?= h($module->title) ?>
                                <span class="badge bg-secondary ms-2 module-video-count" data-module-id="<?= $module->id ?>"><?= count($module->mentorship_videos) ?> vídeo<?= count($module->mentorship_videos) !== 1 ? 's' : '' ?></span>
                                <?php if (empty($module->is_active)): ?>
                                    <span class="badge bg-secondary ms-1">Inativo</span>
                                <?php endif; ?>
                            </h6>
                            <?php if (!empty($module->description)): ?>
                                <p class="text-muted small mb-3"><?= h($module->description) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <a href="/admin/mentorships/add-instance-video/<?= $instance->id ?>" class="btn btn-outline-primary btn-sm" title="Adicionar vídeo">
                                <i class="bi bi-plus-circle"></i>
                            </a>
                            <a href="/admin/mentorships/edit-instance-module/<?= $module->id ?>" class="btn btn-outline-warning btn-sm" title="Editar módulo">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="post" action="/admin/mentorships/delete-instance-module/<?= $module->id ?>" class="d-flex"
                                  onsubmit="return confirm('Excluir este módulo? Os vídeos não serão apagados, apenas ficam sem módulo.');">
                                <input type="hidden" name="_csrfToken" value="<?= h($csrfToken ?? '') ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir módulo">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?= $renderVideoTable($module->mentorship_videos, $module->id) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Vídeos sem módulo -->
        <div class="card glass">
            <div class="card-body pb-0">
                <h6 class="section-title mb-0">
                    <i class="bi bi-collection-play me-2"></i>
                    Sem Módulo
                    <span class="badge bg-secondary ms-2 module-video-count" data-module-id=""><?= count($ungroupedVideos) ?> vídeo<?= count($ungroupedVideos) !== 1 ? 's' : '' ?></span>
                </h6>
            </div>
            <div class="card-body p-0">
                <?= $renderVideoTable($ungroupedVideos, '') ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.querySelectorAll('.video-select-all').forEach(function(selectAll) {
    selectAll.addEventListener('change', function() {
        const table = this.closest('table');
        table.querySelectorAll('.video-checkbox').forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
    });
});

function deleteVideo(videoId) {
    if (confirm('Tem certeza que deseja excluir este vídeo? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/mentorships/delete-instance-video/' + videoId;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_method';
        csrfInput.value = 'DELETE';
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }
}

function updateModuleCount(moduleId, delta) {
    const badge = document.querySelector('.module-video-count[data-module-id="' + moduleId + '"]');
    if (!badge) return;
    const current = parseInt(badge.textContent, 10) || 0;
    const next = Math.max(0, current + delta);
    badge.textContent = next + ' vídeo' + (next !== 1 ? 's' : '');
}

function refreshTbodyState(tbody) {
    const rows = tbody.querySelectorAll('tr[data-video-id]');
    const placeholder = tbody.querySelector('.video-drop-placeholder');
    if (rows.length === 0 && !placeholder) {
        const tr = document.createElement('tr');
        tr.className = 'video-drop-placeholder';
        tr.innerHTML = '<td colspan="8" class="text-center text-muted py-3">Arraste vídeos para cá</td>';
        tbody.appendChild(tr);
    } else if (rows.length > 0 && placeholder) {
        placeholder.remove();
    }

    rows.forEach(function(row, index) {
        const orderBadge = row.querySelector('.order-badge');
        if (orderBadge) {
            orderBadge.textContent = index + 1;
        }
    });
}

document.querySelectorAll('.video-sortable').forEach(function(tbody) {
    new Sortable(tbody, {
        group: 'instance-videos',
        handle: '.drag-handle',
        animation: 150,
        forceFallback: true,
        filter: '.video-drop-placeholder',
        onStart: function() {
            document.body.classList.add('dragging-active');
        },
        onEnd: function(evt) {
            document.body.classList.remove('dragging-active');
            const fromTbody = evt.from;
            const toTbody = evt.to;
            const targetModuleId = toTbody.dataset.moduleId || '';

            const videoIds = Array.from(toTbody.querySelectorAll('tr[data-video-id]'))
                .map(function(row) { return parseInt(row.dataset.videoId, 10); });

            refreshTbodyState(toTbody);
            if (fromTbody !== toTbody) {
                refreshTbodyState(fromTbody);
                updateModuleCount(toTbody.dataset.moduleId || '', 1);
                updateModuleCount(fromTbody.dataset.moduleId || '', -1);
            }

            if (videoIds.length === 0) {
                return;
            }

            fetch('/admin/mentorships/reorder-instance-videos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    module_id: targetModuleId,
                    video_ids: videoIds
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (!data.success) {
                    alert('Erro ao reordenar vídeos: ' + (data.message || 'Tente novamente.'));
                    location.reload();
                }
            })
            .catch(function() {
                alert('Erro ao reordenar vídeos. A página será recarregada.');
                location.reload();
            });
        }
    });
});
</script>

<style>
.section-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-primary, #fff);
    padding-bottom: var(--space-sm, 0.5rem);
    border-bottom: 1px solid var(--glass-border, rgba(0, 255, 136, 0.2));
}

.section-title i {
    color: #00ff88;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:not(:last-child) {
    margin-right: 2px;
}

.drag-handle {
    cursor: grab;
    text-align: center;
}

.drag-handle:active {
    cursor: grabbing;
}

.video-sortable tr.sortable-ghost {
    opacity: 0.4;
    background: var(--glass-border, rgba(0, 255, 136, 0.2));
}

.video-drop-placeholder td {
    border-style: dashed;
}

body.dragging-active,
body.dragging-active * {
    user-select: none !important;
    -webkit-user-select: none !important;
}
</style>
