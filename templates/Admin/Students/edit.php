<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student $student
 * @var \App\Model\Entity\User|null $user
 */
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<div class="students edit content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-person-gear me-2 icon-accent"></i>
                Editar Estudante
            </h1>
            <p class="text-muted mb-0"><?= h($student->name) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/students" class="btn btn-outline-light btn-sm">
                <i class="bi bi-list-ul me-2"></i>Listar Estudantes
            </a>
            <form method="post" action="/admin/students/delete/<?= h($student->id) ?>" class="d-flex"
                onsubmit="return confirm('Tem certeza que deseja excluir este estudante?');">
                <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-2"></i>Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <!-- Formulário -->
        <div class="col-lg-8">
            <div class="card glass">
                <div class="card-body">
                    <form method="post" action="/admin/students/edit/<?= h($student->id) ?>">
                        <input type="hidden" name="_csrfToken" value="<?= h($csrfToken) ?>">

                        <h6 class="section-title">
                            <i class="bi bi-person me-2"></i>Informações do Estudante
                        </h6>
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Nome</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= h($student->name) ?>" required>
                        </div>

                        <h6 class="section-title">
                            <i class="bi bi-key me-2"></i>Acesso do Usuário
                        </h6>

                        <?php if ($user): ?>
                            <div class="row g-3 mb-1">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= h($user->email) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="username" class="form-label fw-semibold">Nome de usuário</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                           value="<?= h($user->username) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">Nova senha</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Deixe em branco para manter a atual">
                                    <div class="form-text">Só preencha se quiser trocar a senha do estudante.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                               <?= !empty($user->active) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="active">Conta ativa</label>
                                        <div class="form-text">Desmarcar impede o login sem excluir o estudante.</div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-block">
                                <i class="bi bi-person-x"></i>
                                <p class="mb-0">Este estudante não possui usuário associado. Não é possível editar dados de acesso.</p>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-secondary-subtle">
                            <a href="/admin/students" class="btn btn-outline-light">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" <?= !$user ? 'disabled' : '' ?>>
                                <i class="bi bi-check-circle me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Status atual -->
        <div class="col-lg-4">
            <div class="card glass">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>Status Atual
                    </h6>

                    <?php if ($user): ?>
                        <ul class="meta-list">
                            <li>
                                <span>Usuário</span>
                                <strong><?= h($user->username) ?></strong>
                            </li>
                            <li>
                                <span>E-mail</span>
                                <strong><?= h($user->email) ?></strong>
                            </li>
                            <li>
                                <span>Papel</span>
                                <span class="badge <?= $user->role === 'admin' ? 'bg-danger' : 'bg-info' ?>">
                                    <?= $user->role === 'admin' ? 'Administrador' : 'Estudante' ?>
                                </span>
                            </li>
                            <li>
                                <span>Situação</span>
                                <?php if (!empty($user->blocked)): ?>
                                    <span class="badge bg-danger">Bloqueado</span>
                                <?php elseif (!empty($user->active)): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>Criado em</span>
                                <strong><?= h($user->created?->format('d/m/Y H:i')) ?></strong>
                            </li>
                            <li>
                                <span>Modificado em</span>
                                <strong><?= h($user->modified?->format('d/m/Y H:i')) ?></strong>
                            </li>
                        </ul>
                    <?php else: ?>
                        <div class="empty-block">
                            <i class="bi bi-person-x"></i>
                            <p class="mb-0">Nenhum usuário associado a este estudante.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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

    .meta-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .meta-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.85rem;
        gap: 0.5rem;
    }

    .meta-list li span:first-child {
        color: var(--text-muted, #b0b0b0);
    }

    .meta-list li strong {
        color: var(--text-primary, #fff);
        font-weight: 600;
        text-align: right;
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
</style>
