<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<?= $this->Form->create(null, ['url' => '/admin/users/login']) ?>
<input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
<div class="mb-3">
    <label for="username" class="form-label">
        <i class="fas fa-user me-2"></i>Usuário ou Email
    </label>
    <?= $this->Form->control('user', [
        'type' => 'text',
        'class' => 'form-control',
        'id' => 'username',
        'required' => true,
        'placeholder' => 'Digite seu usuário ou email',
        'label' => false
    ]) ?>
</div>

<div class="mb-4">
    <label for="password" class="form-label">
        <i class="fas fa-lock me-2"></i>Senha
    </label>
    <?= $this->Form->control('pass', [
        'type' => 'password',
        'class' => 'form-control',
        'id' => 'password',
        'required' => true,
        'placeholder' => 'Digite sua senha',
        'label' => false
    ]) ?>
</div>

<button type="submit" class="btn btn-primary btn-login w-100">
    <i class="fas fa-sign-in-alt me-2"></i>Entrar
</button>
<?= $this->Form->end() ?>