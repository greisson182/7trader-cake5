<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>

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
                <div class="col-md-3">
                    <label for="accountFilter" class="form-label fw-semibold">
                        <i class="bi bi-bank me-2"></i>
                        Tipo de Conta
                    </label>
                    <select id="accountFilter" class="form-select">
                        <option value="">Todas as contas</option>
                        <?php if (!empty($accounts)): ?>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= h($account['id']) ?>"><?= h($account['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-2 text-end">
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
                    <h3 class="stat-number mb-2" id="total-studies-stat"><?= number_format($overallStats['total_studies']) ?></h3>
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
                    <h3 class="stat-number mb-2 text-success" data-stat="winrate" id="winrate-stat"><?= $overallStats['overall_win_rate'] ?>%</h3>
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
                    <h3 class="stat-number mb-2" data-stat="trades" id="total-trades-stat"><?= number_format($overallStats['total_trades']) ?></h3>
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
                    <h3 class="stat-number mb-2 <?= ($overallStats['total_profit_loss'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>" data-stat="profit" id="profit-loss-stat">
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

    <!-- Trading Calendar Section -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card glass">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-calendar3 me-2"></i>
                                Calendário de Trading
                            </h5>
                            <p class="text-muted small mb-0">Resultados diários de trades com métricas detalhadas</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="calendar-navigation">
                                <button id="prevMonth" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <div class="dropdown">
                                    <span id="currentMonthYear" class="mx-3 fw-bold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                                        <?php
                                        $monthNames = [
                                            1 => 'Janeiro',
                                            2 => 'Fevereiro',
                                            3 => 'Março',
                                            4 => 'Abril',
                                            5 => 'Maio',
                                            6 => 'Junho',
                                            7 => 'Julho',
                                            8 => 'Agosto',
                                            9 => 'Setembro',
                                            10 => 'Outubro',
                                            11 => 'Novembro',
                                            12 => 'Dezembro'
                                        ];
                                        echo $monthNames[$currentMonth] . ' ' . $currentYear;
                                        ?>
                                    </span>
                                    <ul class="dropdown-menu" id="monthSelector">
                                        <!-- Meses serão preenchidos dinamicamente via JavaScript -->
                                    </ul>
                                </div>
                                <button id="nextMonth" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-summary">
                                <span class="badge me-2" id="monthlyTotal">
                                    Total: R$ <?php
                                                $monthlyTotal = 0;
                                                if (isset($calendarData['daily'])) {
                                                    foreach ($calendarData['daily'] as $dayData) {
                                                        $monthlyTotal += $dayData['profit_loss'];
                                                    }
                                                }
                                                echo number_format($monthlyTotal, 2, ',', '.');
                                                ?>
                                </span>
                                <span class="badge bg-info" id="activeDays">
                                    <?= isset($calendarData['daily']) ? count($calendarData['daily']) : 0 ?> dias ativos
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Calendar Grid -->
                        <div class="col-lg-9">
                            <div class="trading-calendar">
                                <!-- Calendar Header -->
                                <div class="calendar-header">
                                    <div class="calendar-day-header">Dom</div>
                                    <div class="calendar-day-header">Seg</div>
                                    <div class="calendar-day-header">Ter</div>
                                    <div class="calendar-day-header">Qua</div>
                                    <div class="calendar-day-header">Qui</div>
                                    <div class="calendar-day-header">Sex</div>
                                    <div class="calendar-day-header">Sáb</div>
                                </div>

                                <!-- Calendar Body -->
                                <div class="calendar-body" id="calendarBody">
                                    <!-- Calendar days will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Summary Sidebar -->
                        <div class="col-lg-3">
                            <div class="weekly-summary">
                                <h6 class="mb-3">
                                    <i class="bi bi-bar-chart me-2"></i>
                                    Resumo Semanal
                                </h6>

                                <div id="weeklySummaryContent">
                                    <!-- Weekly summary will be populated by JavaScript -->
                                </div>

                                <div class="summary-footer mt-3 pt-3 border-top" id="summaryFooter">
                                    <!-- Summary footer will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
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
                                <tr class="table-row-hover clickable-row dashboard-row" data-year="<?= h($data['year']) ?>" data-month="<?= h($data['month']) ?>" data-market-ids="<?= h($data['market_ids'] ?? '') ?>" data-account-ids="<?= h($data['account_ids'] ?? '') ?>" style="cursor: pointer;">
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

    /* Trading Calendar Styles */
    .trading-calendar {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 1;
    }

    .calendar-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 1rem;
    }

    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        color: var(--bs-gray-600);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.5rem;
    }

    .calendar-body {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .calendar-week {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .calendar-day {
        aspect-ratio: 1;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px;
        position: relative;
        background: rgba(255, 255, 255, 0.02);
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 80px;
        display: flex;
        flex-direction: column;
    }

    .calendar-day.empty {
        border: none;
        background: transparent;
        cursor: default;
    }

    .calendar-day.profit {
        background: linear-gradient(135deg, rgba(0, 255, 136, 0.15), rgba(0, 255, 136, 0.05));
        border-color: rgba(0, 255, 136, 0.3);
    }

    .calendar-day.loss {
        background: linear-gradient(135deg, rgba(255, 59, 48, 0.15), rgba(255, 59, 48, 0.05));
        border-color: rgba(255, 59, 48, 0.3);
    }

    .calendar-day:hover:not(.empty) {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-color: var(--bs-primary);
    }

    .day-number {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--bs-gray-600);
        margin-bottom: 4px;
    }

    .trade-result {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }

    .trade-value {
        font-size: 0.875rem;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .calendar-day.profit .trade-value {
        color: #00ff88;
    }

    .calendar-day.loss .trade-value {
        color: #ff3b30;
    }

    .trade-details {
        font-size: 0.625rem;
        color: var(--bs-gray-500);
        margin-bottom: 2px;
    }

    .trade-metrics {
        font-size: 0.625rem;
        color: var(--bs-gray-400);
        font-weight: 500;
    }

    /* Weekly Summary Styles */
    .weekly-summary {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        height: fit-content;
    }

    .week-summary-item {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    .week-summary-item:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateX(4px);
    }

    .week-summary-item.best-week {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
        border-color: rgba(255, 193, 7, 0.3);
    }

    .week-summary-item.worst-week {
        background: rgba(108, 117, 125, 0.1);
        border-color: rgba(108, 117, 125, 0.2);
    }

    .week-summary-item.loss-week {
        background: linear-gradient(135deg, rgba(255, 59, 48, 0.15), rgba(255, 59, 48, 0.05));
        border-color: rgba(255, 59, 48, 0.3);
    }

    .week-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--bs-gray-300);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .week-value {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .week-value.profit {
        color: #00ff88;
    }

    .week-value.loss {
        color: #ff3b30;
    }

    .week-value.neutral {
        color: var(--bs-gray-500);
    }

    .week-days {
        font-size: 0.75rem;
        color: var(--bs-gray-500);
    }

    .summary-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .calendar-summary .badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    /* Responsive Calendar */
    @media (max-width: 992px) {
        .calendar-day {
            min-height: 70px;
            padding: 6px;
        }

        .trade-value {
            font-size: 0.75rem;
        }

        .trade-details,
        .trade-metrics {
            font-size: 0.5rem;
        }

        .weekly-summary {
            margin-top: 2rem;
        }
    }

    @media (max-width: 768px) {
        .calendar-day {
            min-height: 60px;
            padding: 4px;
        }

        .day-number {
            font-size: 0.625rem;
        }

        .trade-value {
            font-size: 0.625rem;
        }

        .trade-details,
        .trade-metrics {
            display: none;
        }

        .calendar-summary {
            flex-direction: column;
            gap: 0.5rem;
        }

        .calendar-summary .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
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

            // Function to create gradient based on data values
            function createGradient(ctx, chartArea, data) {
                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);

                // Find min and max values to calculate proportions
                const minValue = Math.min(...data);
                const maxValue = Math.max(...data);
                const range = maxValue - minValue;

                if (minValue < 0 && maxValue > 0) {
                    // Mixed positive and negative values
                    const zeroPosition = Math.abs(minValue) / range;

                    // Red gradient for negative values (bottom)
                    gradient.addColorStop(0, 'rgba(255, 59, 48, 0.3)');
                    gradient.addColorStop(zeroPosition - 0.01, 'rgba(255, 59, 48, 0.1)');

                    // Transition at zero line
                    gradient.addColorStop(zeroPosition, 'rgba(255, 255, 255, 0.05)');

                    // Green gradient for positive values (top)
                    gradient.addColorStop(zeroPosition + 0.01, 'rgba(0, 255, 136, 0.1)');
                    gradient.addColorStop(1, 'rgba(0, 255, 136, 0.3)');
                } else if (maxValue <= 0) {
                    // All negative values - red gradient
                    gradient.addColorStop(0, 'rgba(255, 59, 48, 0.3)');
                    gradient.addColorStop(1, 'rgba(255, 59, 48, 0.1)');
                } else {
                    // All positive values - green gradient
                    gradient.addColorStop(0, 'rgba(0, 255, 136, 0.1)');
                    gradient.addColorStop(1, 'rgba(0, 255, 136, 0.3)');
                }

                return gradient;
            }

            // Function to get border color based on overall trend
            function getBorderColor(data) {
                const lastValue = data[data.length - 1] || 0;
                return lastValue >= 0 ? '#00ff88' : '#ff3b30';
            }

            // Function to get point colors based on individual values - moved to global scope
            window.getPointColors = function(data) {
                return data.map(value => value >= 0 ? '#00ff88' : '#ff3b30');
            };

            window.profitLossChart = new Chart(profitLossCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'P&L ($)',
                        data: chartProfitLoss,
                        borderColor: '#6c757d', // Cor neutra (cinza)
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const {
                                ctx,
                                chartArea
                            } = chart;
                            if (!chartArea) {
                                return null;
                            }
                            return createGradient(ctx, chartArea, chartProfitLoss);
                        },
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: window.getPointColors(chartProfitLoss),
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 10,
                        pointHoverBackgroundColor: function(context) {
                            if (!context || !context.parsed) return '#00cc6a';
                            const value = context.parsed.y;
                            return value >= 0 ? '#00cc6a' : '#cc2e24';
                        },
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
                            titleColor: function(context) {
                                if (!context || !context[0] || !context[0].parsed) return '#ffffff';
                                const value = context[0].parsed.y;
                                return value >= 0 ? '#00ff88' : '#ff3b30';
                            },
                            bodyColor: '#fff',
                            borderColor: function(context) {
                                if (!context || !context[0] || !context[0].parsed) return '#ffffff';
                                const value = context[0].parsed.y;
                                return value >= 0 ? '#00ff88' : '#ff3b30';
                            },
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
                                    if (!context || !context.parsed) return 'N/A';
                                    const value = context.parsed.y;
                                    const prefix = value >= 0 ? '+' : '';
                                    return 'P&L: ' + prefix + '$' + value.toFixed(2);
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
                                color: function(context) {
                                    if (context.tick.value === 0) {
                                        return 'rgba(255, 255, 255, 0.3)'; // Zero line more visible
                                    }
                                    return context.tick.value > 0 ?
                                        'rgba(0, 255, 136, 0.1)' :
                                        'rgba(255, 59, 48, 0.1)';
                                },
                                lineWidth: function(context) {
                                    return context.tick.value === 0 ? 2 : 1;
                                }
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: function(context) {
                                    if (context.tick.value === 0) {
                                        return '#ffffff';
                                    }
                                    return context.tick.value > 0 ? '#00ff88' : '#ff3b30';
                                },
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                callback: function(value) {
                                    const prefix = value > 0 ? '+' : '';
                                    return prefix + '$' + value.toFixed(0);
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
                                    if (!context || !context.parsed || !context.dataset) return 'N/A';
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


        // Add smooth animations
        // Get user currency from PHP
        const userCurrency = '<?= $student['currency'] ?? 'BRL' ?>';

        // Function to format currency based on user preference
        function formatCurrency(value, currency) {
            if (currency === 'BRL') {
                return 'R$ ' + value.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                return '$' + value.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
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
        const accountFilter = document.getElementById('accountFilter');
        const yearFilter = document.getElementById('yearFilter');
        const clearFilter = document.getElementById('clearFilter');
        const dashboardRows = document.querySelectorAll('.dashboard-row');
        const noDataMessage = document.querySelector('.card-body .text-center');

        // Chart instances (will be set after chart creation)
        let profitLossChart = null;
        let winRateChart = null;

        function filterDashboard() {
            const selectedMarket = marketFilter.value;
            const selectedAccount = accountFilter.value;
            let visibleRows = 0;

            dashboardRows.forEach(row => {
                const marketIds = row.getAttribute('data-market-ids');
                const accountIds = row.getAttribute('data-account-ids');

                let showRow = true;

                // Filter by market
                if (selectedMarket && (!marketIds || !marketIds.split(',').includes(selectedMarket))) {
                    showRow = false;
                }

                // Filter by account
                if (selectedAccount && (!accountIds || !accountIds.split(',').includes(selectedAccount))) {
                    showRow = false;
                }

                if (showRow) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no data message
            const tableBody = document.querySelector('.modern-table tbody');
            if (visibleRows === 0 && (selectedMarket || selectedAccount)) {
                if (!document.getElementById('no-dashboard-data')) {
                    const noDataRow = document.createElement('tr');
                    noDataRow.id = 'no-dashboard-data';
                    let message = 'Nenhum dado encontrado';
                    if (selectedMarket && selectedAccount) {
                        message += ' para o mercado e tipo de conta selecionados';
                    } else if (selectedMarket) {
                        message += ' para o mercado selecionado';
                    } else if (selectedAccount) {
                        message += ' para o tipo de conta selecionado';
                    }
                    noDataRow.innerHTML = `
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-search me-2"></i>
                            ${message}
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
            filterCharts(selectedMarket, selectedAccount);
            
            // Update statistics
            updateFilteredStats(selectedMarket, selectedAccount);
        }

        function filterCharts(selectedMarket, selectedAccount, retryCount = 0) {
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
                setTimeout(() => filterCharts(selectedMarket, selectedAccount, retryCount + 1), 200);
                return;
            }

            let filteredLabels = [];
            let filteredProfitLoss = [];
            let filteredWinRate = [];
            let totalWins = 0;
            let totalLosses = 0;

            if (!selectedMarket && !selectedAccount) {
                // Show all data
                filteredLabels = [...window.originalChartLabels];
                filteredProfitLoss = [...window.originalChartProfitLoss];
                filteredWinRate = [...window.originalChartWinRate];

                console.log('Showing all data:', {
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

                console.log('Calculated totals for all data:', {
                    totalWins,
                    totalLosses,
                    totalTrades: totalWins + totalLosses
                });
            } else {
                // Filter by selected market and/or account
                window.originalChartDataDetailed.forEach((monthData, index) => {
                    let monthProfitLoss = 0;
                    let monthWins = 0;
                    let monthLosses = 0;
                    let hasData = false;

                    monthData.markets.forEach(market => {
                        let includeMarket = true;

                        // Filter by market if selected
                        if (selectedMarket && market.market_id != selectedMarket) {
                            includeMarket = false;
                        }

                        // Filter by account if selected
                        if (selectedAccount && market.account_id != selectedAccount) {
                            includeMarket = false;
                        }

                        if (includeMarket) {
                            monthProfitLoss += parseFloat(market.profit_loss);
                            monthWins += parseInt(market.wins);
                            monthLosses += parseInt(market.losses);
                            hasData = true;
                        }
                    });

                    if (hasData) {
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

            // Update colors based on new data
            window.profitLossChart.data.datasets[0].borderColor = '#6c757d'; // Cor neutra (cinza)
            window.profitLossChart.data.datasets[0].pointBackgroundColor = window.getPointColors(filteredProfitLoss);

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
                if (!context || !context.parsed) return 'N/A';
                const value = context.parsed.y;
                const prefix = value >= 0 ? '+' : '';
                if (chartCurrency === 'USD') {
                    return `P&L: ${prefix}$${value.toFixed(2)}`;
                } else {
                    return `P&L: ${prefix}R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }
            };

            // Update Y-axis tick callback for currency formatting
            window.profitLossChart.options.scales.y.ticks.callback = function(value) {
                const prefix = value > 0 ? '+' : '';
                if (chartCurrency === 'USD') {
                    return `${prefix}$${value.toFixed(0)}`;
                } else {
                    return `${prefix}R$ ${value.toFixed(0)}`;
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
                    profitLossElement.textContent = '$' + totalProfitLoss.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else {
                    profitLossElement.textContent = 'R$ ' + totalProfitLoss.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        }

        // Event listeners
        marketFilter.addEventListener('change', function() {
            filterDashboard();
            loadCalendarData(); // Atualizar calendário quando mercado mudar
        });
        accountFilter.addEventListener('change', function() {
            filterDashboard();
            loadCalendarData(); // Atualizar calendário quando conta mudar
        });

        yearFilter.addEventListener('change', function() {
            const selectedYear = yearFilter.value;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('year', selectedYear);
            window.location.href = currentUrl.toString();
        });

        clearFilter.addEventListener('click', function() {
            marketFilter.value = '';
            accountFilter.value = '';
            yearFilter.value = '<?= date('Y') ?>';
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('year');
            window.location.href = currentUrl.toString();
        });

        // Trading Calendar functionality
        let currentCalendarMonth = <?= $currentMonth ?>;
        let currentCalendarYear = <?= $currentYear ?>;
        const studentId = <?= $student['id'] ?>;

        // Initialize calendar with current data
        const initialCalendarData = <?= json_encode($calendarData) ?>;
        renderCalendar(initialCalendarData);

        // Populate month selector dropdown
        function populateMonthSelector() {
            const monthSelector = document.getElementById('monthSelector');
            const monthNames = [
                '', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];

            monthSelector.innerHTML = '';

            // Adicionar meses do ano atual
            for (let month = 1; month <= 12; month++) {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item';
                a.href = '#';
                a.textContent = `${monthNames[month]} ${currentCalendarYear}`;
                a.dataset.month = month;
                a.dataset.year = currentCalendarYear;

                // Destacar o mês atual
                if (month === currentCalendarMonth) {
                    a.classList.add('active');
                }

                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectMonth(parseInt(this.dataset.month), parseInt(this.dataset.year));
                });

                li.appendChild(a);
                monthSelector.appendChild(li);
            }

            // Adicionar separador
            const separator = document.createElement('li');
            separator.innerHTML = '<hr class="dropdown-divider">';
            monthSelector.appendChild(separator);

            // Adicionar meses do próximo ano
            const nextYear = currentCalendarYear + 1;
            for (let month = 1; month <= 12; month++) {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item';
                a.href = '#';
                a.textContent = `${monthNames[month]} ${nextYear}`;
                a.dataset.month = month;
                a.dataset.year = nextYear;

                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectMonth(parseInt(this.dataset.month), parseInt(this.dataset.year));
                });

                li.appendChild(a);
                monthSelector.appendChild(li);
            }
        }

        // Function to select a specific month
        function selectMonth(month, year) {
            currentCalendarMonth = month;
            currentCalendarYear = year;
            loadCalendarData();
            populateMonthSelector(); // Atualizar o dropdown
        }

        // Initialize month selector
        populateMonthSelector();

        // Month navigation event listeners
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentCalendarMonth--;
            if (currentCalendarMonth < 1) {
                currentCalendarMonth = 12;
                currentCalendarYear--;
            }
            loadCalendarData();
            populateMonthSelector(); // Atualizar o dropdown
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentCalendarMonth++;
            if (currentCalendarMonth > 12) {
                currentCalendarMonth = 1;
                currentCalendarYear++;
            }
            loadCalendarData();
            populateMonthSelector(); // Atualizar o dropdown
        });

        // Load calendar data via AJAX
        function loadCalendarData() {
            const selectedAccount = accountFilter.value;
            const selectedMarket = marketFilter.value;
            let url = `/admin/students/get_calendar_data_ajax/${studentId}/${currentCalendarYear}/${currentCalendarMonth}`;

            // Adicionar parâmetros de filtro se selecionados
            const params = [];
            if (selectedAccount) {
                params.push(`account_id=${selectedAccount}`);
            }
            if (selectedMarket) {
                params.push(`market_id=${selectedMarket}`);
            }
            if (params.length > 0) {
                url += `?${params.join('&')}`;
            }

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $csrfToken ?>',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log('AJAX Response:', data);
                    if (data.success) {
                        console.log('Calendar data received:', data.data);
                        renderCalendar(data.data);
                        updateMonthYearDisplay();
                    } else {
                        console.error('Error loading calendar data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Update month/year display
        function updateMonthYearDisplay() {
            const monthNames = [
                '', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];
            document.getElementById('currentMonthYear').textContent =
                monthNames[currentCalendarMonth] + ' ' + currentCalendarYear;
        }

        // Render calendar with data
        function renderCalendar(calendarData) {
            console.log('renderCalendar called with:', calendarData);
            const calendarBody = document.getElementById('calendarBody');
            const weeklySummaryContent = document.getElementById('weeklySummaryContent');
            const summaryFooter = document.getElementById('summaryFooter');

            // Clear existing content
            calendarBody.innerHTML = '';
            weeklySummaryContent.innerHTML = '';
            summaryFooter.innerHTML = '';

            // Calculate days in month and first day of week
            const daysInMonth = new Date(currentCalendarYear, currentCalendarMonth, 0).getDate();
            const firstDayOfWeek = new Date(currentCalendarYear, currentCalendarMonth - 1, 1).getDay();

            // Create calendar weeks
            let currentWeek = document.createElement('div');
            currentWeek.className = 'calendar-week';

            // Add empty cells for days before the first day of the month
            for (let i = 0; i < firstDayOfWeek; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                currentWeek.appendChild(emptyDay);
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = day;
                dayElement.appendChild(dayNumber);

                // Check if there's trading data for this day
                const dayData = calendarData.daily ? calendarData.daily.find(d => parseInt(d.day) === day) : null;

                if (dayData) {
                    const profitLoss = parseFloat(dayData.profit_loss);
                    dayElement.classList.add(profitLoss >= 0 ? 'profit' : 'loss');

                    const tradeResult = document.createElement('div');
                    tradeResult.className = 'trade-result';

                    const tradeValue = document.createElement('div');
                    tradeValue.className = 'trade-value';
                    tradeValue.textContent = (profitLoss >= 0 ? '+' : '-') + 'R$ ' +
                        Math.abs(profitLoss).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                    const tradeDetails = document.createElement('div');
                    tradeDetails.className = 'trade-details';
                    tradeDetails.textContent = dayData.total_trades + ' trade' + (dayData.total_trades > 1 ? 's' : '');

                    const tradeMetrics = document.createElement('div');
                    tradeMetrics.className = 'trade-metrics';
                    const rRisk = dayData.r_risk ? parseFloat(dayData.r_risk).toFixed(1) + 'R' : '0.0R';
                    const winRate = dayData.win_rate ? parseFloat(dayData.win_rate).toFixed(0) + '%' : '0%';
                    tradeMetrics.textContent = rRisk + ', ' + winRate;

                    tradeResult.appendChild(tradeValue);
                    tradeResult.appendChild(tradeDetails);
                    tradeResult.appendChild(tradeMetrics);
                    dayElement.appendChild(tradeResult);

                    // Add tooltip
                    const dateStr = day + ' de ' + getMonthName(currentCalendarMonth);
                    const tooltipText = `${dateStr}: ${tradeValue.textContent} em ${dayData.total_trades} trade${dayData.total_trades > 1 ? 's' : ''} (${rRisk}, ${winRate})`;
                    dayElement.setAttribute('data-bs-toggle', 'tooltip');
                    dayElement.setAttribute('data-bs-placement', 'top');
                    dayElement.setAttribute('title', tooltipText);
                }

                currentWeek.appendChild(dayElement);

                // Start new week on Sunday (day 0)
                if ((firstDayOfWeek + day) % 7 === 0) {
                    calendarBody.appendChild(currentWeek);
                    currentWeek = document.createElement('div');
                    currentWeek.className = 'calendar-week';
                }
            }

            // Add remaining empty cells to complete the last week
            const remainingCells = 7 - currentWeek.children.length;
            for (let i = 0; i < remainingCells; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                currentWeek.appendChild(emptyDay);
            }

            if (currentWeek.children.length > 0) {
                calendarBody.appendChild(currentWeek);
            }

            // Render weekly summary
            renderWeeklySummary(calendarData.weekly || []);

            // Update monthly stats
            updateMonthlyStats(calendarData);

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Render weekly summary
        function renderWeeklySummary(weeklyData) {
            const weeklySummaryContent = document.getElementById('weeklySummaryContent');
            const summaryFooter = document.getElementById('summaryFooter');

            let bestWeek = null;
            let worstWeek = null;
            let bestValue = -Infinity;
            let worstValue = Infinity;

            weeklyData.forEach((week, index) => {
                const weekItem = document.createElement('div');
                weekItem.className = 'week-summary-item';

                const profitLoss = parseFloat(week.profit_loss);

                if (profitLoss > bestValue) {
                    bestValue = profitLoss;
                    bestWeek = {
                        ...week,
                        number: index + 1
                    };
                }
                if (profitLoss < worstValue) {
                    worstValue = profitLoss;
                    worstWeek = {
                        ...week,
                        number: index + 1
                    };
                }

                const weekLabel = document.createElement('div');
                weekLabel.className = 'week-label';
                weekLabel.textContent = `Semana ${index + 1}`;

                if (profitLoss === bestValue && profitLoss > 0) {
                    weekItem.classList.add('best-week');
                    weekLabel.innerHTML += ' <i class="bi bi-trophy-fill text-warning ms-1" title="Melhor semana"></i>';
                }

                // Adicionar classe para semanas com prejuízo
                if (profitLoss < 0) {
                    weekItem.classList.add('loss-week');
                }

                const weekValue = document.createElement('div');
                weekValue.className = 'week-value';
                if (profitLoss > 0) {
                    weekValue.classList.add('profit');
                } else if (profitLoss < 0) {
                    weekValue.classList.add('loss');
                } else {
                    weekValue.classList.add('neutral');
                }
                weekValue.textContent = (profitLoss >= 0 ? '+' : '-') + 'R$ ' + Math.abs(profitLoss).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                const weekDays = document.createElement('div');
                weekDays.className = 'week-days';
                weekDays.textContent = week.days + ' dia' + (week.days !== 1 ? 's' : '');

                weekItem.appendChild(weekLabel);
                weekItem.appendChild(weekValue);
                weekItem.appendChild(weekDays);
                weeklySummaryContent.appendChild(weekItem);
            });

            // Update summary footer
            if (bestWeek && worstWeek) {
                const bestDiv = document.createElement('div');
                bestDiv.className = 'd-flex justify-content-between';
                bestDiv.innerHTML = `
                <span class="text-muted small">Melhor:</span>
                <span class="text-success small fw-bold">Semana ${bestWeek.number} (${bestValue >= 0 ? '+' : '-'}R$ ${Math.abs(bestValue).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})})</span>
            `;

                const worstDiv = document.createElement('div');
                worstDiv.className = 'd-flex justify-content-between';
                worstDiv.innerHTML = `
                <span class="text-muted small">Pior:</span>
                <span class="text-muted small">Semana ${worstWeek.number} (${worstValue >= 0 ? '+' : '-'}R$ ${Math.abs(worstValue).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})})</span>
            `;

                summaryFooter.appendChild(bestDiv);
                summaryFooter.appendChild(worstDiv);
            }
        }

        // Update monthly statistics
        function updateMonthlyStats(calendarData) {
            let monthlyTotal = 0;
            let activeDays = 0;

            if (calendarData.daily) {
                calendarData.daily.forEach(day => {
                    monthlyTotal += parseFloat(day.profit_loss);
                    activeDays++;
                });
            }

            document.getElementById('monthlyTotal').textContent =
                'Total: ' + (monthlyTotal >= 0 ? '+' : '-') + 'R$ ' + Math.abs(monthlyTotal).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            document.getElementById('activeDays').textContent =
                activeDays + ' dias ativos';
        }

        // Helper function to get month name
        function getMonthName(month) {
            const monthNames = [
                '', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
            ];
            return monthNames[month];
        }

        // Function to update filtered statistics
        function updateFilteredStats(selectedMarket, selectedAccount) {
            const studentId = <?= $student['id'] ?>;
            let url = `/admin/students/get_filtered_stats_ajax/${studentId}`;
            
            const params = [];
            if (selectedMarket) {
                params.push(`market_id=${selectedMarket}`);
            }
            if (selectedAccount) {
                params.push(`account_id=${selectedAccount}`);
            }
            
            if (params.length > 0) {
                url += `?${params.join('&')}`;
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= $csrfToken ?>',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.data;
                    
                    // Update total studies
                    document.getElementById('total-studies-stat').textContent = 
                        new Intl.NumberFormat('pt-BR').format(stats.total_studies);
                    
                    // Update win rate
                    const winRateElement = document.getElementById('winrate-stat');
                    winRateElement.textContent = stats.overall_win_rate + '%';
                    
                    // Update total trades
                    document.getElementById('total-trades-stat').textContent = 
                        new Intl.NumberFormat('pt-BR').format(stats.total_trades);
                    
                    // Update profit/loss
                    const profitElement = document.getElementById('profit-loss-stat');
                    const profitValue = parseFloat(stats.total_profit_loss) || 0;
                    const formattedProfit = new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(profitValue);
                    profitElement.textContent = formattedProfit;
                    
                    // Update profit/loss color classes
                    profitElement.className = profitElement.className.replace(/text-(success|danger)/g, '');
                    profitElement.classList.add(profitValue >= 0 ? 'text-success' : 'text-danger');
                    
                    // Update win rate color classes
                    winRateElement.className = winRateElement.className.replace(/text-(success|warning)/g, '');
                    winRateElement.classList.add(stats.overall_win_rate >= 50 ? 'text-success' : 'text-warning');
                    
                } else {
                    console.error('Error loading filtered stats:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
</script>