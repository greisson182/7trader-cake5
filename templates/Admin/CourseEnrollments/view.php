<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseEnrollment $courseEnrollment
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Course Enrollment'), ['action' => 'edit', $courseEnrollment->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Course Enrollment'), ['action' => 'delete', $courseEnrollment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseEnrollment->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Course Enrollments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Course Enrollment'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="courseEnrollments view content">
            <h3><?= h($courseEnrollment->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $courseEnrollment->hasValue('student') ? $this->Html->link($courseEnrollment->student->name, ['controller' => 'Students', 'action' => 'view', $courseEnrollment->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Course') ?></th>
                    <td><?= $courseEnrollment->hasValue('course') ? $this->Html->link($courseEnrollment->course->title, ['controller' => 'Courses', 'action' => 'view', $courseEnrollment->course->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($courseEnrollment->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Progress Percentage') ?></th>
                    <td><?= $courseEnrollment->progress_percentage === null ? '' : $this->Number->format($courseEnrollment->progress_percentage) ?></td>
                </tr>
                <tr>
                    <th><?= __('Enrolled At') ?></th>
                    <td><?= h($courseEnrollment->enrolled_at) ?></td>
                </tr>
                <tr>
                    <th><?= __('Completed At') ?></th>
                    <td><?= h($courseEnrollment->completed_at) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($courseEnrollment->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($courseEnrollment->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Active') ?></th>
                    <td><?= $courseEnrollment->is_active ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>