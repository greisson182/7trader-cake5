<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PermissionsProfile $permissionsProfile
 * @var \Cake\Collection\CollectionInterface|string[] $permissions
 * @var \Cake\Collection\CollectionInterface|string[] $profiles
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Permissions Profiles'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="permissionsProfiles form content">
            <?= $this->Form->create($permissionsProfile) ?>
            <fieldset>
                <legend><?= __('Add Permissions Profile') ?></legend>
                <?php
                    echo $this->Form->control('permission_id', ['options' => $permissions]);
                    echo $this->Form->control('profile_id', ['options' => $profiles]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
