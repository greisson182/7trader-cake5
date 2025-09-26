<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading">Ações</h4>
            <a href="/admin/studies/edit/<?= h($study['id']) ?>" class="btn btn-primary mb-2 btn-with-icon">
                <i class="fas fa-edit me-2"></i>Editar Estudo
            </a>
            <form method="post" action="/admin/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este estudo?');">
                <button type="submit" class="btn btn-danger mb-2 btn-with-icon">
                    <i class="fas fa-trash me-2"></i>Excluir
                </button>
            </form>
            <a href="/admin/studies" class="btn btn-secondary mb-2 btn-with-icon">
                <i class="fas fa-list me-2"></i>Lista de Estudos
            </a>
            <a href="/admin/studies/add" class="btn btn-success mb-2 btn-with-icon">
                <i class="fas fa-plus me-2"></i>Novo Estudo
            </a>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="studies view content">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-line"></i> Detalhes do Estudo de Market Replay</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-user"></i> Informações do Estudante</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Estudante:</strong>
                                        <a href="/admin/students/view/<?= h($study['student']['id']) ?>" class="text-decoration-none"><?= h($study['student']['name']) ?></a>
                                    </p>
                                    <p><strong>Email:</strong> <?= h($study['student']['email']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-calendar"></i> Datas do Estudo</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Data do Estudo:</strong> <?= h($study['study_date']) ?></p>
                                    <p><strong>Criado:</strong> <?= h(date('Y-m-d H:i:s', strtotime($study['created']))) ?></p>
                                    <p><strong>Modificado:</strong> <?= h(date('Y-m-d H:i:s', strtotime($study['modified']))) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Informações do Mercado</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($study['market']) && $study['market']): ?>
                                        <p><strong>Mercado:</strong>
                                            <span class="badge bg-info fs-6"><?= h($study['market']['name']) ?></span>
                                        </p>
                                        <p><strong>Código:</strong> <?= h($study['market']['code']) ?></p>
                                        <?php if (isset($study['market']['description']) && $study['market']['description']): ?>
                                            <p><strong>Descrição:</strong> <?= h($study['market']['description']) ?></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Mercado não especificado</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Performance de Trading</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-6">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h4><?= h($study['wins']) ?></h4>
                                                    <p class="mb-0">Vitórias</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body">
                                                    <h4><?= h($study['losses']) ?></h4>
                                                    <p class="mb-0">Derrotas</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-calculator"></i> Estatísticas Detalhadas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-6">
                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <h3><?= h($study['total_trades']) ?></h3>
                                                    <p class="mb-0">Total de Trades</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card <?= $study['win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?> text-white">
                                                <div class="card-body">
                                                    <h3><?= number_format($study['win_rate'], 2) ?>%</h3>
                                                    <p class="mb-0">Taxa de Vitória</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-dollar-sign"></i> Performance Financeira</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <div class="card <?= $study['profit_loss'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                                            <div class="card-body">
                                                <h3><?= $this->Currency::formatForUser($study['profit_loss'], $study['user']['currency'] ?? 'BRL') ?></h3>
                                                <p class="mb-0">Lucro/Prejuízo Total</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-secondary text-white">
                                            <div class="card-body">
                                                <h3><?= $study['total_trades'] > 0 ? $this->Currency::formatForUser($study['profit_loss'] / $study['total_trades'], $study['user']['currency'] ?? 'BRL') : $this->Currency::formatForUser(0, $study['user']['currency'] ?? 'BRL') ?></h3>
                                                <p class="mb-0">Média por Trade</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($study['notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-sticky-note"></i> Notas</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0"><?= nl2br(h($study['notes'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Seção de Operações -->
                <?php if (!empty($operations)): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Operações (<?= count($operations) ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Ativo</th>
                                                    <th>Tipo</th>
                                                    <th>Abertura</th>
                                                    <th>Fechamento</th>
                                                    <th>Duração</th>
                                                    <th>Resultado</th>
                                                    <th>P&L</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($operations as $operation): ?>
                                                    <tr>
                                                        <td><?= h($operation['asset']) ?></td>
                                                        <td>
                                                            <span class="badge badge-<?= $operation['operation_type'] === 'CALL' ? 'success' : 'danger' ?>">
                                                                <?= h($operation['operation_type']) ?>
                                                            </span>
                                                        </td>
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
                                                            <?php if ($operation['duration']): ?>
                                                                <?= h($operation['duration']) ?>
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($operation['result'] !== null): ?>
                                                                <span class="badge badge-<?= $operation['result'] === 'WIN' ? 'success' : 'danger' ?>">
                                                                    <?= h($operation['result']) ?>
                                                                </span>
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($operation['profit_loss'] !== null): ?>
                                                                <span class="text-<?= $operation['profit_loss'] >= 0 ? 'success' : 'danger' ?>">
                                                                    <?= $this->Currency::formatForUser($operation['profit_loss'], $study['user']['currency'] ?? 'BRL') ?>
                                                                </span>
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line"></i> Operações</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Nenhuma operação encontrada para este estudo.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb"></i> Análise de Performance</h6>
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
        </div>
    </div>
</div>
</div>