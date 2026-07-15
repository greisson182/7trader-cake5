<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order|null $order
 * @var string $outcome success|pending|failure
 */
$statusLabels = [
    'pending' => ['Pagamento em análise', 'text-warning', 'bi-hourglass-split'],
    'approved' => ['Pagamento confirmado', 'text-success', 'bi-check-circle-fill'],
    'rejected' => ['Pagamento não aprovado', 'text-danger', 'bi-x-circle-fill'],
    'cancelled' => ['Pagamento cancelado', 'text-danger', 'bi-x-circle-fill'],
    'refunded' => ['Pagamento estornado', 'text-secondary', 'bi-arrow-counterclockwise'],
];

$currentStatus = $order->status ?? ($outcome === 'failure' ? 'rejected' : 'pending');
[$statusLabel, $statusClass, $statusIcon] = $statusLabels[$currentStatus] ?? $statusLabels['pending'];
?>
<div class="container py-5" style="margin-top: 70px;">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card shadow-sm border-0">
                <div class="card-body py-5">
                    <i class="bi <?= h($statusIcon) ?> <?= h($statusClass) ?>" style="font-size: 3rem;"></i>
                    <h2 class="h4 fw-bold mt-3 mb-2 <?= h($statusClass) ?>"><?= h($statusLabel) ?></h2>

                    <?php if ($currentStatus === 'approved'): ?>
                        <p class="text-muted">
                            Seu acesso já foi liberado. Você também vai receber um e-mail de confirmação.
                        </p>
                        <a href="/login" class="btn btn-success mt-2">Acessar minha conta</a>
                    <?php elseif ($currentStatus === 'pending'): ?>
                        <p class="text-muted">
                            Assim que o pagamento for confirmado pelo Mercado Pago, seu acesso será liberado
                            automaticamente. Isso pode levar alguns minutos, dependendo da forma de pagamento (ex: Pix é quase instantâneo, boleto pode levar até 2 dias úteis).
                        </p>
                    <?php else: ?>
                        <p class="text-muted">
                            Não foi possível confirmar seu pagamento. Você pode tentar novamente ou entrar em
                            contato com a gente pelo WhatsApp.
                        </p>
                    <?php endif; ?>

                    <a href="/" class="btn btn-outline-secondary mt-2 ms-2">Voltar para o site</a>
                </div>
            </div>
        </div>
    </div>
</div>
