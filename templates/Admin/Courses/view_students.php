<?php
$title = htmlspecialchars($course['title']) . ' - 7 Trader';
$description = htmlspecialchars(substr($course['description'], 0, 160));
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-dark border-secondary">
                <li class="breadcrumb-item">
                    <a href="/site/courses" class="text-success text-decoration-none">
                        <i class="fas fa-graduation-cap me-1"></i>Cursos
                    </a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    <?= htmlspecialchars($course['title']) ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Informações do Curso -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-dark border-secondary">
                    <div class="card-body">
                        <!-- Header do Curso -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h1 class="text-white mb-3"><?= htmlspecialchars($course['title']) ?></h1>
                                
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php
                                    $difficultyColors = [
                                        'Iniciante' => 'success',
                                        'Intermediário' => 'warning', 
                                        'Avançado' => 'danger'
                                    ];
                                    $difficultyColor = $difficultyColors[$course['difficulty']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $difficultyColor ?> fs-6">
                                        <?= htmlspecialchars($course['difficulty']) ?>
                                    </span>
                                    
                                    <?php if ($course['is_free']): ?>
                                        <span class="badge bg-success fs-6">GRATUITO</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary fs-6">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ($isEnrolled): ?>
                                        <?php if ($enrollment && $enrollment['completed_at']): ?>
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check-circle me-1"></i>CONCLUÍDO
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info fs-6">
                                                <i class="fas fa-play me-1"></i>EM ANDAMENTO
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-3">
                                        <small class="text-muted d-block">Vídeos</small>
                                        <strong class="text-success"><?= $course['video_count'] ?></strong>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted d-block">Duração</small>
                                        <strong class="text-success">
                                            <?php
                                            $totalMinutes = round($course['total_duration'] / 60);
                                            $hours = floor($totalMinutes / 60);
                                            $minutes = $totalMinutes % 60;
                                            echo $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                                            ?>
                                        </strong>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted d-block">Categoria</small>
                                        <strong class="text-success"><?= htmlspecialchars($course['category']) ?></strong>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted d-block">Posição</small>
                                        <strong class="text-success">#<?= $course['order_position'] ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-center">
                                <?php if ($course['thumbnail']): ?>
                                    <img src="<?= htmlspecialchars($course['thumbnail']) ?>" 
                                         class="img-fluid rounded border border-secondary" 
                                         alt="<?= htmlspecialchars($course['title']) ?>"
                                         style="max-height: 200px;">
                                <?php else: ?>
                                    <div class="bg-gradient rounded d-flex align-items-center justify-content-center border border-secondary" 
                                         style="height: 200px; background: linear-gradient(45deg, #28a745, #20c997) !important;">
                                        <i class="fas fa-play-circle fa-4x text-white opacity-75"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Instrutor -->
                        <?php if ($course['instructor']): ?>
                            <div class="mb-4">
                                <h6 class="text-success mb-2">
                                    <i class="fas fa-user-tie me-2"></i>Instrutor
                                </h6>
                                <p class="text-white mb-0"><?= htmlspecialchars($course['instructor']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Descrição -->
                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-info-circle me-2"></i>Sobre o Curso
                            </h6>
                            <div class="text-light">
                                <?= nl2br(htmlspecialchars($course['description'])) ?>
                            </div>
                        </div>
                        
                        <!-- Ações -->
                        <?php if (!$isEnrolled): ?>
                            <div class="d-grid gap-2 d-md-flex">
                                <?php if ($course['is_free']): ?>
                                    <a href="/site/courses/enroll/<?= $course['id'] ?>" 
                                       class="btn btn-success btn-lg">
                                        <i class="fas fa-graduation-cap me-2"></i>Inscrever-se Gratuitamente
                                    </a>
                                <?php else: ?>
                                    <a href="/site/courses/purchase/<?= $course['id'] ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Comprar Curso - R$ <?= number_format($course['price'], 2, ',', '.') ?>
                                    </a>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>Pagamento seguro • 
                                            <i class="fas fa-clock me-1"></i>Acesso vitalício • 
                                            <i class="fas fa-certificate me-1"></i>Certificado incluído
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Vídeos -->
            <div class="col-lg-4">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Conteúdo do Curso
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($videos)): ?>
                            <div class="p-4 text-center">
                                <i class="fas fa-video-slash fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Nenhum vídeo disponível</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($videos as $index => $video): ?>
                                    <?php
                                    $canWatch = $isEnrolled || $course['is_free'] || $video['is_preview'];
                                    $isWatched = isset($progress[$video['id']]) && $progress[$video['id']]['completed_at'];
                                    $isInProgress = isset($progress[$video['id']]) && !$progress[$video['id']]['completed_at'];
                                    
                                    // Duração formatada
                                    $minutes = floor($video['duration_seconds'] / 60);
                                    $seconds = $video['duration_seconds'] % 60;
                                    $durationText = sprintf('%d:%02d', $minutes, $seconds);
                                    ?>
                                    
                                    <div class="list-group-item bg-dark border-secondary text-white d-flex align-items-center">
                                        <div class="me-3">
                                            <?php if ($isWatched): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php elseif ($isInProgress): ?>
                                                <i class="fas fa-play-circle text-info"></i>
                                            <?php elseif ($canWatch): ?>
                                                <i class="fas fa-play text-muted"></i>
                                            <?php else: ?>
                                                <i class="fas fa-lock text-warning"></i>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 text-white">
                                                        <?= ($index + 1) ?>. <?= htmlspecialchars($video['title']) ?>
                                                    </h6>
                                                    <?php if ($video['is_preview']): ?>
                                                        <small class="badge bg-info">PREVIEW</small>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted"><?= $durationText ?></small>
                                            </div>
                                            
                                            <?php if ($canWatch): ?>
                                                <div class="mt-2">
                                                    <a href="/site/courses/watch/<?= $course['id'] ?>/<?= $video['id'] ?>" 
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-play me-1"></i>Assistir
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-2">
                                                    <small class="text-warning">
                                                        <i class="fas fa-lock me-1"></i>Inscreva-se para assistir
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Progresso (se inscrito) -->
                <?php if ($isEnrolled && !empty($videos)): ?>
                    <div class="card bg-dark border-secondary mt-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Seu Progresso
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $totalVideos = count($videos);
                            $watchedVideos = 0;
                            foreach ($videos as $video) {
                                if (isset($progress[$video['id']]) && $progress[$video['id']]['completed_at']) {
                                    $watchedVideos++;
                                }
                            }
                            $progressPercent = $totalVideos > 0 ? round(($watchedVideos / $totalVideos) * 100) : 0;
                            ?>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-white">Progresso do Curso</small>
                                    <small class="text-white"><?= $progressPercent ?>%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= $progressPercent ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted d-block">Assistidos</small>
                                    <strong class="text-success"><?= $watchedVideos ?>/<?= $totalVideos ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Restantes</small>
                                    <strong class="text-warning"><?= $totalVideos - $watchedVideos ?></strong>
                                </div>
                            </div>
                            
                            <?php if ($enrollment): ?>
                                <hr class="border-secondary">
                                <small class="text-muted">
                                    Inscrito em: <?= date('d/m/Y H:i', strtotime($enrollment['enrolled_at'])) ?>
                                </small>
                                <?php if ($enrollment['completed_at']): ?>
                                    <br>
                                    <small class="text-success">
                                        <i class="fas fa-trophy me-1"></i>
                                        Concluído em: <?= date('d/m/Y H:i', strtotime($enrollment['completed_at'])) ?>
                                    </small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
}

.list-group-item:hover {
    background-color: #2d2d2d !important;
}
</style>