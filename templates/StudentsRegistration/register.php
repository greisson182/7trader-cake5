<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student $student
 */
?>

<?= $this->Form->create(null, [
    'url' => ['controller' => 'StudentsRegistration', 'action' => 'register'],
    'class' => 'registration-form'
]) ?>

<div class="row">
    <div class="col-md-12 mb-3">
        <label for="name" class="form-label">Nome Completo</label>
        <?= $this->Form->control('name', [
            'type' => 'text',
            'class' => 'form-control',
            'placeholder' => 'Digite seu nome completo',
            'required' => true,
            'label' => false
        ]) ?>
    </div>
    <div class="col-md-12 mb-3">
        <label for="email" class="form-label">E-mail</label>
        <?= $this->Form->control('email', [
            'type' => 'email',
            'class' => 'form-control',
            'placeholder' => 'Digite seu e-mail',
            'required' => true,
            'label' => false
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Telefone</label>
        <?= $this->Form->control('phone', [
            'type' => 'text',
            'class' => 'form-control',
            'placeholder' => '(11) 99999-9999',
            'label' => false
        ]) ?>
    </div>
    <div class="col-md-6 mb-3">
        <label for="username" class="form-label">Nome de Usuário</label>
        <?= $this->Form->control('username', [
            'type' => 'text',
            'class' => 'form-control',
            'placeholder' => 'Digite seu username',
            'required' => true,
            'label' => false
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Senha</label>
        <?= $this->Form->control('password', [
            'type' => 'password',
            'class' => 'form-control',
            'placeholder' => 'Digite sua senha',
            'required' => true,
            'label' => false
        ]) ?>
    </div>
    <div class="col-md-6 mb-3">
        <label for="confirm_password" class="form-label">Confirmar Senha</label>
        <?= $this->Form->control('confirm_password', [
            'type' => 'password',
            'class' => 'form-control',
            'placeholder' => 'Confirme sua senha',
            'required' => true,
            'label' => false
        ]) ?>
    </div>
</div>

<div class="d-grid gap-2 mt-4">
    <?= $this->Form->button('Criar Conta', [
        'type' => 'submit',
        'class' => 'btn btn-login'
    ]) ?>
</div>

<?= $this->Form->end() ?>

<div class="text-center mt-3">
    <p class="text-white-50 mb-2">Já possui uma conta?</p>
    <a href="/admin/users/login" style="color: #00ff88; text-decoration: none; font-weight: 500;">Fazer Login</a>
</div>
