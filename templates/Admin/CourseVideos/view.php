<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseVideo $courseVideo
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Course Video'), ['action' => 'edit', $courseVideo->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Course Video'), ['action' => 'delete', $courseVideo->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseVideo->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Course Videos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Course Video'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="courseVideos view content">
            <h3><?= h($courseVideo->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Course') ?></th>
                    <td><?= $courseVideo->hasValue('course') ? $this->Html->link($courseVideo->course->title, ['controller' => 'Courses', 'action' => 'view', $courseVideo->course->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($courseVideo->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Video Url') ?></th>
                    <td><?= h($courseVideo->video_url) ?></td>
                </tr>
                <tr>
                    <th><?= __('Video Type') ?></th>
                    <td><?= h($courseVideo->video_type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($courseVideo->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration Seconds') ?></th>
                    <td><?= $courseVideo->duration_seconds === null ? '' : $this->Number->format($courseVideo->duration_seconds) ?></td>
                </tr>
                <tr>
                    <th><?= __('Order Position') ?></th>
                    <td><?= $courseVideo->order_position === null ? '' : $this->Number->format($courseVideo->order_position) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($courseVideo->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($courseVideo->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Preview') ?></th>
                    <td><?= $courseVideo->is_preview ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Active') ?></th>
                    <td><?= $courseVideo->is_active ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($courseVideo->description)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>