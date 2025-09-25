<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentProgres $studentProgres
 * @var \Cake\Collection\CollectionInterface|string[] $students
 * @var \Cake\Collection\CollectionInterface|string[] $courses
 * @var \Cake\Collection\CollectionInterface|string[] $videos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Student Progress'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="studentProgress form content">
            <?= $this->Form->create($studentProgres) ?>
            <fieldset>
                <legend><?= __('Add Student Progres') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('course_id', ['options' => $courses]);
                    echo $this->Form->control('video_id', ['options' => $videos]);
                    echo $this->Form->control('watched_seconds');
                    echo $this->Form->control('completed');
                    echo $this->Form->control('last_watched', ['empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
