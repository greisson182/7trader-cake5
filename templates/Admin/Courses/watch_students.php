<?php
$title = htmlspecialchars($video['title']) . ' - ' . htmlspecialchars($course['title']) . ' - 7 Trader';
$description = htmlspecialchars(substr($video['description'] ?? '', 0, 160));
?>

<div class="container-fluid py-3" style="background: #000; min-height: 100vh;">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb bg-dark border-secondary">
                <li class="breadcrumb-item">
                    <a href="/site/courses" class="text-success text-decoration-none">
                        <i class="fas fa-graduation-cap me-1"></i>Cursos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/site/courses/view/<?= $course['id'] ?>" class="text-success text-decoration-none">
                        <?= htmlspecialchars($course['title']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    <?= htmlspecialchars($video['title']) ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Player de Vídeo -->
            <div class="col-lg-9 mb-4">
                <div class="card bg-dark border-secondary">
                    <!-- Video Player -->
                    <div class="video-container position-relative">
                        <?php
                        $embedUrl = '';
                        if ($video['video_type'] === 'YouTube') {
                            // Extrair ID do YouTube
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video['video_url'], $matches);
                            $youtubeId = $matches[1] ?? '';
                            if ($youtubeId) {
                                $embedUrl = "https://www.youtube.com/embed/{$youtubeId}?enablejsapi=1&origin=" . urlencode($_SERVER['HTTP_HOST']);
                            }
                        } elseif ($video['video_type'] === 'Vimeo') {
                            // Extrair ID do Vimeo
                            preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $video['video_url'], $matches);
                            $vimeoId = $matches[3] ?? '';
                            if ($vimeoId) {
                                $embedUrl = "https://player.vimeo.com/video/{$vimeoId}";
                            }
                        } else {
                            $embedUrl = $video['video_url'];
                        }
                        ?>
                        
                        <?php if ($embedUrl): ?>
                            <div class="ratio ratio-16x9">
                                <iframe id="videoPlayer" 
                                        src="<?= htmlspecialchars($embedUrl) ?>" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen
                                        class="rounded">
                                </iframe>
                            </div>
                        <?php else: ?>
                            <div class="ratio ratio-16x9 bg-dark d-flex align-items-center justify-content-center">
                                <div class="text-center text-white">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                                    <h5>Erro ao carregar vídeo</h5>
                                    <p class="text-muted">URL do vídeo inválida ou não suportada</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informações do Vídeo -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="text-white mb-2"><?= htmlspecialchars($video['title']) ?></h3>
                                
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($video['video_type']) ?>
                                    </span>
                                    
                                    <?php if ($video['is_preview']): ?>
                                        <span class="badge bg-info">PREVIEW</span>
                                    <?php endif; ?>
                                    
                                    <span class="badge bg-secondary">
                                        <?php
                                        $minutes = floor($video['duration_seconds'] / 60);
                                        $seconds = $video['duration_seconds'] % 60;
                                        echo sprintf('%d:%02d', $minutes, $seconds);
                                        ?>
                                    </span>
                                    
                                    <span class="badge bg-success">
                                        Posição: #<?= $video['order_position'] ?>
                                    </span>
                                </div>
                                
                                <?php if ($video['description']): ?>
                                    <div class="mb-3">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-info-circle me-2"></i>Descrição
                                        </h6>
                                        <div class="text-light">
                                            <?= nl2br(htmlspecialchars($video['description'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Controles de Progresso -->
                                <div class="card bg-secondary">
                                    <div class="card-body text-center">
                                        <h6 class="text-white mb-3">Progresso do Vídeo</h6>
                                        
                                        <div class="mb-3">
                                            <div class="progress mb-2" style="height: 8px;">
                                                <div id="videoProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="text-white">
                                                <span id="watchTime">0:00</span> / 
                                                <span id="totalTime">
                                                    <?php
                                                    $minutes = floor($video['duration_seconds'] / 60);
                                                    $seconds = $video['duration_seconds'] % 60;
                                                    echo sprintf('%d:%02d', $minutes, $seconds);
                                                    ?>
                                                </span>
                                            </small>
                                        </div>
                                        
                                        <?php if ($progress && $progress['completed_at']): ?>
                                            <div class="alert alert-success py-2">
                                                <i class="fas fa-check-circle me-1"></i>
                                                <small>Vídeo Concluído!</small>
                                            </div>
                                        <?php else: ?>
                                            <button id="markCompleted" class="btn btn-success btn-sm" disabled>
                                                <i class="fas fa-check me-1"></i>Marcar como Concluído
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Vídeos -->
            <div class="col-lg-3">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Vídeos do Curso
                        </h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            <?php foreach ($allVideos as $index => $courseVideo): ?>
                                <?php
                                $canWatch = true; // Já verificado no controller
                                $isCurrentVideo = $courseVideo['id'] == $video['id'];
                                $isWatched = isset($progress[$courseVideo['id']]) && $progress[$courseVideo['id']]['completed_at'];
                                $isInProgress = isset($progress[$courseVideo['id']]) && !$progress[$courseVideo['id']]['completed_at'];
                                
                                // Duração formatada
                                $minutes = floor($courseVideo['duration_seconds'] / 60);
                                $seconds = $courseVideo['duration_seconds'] % 60;
                                $durationText = sprintf('%d:%02d', $minutes, $seconds);
                                ?>
                                
                                <div class="list-group-item bg-dark border-secondary text-white <?= $isCurrentVideo ? 'border-success' : '' ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <?php if ($isCurrentVideo): ?>
                                                <i class="fas fa-play-circle text-success"></i>
                                            <?php elseif ($isWatched): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php elseif ($isInProgress): ?>
                                                <i class="fas fa-play-circle text-info"></i>
                                            <?php else: ?>
                                                <i class="fas fa-play text-muted"></i>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 text-white small">
                                                        <?= ($index + 1) ?>. <?= htmlspecialchars($courseVideo['title']) ?>
                                                    </h6>
                                                    <div class="d-flex gap-1 mb-1">
                                                        <?php if ($courseVideo['is_preview']): ?>
                                                            <small class="badge bg-info">PREVIEW</small>
                                                        <?php endif; ?>
                                                        <?php if ($isCurrentVideo): ?>
                                                            <small class="badge bg-success">ATUAL</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <small class="text-muted ms-2"><?= $durationText ?></small>
                                            </div>
                                            
                                            <?php if (!$isCurrentVideo): ?>
                                                <div class="mt-1">
                                                    <a href="/site/courses/watch/<?= $course['id'] ?>/<?= $courseVideo['id'] ?>" 
                                                       class="btn btn-sm btn-outline-success btn-sm">
                                                        <i class="fas fa-play me-1"></i>Assistir
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Navegação -->
                <div class="card bg-dark border-secondary mt-3">
                    <div class="card-body">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-arrows-alt-h me-2"></i>Navegação
                        </h6>
                        
                        <div class="d-grid gap-2">
                            <?php
                            // Encontrar vídeo anterior e próximo
                            $currentIndex = array_search($video['id'], array_column($allVideos, 'id'));
                            $prevVideo = $currentIndex > 0 ? $allVideos[$currentIndex - 1] : null;
                            $nextVideo = $currentIndex < count($allVideos) - 1 ? $allVideos[$currentIndex + 1] : null;
                            ?>
                            
                            <?php if ($prevVideo): ?>
                                <a href="/site/courses/watch/<?= $course['id'] ?>/<?= $prevVideo['id'] ?>" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-chevron-left me-2"></i>Anterior
                                </a>
                            <?php endif; ?>
                            
                            <a href="/site/courses/view/<?= $course['id'] ?>" 
                               class="btn btn-outline-success">
                                <i class="fas fa-list me-2"></i>Ver Curso
                            </a>
                            
                            <?php if ($nextVideo): ?>
                                <a href="/site/courses/watch/<?= $course['id'] ?>/<?= $nextVideo['id'] ?>" 
                                   class="btn btn-outline-secondary">
                                    Próximo<i class="fas fa-chevron-right ms-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let watchStartTime = Date.now();
    let totalWatchTime = <?= $progress['watch_time_seconds'] ?? 0 ?>;
    let isCompleted = <?= $progress && $progress['completed_at'] ? 'true' : 'false' ?>;
    let updateInterval;
    
    const markCompletedBtn = document.getElementById('markCompleted');
    const videoProgressBar = document.getElementById('videoProgress');
    const watchTimeSpan = document.getElementById('watchTime');
    
    // Função para formatar tempo
    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }
    
    // Função para atualizar progresso
    function updateProgress() {
        const currentWatchTime = totalWatchTime + Math.floor((Date.now() - watchStartTime) / 1000);
        const videoDuration = <?= $video['duration_seconds'] ?>;
        const progressPercent = Math.min((currentWatchTime / videoDuration) * 100, 100);
        
        // Atualizar UI
        videoProgressBar.style.width = progressPercent + '%';
        watchTimeSpan.textContent = formatTime(currentWatchTime);
        
        // Habilitar botão de conclusão se assistiu pelo menos 80%
        if (progressPercent >= 80 && !isCompleted) {
            markCompletedBtn.disabled = false;
        }
        
        // Enviar progresso para o servidor a cada 30 segundos
        if (currentWatchTime % 30 === 0) {
            saveProgress(currentWatchTime, progressPercent >= 95);
        }
    }
    
    // Função para salvar progresso
    function saveProgress(watchTime, completed = false) {
        fetch('/site/courses/updateProgress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                video_id: <?= $video['id'] ?>,
                course_id: <?= $course['id'] ?>,
                watch_time: watchTime,
                completed: completed
            })
        }).catch(error => {
            console.error('Erro ao salvar progresso:', error);
        });
    }
    
    // Iniciar tracking de tempo
    updateInterval = setInterval(updateProgress, 1000);
    
    // Marcar como concluído
    if (markCompletedBtn) {
        markCompletedBtn.addEventListener('click', function() {
            const currentWatchTime = totalWatchTime + Math.floor((Date.now() - watchStartTime) / 1000);
            saveProgress(currentWatchTime, true);
            
            this.innerHTML = '<i class="fas fa-check me-1"></i>Concluído!';
            this.disabled = true;
            this.classList.remove('btn-success');
            this.classList.add('btn-outline-success');
            
            isCompleted = true;
            
            // Mostrar mensagem de sucesso
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show mt-2';
            alert.innerHTML = `
                <i class="fas fa-trophy me-2"></i>Parabéns! Você concluiu este vídeo.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            this.parentNode.appendChild(alert);
        });
    }
    
    // Salvar progresso ao sair da página
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
        const currentWatchTime = totalWatchTime + Math.floor((Date.now() - watchStartTime) / 1000);
        saveProgress(currentWatchTime, false);
    });
    
    // Pausar tracking quando a aba não está ativa
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (updateInterval) {
                clearInterval(updateInterval);
                const currentWatchTime = totalWatchTime + Math.floor((Date.now() - watchStartTime) / 1000);
                saveProgress(currentWatchTime, false);
            }
        } else {
            watchStartTime = Date.now();
            updateInterval = setInterval(updateProgress, 1000);
        }
    });
});
</script>

<style>
.video-container {
    background: #000;
}

.list-group-item:hover {
    background-color: #2d2d2d !important;
}

.border-success {
    border-color: #28a745 !important;
    border-width: 2px !important;
}
</style>