<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PermissionsProfile $permissionsProfile
 * @var string[]|\Cake\Collection\CollectionInterface $permissions
 * @var string[]|\Cake\Collection\CollectionInterface $profiles
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $permissionsProfile->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $permissionsProfile->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Permissions Profiles'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="permissionsProfiles form content">
            <?= $this->Form->create($permissionsProfile) ?>
            <fieldset>
                <legend><?= __('Edit Permissions Profile') ?></legend>
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
