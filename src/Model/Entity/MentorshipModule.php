<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MentorshipModule Entity
 *
 * @property int $id
 * @property int $mentorship_id
 * @property int|null $instance_id
 * @property string $title
 * @property string|null $description
 * @property int|null $order_position
 * @property bool|null $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Mentorship $mentorship
 * @property \App\Model\Entity\MentorshipVideo[] $mentorship_videos
 */
class MentorshipModule extends Entity
{
    protected array $_accessible = [
        'mentorship_id' => true,
        'instance_id' => true,
        'title' => true,
        'description' => true,
        'order_position' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'mentorship' => true,
        'mentorship_videos' => true,
    ];
}
