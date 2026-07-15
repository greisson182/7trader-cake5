<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mentorship $mentorship
 * @var array $stats
 */
?>
<div class="mentorships view content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2"><?= h($mentorship->name) ?></h1>
            <span class="badge bg-<?= $mentorship->type === 'particular' ? 'warning text-dark' : 'info' ?>">
                <?= $mentorship->type === 'particular' ? 'Particular' : 'Em grupo' ?>
            </span>
            <?php if ($mentorship->is_active): ?>
                <span class="badge bg-success">Ativa</span>
            <?php else: ?>
                <span class="badge bg-secondary">Inativa</span>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/mentorships/edit/<?= $mentorship->id ?>" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="/admin/mentorships" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Descrição</h5>
                    <p><?= nl2br(h($mentorship->description)) ?: '<span class="text-muted">Sem descrição.</span>' ?></p>
                    <h5 class="fw-bold mt-4">Preço</h5>
                    <p class="fs-4 fw-bold">R$ <?= number_format((float)$mentorship->price, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Pedidos</h5>
                    <p class="mb-1">Total: <strong><?= (int)$stats['total_orders'] ?></strong></p>
                    <p class="mb-0">Aprovados: <strong><?= (int)$stats['approved_orders'] ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
