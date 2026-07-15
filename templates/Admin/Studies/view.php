<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="studies view content fade-in-up">
    <!-- Header / Ações -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-graph-up-arrow me-2 icon-accent"></i>
                Estudo de Market Replay
            </h1>
            <p class="text-muted mb-0">Detalhes e performance do estudo #<?= h($study['id']) ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/studies/edit/<?= h($study['id']) ?>" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <form method="post" action="/admin/studies/delete/<?= h($study['id']) ?>" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este estudo?');">
                <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-2"></i>Excluir
                </button>
            </form>
            <a href="/admin/studies" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Lista de Estudos
            </a>
            <a href="/admin/studies/add" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Novo Estudo
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Informações do Estudante -->
        <div class="col-md-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-user me-2"></i>
                        Informações do Estudante
                    </h6>
                    <ul class="meta-list">
                        <li>
                            <span>Estudante</span>
                            <a href="/admin/students/view/<?= h($study['student']['id']) ?>" class="student-link">
                                <?= h($study['student']['name']) ?>
                            </a>
                        </li>
                        <li>
                            <span>Email</span>
                            <strong><?= h($study['student']['email'] ?: 'Não informado') ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Datas do Estudo -->
        <div class="col-md-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-calendar me-2"></i>
                        Datas do Estudo
                    </h6>
                    <ul class="meta-list">
                        <li>
                            <span>Data do Estudo</span>
                            <strong><?= h($study['study_date']) ?></strong>
                        </li>
                        <li>
                            <span>Criado</span>
                            <strong><?= h(date('d/m/Y H:i:s', strtotime($study['created']))) ?></strong>
                        </li>
                        <li>
                            <span>Modificado</span>
                            <strong><?= h(date('d/m/Y H:i:s', strtotime($study['modified']))) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Informações do Mercado -->
        <div class="col-md-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Informações do Mercado
                    </h6>
                    <?php if (isset($study['market']) && $study['market']): ?>
                        <ul class="meta-list">
                            <li>
                                <span>Mercado</span>
                                <span class="badge bg-info"><?= h($study['market']['name']) ?></span>
                            </li>
                            <li>
                                <span>Código</span>
                                <strong><?= h($study['market']['code']) ?></strong>
                            </li>
                            <?php if (!empty($study['market']['description'])): ?>
                                <li>
                                    <span>Descrição</span>
                                    <strong><?= h($study['market']['description']) ?></strong>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Mercado não especificado</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Performance de Trading -->
        <div class="col-md-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Performance de Trading
                    </h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="stat-box stat-box-success">
                                <div class="stat-value"><?= h($study['wins']) ?></div>
                                <div class="stat-label">Vitórias</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box stat-box-danger">
                                <div class="stat-value"><?= h($study['losses']) ?></div>
                                <div class="stat-label">Derrotas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Detalhadas -->
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-calculator me-2"></i>
                        Estatísticas Detalhadas
                    </h6>
                    <div class="row g-2 text-center">
                        <div class="col-md-6">
                            <div class="stat-box">
                                <div class="stat-value"><?= h($study['total_trades']) ?></div>
                                <div class="stat-label">Total de Trades</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-box <?= $study['win_rate'] >= 50 ? 'stat-box-success' : 'stat-box-warning' ?>">
                                <div class="stat-value"><?= number_format($study['win_rate'], 2) ?>%</div>
                                <div class="stat-label">Taxa de Vitória</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Financeira -->
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Performance Financeira
                    </h6>
                    <div class="row g-2 text-center">
                        <div class="col-md-6">
                            <div class="stat-box <?= $study['profit_loss'] >= 0 ? 'stat-box-success' : 'stat-box-danger' ?>">
                                <div class="stat-value"><?= $this->Currency::formatForUser($study['profit_loss'], $study['user']['currency'] ?? 'BRL') ?></div>
                                <div class="stat-label">Lucro/Prejuízo Total</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-box">
                                <div class="stat-value">
                                    <?= $study['total_trades'] > 0 ? $this->Currency::formatForUser($study['profit_loss'] / $study['total_trades'], $study['user']['currency'] ?? 'BRL') : $this->Currency::formatForUser(0, $study['user']['currency'] ?? 'BRL') ?>
                                </div>
                                <div class="stat-label">Média por Trade</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($study['notes'])): ?>
            <!-- Notas -->
            <div class="col-12">
                <div class="card glass">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="fas fa-sticky-note me-2"></i>
                            Notas
                        </h6>
                        <p class="mb-0"><?= nl2br(h($study['notes'])) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Operações -->
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Operações<?= !empty($operations) ? ' (' . count($operations) . ')' : '' ?>
                    </h6>

                    <?php if (!empty($operations)): ?>
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
                                    <?php foreach ($operations as $operation): ?>
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
                                                    <span class="badge bg-<?= $operation['gross_interval_result'] > 0 ? 'success' : 'danger' ?>">
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
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>Nenhuma operação encontrada para este estudo.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Análise de Performance -->
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <h6><i class="fas fa-lightbulb me-2"></i>Análise de Performance</h6>
                <?php if ($study['win_rate'] >= 60): ?>
                    <p class="mb-0"><strong>Excelente performance!</strong> Taxa de vitória de <?= number_format($study['win_rate'], 2) ?>% indica habilidades de trading sólidas.</p>
                <?php elseif ($study['win_rate'] >= 50): ?>
                    <p class="mb-0"><strong>Boa performance.</strong> Taxa de vitória de <?= number_format($study['win_rate'], 2) ?>% mostra potencial de lucratividade consistente.</p>
                <?php else: ?>
                    <p class="mb-0"><strong>Há espaço para melhorias.</strong> Taxa de vitória de <?= number_format($study['win_rate'], 2) ?>% sugere revisar a estratégia de trading.</p>
                <?php endif; ?>

                <?php if ($study['profit_loss'] > 0): ?>
                    <p class="mb-0">Lucro/prejuízo positivo de <?= $this->Currency::formatForUser($study['profit_loss'], $study['user']['currency'] ?? 'BRL') ?> demonstra gestão de risco eficaz.</p>
                <?php else: ?>
                    <p class="mb-0">Considere revisar o dimensionamento de posições e estratégias de gestão de risco.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-primary, #fff);
        margin-bottom: var(--space-md, 1rem);
        padding-bottom: var(--space-sm, 0.5rem);
        border-bottom: 1px solid var(--glass-border, rgba(0, 255, 136, 0.2));
    }

    .section-title i {
        color: #00ff88;
    }

    .meta-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .meta-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.9rem;
    }

    .meta-list li span {
        color: var(--text-muted, #b0b0b0);
    }

    .meta-list li strong {
        color: var(--text-primary, #fff);
        font-weight: 600;
    }

    .student-link {
        color: var(--text-primary, #fff);
        font-weight: 600;
        text-decoration: none;
    }

    .student-link:hover {
        color: #00ff88;
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--card-border, rgba(255, 255, 255, 0.1));
        border-radius: var(--radius-sm, 8px);
        padding: 1rem 0.5rem;
        height: 100%;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary, #fff);
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-muted, #b0b0b0);
        margin-top: 0.25rem;
    }

    .stat-box-success {
        background: rgba(0, 255, 136, 0.1);
        border-color: rgba(0, 255, 136, 0.3);
    }

    .stat-box-success .stat-value {
        color: #00ff88;
    }

    .stat-box-danger {
        background: rgba(248, 113, 113, 0.1);
        border-color: rgba(248, 113, 113, 0.3);
    }

    .stat-box-danger .stat-value {
        color: #f87171;
    }

    .stat-box-warning {
        background: rgba(255, 193, 7, 0.1);
        border-color: rgba(255, 193, 7, 0.3);
    }

    .stat-box-warning .stat-value {
        color: #ffc107;
    }
</style>
