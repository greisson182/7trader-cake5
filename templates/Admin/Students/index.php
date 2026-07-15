<?php
/**
 * @var \App\View\AppView $this
 * @var array $students
 */
$csrfToken = $this->request->getAttribute('csrfToken');
$isAdmin = isset($logado) && $logado->role === 'admin';

$totalStudents = count($students);
$activeStudents = 0;
$blockedStudents = 0;
foreach ($students as $student) {
    if (!empty($student->user)) {
        if (!empty($student->user->blocked)) {
            $blockedStudents++;
        } elseif (!empty($student->user->active)) {
            $activeStudents++;
        }
    }
}
?>
<div class="students index content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-people-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Estudantes
            </h1>
            <p class="text-muted mb-0">Gerenciar e acompanhar o progresso dos estudantes</p>
        </div>
        <?php if ($isAdmin): ?>
            <a href="/admin/students/add" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus me-2"></i>
                Novo Estudante
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($students)): ?>
        <!-- Quick stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="quick-stat">
                    <div class="fw-bold fs-4"><?= $totalStudents ?></div>
                    <div class="text-muted small">Total</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="quick-stat">
                    <div class="fw-bold fs-4 text-success"><?= $activeStudents ?></div>
                    <div class="text-muted small">Ativos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="quick-stat">
                    <div class="fw-bold fs-4 text-danger"><?= $blockedStudents ?></div>
                    <div class="text-muted small">Bloqueados</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="quick-stat">
                    <input type="search" id="studentSearch" class="form-control" placeholder="Buscar por nome, e-mail ou usuário...">
                </div>
            </div>
        </div>

        <!-- Students Grid -->
        <div class="row g-3" id="studentsGrid">
            <?php foreach ($students as $student): ?>
                <?php
                    $user = $student->user ?? null;
                    $isBlocked = $user && !empty($user->blocked);
                    $isActive = $user && !empty($user->active) && !$isBlocked;
                    $searchValue = mb_strtolower($student->name . ' ' . ($user->email ?? '') . ' ' . ($user->username ?? ''));
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6 student-col" data-search="<?= h($searchValue) ?>">
                    <div class="card glass h-100 student-card">
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="student-avatar-sm flex-shrink-0">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="min-w-0">
                                    <h6 class="fw-bold mb-0 text-truncate" title="<?= h($student->name) ?>"><?= h($student->name) ?></h6>
                                    <div class="small text-muted text-truncate" title="<?= h($user->email ?? '') ?>">
                                        <?= h($user->email ?? 'sem e-mail') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3 small">
                                <span class="text-muted">
                                    <i class="bi bi-hash"></i><?= h($student->id) ?>
                                    &middot; <?= h($user->username ?? '—') ?>
                                </span>
                                <?php if ($isBlocked): ?>
                                    <span class="badge bg-danger">Bloqueado</span>
                                <?php elseif ($isActive): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </div>

                            <div class="text-muted small mb-3">
                                <i class="bi bi-calendar-plus me-1"></i>
                                Cadastrado em <?= date('d/m/Y', strtotime($student->created)) ?>
                            </div>

                            <div class="mt-auto d-grid gap-2">
                                <a href="/admin/students/dashboard/<?= $student->id ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-chart-bar me-1"></i>Dashboard
                                </a>
                                <?php if ($isAdmin): ?>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/students/view/<?= $student->id ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/students/edit/<?= $student->id ?>" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="post" action="/admin/students/delete/<?= $student->id ?>" style="display: inline;"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este estudante?');">
                                            <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
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
            <?php endforeach; ?>
        </div>

        <p id="studentsEmptySearch" class="text-center text-muted py-5 d-none">
            Nenhum estudante encontrado para essa busca.
        </p>

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
                            <?php if ($isAdmin): ?>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('studentSearch');
    if (!searchInput) {
        return;
    }

    const cols = Array.from(document.querySelectorAll('.student-col'));
    const emptyMessage = document.getElementById('studentsEmptySearch');

    searchInput.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();
        let visibleCount = 0;

        cols.forEach(function (col) {
            const matches = col.dataset.search.includes(term);
            col.classList.toggle('d-none', !matches);
            if (matches) {
                visibleCount++;
            }
        });

        emptyMessage.classList.toggle('d-none', visibleCount !== 0);
    });
});
</script>

<style>
    .quick-stat {
        background: rgba(255, 255, 255, 0.05);
        border-radius: var(--radius-sm);
        padding: var(--space-sm) var(--space-md);
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .quick-stat input.form-control {
        text-align: left;
    }

    .student-card {
        transition: all var(--transition-normal);
        border: 1px solid var(--glass-border);
    }

    .student-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg), 0 0 20px rgba(102, 126, 234, 0.15);
    }

    .student-avatar-sm {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
    }

    .min-w-0 {
        min-width: 0;
    }

    .empty-state i {
        opacity: 0.3;
    }
</style>
