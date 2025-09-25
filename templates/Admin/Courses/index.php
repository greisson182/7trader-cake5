<?php
/**
 * @var \App\View\AppView $this
 * @var array $courses
 */
?>
<div class="courses index content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-play-circle-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Cursos em Vídeo
            </h1>
            <p class="text-muted mb-0">Gerenciar cursos e conteúdo educacional</p>
        </div>
        <a href="/admin/courses/add" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Novo Curso
        </a>
    </div>

    <?php if (!empty($courses)): ?>
        <!-- Courses Grid -->
        <div class="row g-4">
            <?php foreach ($courses as $course): ?>
                <div class="col-lg-4 col-md-6">
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
                            
                            <!-- Course Status Badge -->
                            <div class="course-status">
                                <?php if ($course['is_active']): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Free/Paid Badge -->
                            <div class="course-price">
                                <?php if ($course['is_free']): ?>
                                    <span class="badge bg-primary">Gratuito</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Course Title -->
                            <h5 class="card-title fw-bold mb-2"><?= h($course['title']) ?></h5>
                            
                            <!-- Course Description -->
                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                <?= h(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?>
                            </p>

                            <!-- Course Info -->
                            <div class="course-info mb-3">
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-mortarboard text-primary"></i>
                                            <span class="text-muted">Nível</span>
                                            <div class="fw-semibold">
                                                <span class="badge bg-<?php 
                                                    echo $course['difficulty'] === 'Iniciante' ? 'success' : 
                                                        ($course['difficulty'] === 'Intermediário' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?= h($course['difficulty']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-play-fill text-success"></i>
                                            <span class="text-muted">Vídeos</span>
                                            <div class="fw-semibold"><?= (int)$course['video_count'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-clock text-info"></i>
                                            <span class="text-muted">Duração</span>
                                            <div class="fw-semibold">
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-person text-warning"></i>
                                            <span class="text-muted">Instrutor</span>
                                            <div class="fw-semibold small"><?= h($course['instructor'] ?: 'N/A') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Category -->
                            <?php if (!empty($course['category'])): ?>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-tag me-1"></i><?= h($course['category']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Action Buttons -->
                            <div class="course-actions">
                                <div class="d-grid gap-2">
                                    <a href="/admin/courses/view/<?= $course['id'] ?>" class="btn btn-primary">
                                        <i class="bi bi-eye me-1"></i>Visualizar
                                    </a>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/courses/videos/<?= $course['id'] ?>" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-play-circle"></i> Vídeos
                                        </a>
                                        <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <form method="post" action="/admin/courses/delete/<?= $course['id'] ?>" style="display: inline;" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir este curso?');">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state text-center py-5">
            <div class="empty-icon mb-4">
                <i class="bi bi-play-circle" style="font-size: 4rem; color: var(--bs-gray-400);"></i>
            </div>
            <h3 class="text-muted mb-3">Nenhum curso encontrado</h3>
            <p class="text-muted mb-4">Comece criando seu primeiro curso em vídeo</p>
            <a href="/admin/courses/add" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Criar Primeiro Curso
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.course-card {
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.course-thumbnail {
    position: relative;
    height: 200px;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.course-status {
    position: absolute;
    top: 10px;
    left: 10px;
}

.course-price {
    position: absolute;
    top: 10px;
    right: 10px;
}

.info-item {
    text-align: center;
    padding: 8px;
}

.info-item i {
    display: block;
    margin-bottom: 4px;
    font-size: 1.2rem;
}

.empty-state {
    background: rgba(255,255,255,0.5);
    border-radius: 15px;
    margin: 2rem 0;
}
</style>