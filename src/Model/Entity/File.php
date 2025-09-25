<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * File Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $name_simple
 * @property string|null $slug
 * @property string|null $url
 * @property string|null $realpeth
 * @property float|null $size
 * @property string|null $extension
 * @property string|null $content_type
 * @property string|null $type
 * @property string|null $file_key
 * @property string|null $file_log
 * @property string|null $relationship
 * @property string|null $others_info
 * @property string|null $phase
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property int|null $occult
 * @property int|null $user_id
 * @property string|null $status
 *
 * @property \App\Model\Entity\User $user
 */
class File extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'name_simple' => true,
        'slug' => true,
        'url' => true,
        'realpeth' => true,
        'size' => true,
        'extension' => true,
        'content_type' => true,
        'type' => true,
        'file_key' => true,
        'file_log' => true,
        'relationship' => true,
        'others_info' => true,
        'phase' => true,
        'created' => true,
        'modified' => true,
        'occult' => true,
        'user_id' => true,
        'status' => true,
        'user' => true,
    ];
}
