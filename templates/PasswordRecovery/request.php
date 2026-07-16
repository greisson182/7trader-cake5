<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<?= $this->Form->create(null, ['url' => ['controller' => 'PasswordRecovery', 'action' => 'request']]) ?>
<input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

<p class="text-white-50 mb-4">
    Informe o e-mail da sua conta. Se ele estiver cadastrado, enviaremos um link para você criar uma nova senha.
</p>

<div class="mb-4">
    <label for="email" class="form-label">
        <i class="fas fa-envelope me-2"></i>E-mail
    </label>
    <input type="email" class="form-control" id="email" name="email" required
           placeholder="Digite seu e-mail">
</div>

<button type="submit" class="btn btn-primary btn-login w-100">
    <i class="fas fa-paper-plane me-2"></i>Enviar link de redefinição
</button>
<?= $this->Form->end() ?>

<div class="text-center mt-3">
    <a href="/login" style="color: #00ff88; text-decoration: none; font-weight: 500;">Voltar ao login</a>
</div>
