<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $setting->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $setting->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Settings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="settings form content">
            <?= $this->Form->create($setting) ?>
            <fieldset>
                <legend><?= __('Edit Setting') ?></legend>
                <?php
                    echo $this->Form->control('maintenance');
                    echo $this->Form->control('title');
                    echo $this->Form->control('description');
                    echo $this->Form->control('image');
                    echo $this->Form->control('email');
                    echo $this->Form->control('face_app_id');
                    echo $this->Form->control('face_author');
                    echo $this->Form->control('face_publisher');
                    echo $this->Form->control('google_publisher');
                    echo $this->Form->control('google_author');
                    echo $this->Form->control('aws_api');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
