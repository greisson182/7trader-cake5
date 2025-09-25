<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PermissionsProfile $permissionsProfile
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Permissions Profile'), ['action' => 'edit', $permissionsProfile->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Permissions Profile'), ['action' => 'delete', $permissionsProfile->id], ['confirm' => __('Are you sure you want to delete # {0}?', $permissionsProfile->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Permissions Profiles'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Permissions Profile'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="permissionsProfiles view content">
            <h3><?= h($permissionsProfile->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Permission') ?></th>
                    <td><?= $permissionsProfile->hasValue('permission') ? $this->Html->link($permissionsProfile->permission->name, ['controller' => 'Permissions', 'action' => 'view', $permissionsProfile->permission->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Profile') ?></th>
                    <td><?= $permissionsProfile->hasValue('profile') ? $this->Html->link($permissionsProfile->profile->name, ['controller' => 'Profiles', 'action' => 'view', $permissionsProfile->profile->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($permissionsProfile->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>