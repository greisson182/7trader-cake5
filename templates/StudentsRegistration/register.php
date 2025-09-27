<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>

<!-- Rate Limit Warning -->
<div id="rate-limit-warning" class="alert alert-warning d-none" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Atenção!</strong> Você tem <span id="remaining-attempts"></span> tentativas restantes.
    Após esgotar as tentativas, você precisará aguardar <span id="reset-time"></span> minutos para tentar novamente.
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById('username');
    const usernameField = usernameInput.closest('.mb-3');
    let timeoutId;
    
    // Create feedback element
    const feedbackElement = document.createElement('div');
    feedbackElement.className = 'username-feedback mt-1';
    feedbackElement.style.fontSize = '0.875rem';
    usernameField.appendChild(feedbackElement);
    
    usernameInput.addEventListener('input', function() {
        const username = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(timeoutId);
        
        // Clear feedback if empty
        if (username.length === 0) {
            feedbackElement.textContent = '';
            feedbackElement.className = 'username-feedback mt-1';
            usernameInput.classList.remove('is-valid', 'is-invalid');
            return;
        }
        
        // Show loading state
        feedbackElement.textContent = 'Verificando...';
        feedbackElement.className = 'username-feedback mt-1 text-info';
        usernameInput.classList.remove('is-valid', 'is-invalid');
        
        // Debounce the request
        timeoutId = setTimeout(function() {
            fetch('/check-username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?= $csrfToken ?>'
                },
                body: JSON.stringify({
                    username: username
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedbackElement.textContent = data.message;
                    feedbackElement.className = 'username-feedback mt-1 text-danger';
                    usernameInput.classList.remove('is-valid');
                    usernameInput.classList.add('is-invalid');
                } else {
                    feedbackElement.textContent = data.message;
                    feedbackElement.className = 'username-feedback mt-1 text-success';
                    usernameInput.classList.remove('is-invalid');
                    usernameInput.classList.add('is-valid');
                }
            })
            .catch(error => {
                console.error('Erro na verificação:', error);
                feedbackElement.textContent = 'Erro ao verificar nome de usuário';
                feedbackElement.className = 'username-feedback mt-1 text-warning';
                usernameInput.classList.remove('is-valid', 'is-invalid');
            });
        }, 500); // Wait 500ms after user stops typing
    });

    // Rate limiting monitoring
    function checkRateLimit() {
        fetch('<?= $this->Url->build(['controller' => 'StudentsRegistration', 'action' => 'getRateLimitStatus']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= $csrfToken ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            const warningDiv = document.getElementById('rate-limit-warning');
            const remainingSpan = document.getElementById('remaining-attempts');
            const resetTimeSpan = document.getElementById('reset-time');
            
            if (data.remaining_attempts <= 2 && data.remaining_attempts > 0) {
                remainingSpan.textContent = data.remaining_attempts;
                resetTimeSpan.textContent = Math.ceil(data.time_until_reset / 60);
                warningDiv.classList.remove('d-none');
            } else if (data.remaining_attempts <= 0) {
                warningDiv.classList.remove('d-none', 'alert-warning');
                warningDiv.classList.add('alert-danger');
                warningDiv.innerHTML = '<i class="fas fa-ban"></i> <strong>Limite excedido!</strong> Aguarde ' + Math.ceil(data.time_until_reset / 60) + ' minutos para tentar novamente.';
                
                // Disable form
                const form = document.querySelector('.registration-form');
                const inputs = form.querySelectorAll('input, button');
                inputs.forEach(input => input.disabled = true);
            } else {
                warningDiv.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Erro ao verificar rate limit:', error);
        });
    }

    // Check rate limit on page load and periodically
    checkRateLimit();
    setInterval(checkRateLimit, 30000); // Check every 30 seconds
});
</script>
