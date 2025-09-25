<?php
/**
 * @var array $market
 */
?>
<div class="markets form content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-plus-circle"></i> Adicionar Mercado</h3>
        <a href="/admin/markets" class="btn btn-secondary btn-with-icon">
            <i class="bi bi-list"></i>
            <span>Listar Mercados</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/admin/markets/add" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Mercado <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= h($market['name'] ?? '') ?>"
                                   placeholder="Ex: WIN Futuro"
                                   required>
                            <div class="invalid-feedback">
                                Por favor, forneça um nome válido para o mercado.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Código do Mercado <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="code" 
                                   name="code" 
                                   value="<?= h($market['code'] ?? '') ?>"
                                   placeholder="Ex: WINFUT"
                                   style="text-transform: uppercase;"
                                   maxlength="20"
                                   required>
                            <div class="invalid-feedback">
                                Por favor, forneça um código válido para o mercado.
                            </div>
                            <div class="form-text">Código único para identificar o mercado (máx. 20 caracteres).</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Moeda <span class="text-danger">*</span></label>
                            <select class="form-select" 
                                    id="currency" 
                                    name="currency" 
                                    required>
                                <option value="">Selecione a moeda...</option>
                                <option value="BRL" <?= ($market['currency'] ?? '') === 'BRL' ? 'selected' : '' ?>>
                                    🇧🇷 Real Brasileiro (BRL)
                                </option>
                                <option value="USD" <?= ($market['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>
                                    🇺🇸 Dólar Americano (USD)
                                </option>
                                <option value="EUR" <?= ($market['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>
                                    🇪🇺 Euro (EUR)
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione uma moeda válida.
                            </div>
                            <div class="form-text">Moeda base do mercado para cálculos financeiros.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Espaço para futuras expansões -->
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Descrição detalhada do mercado..."><?= h($market['description'] ?? '') ?></textarea>
                    <div class="form-text">Descrição opcional para fornecer mais informações sobre o mercado.</div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="active" 
                               name="active" 
                               value="1" 
                               <?= (!isset($market['active']) || $market['active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="active">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Mercado ativo
                        </label>
                        <div class="form-text">Mercados inativos não aparecerão nas opções de seleção.</div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/admin/markets" class="btn btn-secondary me-md-2 btn-with-icon">
                        <i class="bi bi-x-circle"></i>
                        <span>Cancelar</span>
                    </a>
                    <button type="submit" class="btn btn-primary btn-with-icon">
                        <i class="bi bi-check-circle"></i>
                        <span>Salvar Mercado</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-with-icon {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label .text-danger {
    font-size: 0.9em;
}

.card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
}

.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}

.form-check-input:checked {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}

#code {
    font-family: 'Courier New', monospace;
    font-weight: 600;
}
</style>

<script>
// Validação do formulário
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

// Converter código para maiúsculo automaticamente
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>