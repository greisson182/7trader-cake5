<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Groupp> $groupps
 */
?>
<div class="groupps index content">
    <?= $this->Html->link(__('New Groupp'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Groupps') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('type_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groupps as $groupp): ?>
                <tr>
                    <td><?= $this->Number->format($groupp->id) ?></td>
                    <td><?= h($groupp->name) ?></td>
                    <td><?= h($groupp->created) ?></td>
                    <td><?= h($groupp->modified) ?></td>
                    <td><?= $groupp->hasValue('type') ? $this->Html->link($groupp->type->name, ['controller' => 'Types', 'action' => 'view', $groupp->type->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $groupp->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $groupp->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $groupp->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $groupp->id),
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