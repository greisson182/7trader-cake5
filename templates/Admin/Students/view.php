
<div class="students view content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-user gradient-text"></i> <?= h($student['name']) ?></h3>
        <div class="btn-group" role="group">
            <a href="/admin/students/edit/<?= $student['id'] ?>" class="btn btn-primary">Edit Student</a>
            <a href="/admin/students" class="btn btn-secondary">List Students</a>
            <a href="/admin/students/add" class="btn btn-success">New Student</a>
            <a href="/admin/studies/add" class="btn btn-warning">Add Study</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5><i class="fas fa-info-circle"></i> Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
                            <p><strong>Member Since:</strong> <?= date('Y-m-d', strtotime($student['created'])) ?></p>
                            <p><strong>Last Updated:</strong> <?= date('Y-m-d H:i:s', strtotime($student['modified'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line"></i> Quick Stats</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Studies:</strong> <?= isset($student['studies']) ? count($student['studies']) : 0 ?></p>
                            <?php if (!empty($student['studies'])): ?>
                                <?php 
                                $totalWins = array_sum(array_column($student['studies'], 'wins'));
                                $totalLosses = array_sum(array_column($student['studies'], 'losses'));
                                $totalTrades = $totalWins + $totalLosses;
                                $overallWinRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;
                                $totalProfitLoss = array_sum(array_column($student['studies'], 'profit_loss'));
                                ?>
                                <p><strong>Overall Win Rate:</strong> <?= number_format($overallWinRate, 2) ?>%</p>
                                <p><strong>Total P&L:</strong> 
                                    <span class="<?= $totalProfitLoss >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $this->Currency::formatForUser($totalProfitLoss, $student['currency'] ?? 'BRL') ?>
                                    </span>
                                </p>
                                <p><strong>Total Trades:</strong> <?= $totalTrades ?></p>
                            <?php else: ?>
                                <p class="text-muted">No studies recorded yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($student['studies'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history"></i> Recent Studies</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Study Date</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th>Profit/Loss</th>
                                <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($student['studies'], -10) as $study): ?>
                                <tr>
                                    <td><?= date('Y-m-d', strtotime($study['study_date'])) ?></td>
                                    <td><span class="badge bg-success"><?= $study['wins'] ?></span></td>
                                    <td><span class="badge bg-danger"><?= $study['losses'] ?></span></td>
                                    <td>
                                        <span class="badge <?= $study['win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?>">
                                            <?= number_format($study['win_rate'], 2) ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="<?= $study['profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $this->Currency::formatForUser($study['profit_loss'], $student['currency'] ?? 'BRL') ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="/admin/studies/view/<?= $study['id'] ?>" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/studies/edit/<?= $study['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="/studies/delete/<?= $study['id'] ?>" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this study?')">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($student['studies']) > 10): ?>
                    <div class="text-center mt-3">
                        <a href="/admin/studies/byStudent/<?= $student['id'] ?>" class="btn btn-primary">View All Studies</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> No Studies Yet</h6>
                <p class="mb-0">This student hasn't recorded any market replay studies yet. 
                <a href="/admin/studies/add" class="alert-link">Add the first study</a> to get started!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>