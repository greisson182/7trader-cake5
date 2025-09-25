<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CourseEnrollment> $courseEnrollments
 */
?>
<div class="courseEnrollments index content">
    <?= $this->Html->link(__('New Course Enrollment'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Course Enrollments') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('student_id') ?></th>
                    <th><?= $this->Paginator->sort('course_id') ?></th>
                    <th><?= $this->Paginator->sort('enrolled_at') ?></th>
                    <th><?= $this->Paginator->sort('completed_at') ?></th>
                    <th><?= $this->Paginator->sort('progress_percentage') ?></th>
                    <th><?= $this->Paginator->sort('is_active') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courseEnrollments as $courseEnrollment): ?>
                <tr>
                    <td><?= $this->Number->format($courseEnrollment->id) ?></td>
                    <td><?= $courseEnrollment->hasValue('student') ? $this->Html->link($courseEnrollment->student->name, ['controller' => 'Students', 'action' => 'view', $courseEnrollment->student->id]) : '' ?></td>
                    <td><?= $courseEnrollment->hasValue('course') ? $this->Html->link($courseEnrollment->course->title, ['controller' => 'Courses', 'action' => 'view', $courseEnrollment->course->id]) : '' ?></td>
                    <td><?= h($courseEnrollment->enrolled_at) ?></td>
                    <td><?= h($courseEnrollment->completed_at) ?></td>
                    <td><?= $courseEnrollment->progress_percentage === null ? '' : $this->Number->format($courseEnrollment->progress_percentage) ?></td>
                    <td><?= h($courseEnrollment->is_active) ?></td>
                    <td><?= h($courseEnrollment->created) ?></td>
                    <td><?= h($courseEnrollment->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $courseEnrollment->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $courseEnrollment->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $courseEnrollment->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $courseEnrollment->id),
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