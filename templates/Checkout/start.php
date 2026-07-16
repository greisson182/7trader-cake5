<?php
/**
 * @var \App\View\AppView $this
 * @var array{title: string, price: float} $item
 * @var string $itemType
 * @var int $itemId
 * @var array $formData
 * @var object|null $loggedUser
 * @var bool|null $requiresLogin
 */
$csrfToken = $this->request->getAttribute('csrfToken');
$itemLabel = $itemType === 'mentorship' ? 'Mentoria' : 'Curso';
$formData = $formData ?? [];
$loggedUser = $loggedUser ?? null;
$requiresLogin = $requiresLogin ?? false;
$currentUrl = $this->request->getUri()->getPath();
?>
<div class="container py-5" style="margin-top: 70px;">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <span class="badge bg-success mb-2"><?= h($itemLabel) ?></span>
                    <h2 class="h4 fw-bold mb-1"><?= h($item['title']) ?></h2>
                    <p class="fs-3 fw-bold text-success mb-0">
                        R$ <?= number_format($item['price'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>

            <?php if ($requiresLogin): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h5 class="fw-bold mb-3">Você já tem uma conta</h5>
                        <p class="text-muted mb-4">
                            Já existe um cadastro com esse e-mail. Faça login para continuar a compra.
                        </p>
                        <a href="/login" class="btn btn-success btn-lg">Fazer login</a>
                    </div>
                </div>
            <?php elseif ($loggedUser): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Confirmar compra</h5>
                        <p class="text-muted mb-4">
                            Comprando como <strong><?= h($loggedUser->name) ?></strong>
                            (<?= h($loggedUser->email) ?>).
                        </p>

                        <?= $this->Form->create(null, [
                            'url' => $currentUrl,
                        ]) ?>
                        <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                Ir para o pagamento
                            </button>
                        </div>

                        <p class="text-muted small text-center mt-3 mb-0">
                            Pagamento processado com segurança pelo Mercado Pago.
                        </p>

                        <?= $this->Form->end() ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Seus dados</h5>
                        <p class="text-muted small mb-4">
                            Depois de confirmar o pagamento, seu acesso é liberado automaticamente
                            com o e-mail e senha informados abaixo. Já tem conta?
                            <a href="/login">Faça login</a>.
                        </p>

                        <?= $this->Form->create(null, [
                            'url' => $currentUrl,
                        ]) ?>
                        <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome completo</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?= h($formData['name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?= h($formData['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone (opcional)</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="(11) 99999-9999"
                                   value="<?= h($formData['phone'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirmar senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                Ir para o pagamento
                            </button>
                        </div>

                        <p class="text-muted small text-center mt-3 mb-0">
                            Pagamento processado com segurança pelo Mercado Pago.
                        </p>

                        <?= $this->Form->end() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
