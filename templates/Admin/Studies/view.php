<?php
$csrfToken = $this->request->getAttribute('csrfToken');

// Evolução do capital ao longo do dia: soma acumulada do resultado de cada
// operação, na ordem em que fecharam, começando do zero.
$capitalLabels = ['Início'];
$capitalValues = [0];
$running = 0;
foreach ($operations as $operation) {
    $running += (float)($operation['gross_interval_result'] ?? 0);
    $timeRef = $operation['close_time'] ?? $operation['open_time'] ?? null;
    $capitalLabels[] = $timeRef ? $timeRef->format('H:i:s') : '';
    $capitalValues[] = round($running, 2);
}

// Resultado bruto de cada operação individual (não acumulado) + contagem
// de vencedoras/perdedoras/zeradas pra o gráfico de eficiência.
$operationLabels = [];
$operationResults = [];
$winCount = 0;
$lossCount = 0;
$zeroCount = 0;
foreach ($operations as $operation) {
    $result = (float)($operation['gross_interval_result'] ?? 0);
    $timeRef = $operation['open_time'] ?? $operation['close_time'] ?? null;
    $operationLabels[] = $timeRef ? $timeRef->format('d/m/Y H:i:s') : '';
    $operationResults[] = round($result, 2);
    if ($result > 0) {
        $winCount++;
    } elseif ($result < 0) {
        $lossCount++;
    } else {
        $zeroCount++;
    }
}
$totalOps = count($operations);
$winPct = $totalOps > 0 ? round($winCount / $totalOps * 100, 2) : 0;
$lossPct = $totalOps > 0 ? round($lossCount / $totalOps * 100, 2) : 0;
$zeroPct = $totalOps > 0 ? round($zeroCount / $totalOps * 100, 2) : 0;
?>
<div class="studies view content fade-in-up">
    <!-- Header / Ações -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-graph-up-arrow me-2 icon-accent"></i>
                Estudo de Market
            </h1>
            <p class="text-muted mb-0">Detalhes e performance do estudo #<?= h($study['id']) ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/studies/edit/<?= h($study['id']) ?>" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <form method="post" action="/admin/studies/delete/<?= h($study['id']) ?>" class="d-flex" onsubmit="return confirm('Tem certeza que deseja excluir este estudo?');">
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

    <?php
    $avgPerTrade = $study['total_trades'] > 0 ? $study['profit_loss'] / $study['total_trades'] : 0;
    $currency = $study['user']['currency'] ?? 'BRL';
    ?>

    <div class="row g-3">
        <!-- Detalhes do Estudo -->
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalhes do Estudo
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <ul class="meta-list">
                                <li>
                                    <span>Estudante</span>
                                    <a href="/admin/students/view/<?= h($study['student']['id']) ?>" class="student-link">
                                        <?= h($study['student']['name']) ?>
                                    </a>
                                </li>
                                <li>
                                    <span>E-mail</span>
                                    <strong><?= h($study['student']['email'] ?: 'Não informado') ?></strong>
                                </li>
                                <?php if (isset($study['market']) && $study['market']): ?>
                                    <li>
                                        <span>Mercado</span>
                                        <span class="badge bg-info"><?= h($study['market']['name']) ?> (<?= h($study['market']['code']) ?>)</span>
                                    </li>
                                    <?php if (!empty($study['market']['description'])): ?>
                                        <li>
                                            <span>Descrição</span>
                                            <strong><?= h($study['market']['description']) ?></strong>
                                        </li>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <li>
                                        <span>Mercado</span>
                                        <span class="text-muted">Não especificado</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="meta-list">
                                <li>
                                    <span>Data do Estudo</span>
                                    <strong><?= h(date('d/m/Y', strtotime($study['study_date']))) ?></strong>
                                </li>
                                <li>
                                    <span>Criado em</span>
                                    <strong><?= h(date('d/m/Y H:i', strtotime($study['created']))) ?></strong>
                                </li>
                                <li>
                                    <span>Modificado em</span>
                                    <strong><?= h(date('d/m/Y H:i', strtotime($study['modified']))) ?></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo de Performance -->
        <div class="col-12">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Resumo de Performance
                    </h6>
                    <div class="row g-2 text-center">
                        <div class="col-6 col-md-2">
                            <div class="stat-box stat-box-success">
                                <div class="stat-value"><?= h($study['wins']) ?></div>
                                <div class="stat-label">Vitórias</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-box stat-box-danger">
                                <div class="stat-value"><?= h($study['losses']) ?></div>
                                <div class="stat-label">Derrotas</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-box">
                                <div class="stat-value"><?= h($study['total_trades']) ?></div>
                                <div class="stat-label">Total de Trades</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-box <?= $study['win_rate'] >= 50 ? 'stat-box-success' : 'stat-box-warning' ?>">
                                <div class="stat-value"><?= number_format($study['win_rate'], 2) ?>%</div>
                                <div class="stat-label">Taxa de Vitória</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-box <?= $study['profit_loss'] >= 0 ? 'stat-box-success' : 'stat-box-danger' ?>">
                                <div class="stat-value"><?= $this->Currency::formatForUser($study['profit_loss'], $currency) ?></div>
                                <div class="stat-label">P&amp;L Total</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="stat-box <?= $avgPerTrade >= 0 ? 'stat-box-success' : 'stat-box-danger' ?>">
                                <div class="stat-value"><?= $this->Currency::formatForUser($avgPerTrade, $currency) ?></div>
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

        <?php if (!empty($operations)): ?>
            <!-- Evolução do Capital -->
            <div class="col-12">
                <div class="card glass">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="fas fa-wave-square me-2"></i>
                            Evolução do Capital
                        </h6>
                        <div style="height: 280px;">
                            <canvas id="capitalEvolutionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resultado por Operação -->
            <div class="col-12">
                <div class="card glass">
                    <div class="card-body">
                        <div style="height: 320px;">
                            <canvas id="operationsBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eficiência -->
            <div class="col-12">
                <div class="card glass">
                    <div class="card-body">
                        <div style="height: 320px;">
                            <canvas id="efficiencyChart"></canvas>
                        </div>
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

