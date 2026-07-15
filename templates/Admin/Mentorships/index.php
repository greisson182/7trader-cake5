<?php
/**
 * @var \App\View\AppView $this
 * @var array $mentorships
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="mentorships index content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-people-fill me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Mentorias
            </h1>
            <p class="text-muted mb-0">Gerenciar mentorias em grupo e particulares</p>
        </div>
        <a href="/admin/mentorships/add" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Nova Mentoria
        </a>
    </div>

    <?php if (!empty($mentorships)): ?>
        <div class="row g-4">
            <?php foreach ($mentorships as $mentorship): ?>
                <div class="col-lg-4 col-md-6">
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

                            <h5 class="card-title fw-bold mb-2"><?= h($mentorship->name) ?></h5>
                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                <?= h(substr((string)$mentorship->description, 0, 120)) ?><?= strlen((string)$mentorship->description) > 120 ? '...' : '' ?>
                            </p>

                            <div class="fw-bold fs-5 mb-3">
                                R$ <?= number_format((float)$mentorship->price, 2, ',', '.') ?>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="/admin/mentorships/view/<?= $mentorship->id ?>" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i>Visualizar
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="/admin/mentorships/edit/<?= $mentorship->id ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form method="post" action="/admin/mentorships/delete/<?= $mentorship->id ?>" style="display: inline;"
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta mentoria?');">
                                        <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state text-center py-5">
            <div class="empty-icon mb-4">
                <i class="bi bi-people" style="font-size: 4rem; color: var(--bs-gray-400);"></i>
            </div>
            <h3 class="text-muted mb-3">Nenhuma mentoria cadastrada</h3>
            <p class="text-muted mb-4">Cadastre as mentorias em grupo e particular para vender no site</p>
            <a href="/admin/mentorships/add" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Criar Primeira Mentoria
            </a>
        </div>
    <?php endif; ?>
</div>
