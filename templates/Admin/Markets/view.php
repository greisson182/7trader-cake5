<?php
/**
 * @var array $market
 * @var array $studies
 */
?>
<div class="markets view content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-graph-up"></i> Detalhes do Mercado</h3>
        <div class="btn-group" role="group">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                <a href="/admin/markets/edit/<?= $market['id'] ?>" class="btn btn-primary btn-with-icon">
                    <i class="fas fa-edit me-2"></i>Editar Mercado
                </a>
                <form method="post" action="/admin/markets/delete/<?= $market['id'] ?>" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este mercado?');">
                    <button type="submit" class="btn btn-danger btn-with-icon">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </button>
                </form>
            <?php endif; ?>
            <a href="/admin/markets" class="btn btn-secondary btn-with-icon">
                <i class="fas fa-list me-2"></i>Lista de Mercados
            </a>
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                <a href="/admin/markets/add" class="btn btn-success btn-with-icon">
                    <i class="fas fa-plus me-2"></i>Novo Mercado
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> Informações do Mercado</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">Nome:</label>
                                <div class="info-value"><?= h($market['name']) ?></div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="info-label">Código:</label>
                                <div class="info-value">
                                    <span class="badge bg-primary fs-6"><?= h($market['code']) ?></span>
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="info-label">Status:</label>
                                <div class="info-value">
                                    <?php if ($market['active']): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Inativo
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="info-label">Moeda:</label>
                                <div class="info-value">
                                    <?php
                                    $currencyInfo = [
                                        'BRL' => ['icon' => '🇧🇷', 'name' => 'Real Brasileiro'],
                                        'USD' => ['icon' => '🇺🇸', 'name' => 'Dólar Americano'],
                                        'EUR' => ['icon' => '🇪🇺', 'name' => 'Euro']
                                    ];
                                    $currency = $currencyInfo[$market['currency']] ?? ['icon' => '', 'name' => $market['currency']];
                                    ?>
                                    <span class="badge bg-warning text-dark fs-6">
                                        <?= $currency['icon'] ?> <?= $market['currency'] ?>
                                    </span>
                                    <small class="text-muted ms-2"><?= $currency['name'] ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="info-label">ID do Sistema:</label>
                                <div class="info-value text-muted">#<?= h($market['id']) ?></div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="info-label">Criado em:</label>
                                <div class="info-value"><?= date('d/m/Y H:i:s', strtotime($market['created_at'])) ?></div>
                            </div>
                            <?php if (!empty($market['updated_at']) && $market['updated_at'] !== $market['created_at']): ?>
                                <div class="info-item mb-3">
                                    <label class="info-label">Última atualização:</label>
                                    <div class="info-value"><?= date('d/m/Y H:i:s', strtotime($market['updated_at'])) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($market['description'])): ?>
                        <hr class="my-3">
                        <div class="info-item">
                            <label class="info-label">Descrição:</label>
                            <div class="info-value">
                                <p class="mb-0"><?= nl2br(h($market['description'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-bar-chart"></i> Estatísticas</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($studies)): ?>
                        <?php 
                        $totalStudies = count($studies);
                        $totalWins = array_sum(array_column($studies, 'wins'));
                        $totalLosses = array_sum(array_column($studies, 'losses'));
                        $totalTrades = $totalWins + $totalLosses;
                        $overallWinRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;
                        $totalProfitLoss = array_sum(array_column($studies, 'profit_loss'));
                        ?>
                        <div class="stat-item mb-3">
                            <div class="stat-number text-primary"><?= $totalStudies ?></div>
                            <div class="stat-label">Total de Estudos</div>
                        </div>
                        <div class="stat-item mb-3">
                            <div class="stat-number text-info"><?= $totalTrades ?></div>
                            <div class="stat-label">Total de Trades</div>
                        </div>
                        <div class="stat-item mb-3">
                            <div class="stat-number <?= $overallWinRate >= 50 ? 'text-success' : 'text-warning' ?>">
                                <?= number_format($overallWinRate, 1) ?>%
                            </div>
                            <div class="stat-label">Taxa de Acerto Geral</div>
                        </div>
                        <div class="stat-item mb-3">
                            <div class="stat-number <?= $totalProfitLoss >= 0 ? 'text-success' : 'text-danger' ?>">
                                R$ <?= number_format($totalProfitLoss, 2, ',', '.') ?>
                            </div>
                            <div class="stat-label">P&L Total</div>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-graph-down fs-1 mb-3 d-block"></i>
                            <p class="mb-0">Nenhum estudo registrado ainda para este mercado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($studies)): ?>
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-clock-history"></i> Estudos Recentes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Data do Estudo</th>
                            <th>Estudante</th>
                            <th>Vitórias</th>
                            <th>Derrotas</th>
                            <th>Taxa de Acerto</th>
                            <th>P&L</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($studies, 0, 10) as $study): ?>
                            <?php 
                            $totalTrades = $study['wins'] + $study['losses'];
                            $winRate = $totalTrades > 0 ? ($study['wins'] / $totalTrades) * 100 : 0;
                            ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($study['study_date'])) ?></td>
                                <td>
                                    <a href="/admin/students/view/<?= $study['student_id'] ?>" class="text-decoration-none">
                                        <?= h($study['student_name'] ?? 'N/A') ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?= $study['wins'] ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-danger"><?= $study['losses'] ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= $winRate >= 50 ? 'bg-success' : 'bg-warning' ?>">
                                        <?= number_format($winRate, 1) ?>%
                                    </span>
                                </td>
                                <td>
                                    <span class="<?= $study['profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        R$ <?= number_format($study['profit_loss'], 2, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/studies/view/<?= $study['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (count($studies) > 10): ?>
                <div class="text-center mt-3">
                    <p class="text-muted">Mostrando os 10 estudos mais recentes de <?= count($studies) ?> total.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.btn-with-icon {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
}

.info-item {
    margin-bottom: 1rem;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    font-size: 1rem;
    color: #212529;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(var(--bs-primary-rgb), 0.05);
    border-radius: 8px;
    border: 1px solid rgba(var(--bs-primary-rgb), 0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.9rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.badge {
    font-size: 0.8rem;
}
</style>