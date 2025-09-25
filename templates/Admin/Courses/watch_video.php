<?php
/**
 * Template para assistir vídeo
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-play-circle"></i>
                        <?= htmlspecialchars($video['title']) ?>
                    </h3>
                    <div>
                        <a href="/admin/courses/edit-video/<?= $video['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="/admin/courses/videos/<?= $video['course_id'] ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informações do Curso -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="text-muted">
                                <i class="fas fa-graduation-cap"></i>
                                Curso: <?= htmlspecialchars($video['course_title']) ?>
                            </h5>
                        </div>
                    </div>

                    <!-- Player de Vídeo -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="video-container mb-4">
                                <?php if (isset($video['video_type']) && $video['video_type'] === 'youtube'): ?>
                                    <?php
                                    // Extrair ID do YouTube da URL com regex mais robusta
                                    $videoUrl = $video['video_url'];
                                    $videoId = '';
                                    
                                    // Regex mais robusta para URLs do YouTube
                                    if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                    ?>
                                    
                                    <?php if ($videoId): ?>
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe class="embed-responsive-item" 
                                                    src="https://www.youtube.com/embed/<?= $videoId ?>?rel=0&showinfo=0&modestbranding=1" 
                                                    allowfullscreen>
                                            </iframe>
                                        </div>
                                        
        
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>URL do YouTube inválida.</strong><br>
                                            <small>URL fornecida: <code><?= htmlspecialchars($video['video_url']) ?></code></small><br>
                                            <small>Formatos aceitos:</small>
                                            <ul class="small mb-2">
                                                <li><code>https://www.youtube.com/watch?v=VIDEO_ID</code></li>
                                                <li><code>https://youtu.be/VIDEO_ID</code></li>
                                                <li><code>https://www.youtube.com/embed/VIDEO_ID</code></li>
                                            </ul>
                                            <a href="<?= htmlspecialchars($video['video_url']) ?>" target="_blank" class="btn btn-sm btn-primary ml-2">
                                                Abrir no YouTube
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                <?php elseif (isset($video['video_type']) && $video['video_type'] === 'vimeo'): ?>
                                    <?php
                                    // Extrair ID do Vimeo da URL com regex mais robusta
                                    $videoUrl = $video['video_url'];
                                    $videoId = '';
                                    
                                    // Regex mais robusta para URLs do Vimeo
                                    if (preg_match('/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(?:video\/)?(\d+)(?:\?.*)?/', $videoUrl, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                    ?>
                                    
                                    <?php if ($videoId): ?>
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe class="embed-responsive-item" 
                                                    src="https://player.vimeo.com/video/<?= $videoId ?>?title=0&byline=0&portrait=0" 
                                                    allowfullscreen>
                                            </iframe>
                                        </div>
                                        
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>URL do Vimeo inválida.</strong><br>
                                            <small>URL fornecida: <code><?= htmlspecialchars($video['video_url']) ?></code></small><br>
                                            <small>Formato esperado: <code>https://vimeo.com/123456789</code></small>
                                            <a href="<?= htmlspecialchars($video['video_url']) ?>" target="_blank" class="btn btn-sm btn-primary ml-2">
                                                Abrir no Vimeo
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                <?php else: ?>
                                    <!-- Vídeo direto (MP4, etc.) -->
                                    <video class="w-100" controls>
                                        <source src="<?= htmlspecialchars($video['video_url']) ?>" type="video/mp4">
                                        Seu navegador não suporta o elemento de vídeo.
                                    </video>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Informações do Vídeo -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        Informações do Vídeo
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($video['description'])): ?>
                                        <div class="mb-3">
                                            <strong>Descrição:</strong>
                                            <p class="mt-1"><?= nl2br(htmlspecialchars($video['description'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($video['duration_seconds'])): ?>
                                        <div class="mb-3">
                                            <strong>Duração:</strong>
                                            <span class="badge badge-info">
                                                <?= gmdate("H:i:s", $video['duration_seconds']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <strong>Tipo:</strong>
                                        <span class="badge badge-secondary">
                                            <?= isset($video['video_type']) ? ucfirst($video['video_type']) : 'Não definido' ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Status:</strong>
                                        <span class="badge <?= $video['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $video['is_active'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($video['created'])): ?>
                                        <div class="mb-3">
                                            <strong>Criado em:</strong>
                                            <small class="text-muted d-block">
                                                <?= date('d/m/Y H:i', strtotime($video['created'])) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($video['modified']) && $video['modified'] !== $video['created']): ?>
                                        <div class="mb-3">
                                            <strong>Última atualização:</strong>
                                            <small class="text-muted d-block">
                                                <?= date('d/m/Y H:i', strtotime($video['modified'])) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.video-container {
    position: relative;
    background: #000;
    border-radius: 8px;
    overflow: hidden;
}

.embed-responsive {
    position: relative;
    display: block;
    width: 100%;
    padding: 0;
    overflow: hidden;
}

.embed-responsive-16by9 {
    padding-bottom: 56.25%;
}

.embed-responsive-item {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

video {
    border-radius: 8px;
}
</style>