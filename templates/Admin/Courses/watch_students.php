<?php
$title = h($video['title']) . ' - ' . h($course['title']) . ' - 7 Trader';
$description = h(substr($video['description'] ?? '', 0, 160));
$csrfToken = $this->request->getAttribute('csrfToken');

$formatDuration = function (int $totalSeconds): string {
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;

    return $hours > 0
        ? sprintf('%d:%02d:%02d', $hours, $minutes, $seconds)
        : sprintf('%d:%02d', $minutes, $seconds);
};
?>
<div class="courses-students watch content fade-in-up">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/admin/courses/courses-students">
                    <i class="bi bi-mortarboard-fill me-1"></i>Cursos
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="/admin/courses/view-students/<?= $course['id'] ?>"><?= h($course['title']) ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= h($video['title']) ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-3">
        <h1 class="h4 fw-bold mb-1">
            <i class="bi bi-play-circle-fill me-2 icon-accent"></i>
            <?= h($video['title']) ?>
        </h1>
        <p class="text-muted mb-0">
            <i class="bi bi-mortarboard me-1"></i><?= h($course['title']) ?>
        </p>
    </div>

    <div class="row g-3">
        <!-- Player de Vídeo -->
        <div class="col-lg-9">
            <div class="card glass mb-3">
                <div class="video-container position-relative">
                    <?php
                    $embedUrl = '';
                    // Retoma do ponto assistido (deixa 1s de folga do fim pra não reiniciar um vídeo já concluído).
                    $resumeSeconds = $currentProgress ? (int)$currentProgress->watched_seconds : 0;
                    $resumeSeconds = max(0, min($resumeSeconds, max(0, (int)$video['duration_seconds'] - 1)));

                    if ($video['video_type'] === 'youtube') {
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video['video_url'], $matches);
                        $youtubeId = $matches[1] ?? '';
                        if ($youtubeId) {
                            $embedUrl = "https://www.youtube.com/embed/{$youtubeId}?enablejsapi=1&origin=" . urlencode($_SERVER['HTTP_HOST']);
                            if ($resumeSeconds > 0) {
                                $embedUrl .= '&start=' . $resumeSeconds;
                            }
                        }
                    } elseif ($video['video_type'] === 'vimeo') {
                        preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $video['video_url'], $matches);
                        $vimeoId = $matches[3] ?? '';
                        if ($vimeoId) {
                            $embedUrl = "https://player.vimeo.com/video/{$vimeoId}";
                            if ($resumeSeconds > 0) {
                                $embedUrl .= '#t=' . $resumeSeconds . 's';
                            }
                        }
                    } else {
                        $embedUrl = $video['video_url'];
                        if ($resumeSeconds > 0) {
                            $embedUrl .= '#t=' . $resumeSeconds;
                        }
                    }
                    ?>
                    <?php if ($embedUrl): ?>
                        <div class="ratio ratio-16x9">
                            <iframe id="videoPlayer"
                                src="<?= h($embedUrl) ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="rounded">
                            </iframe>
                        </div>
                    <?php else: ?>
                        <div class="ratio ratio-16x9 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="bi bi-exclamation-triangle-fill fa-3x mb-3 text-warning"></i>
                                <h5>Erro ao carregar vídeo</h5>
                                <p class="text-muted">URL do vídeo inválida ou não suportada</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <?php if ($video['description']): ?>
                            <div class="col-12">
                                <h6 class="section-title">
                                    <i class="bi bi-info-circle me-2"></i>Descrição
                                </h6>
                                <p class="text-muted mb-0"><?= nl2br(h($video['description'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <div class="progress-card">
                                <h6 class="section-title mb-2">Progresso do Vídeo</h6>

                                <?php
                                $watchedSeconds = $currentProgress ? (int)$currentProgress->watched_seconds : 0;
                                $initialPercent = $video['duration_seconds'] > 0
                                    ? min(($watchedSeconds / $video['duration_seconds']) * 100, 100)
                                    : 0;
                                ?>
                                <div class="mb-3">
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div id="videoProgress" class="progress-bar bg-success" role="progressbar" style="width: <?= $initialPercent ?>%"></div>
                                    </div>
                                    <small class="text-muted">
                                        <span id="watchTime"><?= $formatDuration($watchedSeconds) ?></span> /
                                        <span id="totalTime"><?= $formatDuration($video['duration_seconds']) ?></span>
                                    </small>
                                </div>

                                <?php if ($currentProgress && $currentProgress->completed): ?>
                                    <div class="alert alert-success py-2 mb-0">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        <small>Vídeo Concluído!</small>
                                    </div>
                                <?php else: ?>
                                    <button id="markCompleted" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-check-lg me-1"></i>Marcar como Concluído
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Vídeos -->
        <div class="col-lg-3">
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-collection-play me-2"></i>Vídeos do Curso
                    </h6>
                    <div class="row-list" style="max-height: 500px; overflow-y: auto;">
                        <?php foreach ($allVideos as $index => $courseVideo): ?>
                            <?php
                            $isCurrentVideo = $courseVideo['id'] == $video['id'];
                            $isWatched = isset($progress[$courseVideo['id']]) && $progress[$courseVideo['id']]->completed;
                            $isInProgress = isset($progress[$courseVideo['id']]) && !$progress[$courseVideo['id']]->completed;

                            $durationText = $formatDuration($courseVideo['duration_seconds']);
                            ?>

                            <div class="row-item align-items-start <?= $isCurrentVideo ? 'row-item-current' : '' ?>">
                                <div class="row-item-main">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <?php if ($isCurrentVideo): ?>
                                            <i class="bi bi-play-circle-fill text-success"></i>
                                        <?php elseif ($isWatched): ?>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        <?php elseif ($isInProgress): ?>
                                            <i class="bi bi-play-circle-fill text-info"></i>
                                        <?php else: ?>
                                            <i class="bi bi-play-fill text-muted"></i>
                                        <?php endif; ?>
                                        <span class="fw-semibold small"><?= $index + 1 ?>. <?= h($courseVideo['title']) ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <?php if ($courseVideo['is_preview']): ?>
                                            <span class="badge bg-info">Preview</span>
                                        <?php endif; ?>
                                        <?php if ($isCurrentVideo): ?>
                                            <span class="badge bg-success">Atual</span>
                                        <?php endif; ?>
                                        <small class="text-muted"><?= $durationText ?></small>
                                    </div>

                                    <?php if (!$isCurrentVideo): ?>
                                        <a href="/admin/courses/watch-students/<?= $course['id'] ?>/<?= $courseVideo['id'] ?>"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-play me-1"></i>Assistir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Navegação -->
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-arrow-left-right me-2"></i>Navegação
                    </h6>

                    <?php
                    $currentIndex = array_search($video['id'], array_column($allVideos, 'id'));
                    $prevVideo = $currentIndex > 0 ? $allVideos[$currentIndex - 1] : null;
                    $nextVideo = $currentIndex < count($allVideos) - 1 ? $allVideos[$currentIndex + 1] : null;
                    ?>

                    <div class="d-grid gap-2">
                        <?php if ($prevVideo): ?>
                            <a href="/admin/courses/watch-students/<?= $course['id'] ?>/<?= $prevVideo['id'] ?>"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-chevron-left me-2"></i>Anterior
                            </a>
                        <?php endif; ?>

                        <a href="/admin/courses/view-students/<?= $course['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list-ul me-2"></i>Ver Curso
                        </a>

                        <?php if ($nextVideo): ?>
                            <a href="/admin/courses/watch-students/<?= $course['id'] ?>/<?= $nextVideo['id'] ?>"
                                class="btn btn-outline-secondary btn-sm">
                                Próximo<i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = <?= json_encode($csrfToken) ?>;
        let watchStartTime = Date.now();
        let totalWatchTime = <?= $currentProgress->watched_seconds ?? 0 ?>;
        let isCompleted = <?= $currentProgress && $currentProgress->completed ? 'true' : 'false' ?>;
        let updateInterval;

        const markCompletedBtn = document.getElementById('markCompleted');
        const videoProgressBar = document.getElementById('videoProgress');
        const watchTimeSpan = document.getElementById('watchTime');
        const videoDuration = <?= $video['duration_seconds'] ?>;

        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            if (hours > 0) {
                return `${hours}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        function getCurrentWatchTime() {
            const elapsed = totalWatchTime + Math.floor((Date.now() - watchStartTime) / 1000);
            return Math.min(elapsed, videoDuration);
        }

        function updateProgress() {
            const currentWatchTime = getCurrentWatchTime();
            const progressPercent = Math.min((currentWatchTime / videoDuration) * 100, 100);

            videoProgressBar.style.width = progressPercent + '%';
            watchTimeSpan.textContent = formatTime(currentWatchTime);

            if (progressPercent >= 80 && !isCompleted && markCompletedBtn) {
                markCompletedBtn.disabled = false;
            }

            if (currentWatchTime % 20 === 0) {
                saveProgress(currentWatchTime, progressPercent >= 95);
            }
        }

        function saveProgress(watchTime, completed = false) {
            fetch('/admin/courses/updateProgress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
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

        updateInterval = setInterval(updateProgress, 1000);

        if (markCompletedBtn) {
            markCompletedBtn.addEventListener('click', function() {
                const currentWatchTime = getCurrentWatchTime();
                saveProgress(currentWatchTime, true);

                this.innerHTML = '<i class="bi bi-check-lg me-1"></i>Concluído!';
                this.disabled = true;
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-success');

                isCompleted = true;

                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                alert.innerHTML = `
                <i class="bi bi-trophy-fill me-2"></i>Parabéns! Você concluiu este vídeo.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
                this.parentNode.appendChild(alert);
            });
        }

        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            saveProgress(getCurrentWatchTime(), false);
        });

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (updateInterval) {
                    clearInterval(updateInterval);
                    saveProgress(getCurrentWatchTime(), false);
                }
            } else {
                watchStartTime = Date.now();
                updateInterval = setInterval(updateProgress, 1000);
            }
        });
    });
</script>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .video-container {
        background: #000;
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

    .progress-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--card-border, rgba(255, 255, 255, 0.1));
        border-radius: var(--radius-sm, 8px);
        padding: 1rem;
        text-align: center;
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
        padding: 0.6rem 0.75rem;
        transition: background 0.2s ease;
    }

    .row-item:hover {
        background: rgba(0, 255, 136, 0.05);
    }

    .row-item-current {
        border-color: #00ff88;
    }

    .row-item-main {
        min-width: 0;
        flex-grow: 1;
    }
</style>
