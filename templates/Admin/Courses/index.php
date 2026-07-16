<?php
/**
 * @var \App\View\AppView $this
 * @var array $courses
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="courses index content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-play-circle-fill me-2 icon-accent"></i>
                Cursos em Vídeo
            </h1>
            <p class="text-muted mb-0">Gerenciar cursos e conteúdo educacional</p>
        </div>
        <a href="/admin/courses/add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Novo Curso
        </a>
    </div>

    <?php if (!empty($courses)): ?>
        <!-- Courses Grid -->
        <div class="row g-3">
            <?php foreach ($courses as $course): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card glass h-100 course-card">
                        <!-- Course Thumbnail -->
                        <div class="course-thumbnail">
                            <?php if (!empty($course['thumbnail'])): ?>
                                <img src="<?= h($course['thumbnail']) ?>" class="card-img-top" alt="<?= h($course['title']) ?>">
                            <?php else: ?>
                                <div class="placeholder-thumbnail">
                                    <i class="bi bi-play-circle-fill"></i>
                                </div>
                            <?php endif; ?>

                            <div class="course-status">
                                <?php if ($course['is_active']): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </div>

                            <div class="course-price">
                                <?php if ($course['is_free']): ?>
                                    <span class="badge bg-primary">Gratuito</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold mb-1"><?= h($course['title']) ?></h6>

                            <p class="card-text text-muted small mb-2 clamp-2">
                                <?= h(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?>
                            </p>

                            <!-- Course Meta -->
                            <div class="course-meta mb-2">
                                <span>
                                    <i class="bi bi-mortarboard"></i>
                                    <?= h($course['difficulty']) ?>
                                </span>
                                <span>
                                    <i class="bi bi-play-fill"></i>
                                    <?= (int)$course['video_count'] ?> vídeo<?= (int)$course['video_count'] !== 1 ? 's' : '' ?>
                                </span>
                                <span>
                                    <i class="bi bi-clock"></i>
                                    <?php
                                    $totalMinutes = floor(($course['total_duration'] ?? 0) / 60);
                                    if ($totalMinutes < 60) {
                                        echo $totalMinutes . ' min';
                                    } else {
                                        $hours = floor($totalMinutes / 60);
                                        $minutes = $totalMinutes % 60;
                                        echo $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'min' : '');
                                    }
                                    ?>
                                </span>
                                <?php if (!empty($course['instructor'])): ?>
                                    <span>
                                        <i class="bi bi-person"></i>
                                        <?= h($course['instructor']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($course['category'])): ?>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-tag me-1"></i><?= h($course['category']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="course-actions mt-auto">
                                <a href="/admin/courses/view/<?= $course['id'] ?>" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye me-1"></i>Visualizar
                                </a>
                                <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-light btn-sm" title="Vídeos">
                                    <i class="bi bi-play-circle"></i>
                                </a>
                                <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="/admin/courses/delete/<?= $course['id'] ?>" class="d-flex"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este curso?');">
                                    <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-block">
            <i class="bi bi-play-circle"></i>
            <h5 class="text-muted mb-2">Nenhum curso encontrado</h5>
            <p class="text-muted mb-3">Comece criando seu primeiro curso em vídeo</p>
            <a href="/admin/courses/add" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Criar Primeiro Curso
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .course-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg), 0 0 20px rgba(0, 255, 136, 0.15);
    }

    .course-thumbnail {
        position: relative;
        height: 150px;
        overflow: hidden;
    }

    .course-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .placeholder-thumbnail {
        width: 100%;
        height: 100%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
    }

    .course-status {
        position: absolute;
        top: 8px;
        left: 8px;
    }

    .course-price {
        position: absolute;
        top: 8px;
        right: 8px;
    }

    .clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5em;
    }

    .course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 0.75rem;
        font-size: 0.78rem;
        color: var(--text-muted, #b0b0b0);
    }

    .course-meta i {
        color: #00ff88;
        margin-right: 0.2rem;
    }

    .course-actions {
        display: flex;
        align-items: stretch;
        gap: 0.35rem;
    }

    .course-actions form button {
        height: 100%;
    }

    .empty-block {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted, #b0b0b0);
    }

    .empty-block i {
        font-size: 3rem;
        opacity: 0.4;
        display: block;
        margin-bottom: 1rem;
    }
</style>
