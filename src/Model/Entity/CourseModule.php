<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseModule Entity
 *
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property string|null $description
 * @property int|null $order_position
 * @property bool|null $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\CourseVideo[] $course_videos
 */
class CourseModule extends Entity
{
    protected array $_accessible = [
        'course_id' => true,
        'title' => true,
        'description' => true,
        'order_position' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'course' => true,
        'course_videos' => true,
    ];
}
