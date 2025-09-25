<div class="students metrics content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-line"></i> Metrics for <?= h($student->name) ?></h3>
        <?= $this->Html->link(__('Back to Students'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Studies</h5>
                    <h2><?= $overallStats['total_studies'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Overall Win Rate</h5>
                    <h2><?= $overallStats['overall_win_rate'] ?>%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Trades</h5>
                    <h2><?= $overallStats['total_trades'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card <?= $overallStats['total_profit_loss'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total P&L</h5>
                    <h2><?= $this->Currency::formatForUser($overallStats['total_profit_loss'], $student->currency ?? 'BRL') ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Selection Forms -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-day"></i> Daily Metrics</h5>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3']) ?>
                    <div class="input-group">
                        <?= $this->Form->control('date', [
                            'type' => 'date',
                            'value' => $currentDate,
                            'class' => 'form-control',
                            'label' => false
                        ]) ?>
                        <button class="btn btn-outline-secondary" type="submit">Update</button>
                    </div>
                    <?= $this->Form->end() ?>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h6>Win Rate</h6>
                                <h4 class="text-success"><?= $dailyMetrics['win_rate'] ?>%</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h6>P&L</h6>
                                <h4 class="<?= $dailyMetrics['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $this->Currency::formatForUser($dailyMetrics['total_profit_loss'], $student->currency ?? 'BRL') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Wins</small><br>
                            <strong><?= $dailyMetrics['total_wins'] ?></strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Losses</small><br>
                            <strong><?= $dailyMetrics['total_losses'] ?></strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Total</small><br>
                            <strong><?= $dailyMetrics['total_trades'] ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt"></i> Monthly Metrics</h5>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3']) ?>
                    <div class="row">
                        <div class="col-6">
                            <?= $this->Form->control('year', [
                                'type' => 'number',
                                'value' => $currentYear,
                                'class' => 'form-control',
                                'label' => 'Year',
                                'min' => 2020,
                                'max' => date('Y') + 1
                            ]) ?>
                        </div>
                        <div class="col-6">
                            <?= $this->Form->control('month', [
                                'type' => 'select',
                                'options' => [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                ],
                                'value' => $currentMonth,
                                'class' => 'form-control',
                                'label' => 'Month'
                            ]) ?>
                        </div>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm mt-2" type="submit">Update</button>
                    <?= $this->Form->end() ?>

                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h6>Win Rate</h6>
                                <h4 class="text-success"><?= $monthlyMetrics['win_rate'] ?>%</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h6>P&L</h6>
                                <h4 class="<?= $monthlyMetrics['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $this->Currency::formatForUser($monthlyMetrics['total_profit_loss'], $student->currency ?? 'BRL') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Wins</small><br>
                            <strong><?= $monthlyMetrics['total_wins'] ?></strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Losses</small><br>
                            <strong><?= $monthlyMetrics['total_losses'] ?></strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Total</small><br>
                            <strong><?= $monthlyMetrics['total_trades'] ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Studies -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history"></i> Recent Studies</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($recentStudies)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Study Date</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th>Win Rate</th>
                                <th>Profit/Loss</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudies as $study): ?>
                            <tr>
                                <td><?= h($study->study_date->format('Y-m-d')) ?></td>
                                <td><span class="badge bg-success"><?= $study->wins ?></span></td>
                                <td><span class="badge bg-danger"><?= $study->losses ?></span></td>
                                <td><?= $study->win_rate ?>%</td>
                                <td class="<?= $study->profit_loss >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $this->Currency::formatForUser($study->profit_loss, $student->currency ?? 'BRL') ?>
                                </td>
                                <td>
                                    <?= $this->Html->link('<i class="fas fa-edit"></i>', ['controller' => 'Studies', 'action' => 'edit', $study->id], ['class' => 'btn btn-sm btn-outline-warning', 'escape' => false]) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No studies found for this student.</p>
            <?php endif; ?>
        </div>
    </div>
</div>