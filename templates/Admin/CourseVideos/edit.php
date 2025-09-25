<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseVideo $courseVideo
 * @var string[]|\Cake\Collection\CollectionInterface $courses
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $courseVideo->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $courseVideo->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Course Videos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="courseVideos form content">
            <?= $this->Form->create($courseVideo) ?>
            <fieldset>
                <legend><?= __('Edit Course Video') ?></legend>
                <?php
                    echo $this->Form->control('course_id', ['options' => $courses]);
                    echo $this->Form->control('title');
                    echo $this->Form->control('description');
                    echo $this->Form->control('video_url');
                    echo $this->Form->control('video_type');
                    echo $this->Form->control('duration_seconds');
                    echo $this->Form->control('order_position');
                    echo $this->Form->control('is_preview');
                    echo $this->Form->control('is_active');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
