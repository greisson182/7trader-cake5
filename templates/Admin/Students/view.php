<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student $student
 * @var array $studies
 * @var array $overallStats
 * @var array $courseTitles
 * @var array $mentorshipNames
 */
$user = $student->user ?? null;
?>
<div class="students view content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-person-fill me-2 icon-accent"></i>
                <?= h($student->name) ?>
            </h1>
            <p class="text-muted mb-0"><?= h($user->email ?? 'sem e-mail') ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/students/edit/<?= h($student->id) ?>" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="/admin/students" class="btn btn-outline-light btn-sm">
                <i class="bi bi-list-ul me-2"></i>Listar Estudantes
            </a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>Informações Pessoais
                    </h6>
                    <div class="row g-2">
                        <div class="col-sm-6 meta-row">
                            <span><i class="bi bi-hash me-1"></i> ID</span>
                            <strong>#<?= h($student->id) ?></strong>
                        </div>
                        <div class="col-sm-6 meta-row">
                            <span><i class="bi bi-person-badge me-1"></i> Usuário</span>
                            <strong><?= h($user->username ?? '—') ?></strong>
                        </div>
                        <div class="col-sm-6 meta-row">
                            <span><i class="bi bi-calendar-plus me-1"></i> Cadastrado em</span>
                            <strong><?= h(date('d/m/Y', strtotime($student->created))) ?></strong>
                        </div>
                        <div class="col-sm-6 meta-row">
                            <span><i class="bi bi-clock-history me-1"></i> Último acesso</span>
                            <strong>
                                <?= !empty($user->last_login) ? h($user->last_login->format('d/m/Y H:i')) : 'Nunca acessou' ?>
                            </strong>
                        </div>
                        <div class="col-sm-6 meta-row">
                            <span><i class="bi bi-mortarboard me-1"></i> Cursos inscritos</span>
                            <strong><?= !empty($courseTitles) ? h(implode(', ', $courseTitles)) : 'Nenhum curso' ?></strong>
                        </div>
                        <div class="col-sm-6 meta-row">
                            <span><i class="bi bi-people me-1"></i> Mentoria</span>
                            <strong><?= !empty($mentorshipNames) ? h(implode(', ', $mentorshipNames)) : 'Nenhuma mentoria' ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card glass h-100">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-graph-up me-2"></i>Estatísticas Gerais
                    </h6>
                    <div class="row g-2 text-center mb-3">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-value"><?= (int)$overallStats['total_studies'] ?></div>
                                <div class="stat-label">Estudos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-value stat-value-success"><?= (int)$overallStats['total_trades'] ?></div>
                                <div class="stat-label">Operações</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-value"><?= number_format($overallStats['win_rate'], 2) ?>%</div>
                                <div class="stat-label">Taxa de Acerto</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="stat-value <?= $overallStats['total_profit_loss'] >= 0 ? 'stat-value-success' : 'stat-value-danger' ?>">
                                    <?= $this->Currency::formatForUser($overallStats['total_profit_loss'], 'BRL') ?>
                                </div>
                                <div class="stat-label">P&amp;L Total</div>
                            </div>
                        </div>
                    </div>
                    <ul class="meta-list">
                        <li>
                            <span>Gain</span>
                            <strong class="text-success"><?= (int)$overallStats['total_wins'] ?></strong>
                        </li>
                        <li>
                            <span>Loss</span>
                            <strong class="text-danger"><?= (int)$overallStats['total_losses'] ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Estudos recentes: card com 100% da largura -->
    <div class="card glass">
        <div class="card-body">
            <h6 class="section-title">
                <i class="bi bi-clock-history me-2"></i>Estudos Recentes
            </h6>

            <?php if (empty($studies)): ?>
                <div class="empty-block">
                    <i class="bi bi-journal-x"></i>
                    <p class="mb-2">Este estudante ainda não registrou nenhum estudo de market replay.</p>
                    <a href="/admin/studies/add" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>Adicionar Estudo
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Mercado</th>
                                <th>Conta</th>
                                <th>Gain</th>
                                <th>Loss</th>
                                <th>Taxa de Acerto</th>
                                <th>P&amp;L</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($studies, 0, 10) as $study): ?>
                                <?php
                                $studyTrades = (int)$study->wins + (int)$study->losses;
                                $studyWinRate = $studyTrades > 0 ? ((int)$study->wins / $studyTrades) * 100 : 0;
                                $studyCurrency = $study->market->currency ?? 'BRL';
                                ?>
                                <tr>
                                    <td><?= h(date('d/m/Y', strtotime((string)$study->study_date))) ?></td>
                                    <td>
                                        <?php if ($study->market): ?>
                                            <span class="badge bg-info"><?= h($study->market->name) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($study->account): ?>
                                            <span class="badge bg-secondary"><?= h($study->account->name) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-success"><?= h($study->wins) ?></span></td>
                                    <td><span class="badge bg-danger"><?= h($study->losses) ?></span></td>
                                    <td>
                                        <span class="badge <?= $studyWinRate >= 50 ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= number_format($studyWinRate, 2) ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="pl-badge <?= $study->profit_loss >= 0 ? 'pl-badge-gain' : 'pl-badge-loss' ?>">
                                            <?= $this->Currency::formatForUser($study->profit_loss, $studyCurrency) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/studies/view/<?= h($study->id) ?>" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (count($studies) > 10): ?>
                    <div class="text-center mt-3">
                        <a href="/admin/studies?student=<?= h($student->id) ?>" class="btn btn-outline-primary btn-sm">
                            Ver Todos os Estudos (<?= count($studies) ?>)
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
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

    .meta-row {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        margin-bottom: 0.5rem;
    }

    .meta-row span {
        font-size: 0.8rem;
        color: var(--text-muted, #b0b0b0);
    }

    .meta-row strong {
        color: var(--text-primary, #fff);
        font-weight: 600;
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
        font-size: 0.85rem;
    }

    .meta-list li span {
        color: var(--text-muted, #b0b0b0);
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-sm, 8px);
        padding: 0.75rem 0.5rem;
    }

    .stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
    }

    .stat-value-success {
        color: #00ff88;
    }

    .stat-value-danger {
        color: #dc3545;
    }

    .stat-label {
        font-size: 0.72rem;
        color: var(--text-muted, #b0b0b0);
    }

    .empty-block {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--text-muted, #b0b0b0);
    }

    .empty-block i {
        font-size: 2.5rem;
        opacity: 0.4;
        display: block;
        margin-bottom: 0.75rem;
    }

    .pl-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-weight: 700;
        border: 1.5px solid transparent;
        font-size: 0.8rem;
    }

    .pl-badge-gain {
        border-color: #00ff88;
        color: #00ff88;
        background: rgba(0, 255, 136, 0.08);
    }

    .pl-badge-loss {
        border-color: #dc3545;
        color: #dc3545;
        background: rgba(220, 53, 69, 0.08);
    }
</style>
