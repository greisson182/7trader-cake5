<?php
$title = h($mentorship['name']) . ' - 7 Trader';
$description = h(substr($mentorship['description'] ?? '', 0, 160));

$typeLabel = $mentorship['type'] === 'particular' ? 'Particular' : 'Em Grupo';

$totalMinutes = round($mentorship['total_duration'] / 60);
$hours = floor($totalMinutes / 60);
$minutes = $totalMinutes % 60;
$durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";

$modules = $modules ?? [];
$ungroupedVideos = $ungroupedVideos ?? $videos;

$renderVideoItem = function ($video, $number) use ($isAcquired, $progress, $mentorship) {
    $canWatch = $isAcquired || $video['is_preview'];
    $isWatched = isset($progress[$video['id']]) && $progress[$video['id']]['completed'];
    $isInProgress = isset($progress[$video['id']]) && !$progress[$video['id']]['completed'];

    $vHours = floor($video['duration_seconds'] / 3600);
    $vMinutes = floor(($video['duration_seconds'] % 3600) / 60);
    $vSeconds = $video['duration_seconds'] % 60;
    ?>
    <div class="row-item align-items-start">
        <div class="row-item-main">
            <div class="d-flex align-items-center gap-2 mb-1">
                <?php if ($isWatched): ?>
                    <i class="bi bi-check-circle-fill text-success"></i>
                <?php elseif ($isInProgress): ?>
                    <i class="bi bi-play-circle-fill text-info"></i>
                <?php elseif ($canWatch): ?>
                    <i class="bi bi-play-fill text-muted"></i>
                <?php else: ?>
                    <i class="bi bi-lock-fill text-warning"></i>
                <?php endif; ?>
                <span class="fw-semibold"><?= $number ?>. <?= h($video['title']) ?></span>
                <?php if ($video['is_preview']): ?>
                    <span class="badge bg-info">Preview</span>
                <?php endif; ?>
            </div>
            <div class="small text-muted mb-2">
                <?= $vHours > 0 ? sprintf('%d:%02d:%02d', $vHours, $vMinutes, $vSeconds) : sprintf('%d:%02d', $vMinutes, $vSeconds) ?>
            </div>
            <?php if ($canWatch): ?>
                <a href="/admin/mentorships/watch-students/<?= $mentorship['id'] ?>/<?= $video['id'] ?>"
                   class="btn btn-outline-primary btn-sm">
                    <?php if ($isWatched): ?>
                        <i class="bi bi-arrow-repeat me-1"></i>Assistir Novamente
                    <?php else: ?>
                        <i class="bi bi-play me-1"></i>Assistir
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <small class="text-warning">
                    <i class="bi bi-lock me-1"></i>Adquira a mentoria para assistir
                </small>
            <?php endif; ?>
        </div>
    </div>
    <?php
};
?>
<div class="courses-students view content fade-in-up">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/admin/mentorships/index-students">
                    <i class="bi bi-people-fill me-1"></i>Mentorias
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= h($mentorship['name']) ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">
            <i class="bi bi-play-circle-fill me-2 icon-accent"></i>
            <?= h($mentorship['name']) ?>
        </h1>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-info"><?= h($typeLabel) ?></span>
            <span class="badge bg-warning text-dark">R$ <?= number_format($mentorship['price'], 2, ',', '.') ?></span>
            <?php if ($isAcquired): ?>
                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Adquirida</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-3">
        <!-- Informações da Mentoria -->
        <div class="col-12">
            <div class="card glass mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="section-title">
                                <i class="bi bi-info-circle me-2"></i>
                                Informações da Mentoria
                            </h6>
                            <div class="row g-2">
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-play-fill me-1"></i> Vídeos</span>
                                    <strong><?= (int)$mentorship['video_count'] ?></strong>
                                </div>
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-clock me-1"></i> Duração</span>
                                    <strong><?= $durationText ?></strong>
                                </div>
                                <div class="col-sm-6 meta-row">
                                    <span><i class="bi bi-person-lines-fill me-1"></i> Tipo</span>
                                    <strong><?= h($typeLabel) ?></strong>
                                </div>
                            </div>
                        </div>

                        <?php if ($mentorship['description']): ?>
                            <div class="col-12">
                                <h6 class="section-title">
                                    <i class="bi bi-text-paragraph me-2"></i>
                                    Sobre a Mentoria
                                </h6>
                                <p class="text-muted mb-0"><?= nl2br(h($mentorship['description'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$isAcquired): ?>
                        <div class="mt-4 pt-3 border-top border-secondary-subtle">
                            <a href="/checkout/mentoria/<?= $mentorship['id'] ?>" class="btn btn-primary">
                                <i class="bi bi-credit-card me-2"></i>Comprar Mentoria - R$ <?= number_format($mentorship['price'], 2, ',', '.') ?>
                            </a>
                            <div class="mt-2 small text-muted">
                                <i class="bi bi-shield-check me-1"></i>Pagamento seguro ·
                                <i class="bi bi-infinity me-1"></i>Acesso vitalício
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($isAcquired && !empty($videos)): ?>
                        <?php
                        $totalVideos = count($videos);
                        $watchedVideos = 0;
                        foreach ($videos as $video) {
                            if (isset($progress[$video['id']]) && $progress[$video['id']]['completed']) {
                                $watchedVideos++;
                            }
                        }
                        $progressPercent = $totalVideos > 0 ? round(($watchedVideos / $totalVideos) * 100) : 0;
                        ?>
                        <div class="mt-4 pt-3 border-top border-secondary-subtle">
                            <h6 class="section-title">
                                <i class="bi bi-graph-up me-2"></i>
                                Seu Progresso
                            </h6>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>Progresso da Mentoria</span>
                                    <span><?= $progressPercent ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPercent ?>%"></div>
                                </div>
                            </div>

                            <div class="row g-2 text-center">
                                <div class="col-6 col-md-3">
                                    <div class="stat-box">
                                        <div class="stat-value"><?= $watchedVideos ?>/<?= $totalVideos ?></div>
                                        <div class="stat-label">Assistidos</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="stat-box">
                                        <div class="stat-value stat-value-warning"><?= $totalVideos - $watchedVideos ?></div>
                                        <div class="stat-label">Restantes</div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($order): ?>
                                <ul class="meta-list mt-3">
                                    <li>
                                        <span>Adquirida em</span>
                                        <strong><?= date('d/m/Y H:i', strtotime($order['fulfilled_at'] ?? $order['created'])) ?></strong>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Conteúdo da Mentoria -->
        <div class="col-12">
            <div class="card glass mb-3">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-collection-play me-2"></i>
                        Conteúdo da Mentoria
                    </h6>

                    <?php if (empty($videos)): ?>
                        <div class="empty-block">
                            <i class="bi bi-camera-video-off"></i>
                            <p class="mb-0">Nenhum vídeo disponível</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($modules as $module): ?>
                            <?php if (!empty($module->mentorship_videos)): ?>
                                <h6 class="module-subtitle">
                                    <i class="bi bi-folder2-open me-2"></i>
                                    <?= h($module->title) ?>
                                </h6>
                                <div class="row-list mb-3">
                                    <?php foreach ($module->mentorship_videos as $i => $video): ?>
                                        <?= $renderVideoItem($video, $i + 1) ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (!empty($ungroupedVideos)): ?>
                            <?php if (!empty($modules)): ?>
                                <h6 class="module-subtitle">
                                    <i class="bi bi-collection-play me-2"></i>
                                    Outros Vídeos
                                </h6>
                            <?php endif; ?>
                            <div class="row-list">
                                <?php foreach ($ungroupedVideos as $i => $video): ?>
                                    <?= $renderVideoItem($video, $i + 1) ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

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

    .module-subtitle {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--text-muted, #b0b0b0);
        margin-bottom: 0.5rem;
    }

    .module-subtitle i {
        color: #00ff88;
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

    .stat-value-warning {
        color: #ffc107;
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
