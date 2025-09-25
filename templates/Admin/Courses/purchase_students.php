<?php
$title = 'Comprar Curso: ' . htmlspecialchars($course['title']) . ' - 7 Trader';
$description = 'Adquira acesso completo ao curso ' . htmlspecialchars($course['title']);
?>

<div class="container-fluid bg-dark text-white min-vh-100">
    <!-- Breadcrumb -->
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/admin/courses-students" class="text-success text-decoration-none">
                        <i class="fas fa-graduation-cap me-1"></i>Cursos
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/admin/courses/view-students/<?= $course['id'] ?>" class="text-success text-decoration-none">
                        <?= htmlspecialchars($course['title']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    Comprar
                </li>
            </ol>
        </nav>
    </div>

    <!-- Purchase Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>Finalizar Compra
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Course Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <?php if ($course['thumbnail']): ?>
                                    <img src="<?= htmlspecialchars($course['thumbnail']) ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?= htmlspecialchars($course['title']) ?>">
                                <?php else: ?>
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-play-circle fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h4 class="text-white mb-3"><?= htmlspecialchars($course['title']) ?></h4>
                                
                                <div class="mb-3">
                                    <?php
                                    $difficultyColors = [
                                        'Iniciante' => 'success',
                                        'Intermediário' => 'warning', 
                                        'Avançado' => 'danger'
                                    ];
                                    $difficultyColor = $difficultyColors[$course['difficulty']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $difficultyColor ?> me-2">
                                        <?= htmlspecialchars($course['difficulty']) ?>
                                    </span>
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($course['category']) ?>
                                    </span>
                                </div>

                                <p class="text-muted mb-3">
                                    <?= nl2br(htmlspecialchars(substr($course['description'], 0, 200))) ?>
                                    <?= strlen($course['description']) > 200 ? '...' : '' ?>
                                </p>

                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="border-end border-secondary">
                                            <i class="fas fa-play-circle text-success mb-2 d-block"></i>
                                            <div class="fw-bold text-white">Vídeos</div>
                                            <small class="text-muted">Acesso completo</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-end border-secondary">
                                            <i class="fas fa-clock text-success mb-2 d-block"></i>
                                            <div class="fw-bold text-white">Vitalício</div>
                                            <small class="text-muted">Sem prazo</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-certificate text-success mb-2 d-block"></i>
                                        <div class="fw-bold text-white">Certificado</div>
                                        <small class="text-muted">Incluído</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary">

                        <!-- Price Summary -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="text-white">Resumo do Pedido</h5>
                                <div class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Curso: <?= htmlspecialchars($course['title']) ?></span>
                                    <span class="text-white">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-top border-secondary">
                                    <span class="fw-bold text-white">Total</span>
                                    <span class="fw-bold text-success fs-4">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form method="post" action="/admin/courses/purchase-students/<?= $course['id'] ?>">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Modo de Desenvolvimento:</strong> 
                                Esta é uma simulação de compra. Clique em "Finalizar Compra" para obter acesso imediato ao curso.
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card bg-secondary border-0">
                                        <div class="card-body text-center">
                                            <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                                            <h6 class="text-white">Pagamento Seguro</h6>
                                            <small class="text-muted">Seus dados estão protegidos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-secondary border-0">
                                        <div class="card-body text-center">
                                            <i class="fas fa-headset text-success fa-2x mb-2"></i>
                                            <h6 class="text-white">Suporte 24/7</h6>
                                            <small class="text-muted">Estamos aqui para ajudar</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/admin/courses/view-students/<?= $course['id'] ?>" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Voltar
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Finalizar Compra - R$ <?= number_format($course['price'], 2, ',', '.') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guarantee -->
                <div class="text-center mt-4">
                    <div class="card bg-dark border-success">
                        <div class="card-body">
                            <i class="fas fa-medal text-success fa-2x mb-3"></i>
                            <h5 class="text-white">Garantia de Satisfação</h5>
                            <p class="text-muted mb-0">
                                Acesso vitalício ao conteúdo, atualizações gratuitas e suporte completo.
                                Invista no seu conhecimento com total segurança.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>