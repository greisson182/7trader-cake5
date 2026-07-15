<?php
/**
 * @var \App\View\AppView $this
 * @var iterable $orders
 * @var string|null $status
 * @var string|null $itemType
 */
$statusBadges = [
    'pending' => 'warning text-dark',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
    'refunded' => 'dark',
];
?>
<div class="orders index content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-receipt-cutoff me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Pedidos
            </h1>
            <p class="text-muted mb-0">Histórico de compras de cursos e mentorias</p>
        </div>
    </div>

    <form method="get" class="row g-2 mb-4">
        <div class="col-auto">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Todos os status</option>
                <?php foreach (['pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado', 'cancelled' => 'Cancelado', 'refunded' => 'Estornado'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $status === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <select name="item_type" class="form-select" onchange="this.form.submit()">
                <option value="">Todos os tipos</option>
                <option value="course" <?= $itemType === 'course' ? 'selected' : '' ?>>Curso</option>
                <option value="mentorship" <?= $itemType === 'mentorship' ? 'selected' : '' ?>>Mentoria</option>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Comprador</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order->id ?></td>
                        <td>
                            <?= h($order->buyer_name) ?>
                            <div class="text-muted small"><?= h($order->buyer_email) ?></div>
                        </td>
                        <td><?= $order->item_type === 'mentorship' ? 'Mentoria' : 'Curso' ?></td>
                        <td>R$ <?= number_format((float)$order->amount, 2, ',', '.') ?></td>
                        <td>
                            <span class="badge bg-<?= $statusBadges[$order->status] ?? 'secondary' ?>">
                                <?= h($order->status) ?>
                            </span>
                        </td>
                        <td><?= h($order->created?->format('d/m/Y H:i')) ?></td>
                        <td>
                            <a href="/admin/orders/view/<?= $order->id ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($orders)): ?>
        <div class="text-center text-muted py-5">Nenhum pedido encontrado.</div>
    <?php endif; ?>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('«') ?>
            <?= $this->Paginator->prev('‹') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('›') ?>
            <?= $this->Paginator->last('»') ?>
        </ul>
    </div>
</div>
