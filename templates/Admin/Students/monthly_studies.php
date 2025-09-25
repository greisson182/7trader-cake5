<?php
/**
 * @var \App\View\AppView $this
 * @var array $student
 * @var array $studies
 * @var string $year
 * @var string $month
 * @var string $monthName
 * @var int $totalStudies
 * @var int $totalWins
 * @var int $totalLosses
 * @var int $totalTrades
 * @var float $totalProfitLoss
 * @var float $winRate
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Estudos de <?= h($monthName) ?> <?= h($year) ?></h2>
                    <p class="text-muted mb-0">Estudante: <?= h($student['name']) ?></p>
                </div>
                <div>
                    <a href="/admin/students/dashboard/<?= h($student['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($totalStudies) ?></h3>
                            <small>Total de Estudos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($totalTrades) ?></h3>
                            <small>Total de Trades</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($totalWins) ?></h3>
                            <small>Vitórias</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($totalLosses) ?></h3>
                            <small>Derrotas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($winRate, 1) ?>%</h3>
                            <small>Taxa de Vitória</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card <?= $totalProfitLoss >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">R$ <?= number_format($totalProfitLoss, 2, ',', '.') ?></h3>
                            <small>P&L Total</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Studies Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lista de Estudos - <?= h($monthName) ?> <?= h($year) ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($studies)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum estudo encontrado</h5>
                            <p class="text-muted">Não há estudos registrados para este período.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Data do Estudo</th>
                                        <th>Vitórias</th>
                                        <th>Derrotas</th>
                                        <th>Taxa de Vitória</th>
                                        <th>P&L</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studies as $study): ?>
                                        <?php
                                        $studyTrades = $study['wins'] + $study['losses'];
                                        $studyWinRate = $studyTrades > 0 ? ($study['wins'] / $studyTrades) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($study['study_date'])) ?></td>
                                            <td><span class="badge bg-success"><?= $study['wins'] ?></span></td>
                                            <td><span class="badge bg-danger"><?= $study['losses'] ?></span></td>
                                            <td>
                                                <span class="badge <?= $studyWinRate >= 50 ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= number_format($studyWinRate, 1) ?>%
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos removidos - usando novo sistema de tabelas do style.css -->