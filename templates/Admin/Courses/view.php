<?php
/**
 * @var \App\View\AppView $this
 * @var array $course
 * @var array $videos
 * @var array $stats
 * @var array $enrolledStudents
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="courses view content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                <i class="bi bi-eye me-2 icon-accent"></i>
                <?= h($course['title']) ?>
            </h1>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-<?= $course['is_active'] ? 'success' : 'secondary' ?>">
                    <i class="bi bi-<?= $course['is_active'] ? 'check-circle' : 'x-circle' ?> me-1"></i>
                    <?= $course['is_active'] ? 'Ativo' : 'Inativo' ?>
                </span>
                <span class="badge bg-info">
                    <i class="bi bi-mortarboard me-1"></i>
                    <?= h($course['difficulty']) ?>
                </span>
                <span class="badge bg-<?= $course['is_free'] ? 'success' : 'warning' ?>">
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

    <div class="row g-3">
        <!-- Course Information -->
        <div class="col-lg-8">
            <!-- Course Details Card -->
            <div class="card glass mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Thumbnail -->
                        <?php if (!empty($course['thumbnail'])): ?>
                        <div class="col-md-4">
                            <img src="<?= h($course['thumbnail']) ?>"
                                 alt="<?= h($course['title']) ?>"
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 160px; object-fit: cover;">
                        </div>
                        <?php endif; ?>

                        <!-- Course Info -->
                        <div class="col-md-<?= !empty($course['thumbnail']) ? '8' : '12' ?>">
                            <h6 class="section-title">
                                <i class="bi bi-info-circle me-2"></i>
                                Informações do Curso
                            </h6>

                            <div class="row g-2">
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-tag me-1"></i> Categoria</span>
                                    <strong><?= h($course['category']) ?: 'Não informado' ?></strong>
                                </div>
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-person me-1"></i> Instrutor</span>
                                    <strong><?= h($course['instructor']) ?: 'Não informado' ?></strong>
                                </div>
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-clock me-1"></i> Duração</span>
                                    <strong>
                                        <?php if ($course['duration_minutes']): ?>
                                            <?= floor($course['duration_minutes'] / 60) ?>h <?= $course['duration_minutes'] % 60 ?>min
                                        <?php else: ?>
                                            Não informado
                                        <?php endif; ?>
                                    </strong>
                                </div>
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-sort-numeric-up me-1"></i> Posição</span>
                                    <strong><?= h($course['order_position']) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <?php if (!empty($course['description'])): ?>
                        <div class="col-12">
                            <h6 class="section-title">
                                <i class="bi bi-text-paragraph me-2"></i>
                                Descrição
                            </h6>
                            <p class="text-muted mb-0"><?= nl2br(h($course['description'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Videos List -->
            <div class="card glass mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="section-title mb-0 flex-grow-1">
                            <i class="bi bi-collection-play me-2"></i>
                            Vídeos do Curso (<?= count($videos) ?>)
                        </h6>
                        <a href="/admin/courses/add-video/<?= $course['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>
                            Adicionar
                        </a>
                    </div>

                    <?php if (empty($videos)): ?>
                        <div class="empty-block">
                            <i class="bi bi-collection-play"></i>
                            <p class="mb-3">Nenhum vídeo cadastrado.</p>
                            <a href="/admin/courses/add-video/<?= $course['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-2"></i>
                                Adicionar Primeiro Vídeo
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row-list">
                            <?php foreach ($videos as $video): ?>
                            <div class="row-item">
                                <div class="row-item-main">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-secondary"><?= h($video['order_position']) ?></span>
                                        <span class="fw-semibold"><?= h($video['title']) ?></span>
                                        <?php if ($video['is_preview']): ?>
                                            <span class="badge bg-info">Preview</span>
                                        <?php endif; ?>
                                        <?php if (!$video['is_active']): ?>
                                            <span class="badge bg-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($video['description'])): ?>
                                        <p class="text-muted small mb-1"><?= h($video['description']) ?></p>
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

            <!-- Alunos Inscritos -->
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-people me-2"></i>
                        Alunos Inscritos (<?= count($enrolledStudents) ?>)
                    </h6>

                    <?php if (empty($enrolledStudents)): ?>
                        <p class="text-muted mb-0">Nenhum aluno inscrito neste curso.</p>
                    <?php else: ?>
                        <div class="row-list">
                            <?php foreach ($enrolledStudents as $enrollment): ?>
                                <div class="row-item">
                                    <div class="row-item-main">
                                        <span class="fw-semibold"><?= h($enrollment->student->name ?? 'Aluno #' . $enrollment->student_id) ?></span>
                                        <div class="text-muted small">
                                            Inscrito em <?= h($enrollment->enrolled_at?->format('d/m/Y H:i')) ?>
                                        </div>
                                    </div>
                                    <form method="post" action="/admin/courses/unenroll-student/<?= $course['id'] ?>/<?= $enrollment->student_id ?>"
                                          onsubmit="return confirm('Desinscrever este aluno do curso?');">
                                        <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-person-dash me-1"></i>
                                            Desinscrever
                                        </button>
                                    </form>
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
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-graph-up me-2"></i>
                        Estatísticas
                    </h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-value"><?= count($videos) ?></div>
                                <div class="stat-label">Vídeos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-value stat-value-success"><?= (int)$stats['total_enrollments'] ?></div>
                                <div class="stat-label">Inscritos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Metadata -->
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-calendar me-2"></i>
                        Informações do Sistema
                    </h6>
                    <ul class="meta-list">
                        <li>
                            <span>ID do Curso</span>
                            <strong>#<?= h($course['id']) ?></strong>
                        </li>
                        <li>
                            <span>Criado em</span>
                            <strong><?= date('d/m/Y H:i', strtotime($course['created'])) ?></strong>
                        </li>
                        <li>
                            <span>Modificado em</span>
                            <strong><?= date('d/m/Y H:i', strtotime($course['modified'])) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

    .meta-row {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        margin-bottom: 0.5rem;
    }

    .meta-row span {
        font-size: 0.8rem;
        color: var(--text-muted, #b0b0b0);
    }

    .meta-row strong {
        color: var(--text-primary, #fff);
        font-weight: 600;
    }

    .row-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .row-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--card-border, rgba(255, 255, 255, 0.1));
        border-radius: var(--radius-sm, 8px);
        padding: 0.75rem 1rem;
        transition: background 0.2s ease;
    }

    .row-item:hover {
        background: rgba(0, 255, 136, 0.05);
    }

    .row-item-main {
        min-width: 0;
        flex-grow: 1;
    }

    .empty-block {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--text-muted, #b0b0b0);
    }

    .empty-block i {
        font-size: 2.5rem;
        opacity: 0.4;
        display: block;
        margin-bottom: 0.75rem;
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-sm, 8px);
        padding: 0.75rem 0.5rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00ff88;
    }

    .stat-value-success {
        color: #00ff88;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--text-muted, #b0b0b0);
    }

    .meta-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
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
