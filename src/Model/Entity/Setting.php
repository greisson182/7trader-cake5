<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setting Entity
 *
 * @property int $id
 * @property int $maintenance
 * @property string|null $title
 * @property string|null $description
 * @property string|null $image
 * @property string|null $email
 * @property string|null $face_app_id
 * @property string|null $face_author
 * @property string|null $face_publisher
 * @property string|null $google_publisher
 * @property string|null $google_author
 * @property string|null $aws_api
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class Setting extends Entity
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
        'maintenance' => true,
        'title' => true,
        'description' => true,
        'image' => true,
        'email' => true,
        'face_app_id' => true,
        'face_author' => true,
        'face_publisher' => true,
        'google_publisher' => true,
        'google_author' => true,
        'aws_api' => true,
        'created' => true,
        'modified' => true,
    ];
}
