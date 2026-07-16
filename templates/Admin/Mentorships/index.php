<?php
/**
 * @var \App\View\AppView $this
 * @var array $mentorships
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="mentorships index content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-people-fill me-2 icon-accent"></i>
                Mentorias
            </h1>
            <p class="text-muted mb-0">Gerenciar mentorias em grupo e particulares</p>
        </div>
        <a href="/admin/mentorships/add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nova Mentoria
        </a>
    </div>

    <?php if (!empty($mentorships)): ?>
        <div class="row g-3">
            <?php foreach ($mentorships as $mentorship): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card glass h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-<?= $mentorship->type === 'particular' ? 'warning text-dark' : 'info' ?>">
                                    <?= $mentorship->type === 'particular' ? 'Particular' : 'Em grupo' ?>
                                </span>
                                <?php if ($mentorship->is_active): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativa</span>
                                <?php endif; ?>
                            </div>

                            <h6 class="card-title fw-bold mb-1"><?= h($mentorship->name) ?></h6>
                            <p class="card-text text-muted small mb-2 clamp-2">
                                <?= h(substr((string)$mentorship->description, 0, 120)) ?><?= strlen((string)$mentorship->description) > 120 ? '...' : '' ?>
                            </p>

                            <div class="fw-bold fs-5 mb-3">
                                R$ <?= number_format((float)$mentorship->price, 2, ',', '.') ?>
                            </div>

                            <div class="course-actions mt-auto">
                                <a href="/admin/mentorships/view/<?= $mentorship->id ?>" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye me-1"></i>Visualizar
                                </a>
                                <a href="/admin/mentorships/edit/<?= $mentorship->id ?>" class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="/admin/mentorships/delete/<?= $mentorship->id ?>" class="d-flex"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta mentoria?');">
                                    <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-block">
            <i class="bi bi-people"></i>
            <h5 class="text-muted mb-2">Nenhuma mentoria cadastrada</h5>
            <p class="text-muted mb-3">Cadastre as mentorias em grupo e particular para vender no site</p>
            <a href="/admin/mentorships/add" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Criar Primeira Mentoria
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5em;
    }

    .course-actions {
        display: flex;
        align-items: stretch;
        gap: 0.35rem;
    }

    .course-actions form button {
        height: 100%;
    }

    .empty-block {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted, #b0b0b0);
    }

    .empty-block i {
        font-size: 3rem;
        opacity: 0.4;
        display: block;
        margin-bottom: 1rem;
    }
</style>
