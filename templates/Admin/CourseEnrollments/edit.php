<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CourseEnrollment $courseEnrollment
 * @var string[]|\Cake\Collection\CollectionInterface $students
 * @var string[]|\Cake\Collection\CollectionInterface $courses
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $courseEnrollment->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $courseEnrollment->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Course Enrollments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="courseEnrollments form content">
            <?= $this->Form->create($courseEnrollment) ?>
            <fieldset>
                <legend><?= __('Edit Course Enrollment') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('course_id', ['options' => $courses]);
                    echo $this->Form->control('enrolled_at', ['empty' => true]);
                    echo $this->Form->control('completed_at', ['empty' => true]);
                    echo $this->Form->control('progress_percentage');
                    echo $this->Form->control('is_active');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
