<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StudentProgres Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $course_id
 * @property int $video_id
 * @property int|null $watched_seconds
 * @property bool|null $completed
 * @property \Cake\I18n\DateTime|null $last_watched
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\CourseVideo $video
 */
class StudentProgres extends Entity
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
        'student_id' => true,
        'course_id' => true,
        'video_id' => true,
        'watched_seconds' => true,
        'completed' => true,
        'last_watched' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'course' => true,
        'video' => true,
    ];
}
