<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PermissionsProfile> $permissionsProfiles
 */
?>
<div class="permissionsProfiles index content">
    <?= $this->Html->link(__('New Permissions Profile'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Permissions Profiles') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('permission_id') ?></th>
                    <th><?= $this->Paginator->sort('profile_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permissionsProfiles as $permissionsProfile): ?>
                <tr>
                    <td><?= $this->Number->format($permissionsProfile->id) ?></td>
                    <td><?= $permissionsProfile->hasValue('permission') ? $this->Html->link($permissionsProfile->permission->name, ['controller' => 'Permissions', 'action' => 'view', $permissionsProfile->permission->id]) : '' ?></td>
                    <td><?= $permissionsProfile->hasValue('profile') ? $this->Html->link($permissionsProfile->profile->name, ['controller' => 'Profiles', 'action' => 'view', $permissionsProfile->profile->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $permissionsProfile->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $permissionsProfile->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $permissionsProfile->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $permissionsProfile->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>