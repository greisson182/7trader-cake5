<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Groupp $groupp
 * @var string[]|\Cake\Collection\CollectionInterface $types
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $groupp->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $groupp->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Groupps'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="groupps form content">
            <?= $this->Form->create($groupp) ?>
            <fieldset>
                <legend><?= __('Edit Groupp') ?></legend>
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
