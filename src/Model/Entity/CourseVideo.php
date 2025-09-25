<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseVideo Entity
 *
 * @property int $id
 * @property int $course_id
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
 * @property \App\Model\Entity\Course $course
 */
class CourseVideo extends Entity
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
        'course_id' => true,
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
        'course' => true,
    ];
}