<?php if (!empty($operations)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not available');
                return;
            }

            const capitalLabels = <?= json_encode($capitalLabels) ?>;
            const capitalValues = <?= json_encode($capitalValues) ?>;

            function createGradient(ctx, chartArea, data) {
                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                const minValue = Math.min(...data);
                const maxValue = Math.max(...data);
                const range = maxValue - minValue;

                function clamp(value, min = 0, max = 1) {
                    return Math.max(min, Math.min(max, value));
                }

                if (minValue < 0 && maxValue > 0 && range > 0) {
                    const zeroPosition = clamp(Math.abs(minValue) / range);
                    gradient.addColorStop(0, 'rgba(255, 59, 48, 0.3)');
                    gradient.addColorStop(clamp(zeroPosition - 0.01), 'rgba(255, 59, 48, 0.1)');
                    gradient.addColorStop(zeroPosition, 'rgba(255, 255, 255, 0.05)');
                    gradient.addColorStop(clamp(zeroPosition + 0.01), 'rgba(0, 255, 136, 0.1)');
                    gradient.addColorStop(1, 'rgba(0, 255, 136, 0.3)');
                } else if (maxValue <= 0) {
                    gradient.addColorStop(0, 'rgba(255, 59, 48, 0.3)');
                    gradient.addColorStop(1, 'rgba(255, 59, 48, 0.1)');
                } else {
                    gradient.addColorStop(0, 'rgba(0, 255, 136, 0.1)');
                    gradient.addColorStop(1, 'rgba(0, 255, 136, 0.3)');
                }

                return gradient;
            }

            function getPointColors(data) {
                return data.map(value => value >= 0 ? '#00ff88' : '#ff3b30');
            }

            const ctxEl = document.getElementById('capitalEvolutionChart');
            if (!ctxEl) {
                return;
            }

            Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";

            new Chart(ctxEl, {
                type: 'line',
                data: {
                    labels: capitalLabels,
                    datasets: [{
                        label: 'Capital',
                        data: capitalValues,
                        borderColor: '#6c757d',
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;
                            if (!chartArea) {
                                return null;
                            }
                            return createGradient(ctx, chartArea, capitalValues);
                        },
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: getPointColors(capitalValues),
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                            titleColor: function(context) {
                                if (!context || !context[0] || !context[0].parsed) return '#ffffff';
                                return context[0].parsed.y >= 0 ? '#00ff88' : '#ff3b30';
                            },
                            bodyColor: '#fff',
                            borderColor: function(context) {
                                if (!context || !context[0] || !context[0].parsed) return '#ffffff';
                                return context[0].parsed.y >= 0 ? '#00ff88' : '#ff3b30';
                            },
                            borderWidth: 2,
                            cornerRadius: 12,
                            displayColors: false,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    if (!context || !context.parsed) return 'N/A';
                                    const value = context.parsed.y;
                                    const prefix = value >= 0 ? '+' : '';
                                    return 'Capital: ' + prefix + 'R$ ' + value.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#a0a0a0', font: { size: 11 } }
                        },
                        y: {
                            grid: {
                                color: function(context) {
                                    if (context.tick.value === 0) {
                                        return 'rgba(255, 255, 255, 0.3)';
                                    }
                                    return context.tick.value > 0 ? 'rgba(0, 255, 136, 0.1)' : 'rgba(255, 59, 48, 0.1)';
                                },
                                lineWidth: function(context) {
                                    return context.tick.value === 0 ? 2 : 1;
                                }
                            },
                            border: { display: false },
                            ticks: {
                                color: function(context) {
                                    return context.tick.value === 0 ? '#ffffff' : '#a0a0a0';
                                },
                                callback: function(value) {
                                    return 'R$ ' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Resultado por Operação (barras individuais, não acumulado)
            const operationLabels = <?= json_encode($operationLabels) ?>;
            const operationResults = <?= json_encode($operationResults) ?>;
            const opsCtxEl = document.getElementById('operationsBarChart');
            if (opsCtxEl) {
                new Chart(opsCtxEl, {
                    type: 'bar',
                    data: {
                        labels: operationLabels,
                        datasets: [{
                            label: 'Saldo Bruto (R$)',
                            data: operationResults,
                            backgroundColor: operationResults.map(v => v >= 0 ? 'rgba(0, 204, 106, 0.85)' : 'rgba(255, 59, 48, 0.85)'),
                            borderRadius: 3,
                            maxBarThickness: 28
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Operações',
                                color: '#ffffff',
                                font: { size: 15, weight: 'bold' },
                                padding: { bottom: 16 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderWidth: 0,
                                cornerRadius: 8,
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        const prefix = value >= 0 ? '+' : '';
                                        return prefix + 'R$ ' + value.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: '#a0a0a0', font: { size: 10 }, maxRotation: 45, minRotation: 45 }
                            },
                            y: {
                                position: 'right',
                                title: { display: true, text: 'Saldo Bruto (R$)', color: '#a0a0a0' },
                                grid: {
                                    color: function(context) {
                                        return context.tick.value === 0 ? 'rgba(255, 255, 255, 0.3)' : 'rgba(255, 255, 255, 0.05)';
                                    }
                                },
                                border: { display: false },
                                ticks: { color: '#a0a0a0' }
                            }
                        }
                    }
                });
            }

            // Eficiência (vencedoras / perdedoras / zeradas)
            const effCtxEl = document.getElementById('efficiencyChart');
            if (effCtxEl) {
                new Chart(effCtxEl, {
                    type: 'pie',
                    data: {
                        labels: [
                            'Vencedoras (<?= number_format($winPct, 2, ',', '.') ?>%)',
                            'Perdedoras (<?= number_format($lossPct, 2, ',', '.') ?>%)',
                            'Zeradas (<?= number_format($zeroPct, 2, ',', '.') ?>%)'
                        ],
                        datasets: [{
                            data: [<?= $winCount ?>, <?= $lossCount ?>, <?= $zeroCount ?>],
                            backgroundColor: ['#00cc6a', '#ff3b30', '#c9c9c9'],
                            borderColor: 'rgba(0, 0, 0, 0.3)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: 24 },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 16,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#a0a0a0',
                                    font: { size: 12, weight: '500' }
                                }
                            },
                            title: {
                                display: true,
                                text: 'Eficiência',
                                color: '#ffffff',
                                font: { size: 15, weight: 'bold' },
                                padding: { bottom: 16 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderWidth: 0,
                                cornerRadius: 8,
                                padding: 10
                            }
                        }
                    }
                });
            }
        });
    </script>
<?php endif; ?>

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
