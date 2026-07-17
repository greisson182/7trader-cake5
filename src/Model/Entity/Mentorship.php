<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Mentorship Entity
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property string|null $thumbnail
 * @property string $price
 * @property bool|null $is_active
 * @property int|null $order_position
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\MentorshipModule[] $mentorship_modules
 * @property \App\Model\Entity\MentorshipVideo[] $mentorship_videos
 */
class Mentorship extends Entity
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
        'type' => true,
        'description' => true,
        'thumbnail' => true,
        'price' => true,
        'is_active' => true,
        'order_position' => true,
        'created' => true,
        'modified' => true,
        'mentorship_modules' => true,
        'mentorship_videos' => true,
    ];
}
