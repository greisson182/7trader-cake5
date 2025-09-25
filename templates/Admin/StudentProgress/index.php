<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\StudentProgres> $studentProgress
 */
?>
<div class="studentProgress index content">
    <?= $this->Html->link(__('New Student Progres'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Student Progress') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('course_id') ?></th>
                    <th><?= $this->Paginator->sort('video_id') ?></th>
                    <th><?= $this->Paginator->sort('watched_seconds') ?></th>
                    <th><?= $this->Paginator->sort('completed') ?></th>
                    <th><?= $this->Paginator->sort('last_watched') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentProgress as $studentProgres): ?>
                <tr>
                    <td><?= $this->Number->format($studentProgres->id) ?></td>
                    <td><?= $studentProgres->hasValue('student') ? $this->Html->link($studentProgres->student->name, ['controller' => 'Students', 'action' => 'view', $studentProgres->student->id]) : '' ?></td>
                    <td><?= $studentProgres->hasValue('course') ? $this->Html->link($studentProgres->course->title, ['controller' => 'Courses', 'action' => 'view', $studentProgres->course->id]) : '' ?></td>
                    <td><?= $studentProgres->hasValue('video') ? $this->Html->link($studentProgres->video->title, ['controller' => 'CourseVideos', 'action' => 'view', $studentProgres->video->id]) : '' ?></td>
                    <td><?= $studentProgres->watched_seconds === null ? '' : $this->Number->format($studentProgres->watched_seconds) ?></td>
                    <td><?= h($studentProgres->completed) ?></td>
                    <td><?= h($studentProgres->last_watched) ?></td>
                    <td><?= h($studentProgres->created) ?></td>
                    <td><?= h($studentProgres->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $studentProgres->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $studentProgres->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $studentProgres->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $studentProgres->id),
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