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
            <?= $this->Html->link(__('Edit Setting'), ['action' => 'edit', $setting->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Setting'), ['action' => 'delete', $setting->id], ['confirm' => __('Are you sure you want to delete # {0}?', $setting->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Settings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Setting'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="settings view content">
            <h3><?= h($setting->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($setting->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Description') ?></th>
                    <td><?= h($setting->description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Image') ?></th>
                    <td><?= h($setting->image) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($setting->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Face App Id') ?></th>
                    <td><?= h($setting->face_app_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Face Author') ?></th>
                    <td><?= h($setting->face_author) ?></td>
                </tr>
                <tr>
                    <th><?= __('Face Publisher') ?></th>
                    <td><?= h($setting->face_publisher) ?></td>
                </tr>
                <tr>
                    <th><?= __('Google Publisher') ?></th>
                    <td><?= h($setting->google_publisher) ?></td>
                </tr>
                <tr>
                    <th><?= __('Google Author') ?></th>
                    <td><?= h($setting->google_author) ?></td>
                </tr>
                <tr>
                    <th><?= __('Aws Api') ?></th>
                    <td><?= h($setting->aws_api) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($setting->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Maintenance') ?></th>
                    <td><?= $this->Number->format($setting->maintenance) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($setting->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($setting->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>