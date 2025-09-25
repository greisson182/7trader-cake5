<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentProgres $studentProgres
 * @var string[]|\Cake\Collection\CollectionInterface $students
 * @var string[]|\Cake\Collection\CollectionInterface $courses
 * @var string[]|\Cake\Collection\CollectionInterface $videos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $studentProgres->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $studentProgres->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Student Progress'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="studentProgress form content">
            <?= $this->Form->create($studentProgres) ?>
            <fieldset>
                <legend><?= __('Edit Student Progres') ?></legend>
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
