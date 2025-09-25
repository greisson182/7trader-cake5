<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CourseVideo> $courseVideos
 */
?>
<div class="courseVideos index content">
    <?= $this->Html->link(__('New Course Video'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Course Videos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('course_id') ?></th>
                    <th><?= $this->Paginator->sort('title') ?></th>
                    <th><?= $this->Paginator->sort('video_url') ?></th>
                    <th><?= $this->Paginator->sort('video_type') ?></th>
                    <th><?= $this->Paginator->sort('duration_seconds') ?></th>
                    <th><?= $this->Paginator->sort('order_position') ?></th>
                    <th><?= $this->Paginator->sort('is_preview') ?></th>
                    <th><?= $this->Paginator->sort('is_active') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courseVideos as $courseVideo): ?>
                <tr>
                    <td><?= $this->Number->format($courseVideo->id) ?></td>
                    <td><?= $courseVideo->hasValue('course') ? $this->Html->link($courseVideo->course->title, ['controller' => 'Courses', 'action' => 'view', $courseVideo->course->id]) : '' ?></td>
                    <td><?= h($courseVideo->title) ?></td>
                    <td><?= h($courseVideo->video_url) ?></td>
                    <td><?= h($courseVideo->video_type) ?></td>
                    <td><?= $courseVideo->duration_seconds === null ? '' : $this->Number->format($courseVideo->duration_seconds) ?></td>
                    <td><?= $courseVideo->order_position === null ? '' : $this->Number->format($courseVideo->order_position) ?></td>
                    <td><?= h($courseVideo->is_preview) ?></td>
                    <td><?= h($courseVideo->is_active) ?></td>
                    <td><?= h($courseVideo->created) ?></td>
                    <td><?= h($courseVideo->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $courseVideo->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $courseVideo->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $courseVideo->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $courseVideo->id),
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