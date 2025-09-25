<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Course Entity
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $thumbnail
 * @property int|null $duration_minutes
 * @property string|null $difficulty
 * @property string|null $category
 * @property string|null $instructor
 * @property string|null $price
 * @property bool|null $is_free
 * @property bool|null $is_active
 * @property int|null $order_position
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\CourseEnrollment[] $course_enrollments
 * @property \App\Model\Entity\CourseVideo[] $course_videos
 * @property \App\Model\Entity\StudentProgres[] $student_progress
 */
class Course extends Entity
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
        'title' => true,
        'description' => true,
        'thumbnail' => true,
        'duration_minutes' => true,
        'difficulty' => true,
        'category' => true,
        'instructor' => true,
        'price' => true,
        'is_free' => true,
        'is_active' => true,
        'order_position' => true,
        'created' => true,
        'modified' => true,
        'course_enrollments' => true,
        'course_videos' => true,
        'student_progress' => true,
    ];
}
