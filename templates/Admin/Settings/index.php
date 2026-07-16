<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Setting> $settings
 */
?>
<div class="settings-page content fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-gear-fill me-2 icon-accent"></i>
                Configurações
            </h1>
            <p class="text-muted mb-0">Registros de configuração do site</p>
        </div>
        <a href="/admin/settings/add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Nova Configuração
        </a>
    </div>

    <div class="card glass">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('id', '#') ?></th>
                            <th><?= $this->Paginator->sort('title', 'Título') ?></th>
                            <th><?= $this->Paginator->sort('email', 'E-mail') ?></th>
                            <th><?= $this->Paginator->sort('maintenance', 'Manutenção') ?></th>
                            <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings as $setting): ?>
                        <tr>
                            <td>#<?= $this->Number->format($setting->id) ?></td>
                            <td><?= h($setting->title) ?: '—' ?></td>
                            <td><?= h($setting->email) ?: '—' ?></td>
                            <td>
                                <?php if ($setting->maintenance): ?>
                                    <span class="badge bg-warning text-dark">Ligado</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Desligado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= h($setting->modified) ?></td>
                            <td class="text-end">
                                <a href="/admin/settings/view/<?= $setting->id ?>" class="btn btn-outline-primary btn-sm" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/admin/settings/edit/<?= $setting->id ?>" class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="paginator mt-3">
        <ul class="pagination">
            <?= $this->Paginator->first('« ' . __('primeira')) ?>
            <?= $this->Paginator->prev('‹ ' . __('anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('próxima') . ' ›') ?>
            <?= $this->Paginator->last(__('última') . ' »') ?>
        </ul>
    </div>
</div>

<style>
    .icon-accent {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
