<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="studies form content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-line"></i> Add Estudo</h3>
        <a href="/admin/studies" class="btn btn-secondary btn-with-icon">
            <i class="fas fa-list"></i>
            <span>Listar Estudos</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/admin/studies/add" class="needs-validation" novalidate>
                <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="study_date" class="form-label">Data do Estudo</label>
                            <input type="date" class="form-control" id="study_date" name="study_date" required>
                            <div class="invalid-feedback">
                                Please provide a valid study date.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="market_id" class="form-label">Mercado</label>
                            <select class="form-select" id="market_id" name="market_id" required>
                                <option value="">Selecione um mercado</option>
                                <?php if (!empty($markets)): ?>
                                    <?php foreach ($markets as $market): ?>
                                        <option value="<?= $market['id'] ?>"><?= htmlspecialchars($market['name']) ?> (<?= htmlspecialchars($market['code']) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione um mercado.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="account_id" class="form-label">Tipo de Conta</label>
                            <select class="form-select" id="account_id" name="account_id" required>
                                <?php if (!empty($accounts)): ?>
                                    <?php foreach ($accounts as $account): ?>
                                        <option value="<?= $account['id'] ?>"><?= htmlspecialchars($account['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione um mercado.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="profit_loss" class="form-label">Lucro/Prejuízo (R$)</label>
                            <input type="number" class="form-control" id="profit_loss" name="profit_loss" step="0.01" required>
                            <div class="invalid-feedback">
                                Please provide a valid profit/loss amount.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="wins" class="form-label">Wins</label>
                            <input type="number" class="form-control" id="wins" name="wins" min="0" required>
                            <div class="invalid-feedback">
                                Please provide a valid number of wins.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="losses" class="form-label">Losses</label>
                            <input type="number" class="form-control" id="losses" name="losses" min="0" required>
                            <div class="invalid-feedback">
                                Please provide a valid number of losses.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Diário de Trade</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Adicione suas observações sobre este estudo..."></textarea>
                            <div class="form-text">Campo opcional para anotações e observações sobre o estudo.</div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/admin/studies" class="btn btn-secondary me-md-2 btn-with-icon">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="btn btn-primary btn-with-icon">
                        <i class="fas fa-paper-plane"></i>
                        <span>Submit</span>
                    </button>
                </div>
            </form>
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
</script>