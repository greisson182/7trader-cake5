<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="students index content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-people-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Estudantes
            </h1>
            <p class="text-muted mb-0">Gerenciar e acompanhar o progresso dos estudantes</p>
        </div>
        <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
            <a href="/admin/students/add" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus me-2"></i>
                Novo Estudante
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($students)): ?>
        <!-- Students Grid -->
        <div class="row g-4">
            <?php foreach ($students as $student): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card glass h-100 student-card">
                        <div class="card-body d-flex flex-column">
                            <!-- Student Avatar -->
                            <div class="text-center mb-3">
                                <div class="student-avatar mx-auto mb-3">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-1"><?= h($student['name']) ?></h5>
                                <p class="text-muted small mb-0"><?= h($student['email']) ?></p>
                            </div>

                            <!-- Student Info -->
                            <div class="student-info mb-3 flex-grow-1">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-hash text-primary"></i>
                                            <span class="small text-muted">ID</span>
                                            <div class="fw-semibold"><?= h($student['id']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-calendar-plus text-success"></i>
                                            <span class="small text-muted">Cadastrado</span>
                                            <div class="fw-semibold small"><?= date('d/m/Y', strtotime($student['created'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="student-actions">
                                <div class="d-grid gap-2">
                                    <a href="/admin/students/dashboard/<?= $student['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-chart-bar me-1"></i>Dashboard
                                    </a>
                                    <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                                        <div class="btn-group" role="group">
                                            <a href="/admin/students/view/<?= $student['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/students/edit/<?= $student['id'] ?>" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="post" action="/admin/students/delete/<?= $student['id'] ?>" style="display: inline;"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este estudante?');">
                                                <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Statistics Card -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card glass">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <i class="bi bi-people-fill display-4 text-primary mb-2"></i>
                                    <h3 class="fw-bold"><?= count($students) ?></h3>
                                    <p class="text-muted mb-0">Total de Estudantes</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <i class="bi bi-graph-up display-4 text-success mb-2"></i>
                                    <h3 class="fw-bold">Ativo</h3>
                                    <p class="text-muted mb-0">Progresso de Aprendizado</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <i class="bi bi-award display-4 text-warning mb-2"></i>
                                    <h3 class="fw-bold">Crescendo</h3>
                                    <p class="text-muted mb-0">Desenvolvimento de Habilidades</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card glass text-center">
                    <div class="card-body py-5">
                        <div class="empty-state">
                            <i class="bi bi-people display-1 text-muted mb-4"></i>
                            <h3 class="fw-bold mb-3">Nenhum Estudante Ainda</h3>
                            <p class="text-muted mb-4">
                                Comece adicionando seu primeiro estudante para começar a acompanhar o progresso.
                            </p>
                            <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                                <a href="/admin/students/add" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Criar Primeiro Estudante
                                </a>
                            <?php else: ?>
                                <p class="text-muted small">Entre em contato com seu administrador para adicionar estudantes.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Student Card Styles */
    .student-card {
        transition: all var(--transition-normal);
        border: 1px solid var(--glass-border);
    }

    .student-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-xl), 0 0 30px rgba(102, 126, 234, 0.2);
    }

    .student-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        box-shadow: var(--shadow-lg);
    }

    .info-item {
        text-align: center;
        padding: var(--space-sm);
        border-radius: var(--radius-sm);
        background: rgba(255, 255, 255, 0.05);
        transition: var(--transition-fast);
    }

    .info-item:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }

    .info-item i {
        font-size: 1.2rem;
        margin-bottom: var(--space-xs);
        display: block;
    }

    .student-actions .btn {
        transition: var(--transition-normal);
    }

    .student-actions .btn:hover {
        transform: translateY(-2px);
    }

    .stat-item {
        padding: var(--space-lg);
        transition: var(--transition-normal);
    }

    .stat-item:hover {
        transform: translateY(-5px);
    }

    .empty-state i {
        opacity: 0.3;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .student-card {
            margin-bottom: var(--space-lg);
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            border-radius: var(--radius-sm) !important;
            margin-bottom: var(--space-xs);
        }
    }
</style>