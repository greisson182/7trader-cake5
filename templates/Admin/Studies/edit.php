<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading">Ações</h4>
            <form method="post" action="/admin/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir # <?= h($study['id']) ?>?');">
                <button type="submit" class="btn btn-danger btn-with-icon">
                    <i class="fas fa-trash me-2"></i>Excluir
                </button>
            </form>
            <a href="/admin/studies" class="btn btn-secondary btn-with-icon mb-2">
                <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
            </a>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="studies form content">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit"></i> Editar Estudo de Market Replay</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/studies/edit/<?= h($study['id']) ?>" class="needs-validation" novalidate>
                        <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="study_date" class="form-label">Data do Estudo</label>
                                        <input type="date" name="study_date" id="study_date" class="form-control" value="<?= h($study['study_date']) ?>" required>
                                        <div class="form-text">A data em que o estudo foi realizado</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="market_id" class="form-label">Mercado</label>
                                        <select class="form-select" id="market_id" name="market_id" required>
                                            <option value="">Selecione um mercado</option>
                                            <?php if (!empty($markets)): ?>
                                                <?php foreach ($markets as $market): ?>
                                                    <option value="<?= $market['id'] ?>" <?= ($study['market_id'] == $market['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($market['name']) ?> (<?= htmlspecialchars($market['code']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="form-text">Mercado associado ao estudo</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="account_id" class="form-label">Tipo de Conta</label>
                                        <select class="form-select" id="account_id" name="account_id" required>
                                            <option value="">Selecione um mercado</option>
                                            <?php if (!empty($accounts)): ?>
                                                <?php foreach ($accounts as $account): ?>
                                                    <option value="<?= $account['id'] ?>" <?= ($study['account_id'] == $account['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($account['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="form-text">Conta Real ou Simulador</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="profit_loss" class="form-label">Lucro/Prejuízo (R$)</label>
                                        <input type="number" name="profit_loss" id="profit_loss" class="form-control" value="<?= h($study['profit_loss']) ?>" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="wins" class="form-label">Gain</label>
                                        <input type="number" name="wins" id="wins" class="form-control" value="<?= h($study['wins']) ?>" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="losses" class="form-label">Loss</label>
                                        <input type="number" name="losses" id="losses" class="form-control" value="<?= h($study['losses']) ?>" min="0" required>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Diário de Trade</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Adicione suas observações sobre este estudo..."><?= h($study['notes'] ?? '') ?></textarea>
                                        <div class="form-text">Campo opcional para anotações e observações sobre o estudo.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Taxa de Acerto Atual:</strong> <?= isset($study['win_rate']) ? h($study['win_rate']) : '0' ?>%
                                (<?= isset($study['wins']) ? h($study['wins']) : '0' ?> vitórias de <?= isset($study['total_trades']) ? h($study['total_trades']) : '0' ?> operações totais)
                            </div>
                        </fieldset>
                        <div class="form-actions">
                            <a href="/admin/studies" class="btn btn-secondary btn-with-icon">
                                <i class="fas fa-times"></i>
                                <span>Cancelar</span>
                            </a>
                            <button type="submit" class="btn btn-primary btn-with-icon">
                                <i class="fas fa-save"></i>
                                <span>Salvar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Seção de Operações -->
            <?php if (!empty($operations)): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line"></i> Operações (<?= count($operations) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Ativo</th>
                                        <th>Abertura</th>
                                        <th>Fechamento</th>
                                        <th>Duração</th>
                                        <th>Resultado</th>
                                        <th>Qtd Compra</th>
                                        <th>Qtd Venda</th>
                                        <th>Lado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                   // pr($operations);
                                    foreach ($operations as $operation): ?>
                                        <tr>
                                            <td><?= h($operation['asset']) ?></td>
                                            <td>
                                                <?php if ($operation['open_time']): ?>
                                                    <?= $operation['open_time']->format('d/m/Y H:i:s') ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($operation['close_time']): ?>
                                                    <?= $operation['close_time']->format('d/m/Y H:i:s') ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($operation['trade_duration']): ?>
                                                    <?= $this->Custom->formatarTempo($operation['trade_duration']->format('H:i:s')) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($operation['gross_interval_result'] !== null): ?>
                                                    <span class="badge badge-<?= $operation['gross_interval_result'] > 0 ? 'success' : 'danger' ?>">
                                                        <?= $this->Currency::formatForUser($operation['gross_interval_result'], $study['user']['currency'] ?? 'BRL') ?>
                                                    </span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($operation['buy_quantity'] !== null): ?>
                                                    <span class="text-<?= $operation['buy_quantity'] >= 0 ? 'success' : 'danger' ?>">
                                                        <?= $operation['buy_quantity'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($operation['sell_quantity'] !== null): ?>
                                                    <span class="text-<?= $operation['sell_quantity'] >= 0 ? 'success' : 'danger' ?>">
                                                        <?= $operation['sell_quantity'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if ($operation['side'] !== null): ?>
                                                    <span class="text-<?= $operation['side'] === 'C' ? 'success' : 'danger' ?>">
                                                        <?= $operation['side'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line"></i> Operações</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhuma operação encontrada para este estudo.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const winsInput = document.querySelector('input[name="wins"]');
        const lossesInput = document.querySelector('input[name="losses"]');

        function updateWinRate() {
            const wins = parseInt(winsInput.value) || 0;
            const losses = parseInt(lossesInput.value) || 0;
            const total = wins + losses;
            const winRate = total > 0 ? ((wins / total) * 100).toFixed(2) : 0;

            // Update or create win rate display
            let winRateDisplay = document.getElementById('win-rate-display');
            if (!winRateDisplay) {
                winRateDisplay = document.createElement('div');
                winRateDisplay.id = 'win-rate-display';
                winRateDisplay.className = 'alert alert-success mt-2';
                lossesInput.parentNode.appendChild(winRateDisplay);
            }

            winRateDisplay.innerHTML = `<strong>Taxa de Acerto Atualizada: ${winRate}%</strong> (${wins} vitórias de ${total} operações totais)`;
        }

        winsInput.addEventListener('input', updateWinRate);
        lossesInput.addEventListener('input', updateWinRate);
    });
</script>