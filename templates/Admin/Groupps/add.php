<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Groupp $groupp
 * @var \Cake\Collection\CollectionInterface|string[] $types
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Groupps'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="groupps form content">
            <?= $this->Form->create($groupp) ?>
            <fieldset>
                <legend><?= __('Add Groupp') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('type_id', ['options' => $types, 'empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
