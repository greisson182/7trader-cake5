<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\File $file
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $file->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $file->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Files'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="files form content">
            <?= $this->Form->create($file) ?>
            <fieldset>
                <legend><?= __('Edit File') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('name_simple');
                    echo $this->Form->control('slug');
                    echo $this->Form->control('url');
                    echo $this->Form->control('realpeth');
                    echo $this->Form->control('size');
                    echo $this->Form->control('extension');
                    echo $this->Form->control('content_type');
                    echo $this->Form->control('type');
                    echo $this->Form->control('file_key');
                    echo $this->Form->control('file_log');
                    echo $this->Form->control('relationship');
                    echo $this->Form->control('others_info');
                    echo $this->Form->control('phase');
                    echo $this->Form->control('occult');
                    echo $this->Form->control('user_id', ['options' => $users, 'empty' => true]);
                    echo $this->Form->control('status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
