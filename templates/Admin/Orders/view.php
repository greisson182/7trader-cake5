<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var string|null $itemTitle
 */
$csrfToken = $this->request->getAttribute('csrfToken');
$statusInfo = [
    'pending' => ['label' => 'Pendente', 'color' => 'warning text-dark', 'icon' => 'bi-hourglass-split'],
    'approved' => ['label' => 'Aprovado', 'color' => 'success', 'icon' => 'bi-check-circle-fill'],
    'rejected' => ['label' => 'Rejeitado', 'color' => 'danger', 'icon' => 'bi-x-circle-fill'],
    'cancelled' => ['label' => 'Cancelado', 'color' => 'secondary', 'icon' => 'bi-slash-circle'],
    'refunded' => ['label' => 'Estornado', 'color' => 'dark', 'icon' => 'bi-arrow-counterclockwise'],
][$order->status] ?? ['label' => h($order->status), 'color' => 'secondary', 'icon' => 'bi-question-circle'];
?>
<div class="orders view content fade-in-up">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/admin/orders"><i class="bi bi-receipt-cutoff me-1"></i>Pedidos</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Pedido #<?= $order->id ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                <i class="bi bi-receipt-cutoff me-2 icon-accent"></i>
                Pedido #<?= $order->id ?>
            </h1>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-<?= $statusInfo['color'] ?>">
                    <i class="bi <?= $statusInfo['icon'] ?> me-1"></i><?= $statusInfo['label'] ?>
                </span>
                <span class="badge bg-<?= $order->item_type === 'mentorship' ? 'info' : 'primary' ?>">
                    <i class="bi <?= $order->item_type === 'mentorship' ? 'bi-people-fill' : 'bi-play-circle-fill' ?> me-1"></i>
                    <?= $order->item_type === 'mentorship' ? 'Mentoria' : 'Curso' ?>
                </span>
            </div>
        </div>
        <a href="/admin/orders" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <!-- Resumo -->
    <div class="card glass mb-3">
        <div class="card-body">
            <div class="row g-3 text-center text-md-start">
                <div class="col-md-6">
                    <div class="small text-muted mb-1"><i class="bi bi-bookmark me-1"></i>Produto</div>
                    <div class="fw-bold fs-5"><?= h($itemTitle ?? ('#' . $order->item_id)) ?></div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted mb-1"><i class="bi bi-cash-stack me-1"></i>Valor</div>
                    <div class="fw-bold fs-4 summary-amount">R$ <?= number_format((float)$order->amount, 2, ',', '.') ?></div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted mb-1"><i class="bi bi-calendar-event me-1"></i>Criado em</div>
                    <div class="fw-bold fs-6 pt-1"><?= h($order->created?->format('d/m/Y H:i')) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-person me-2"></i>
                        Comprador
                    </h6>
                    <div class="row g-2">
                        <div class="col-12 meta-row">
                            <span><i class="bi bi-person-fill me-1"></i> Nome</span>
                            <strong><?= h($order->buyer_name) ?></strong>
                        </div>
                        <div class="col-12 meta-row">
                            <span><i class="bi bi-envelope me-1"></i> E-mail</span>
                            <strong><a href="mailto:<?= h($order->buyer_email) ?>" class="student-link"><?= h($order->buyer_email) ?></a></strong>
                        </div>
                        <div class="col-12 meta-row">
                            <span><i class="bi bi-telephone me-1"></i> Telefone</span>
                            <strong><?= $order->buyer_phone ? h($order->buyer_phone) : '—' ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-link-45deg me-2"></i>
                        Conta vinculada
                    </h6>
                    <?php if ($order->student): ?>
                        <a href="/admin/students/view/<?= $order->student->id ?>" class="d-flex align-items-center gap-2 text-decoration-none student-link">
                            <span class="account-avatar"><i class="bi bi-person-fill"></i></span>
                            <span class="fw-semibold fs-6"><?= h($order->student->name) ?></span>
                        </a>
                    <?php else: ?>
                        <p class="text-muted mb-0">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Ainda não vinculado (pedido não confirmado).
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-credit-card me-2"></i>
                        Pagamento
                    </h6>
                    <div class="row g-2">
                        <div class="col-sm-6 col-lg-3 meta-row">
                            <span><i class="bi bi-wallet2 me-1"></i> Método</span>
                            <strong><?= $order->payment_method ? h($order->payment_method) : '—' ?></strong>
                        </div>
                        <div class="col-sm-6 col-lg-3 meta-row">
                            <span><i class="bi bi-hash me-1"></i> ID Mercado Pago</span>
                            <strong><?= $order->mp_payment_id ? h($order->mp_payment_id) : '—' ?></strong>
                        </div>
                        <div class="col-sm-6 col-lg-3 meta-row">
                            <span><i class="bi bi-calendar-check me-1"></i> Confirmado em</span>
                            <strong><?= $order->fulfilled_at ? h($order->fulfilled_at->format('d/m/Y H:i')) : '—' ?></strong>
                        </div>
                        <?php if ($order->ip_address): ?>
                            <div class="col-sm-6 col-lg-3 meta-row">
                                <span><i class="bi bi-geo-alt me-1"></i> IP de origem</span>
                                <strong><?= h($order->ip_address) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-chat-left-text me-2"></i>
                        Observações (acompanhamento de mentoria)
                    </h6>
                    <?= $this->Form->create(null, ['url' => ['action' => 'editNotes', $order->id]]) ?>
                    <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                    <textarea class="form-control mb-3" name="notes" rows="4"
                        placeholder="Anotações internas sobre o acompanhamento deste pedido..."><?= h($order->notes) ?></textarea>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Salvar Observações
                    </button>
                    <?= $this->Form->end() ?>
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

    .summary-amount {
        color: #00ff88;
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
        word-break: break-word;
    }

    .account-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
</style>
