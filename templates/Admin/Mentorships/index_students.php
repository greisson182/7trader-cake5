<div class="mentorships-students index content fade-in-up">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">
            <i class="bi bi-people-fill me-2 icon-accent"></i>
            Mentorias
        </h1>
        <p class="text-muted mb-0">Acompanhamento direto para acelerar sua evolução no trading.</p>
    </div>

    <!-- Lista de Mentorias -->
    <div class="card mb-4">
        <div class="card-body">
    <?php if (empty($mentorships)): ?>
        <div class="empty-block">
            <i class="bi bi-people"></i>
            <h5 class="text-muted mb-2">Nenhuma mentoria disponível</h5>
            <p class="text-muted mb-0">Em breve teremos mentorias para você!</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($mentorships as $mentorship): ?>
                <?php
                $isAcquired = isset($mentorshipOrders[$mentorship->id]);
                $typeLabel = $mentorship->type === 'particular' ? 'Particular' : 'Em Grupo';
                ?>

                <div class="col-lg-3 col-md-6">
                    <div class="card glass h-100 mentorship-card">
                        <div class="mentorship-thumbnail">
                            <div class="placeholder-thumbnail">
                                <i class="bi bi-people-fill"></i>
                            </div>

                            <div class="mentorship-price">
                                <span class="badge bg-warning text-dark">R$ <?= number_format($mentorship->price, 2, ',', '.') ?></span>
                            </div>

                            <?php if ($isAcquired): ?>
                                <div class="mentorship-status">
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Adquirida</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold mb-1"><?= h($mentorship->name) ?></h6>

                            <?php if ($mentorship->description): ?>
                                <p class="card-text text-muted small mb-2 clamp-2">
                                    <?= h(substr($mentorship->description, 0, 100)) ?><?= strlen($mentorship->description) > 100 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <div class="mentorship-meta mb-3">
                                <span><i class="bi bi-person-lines-fill"></i><?= h($typeLabel) ?></span>
                            </div>

                            <div class="mt-auto">
                                <?php if ($isAcquired): ?>
                                    <a href="/admin/mentorships/view-students/<?= $mentorship->id ?>" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-play-fill me-1"></i>Assistir
                                    </a>
                                <?php else: ?>
                                    <div class="d-grid gap-2">
                                        <a href="/admin/mentorships/view-students/<?= $mentorship->id ?>" class="btn btn-outline-light btn-sm">
                                            <i class="bi bi-eye me-1"></i>Ver Detalhes
                                        </a>
                                        <a href="/checkout/mentoria/<?= $mentorship->id ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-credit-card me-1"></i>Comprar por R$ <?= number_format($mentorship->price, 2, ',', '.') ?>
                                        </a>
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
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .mentorship-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .mentorship-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg), 0 0 20px rgba(0, 255, 136, 0.15);
    }

    .mentorship-thumbnail {
        position: relative;
        height: 150px;
        overflow: hidden;
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

    .mentorship-status {
        position: absolute;
        top: 8px;
        left: 8px;
    }

    .mentorship-price {
        position: absolute;
        top: 8px;
        right: 8px;
    }

    .clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5em;
    }

    .mentorship-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 0.75rem;
        font-size: 0.78rem;
        color: var(--text-muted, #b0b0b0);
    }

    .mentorship-meta i {
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
