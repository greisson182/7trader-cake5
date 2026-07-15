<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="bi bi-speedometer2 me-2 icon-accent"></i>
                        Dashboard Administrativo
                    </h1>
                    <p class="mb-0 text-muted">Visão geral do sistema e performance dos alunos</p>
                </div>
                <div>
                    <a href="/admin/students" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>Ver Todos os Estudantes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="stat-number mb-2"><?= number_format($totalStudents) ?></h3>
                    <p class="stat-label mb-0">Total de Alunos</p>
                    <small class="text-muted"><?= $activeStudents ?> ativos</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-info">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h3 class="stat-number mb-2"><?= number_format($totalStudies) ?></h3>
                    <p class="stat-label mb-0">Total de Estudos</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-success">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3 class="stat-number mb-2 text-success"><?= $overallWinRate ?>%</h3>
                    <p class="stat-label mb-0">Taxa de Acerto Geral</p>
                    <small class="text-muted"><?= number_format($totalTrades) ?> trades</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 <?= $totalProfitLoss >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h3 class="stat-number mb-2 <?= $totalProfitLoss >= 0 ? 'text-success' : 'text-danger' ?>">
                        R$ <?= number_format($totalProfitLoss, 2, ',', '.') ?>
                    </h3>
                    <p class="stat-label mb-0">P&L Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Performance Mensal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Performance Mensal (Últimos 12 meses)
                    </h6>
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top e Worst Performers -->
    <div class="row mb-4">
        <!-- Top 5 Alunos -->
        <div class="col-lg-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-trophy text-warning me-2"></i>
                        Top 5 Alunos
                    </h6>
                    <?php if (!empty($topStudents)): ?>
                        <?php foreach ($topStudents as $index => $student): ?>
                            <div class="d-flex align-items-center mb-2 <?= $index < count($topStudents) - 1 ? 'border-bottom pb-2' : '' ?>">
                                <div class="rank-badge me-3">
                                    <span class="badge bg-warning text-dark"><?= $index + 1 ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="/admin/students/view/<?= $student['id'] ?>" class="text-decoration-none student-link">
                                            <?= h($student['name']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?= $student['total_studies'] ?> estudos •
                                        <?php
                                        $totalTrades = ($student['total_wins'] + $student['total_losses']);
                                        $winRate = $totalTrades > 0 ? round(($student['total_wins'] / $totalTrades) * 100, 2) : 0;
                                        ?>
                                        <?= $winRate ?>% win rate
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-success">
                                        <?= $this->Currency::formatForUser($student['total_profit_loss'], 'BRL') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Nenhum dado disponível</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alunos que Precisam de Atenção -->
        <div class="col-lg-6">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Precisam de Atenção
                    </h6>
                    <?php if (!empty($worstStudents)): ?>
                        <?php foreach ($worstStudents as $index => $student): ?>
                            <div class="d-flex align-items-center mb-2 <?= $index < count($worstStudents) - 1 ? 'border-bottom pb-2' : '' ?>">
                                <div class="rank-badge me-3">
                                    <span class="badge bg-danger"><?= $index + 1 ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="/admin/students/view/<?= $student['id'] ?>" class="text-decoration-none student-link">
                                            <?= h($student['name']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?= $student['total_studies'] ?> estudos •
                                        <?php
                                        $totalTrades = ($student['total_wins'] + $student['total_losses']);
                                        $winRate = $totalTrades > 0 ? round(($student['total_wins'] / $totalTrades) * 100, 2) : 0;
                                        ?>
                                        <?= $winRate ?>% win rate
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-danger">
                                        <?= $this->Currency::formatForUser($student['total_profit_loss'], 'BRL') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">Nenhum dado disponível</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Atividade Recente -->
    <div class="row">
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Atividade Recente
                    </h6>
                    <?php if (!empty($recentActivity)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Aluno</th>
                                        <th>Data</th>
                                        <th>Wins</th>
                                        <th>Losses</th>
                                        <th>Win Rate</th>
                                        <th>P&L</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <tr>
                                            <td>
                                                <a href="/admin/students/view/<?= $activity['student_id'] ?>" class="text-decoration-none student-link">
                                                    <?= h($activity['student_name']) ?>
                                                </a>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($activity['study_date'])) ?></td>
                                            <td><span class="badge bg-success"><?= $activity['wins'] ?></span></td>
                                            <td><span class="badge bg-danger"><?= $activity['losses'] ?></span></td>
                                            <td>
                                                <?php
                                                $totalTrades = $activity['wins'] + $activity['losses'];
                                                $winRate = $totalTrades > 0 ? round(($activity['wins'] / $totalTrades) * 100, 2) : 0;
                                                ?>
                                                <span class="badge <?= $winRate >= 50 ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= $winRate ?>%
                                                </span>
                                            </td>
                                            <td class="<?= $activity['profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= $this->Currency::formatForUser($activity['profit_loss'], 'BRL') 
                                                ?>
                                            </td>
                                            <td>
                                                <a href="/admin/studies/view/<?= $activity['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Nenhuma atividade recente</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Performance Mensal
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

        const monthlyLabels = [
            <?php foreach ($monthlyData as $data): ?> 
                '<?= $this->MorfDate::formatMonthYear($data['month'], $data['year'], true) ?>',
            <?php endforeach; ?>
        ];

        const monthlyStudies = [
            <?php foreach ($monthlyData as $data): ?>
                <?= $data['total_studies'] ?>,
            <?php endforeach; ?>
        ];

        const monthlyProfitLoss = [
            <?php foreach ($monthlyData as $data): ?>
                <?= $data['total_profit_loss'] ?>,
            <?php endforeach; ?>
        ];

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Estudos',
                    data: monthlyStudies,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    yAxisID: 'y'
                }, {
                    label: 'P&L (R$)',
                    data: monthlyProfitLoss,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Mês'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Número de Estudos'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'P&L (R$)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 1) {
                                    return 'P&L: R$ ' + context.parsed.y.toFixed(2);
                                }
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

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

    .student-link {
        color: var(--text-primary, #fff);
        font-weight: 600;
    }

    .student-link:hover {
        color: #00ff88;
    }
</style>