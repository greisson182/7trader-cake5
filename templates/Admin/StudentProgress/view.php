<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentProgres $studentProgres
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Student Progres'), ['action' => 'edit', $studentProgres->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Student Progres'), ['action' => 'delete', $studentProgres->id], ['confirm' => __('Are you sure you want to delete # {0}?', $studentProgres->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Student Progress'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Student Progres'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="studentProgress view content">
            <h3><?= h($studentProgres->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $studentProgres->hasValue('student') ? $this->Html->link($studentProgres->student->name, ['controller' => 'Students', 'action' => 'view', $studentProgres->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Course') ?></th>
                    <td><?= $studentProgres->hasValue('course') ? $this->Html->link($studentProgres->course->title, ['controller' => 'Courses', 'action' => 'view', $studentProgres->course->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Video') ?></th>
                    <td><?= $studentProgres->hasValue('video') ? $this->Html->link($studentProgres->video->title, ['controller' => 'CourseVideos', 'action' => 'view', $studentProgres->video->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($studentProgres->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Watched Seconds') ?></th>
                    <td><?= $studentProgres->watched_seconds === null ? '' : $this->Number->format($studentProgres->watched_seconds) ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Watched') ?></th>
                    <td><?= h($studentProgres->last_watched) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($studentProgres->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($studentProgres->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Completed') ?></th>
                    <td><?= $studentProgres->completed ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>