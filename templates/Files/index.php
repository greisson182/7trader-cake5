<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\File> $files
 */
?>
<div class="files index content">
    <?= $this->Html->link(__('New File'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Files') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('name_simple') ?></th>
                    <th><?= $this->Paginator->sort('slug') ?></th>
                    <th><?= $this->Paginator->sort('url') ?></th>
                    <th><?= $this->Paginator->sort('realpeth') ?></th>
                    <th><?= $this->Paginator->sort('size') ?></th>
                    <th><?= $this->Paginator->sort('extension') ?></th>
                    <th><?= $this->Paginator->sort('content_type') ?></th>
                    <th><?= $this->Paginator->sort('type') ?></th>
                    <th><?= $this->Paginator->sort('file_key') ?></th>
                    <th><?= $this->Paginator->sort('relationship') ?></th>
                    <th><?= $this->Paginator->sort('others_info') ?></th>
                    <th><?= $this->Paginator->sort('phase') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('occult') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                <tr>
                    <td><?= $this->Number->format($file->id) ?></td>
                    <td><?= h($file->name) ?></td>
                    <td><?= h($file->name_simple) ?></td>
                    <td><?= h($file->slug) ?></td>
                    <td><?= h($file->url) ?></td>
                    <td><?= h($file->realpeth) ?></td>
                    <td><?= $file->size === null ? '' : $this->Number->format($file->size) ?></td>
                    <td><?= h($file->extension) ?></td>
                    <td><?= h($file->content_type) ?></td>
                    <td><?= h($file->type) ?></td>
                    <td><?= h($file->file_key) ?></td>
                    <td><?= h($file->relationship) ?></td>
                    <td><?= h($file->others_info) ?></td>
                    <td><?= h($file->phase) ?></td>
                    <td><?= h($file->created) ?></td>
                    <td><?= h($file->modified) ?></td>
                    <td><?= $file->occult === null ? '' : $this->Number->format($file->occult) ?></td>
                    <td><?= $file->hasValue('user') ? $this->Html->link($file->user->username, ['controller' => 'Users', 'action' => 'view', $file->user->id]) : '' ?></td>
                    <td><?= h($file->status) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $file->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $file->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $file->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $file->id),
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