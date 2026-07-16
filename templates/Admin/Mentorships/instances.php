<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Mentorship $mentorship
 * @var array $instances
 */
?>
<div class="mentorship-instances content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-people-fill me-2 icon-accent"></i>
                Alunos — <?= h($mentorship->name) ?>
            </h1>
            <p class="text-muted mb-0">Cada aluno tem sua própria instância privada com os vídeos das sessões dele.</p>
        </div>
        <a href="/admin/mentorships/view/<?= $mentorship->id ?>" class="btn btn-outline-light">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <div class="card glass">
        <div class="card-body">
            <?php if (empty($instances)): ?>
                <div class="empty-block">
                    <i class="bi bi-person-x"></i>
                    <h5 class="text-muted mb-2">Nenhum aluno ainda</h5>
                    <p class="text-muted mb-0">Assim que alguém comprar esta mentoria, a instância dele aparece aqui automaticamente.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Vídeos</th>
                                <th>Adquirida em</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instances as $instance): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= h($instance->student->name ?? 'Aluno removido') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= (int)$instance->video_count ?> vídeo<?= (int)$instance->video_count !== 1 ? 's' : '' ?></span>
                                    </td>
                                    <td><?= $instance->created ? $instance->created->format('d/m/Y H:i') : '-' ?></td>
                                    <td>
                                        <a href="/admin/mentorships/instance-videos/<?= $instance->id ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-collection-play me-1"></i>Gerenciar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
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
