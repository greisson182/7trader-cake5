<div class="courses-students index content fade-in-up">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">
            <i class="bi bi-mortarboard-fill me-2 icon-accent"></i>
            Cursos de Trading
        </h1>
        <p class="text-muted mb-0">Aprenda a ler o movimento do mercado e identificar padrões de trading.</p>
    </div>

    <!-- Lista de Cursos -->
    <div class="card mb-4">
        <div class="card-body">
    <?php if (empty($courses)): ?>
        <div class="empty-block">
            <i class="bi bi-journal-x"></i>
            <h5 class="text-muted mb-2">Nenhum curso disponível</h5>
            <p class="text-muted mb-0">Em breve teremos novos cursos para você!</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($courses as $course): ?>
                <?php
                $isEnrolled = isset($enrollments[$course['id']]);
                $enrollment = $enrollments[$course['id']] ?? null;
                $isCompleted = $enrollment && $enrollment['completed_at'];

                $totalMinutes = round($course['total_duration'] / 60);
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                ?>

                <div class="col-lg-3 col-md-6">
                    <div class="card glass h-100 course-card">
                        <div class="course-thumbnail">
                            <?php if ($course['thumbnail']): ?>
                                <img src="<?= h($course['thumbnail']) ?>" class="card-img-top" alt="<?= h($course['title']) ?>">
                            <?php else: ?>
                                <div class="placeholder-thumbnail">
                                    <i class="bi bi-play-circle-fill"></i>
                                </div>
                            <?php endif; ?>

                            <div class="course-price">
                                <?php if ($course['is_free']): ?>
                                    <span class="badge bg-primary">Gratuito</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($isEnrolled): ?>
                                <div class="course-status">
                                    <?php if ($isCompleted): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Concluído</span>
                                    <?php else: ?>
                                        <span class="badge bg-info"><i class="bi bi-play-fill me-1"></i>Em andamento</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold mb-1"><?= h($course['title']) ?></h6>

                            <p class="card-text text-muted small mb-2 clamp-2">
                                <?= h(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?>
                            </p>

                            <div class="course-meta mb-2">
                                <span><i class="bi bi-mortarboard"></i><?= h($course['difficulty']) ?></span>
                                <span><i class="bi bi-play-fill"></i><?= (int)$course['video_count'] ?> vídeo<?= (int)$course['video_count'] !== 1 ? 's' : '' ?></span>
                                <span><i class="bi bi-clock"></i><?= $durationText ?></span>
                                <?php if ($course['instructor']): ?>
                                    <span><i class="bi bi-person"></i><?= h($course['instructor']) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($course['category']): ?>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-tag me-1"></i><?= h($course['category']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto">
                                <?php if ($isEnrolled): ?>
                                    <a href="/admin/courses/view-students/<?= $course['id'] ?>" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-play-fill me-1"></i>Continuar Curso
                                    </a>
                                <?php else: ?>
                                    <div class="d-grid gap-2">
                                        <a href="/admin/courses/view-students/<?= $course['id'] ?>" class="btn btn-outline-light btn-sm">
                                            <i class="bi bi-eye me-1"></i>Ver Detalhes
                                        </a>
                                        <?php if ($course['is_free']): ?>
                                            <a href="/admin/courses/enroll/<?= $course['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-mortarboard me-1"></i>Inscrever-se Grátis
                                            </a>
                                        <?php else: ?>
                                            <a href="/checkout/curso/<?= $course['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-credit-card me-1"></i>Comprar por R$ <?= number_format($course['price'], 2, ',', '.') ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
        </div>
    </div>
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
