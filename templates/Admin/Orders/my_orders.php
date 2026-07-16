<div class="orders-students index content fade-in-up">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">
            <i class="bi bi-receipt-cutoff me-2 icon-accent"></i>
            Meus Pedidos
        </h1>
        <p class="text-muted mb-0">Histórico de compras de cursos e mentorias.</p>
    </div>

    <div class="card mb-4">
        <div class="card-body">
    <?php if (empty($orders)): ?>
        <div class="empty-block">
            <i class="bi bi-receipt"></i>
            <h5 class="text-muted mb-2">Nenhum pedido ainda</h5>
            <p class="text-muted mb-0">Suas compras de cursos e mentorias vão aparecer aqui.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orderStatusLabels = [
                        'pending' => ['Pendente', 'warning'],
                        'approved' => ['Aprovado', 'success'],
                        'rejected' => ['Rejeitado', 'danger'],
                        'cancelled' => ['Cancelado', 'secondary'],
                        'refunded' => ['Reembolsado', 'info'],
                    ];
                    ?>
                    <?php foreach ($orders as $order): ?>
                        <?php [$statusLabel, $statusColor] = $orderStatusLabels[$order->status] ?? [ucfirst($order->status), 'secondary']; ?>
                        <tr>
                            <td><?= h($order->item_title) ?></td>
                            <td><?= $order->item_type === 'course' ? 'Curso' : 'Mentoria' ?></td>
                            <td>R$ <?= number_format($order->amount, 2, ',', '.') ?></td>
                            <td><span class="badge bg-<?= $statusColor ?>"><?= h($statusLabel) ?></span></td>
                            <td><?= $order->created ? $order->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
