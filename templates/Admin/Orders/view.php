<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var string|null $itemTitle
 */
$csrfToken = $this->request->getAttribute('csrfToken');
$statusBadges = [
    'pending' => 'warning text-dark',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
    'refunded' => 'dark',
];
?>
<div class="orders view content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">Pedido #<?= $order->id ?></h1>
            <span class="badge bg-<?= $statusBadges[$order->status] ?? 'secondary' ?>"><?= h($order->status) ?></span>
        </div>
        <a href="/admin/orders" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Produto</h5>
                    <p class="mb-1"><strong>Tipo:</strong> <?= $order->item_type === 'mentorship' ? 'Mentoria' : 'Curso' ?></p>
                    <p class="mb-1"><strong>Item:</strong> <?= h($itemTitle ?? ('#' . $order->item_id)) ?></p>
                    <p class="mb-0"><strong>Valor:</strong> R$ <?= number_format((float)$order->amount, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Comprador</h5>
                    <p class="mb-1"><strong>Nome:</strong> <?= h($order->buyer_name) ?></p>
                    <p class="mb-1"><strong>E-mail:</strong> <?= h($order->buyer_email) ?></p>
                    <p class="mb-0"><strong>Telefone:</strong> <?= h($order->buyer_phone) ?: '—' ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Pagamento</h5>
                    <p class="mb-1"><strong>Método:</strong> <?= h($order->payment_method) ?: '—' ?></p>
                    <p class="mb-1"><strong>ID Mercado Pago:</strong> <?= h($order->mp_payment_id) ?: '—' ?></p>
                    <p class="mb-1"><strong>Criado em:</strong> <?= h($order->created?->format('d/m/Y H:i')) ?></p>
                    <p class="mb-0"><strong>Confirmado em:</strong> <?= $order->fulfilled_at ? h($order->fulfilled_at->format('d/m/Y H:i')) : '—' ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Conta vinculada</h5>
                    <?php if ($order->student): ?>
                        <p class="mb-0">
                            <a href="/admin/students/view/<?= $order->student->id ?>">
                                <?= h($order->student->name) ?>
                            </a>
                        </p>
                    <?php else: ?>
                        <p class="text-muted mb-0">Ainda não vinculado (pedido não confirmado).</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Observações (acompanhamento de mentoria)</h5>
                    <?= $this->Form->create(null, ['url' => ['action' => 'editNotes', $order->id]]) ?>
                    <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                    <textarea class="form-control mb-3" name="notes" rows="4"><?= h($order->notes) ?></textarea>
                    <button type="submit" class="btn btn-primary">Salvar observações</button>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
