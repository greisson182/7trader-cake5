<?php
/**
 * @var string|null $rawToken
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<?= $this->Form->create(null, ['url' => ['controller' => 'PasswordRecovery', 'action' => 'reset', $rawToken]]) ?>
<input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

<p class="text-white-50 mb-4">
    Escolha sua nova senha.
</p>

<div class="mb-3">
    <label for="password" class="form-label">
        <i class="fas fa-lock me-2"></i>Nova senha
    </label>
    <input type="password" class="form-control" id="password" name="password" required minlength="6"
           placeholder="Digite sua nova senha">
</div>

<div class="mb-4">
    <label for="confirm_password" class="form-label">
        <i class="fas fa-lock me-2"></i>Confirmar nova senha
    </label>
    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6"
           placeholder="Confirme sua nova senha">
</div>

<button type="submit" class="btn btn-primary btn-login w-100">
    <i class="fas fa-check me-2"></i>Redefinir senha
</button>
<?= $this->Form->end() ?>

<div class="text-center mt-3">
    <a href="/login" style="color: #00ff88; text-decoration: none; font-weight: 500;">Voltar ao login</a>
</div>
