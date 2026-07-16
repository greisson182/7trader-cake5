<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MentorshipVideo Entity
 *
 * @property int $id
 * @property int $mentorship_id
 * @property int|null $instance_id
 * @property int|null $module_id
 * @property string $title
 * @property string|null $description
 * @property string $video_url
 * @property string|null $video_type
 * @property int|null $duration_seconds
 * @property int|null $order_position
 * @property bool|null $is_preview
 * @property bool|null $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Mentorship $mentorship
 * @property \App\Model\Entity\MentorshipModule $mentorship_module
 */
class MentorshipVideo extends Entity
{
    protected array $_accessible = [
        'mentorship_id' => true,
        'instance_id' => true,
        'module_id' => true,
        'title' => true,
        'description' => true,
        'video_url' => true,
        'video_type' => true,
        'duration_seconds' => true,
        'order_position' => true,
        'is_preview' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'mentorship' => true,
        'mentorship_module' => true,
    ];
}
