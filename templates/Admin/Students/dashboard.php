
<div class="students dashboard content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-5 fw-bold mb-2">
                <i class="bi bi-speedometer2 me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Painel
            </h1>
            <p class="text-muted mb-0">Análise de desempenho para <span class="fw-semibold text-primary"><?= h($student['name']) ?></span></p>
        </div>
    </div>

    <!-- Market Filter Section -->
    <div class="card glass mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="marketFilter" class="form-label fw-semibold">
                        <i class="bi bi-funnel me-2"></i>
                        Filtrar por Mercado
                    </label>
                    <select id="marketFilter" class="form-select">
                        <option value="">Todos os mercados</option>
                        <?php if (!empty($markets)): ?>
                            <?php foreach ($markets as $market): ?>
                                <option value="<?= h($market['id']) ?>">
                                    <?= h($market['name']) ?> (<?= h($market['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="yearFilter" class="form-label fw-semibold">
                        <i class="bi bi-calendar me-2"></i>
                        Filtrar por Ano
                    </label>
                    <select id="yearFilter" class="form-select">
                        <?php
                        $currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                        $startYear = 2020;
                        $endYear = date('Y') + 1;
                        for ($year = $endYear; $year >= $startYear; $year--): ?>
                            <option value="<?= $year ?>" <?= $year == $currentYear ? 'selected' : '' ?>>
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" id="clearFilter" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h3 class="stat-number mb-2"><?= number_format($overallStats['total_studies']) ?></h3>
                    <p class="stat-label mb-0">Total de Estudos</p>
                    <div class="stat-trend">
                        <i class="bi bi-graph-up text-success"></i>
                        <small class="text-success">Ativo</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-success">
                        <i class="bi bi-percent"></i>
                    </div>
                    <h3 class="stat-number mb-2 text-success" data-stat="winrate"><?= $overallStats['overall_win_rate'] ?>%</h3>
                    <p class="stat-label mb-0">Taxa de Acerto</p>
                    <div class="stat-trend">
                        <i class="bi bi-<?= $overallStats['overall_win_rate'] >= 50 ? 'graph-up text-success' : 'graph-down text-warning' ?>"></i>
                        <small class="<?= $overallStats['overall_win_rate'] >= 50 ? 'text-success' : 'text-warning' ?>">
                            <?= $overallStats['overall_win_rate'] >= 50 ? 'Bom' : 'Precisa Melhorar' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-info">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h3 class="stat-number mb-2" data-stat="trades"><?= number_format($overallStats['total_trades']) ?></h3>
                    <p class="stat-label mb-0">Total de Operações</p>
                    <div class="stat-trend">
                        <i class="bi bi-graph-up text-info"></i>
                        <small class="text-info">Ativo</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 <?= ($overallStats['total_profit_loss'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h3 class="stat-number mb-2 <?= ($overallStats['total_profit_loss'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>" data-stat="profit">
                        <?= $this->Currency::formatForUser((float)($overallStats['total_profit_loss'] ?? 0), $student['currency'] ?? 'BRL') ?>
                    </h3>
                    <p class="stat-label mb-0">P&L Total</p>
                    <div class="stat-trend">
                        <i class="bi bi-<?= ($overallStats['total_profit_loss'] ?? 0) >= 0 ? 'graph-up text-success' : 'graph-down text-danger' ?>"></i>
                        <small class="<?= ($overallStats['total_profit_loss'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($overallStats['total_profit_loss'] ?? 0) >= 0 ? 'Lucro' : 'Prejuízo' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card glass chart-card h-100">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-graph-up me-2"></i>
                                Evolução P&L
                            </h5>
                            <p class="text-muted small mb-0">Desempenho dos últimos 12 meses</p>
                        </div>
                        <div class="chart-controls">
                            <button class="btn btn-sm btn-outline-primary active" data-chart="line">
                                <i class="bi bi-graph-up"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" data-chart="area">
                                <i class="bi bi-area-chart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="profitLossChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card glass chart-card h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title mb-1">
                        <i class="bi bi-pie-chart me-2"></i>
                        Tendência Taxa de Acerto
                    </h5>
                    <p class="text-muted small mb-0">Desempenho mensal</p>
                </div>
                <div class="card-body">
                    <canvas id="winRateChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card glass">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">
                        <i class="bi bi-calendar3 me-2"></i>
                        Desempenho Mensal
                    </h5>
                    <p class="text-muted small mb-0">Detalhamento por mês</p>
                </div>
                <div class="table-controls">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($monthlyData)): ?>
                <div class="table-responsive">
                    <table class="table table-hover modern-table">
                        <thead>
                            <tr>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    Período
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-journal me-1"></i>
                                    Estudos
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Vitórias
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Derrotas
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-bar-chart me-1"></i>
                                    Operações
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-percent me-1"></i>
                                    Taxa de Acerto
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-currency-dollar me-1"></i>
                                    P&L
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Período
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlyData as $data): ?>
                            <tr class="table-row-hover clickable-row dashboard-row" data-year="<?= h($data['year']) ?>" data-month="<?= h($data['month']) ?>" data-market-ids="<?= h($data['market_ids'] ?? '') ?>" style="cursor: pointer;">
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center">
                                        <div class="month-indicator me-2"></div>
                                        <?= h($data['month_name']) ?> <?= h($data['year']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill"><?= number_format($data['total_studies']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill"><?= number_format($data['total_wins']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-danger rounded-pill"><?= number_format($data['total_losses']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill"><?= number_format($data['total_trades']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress me-2" style="width: 40px; height: 6px;">
                                            <div class="progress-bar <?= $data['avg_win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?>" 
                                                 style="width: <?= $data['avg_win_rate'] ?>%"></div>
                                        </div>
                                        <span class="badge <?= $data['avg_win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?> rounded-pill">
                                            <?= $data['avg_win_rate'] ?>%
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold <?= ($data['total_profit_loss'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="bi bi-<?= ($data['total_profit_loss'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?> me-1"></i>
                                        <?= $this->Currency::formatForUser((float)($data['total_profit_loss'] ?? 0), $student['currency'] ?? 'BRL') ?>
                                    </span>
                                </td>
                                <td class="text-muted small text-period">
                                    <?= date('d/m', strtotime($data['first_study'])) ?> - <?= date('d/m', strtotime($data['last_study'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-4">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h4 class="text-muted mb-3">Nenhum Estudo Encontrado</h4>
                    <p class="text-muted mb-4">Comece adicionando estudos para ver suas análises de desempenho aqui.</p>
                    <a href="/admin/studies/add" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        Adicionar Primeiro Estudo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Dashboard Specific Styles */
.stat-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 1.5rem;
    color: var(--bs-primary);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    color: var(--bs-gray-600);
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.stat-trend {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.chart-card {
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.chart-controls .btn {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    margin-left: 0.25rem;
}

.chart-controls .btn.active {
    background: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

/* Estilos removidos - usando novo sistema de tabelas do style.css */

.month-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--primary-gradient);
}

.empty-state .empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2rem;
    color: var(--bs-primary);
}

.table-controls .btn {
    border-radius: 20px;
    padding: 0.375rem 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stat-number {
        font-size: 1.5rem;
    }
    
    .chart-controls {
        display: none;
    }
    
    .modern-table {
        font-size: 0.875rem;
    }
}
</style>
<script>
// Chart instances (declared globally)
window.profitLossChart = null;
window.winRateChart = null;

// Wait for everything to load
setTimeout(function() {
    // Check if Chart is available
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not available');
        return;
    }

    console.log('Starting chart initialization...');

    // Chart data
    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartProfitLoss = <?= json_encode($chartProfitLoss) ?>;
    const chartWinRate = <?= json_encode($chartWinRate) ?>;
    const chartDataDetailed = <?= json_encode($chartDataDetailed) ?>;

    // Store original chart data for filtering (also global)
    window.originalChartLabels = [...chartLabels];
    window.originalChartProfitLoss = [...chartProfitLoss];
    window.originalChartWinRate = [...chartWinRate];
    window.originalChartDataDetailed = [...chartDataDetailed];

    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#6c757d';

    // P&L Chart
    const profitLossCtx = document.getElementById('profitLossChart');
    if (profitLossCtx) {
        console.log('Creating P&L chart...');
        window.profitLossChart = new Chart(profitLossCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'P&L ($)',
                    data: chartProfitLoss,
                    borderColor: '#00ff88',
                    backgroundColor: 'rgba(0, 255, 136, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#00ff88',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointHoverBackgroundColor: '#00cc6a',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#00ff88',
                        bodyColor: '#fff',
                        borderColor: '#00ff88',
                        borderWidth: 2,
                        cornerRadius: 12,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'P&L: $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#a0a0a0',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 255, 136, 0.1)',
                            lineWidth: 1
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#a0a0a0',
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            callback: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Chart controls
        document.querySelectorAll('[data-chart]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-chart]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const chartType = this.dataset.chart;
                window.profitLossChart.config.type = chartType;
            window.profitLossChart.update();
            });
        });
    }

    // Win Rate Chart
    const winRateCtx = document.getElementById('winRateChart');
    if (winRateCtx) {
        console.log('Creating Win Rate chart...');
        window.winRateChart = new Chart(winRateCtx, {
            type: 'doughnut',
            data: {
                labels: ['Wins', 'Losses'],
                datasets: [{
                    data: [
                        <?= $overallStats['total_trades'] > 0 ? ($overallStats['overall_win_rate'] / 100 * $overallStats['total_trades']) : 0 ?>,
                        <?= $overallStats['total_trades'] > 0 ? ((100 - $overallStats['overall_win_rate']) / 100 * $overallStats['total_trades']) : 0 ?>
                    ],
                    backgroundColor: [
                        '#00ff88',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        '#00cc6a',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 3,
                    cutout: '70%',
                    hoverBackgroundColor: [
                        '#00cc6a',
                        'rgba(255, 99, 132, 0.9)'
                    ],
                    hoverBorderColor: [
                        '#00ff88',
                        'rgba(255, 99, 132, 1)'
                    ],
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#a0a0a0',
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#00ff88',
                        bodyColor: '#fff',
                        borderColor: '#00ff88',
                        borderWidth: 2,
                        cornerRadius: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + percentage + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    console.log('Charts initialization completed. P&L Chart:', !!window.profitLossChart, 'Win Rate Chart:', !!window.winRateChart);

    // Add smooth animations
    // Get user currency from PHP
    const userCurrency = '<?= $student['currency'] ?? 'BRL' ?>';
    
    // Function to format currency based on user preference
    function formatCurrency(value, currency) {
        if (currency === 'BRL') {
            return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else {
            return '$' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }
    
    // Animate stat numbers
    document.querySelectorAll('.stat-number').forEach(el => {
        const originalText = el.textContent;
        // Improved parsing for Brazilian currency format (R$ 1.468,00)
        let finalValue;
        if (originalText.includes('R$')) {
            // Remove R$ and spaces, then handle Brazilian number format
            let cleanValue = originalText.replace(/R\$\s*/g, '').trim();
            
            // Handle Brazilian number format properly for all value ranges
            if (cleanValue.includes(',')) {
                // Brazilian format uses comma as decimal separator
                const parts = cleanValue.split(',');
                if (parts.length === 2) {
                    // Remove all dots from the integer part (thousands separators)
                    const integerPart = parts[0].replace(/\./g, '');
                    const decimalPart = parts[1];
                    cleanValue = integerPart + '.' + decimalPart;
                } else {
                    // No decimal part, just remove dots (thousands separators)
                    cleanValue = cleanValue.replace(/\./g, '').replace(',', '');
                }
            } else if (cleanValue.includes('.')) {
                // Check if it's thousands separator or decimal separator
                const dotCount = (cleanValue.match(/\./g) || []).length;
                if (dotCount === 1 && cleanValue.split('.')[1].length <= 2) {
                    // Single dot with 1-2 digits after = decimal separator, keep as is
                    // This handles cases like "1500.50"
                } else {
                    // Multiple dots or more than 2 digits after = thousands separators
                    // Remove all dots: "1.000.000" -> "1000000"
                    cleanValue = cleanValue.replace(/\./g, '');
                }
            }
            
            finalValue = parseFloat(cleanValue) || 0;
        } else {
            // For other values (percentages, counts)
            finalValue = parseFloat(originalText.replace(/[^0-9.,-]/g, '').replace(',', '.')) || 0;
        }
        
        let currentValue = 0;
        const increment = finalValue / 50;
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            
            // Check if this element contains currency
            if (originalText.includes('$') || originalText.includes('R$')) {
                el.textContent = formatCurrency(currentValue, userCurrency);
            } else if (originalText.includes('%')) {
                el.textContent = Math.floor(currentValue).toLocaleString() + '%';
            } else {
                el.textContent = Math.floor(currentValue).toLocaleString();
            }
        }, 30);
    });
}, 1000); // Wait 1 second for everything to load

// Add click functionality to table rows
document.addEventListener('DOMContentLoaded', function() {
    const clickableRows = document.querySelectorAll('.clickable-row');
    
    clickableRows.forEach(row => {
        row.addEventListener('click', function() {
            const year = this.getAttribute('data-year');
            const month = this.getAttribute('data-month');
            const studentId = <?= json_encode($student['id']) ?>;
            
            // Navigate to monthly studies page
            window.location.href = `/admin/students/monthly-studies/${studentId}/${year}/${month}`;
        });
        
        // Add hover effect
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(var(--bs-primary-rgb), 0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Market filter functionality
    const marketFilter = document.getElementById('marketFilter');
    const yearFilter = document.getElementById('yearFilter');
    const clearFilter = document.getElementById('clearFilter');
    const dashboardRows = document.querySelectorAll('.dashboard-row');
    const noDataMessage = document.querySelector('.card-body .text-center');

    // Chart instances (will be set after chart creation)
    let profitLossChart = null;
    let winRateChart = null;

    function filterDashboard() {
        const selectedMarket = marketFilter.value;
        let visibleRows = 0;

        dashboardRows.forEach(row => {
            const marketIds = row.getAttribute('data-market-ids');
            
            if (!selectedMarket || (marketIds && marketIds.split(',').includes(selectedMarket))) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no data message
        const tableBody = document.querySelector('.modern-table tbody');
        if (visibleRows === 0 && selectedMarket) {
            if (!document.getElementById('no-dashboard-data')) {
                const noDataRow = document.createElement('tr');
                noDataRow.id = 'no-dashboard-data';
                noDataRow.innerHTML = `
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-search me-2"></i>
                            Nenhum dado encontrado para o mercado selecionado
                        </div>
                    </td>
                `;
                tableBody.appendChild(noDataRow);
            }
        } else {
            const noDataRow = document.getElementById('no-dashboard-data');
            if (noDataRow) {
                noDataRow.remove();
            }
        }

        // Filter charts
        filterCharts(selectedMarket);
    }

    function filterCharts(selectedMarket, retryCount = 0) {
        const maxRetries = 10; // Limite máximo de tentativas
        
        // Verificação mais robusta - checando se as variáveis existem E se são instâncias válidas do Chart
        if (!window.profitLossChart || !window.winRateChart || 
            !(window.profitLossChart instanceof Chart) || !(window.winRateChart instanceof Chart)) {
            if (retryCount >= maxRetries) {
                console.error('Charts failed to initialize after', maxRetries, 'attempts');
                console.log('Debug - profitLossChart:', window.profitLossChart);
                console.log('Debug - winRateChart:', window.winRateChart);
                return;
            }
            console.log(`Charts not initialized yet, retry ${retryCount + 1}/${maxRetries}...`);
            // Retry after charts are initialized
            setTimeout(() => filterCharts(selectedMarket, retryCount + 1), 200);
            return;
        }

        console.log('Charts initialized successfully, applying filter...');

        let filteredLabels = [];
        let filteredProfitLoss = [];
        let filteredWinRate = [];
        let totalWins = 0;
        let totalLosses = 0;

        if (!selectedMarket) {
            // Show all data
            filteredLabels = [...window.originalChartLabels];
            filteredProfitLoss = [...window.originalChartProfitLoss];
            filteredWinRate = [...window.originalChartWinRate];
            
            console.log('Showing all markets data:', {
                originalDataDetailed: window.originalChartDataDetailed,
                filteredLabels,
                filteredProfitLoss,
                filteredWinRate
            });
            
            // Calculate overall stats from original data
            window.originalChartDataDetailed.forEach(monthData => {
                monthData.markets.forEach(market => {
                    totalWins += parseInt(market.wins) || 0;
                    totalLosses += parseInt(market.losses) || 0;
                });
            });
            
            console.log('Calculated totals for all markets:', {
                totalWins,
                totalLosses,
                totalTrades: totalWins + totalLosses
            });
        } else {
            // Filter by selected market
            window.originalChartDataDetailed.forEach((monthData, index) => {
                let monthProfitLoss = 0;
                let monthWins = 0;
                let monthLosses = 0;
                let hasMarketData = false;

                monthData.markets.forEach(market => {
                    if (market.market_id == selectedMarket) {
                        monthProfitLoss += parseFloat(market.profit_loss);
                        monthWins += parseInt(market.wins);
                        monthLosses += parseInt(market.losses);
                        hasMarketData = true;
                    }
                });

                if (hasMarketData) {
                    filteredLabels.push(window.originalChartLabels[index]);
                    filteredProfitLoss.push(monthProfitLoss);
                    
                    const monthTotalTrades = monthWins + monthLosses;
                    const monthWinRate = monthTotalTrades > 0 ? (monthWins / monthTotalTrades) * 100 : 0;
                    filteredWinRate.push(monthWinRate);
                    
                    totalWins += monthWins;
                    totalLosses += monthLosses;
                }
            });
        }

        // Update P&L Chart
        window.profitLossChart.data.labels = filteredLabels;
        window.profitLossChart.data.datasets[0].data = filteredProfitLoss;
        
        // Get selected market currency for chart formatting
        let chartCurrency = 'BRL'; // Default currency
        if (selectedMarket) {
            const selectedMarketData = <?= json_encode($markets) ?>.find(m => m.id == selectedMarket);
            chartCurrency = selectedMarketData ? selectedMarketData.currency : 'BRL';
        } else {
            // When showing all markets, determine currency based on the markets data
            const marketsData = <?= json_encode($markets) ?>;
            const currencies = marketsData.map(m => m.currency).filter((v, i, a) => a.indexOf(v) === i);
            
            // If all markets use the same currency, use that currency
            // Otherwise, default to BRL for mixed currencies
            if (currencies.length === 1) {
                chartCurrency = currencies[0];
            } else {
                chartCurrency = 'BRL'; // Default for mixed currencies
            }
        }
        
        // Update chart label and tooltip based on currency
        const currencySymbol = chartCurrency === 'USD' ? '$' : 'R$';
        window.profitLossChart.data.datasets[0].label = `P&L (${currencySymbol})`;
        
        // Update tooltip callback for currency formatting
        window.profitLossChart.options.plugins.tooltip.callbacks.label = function(context) {
            const value = context.parsed.y;
            if (chartCurrency === 'USD') {
                return `P&L: $${value.toFixed(2)}`;
            } else {
                return `P&L: R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
        };
        
        // Update Y-axis tick callback for currency formatting
        window.profitLossChart.options.scales.y.ticks.callback = function(value) {
            if (chartCurrency === 'USD') {
                return `$${value.toFixed(0)}`;
            } else {
                return `R$ ${value.toFixed(0)}`;
            }
        };
        
        window.profitLossChart.update();

        // Update Win Rate Chart
        const totalTrades = totalWins + totalLosses;
        window.winRateChart.data.datasets[0].data = [totalWins, totalLosses];
        window.winRateChart.update();

        // Update statistics cards
        updateStatisticsCards(selectedMarket, totalWins, totalLosses, filteredProfitLoss, chartCurrency);
    }

    function updateStatisticsCards(selectedMarket, totalWins, totalLosses, profitLossData, currency) {
        const totalTrades = totalWins + totalLosses;
        const winRate = totalTrades > 0 ? (totalWins / totalTrades) * 100 : 0;
        const totalProfitLoss = profitLossData.reduce((sum, value) => sum + value, 0);

        console.log('Updating statistics:', {
            totalWins,
            totalLosses,
            totalTrades,
            winRate,
            totalProfitLoss,
            currency
        });

        // Update win rate card
        const winRateElement = document.querySelector('.stat-number[data-stat="winrate"]');
        if (winRateElement) {
            winRateElement.textContent = winRate.toFixed(1) + '%';
        }

        // Update total trades card
        const totalTradesElement = document.querySelector('.stat-number[data-stat="trades"]');
        if (totalTradesElement) {
            totalTradesElement.textContent = totalTrades.toLocaleString();
        }

        // Update profit/loss card
        const profitLossElement = document.querySelector('.stat-number[data-stat="profit"]');
        if (profitLossElement) {
            if (currency === 'USD') {
                profitLossElement.textContent = '$' + totalProfitLoss.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                profitLossElement.textContent = 'R$ ' + totalProfitLoss.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
    }

    // Event listeners
    marketFilter.addEventListener('change', filterDashboard);
    
    yearFilter.addEventListener('change', function() {
        const selectedYear = yearFilter.value;
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('year', selectedYear);
        window.location.href = currentUrl.toString();
    });
    
    clearFilter.addEventListener('click', function() {
        marketFilter.value = '';
        yearFilter.value = '<?= date('Y') ?>';
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('year');
        window.location.href = currentUrl.toString();
    });
});
</script>
