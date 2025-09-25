<?php
require_once ROOT . DS . 'src' . DS . 'Helper' . DS . 'CurrencyHelper.php';
use App\Helper\CurrencyHelper;
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="bi bi-person-gear me-2"></i>Editar Perfil</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/profile/edit" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= isset($user['username']) ? htmlspecialchars($user['username']) : '' ?>" required>
                                    <div class="form-text">Username para login no sistema</div>
                                    <div class="invalid-feedback">
                                        Por favor, forneça um username válido.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>" required>
                                    <div class="form-text">Endereço de email para contato</div>
                                    <div class="invalid-feedback">
                                        Por favor, forneça um email válido.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Função</label>
                                    <input type="text" class="form-control" id="role" 
                                           value="<?= isset($user['role']) ? ucfirst($user['role']) : '' ?>" readonly>
                                    <div class="form-text">Sua função no sistema (não pode ser alterada)</div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6><i class="bi bi-shield-lock me-2"></i>Alterar Senha</h6>
                        <p class="text-muted small">Deixe os campos em branco se não quiser alterar a senha</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Senha Atual</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" 
                                           placeholder="Digite sua senha atual">
                                    <div class="form-text">Necessário para confirmar alteração de senha</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nova Senha</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           placeholder="Digite a nova senha">
                                    <div class="form-text">Mínimo 6 caracteres</div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-1"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Informações do Perfil -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle me-2"></i>Informações do Perfil</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($user) && $user): ?>
                        <div class="mb-3">
                            <strong>Username Atual:</strong> 
                            <span class="badge bg-primary"><?= htmlspecialchars($user['username']) ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Função:</strong> 
                            <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-info' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Status da Conta:</strong> 
                            <span class="badge <?= $user['active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $user['active'] ? 'Ativa' : 'Inativa' ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Conta Criada:</strong> 
                            <small class="text-muted"><?= isset($user['created']) ? date('d/m/Y H:i', strtotime($user['created'])) : 'N/A' ?></small>
                        </div>
                        <div class="mb-3">
                            <strong>Última Modificação:</strong> 
                            <small class="text-muted"><?= isset($user['modified']) ? date('d/m/Y H:i', strtotime($user['modified'])) : 'N/A' ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Dicas de Segurança -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="bi bi-shield-check me-2"></i>Dicas de Segurança</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Use uma senha forte com pelo menos 8 caracteres</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Inclua letras maiúsculas, minúsculas e números</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Não compartilhe suas credenciais</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Altere sua senha regularmente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Validação de senha
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const currentPasswordField = document.getElementById('current_password');
    
    if (password.length > 0) {
        currentPasswordField.setAttribute('required', 'required');
        if (password.length < 6) {
            this.setCustomValidity('A senha deve ter pelo menos 6 caracteres');
        } else {
            this.setCustomValidity('');
        }
    } else {
        currentPasswordField.removeAttribute('required');
        this.setCustomValidity('');
    }
});

document.getElementById('current_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    
    if (newPassword.length > 0 && this.value.length === 0) {
        this.setCustomValidity('Digite sua senha atual para confirmar a alteração');
    } else {
        this.setCustomValidity('');
    }
});
</script>