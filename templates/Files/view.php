<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\File $file
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit File'), ['action' => 'edit', $file->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete File'), ['action' => 'delete', $file->id], ['confirm' => __('Are you sure you want to delete # {0}?', $file->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Files'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New File'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="files view content">
            <h3><?= h($file->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($file->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name Simple') ?></th>
                    <td><?= h($file->name_simple) ?></td>
                </tr>
                <tr>
                    <th><?= __('Slug') ?></th>
                    <td><?= h($file->slug) ?></td>
                </tr>
                <tr>
                    <th><?= __('Url') ?></th>
                    <td><?= h($file->url) ?></td>
                </tr>
                <tr>
                    <th><?= __('Realpeth') ?></th>
                    <td><?= h($file->realpeth) ?></td>
                </tr>
                <tr>
                    <th><?= __('Extension') ?></th>
                    <td><?= h($file->extension) ?></td>
                </tr>
                <tr>
                    <th><?= __('Content Type') ?></th>
                    <td><?= h($file->content_type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Type') ?></th>
                    <td><?= h($file->type) ?></td>
                </tr>
                <tr>
                    <th><?= __('File Key') ?></th>
                    <td><?= h($file->file_key) ?></td>
                </tr>
                <tr>
                    <th><?= __('Relationship') ?></th>
                    <td><?= h($file->relationship) ?></td>
                </tr>
                <tr>
                    <th><?= __('Others Info') ?></th>
                    <td><?= h($file->others_info) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phase') ?></th>
                    <td><?= h($file->phase) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $file->hasValue('user') ? $this->Html->link($file->user->username, ['controller' => 'Users', 'action' => 'view', $file->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($file->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($file->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Size') ?></th>
                    <td><?= $file->size === null ? '' : $this->Number->format($file->size) ?></td>
                </tr>
                <tr>
                    <th><?= __('Occult') ?></th>
                    <td><?= $file->occult === null ? '' : $this->Number->format($file->occult) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($file->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($file->modified) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('File Log') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($file->file_log)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>