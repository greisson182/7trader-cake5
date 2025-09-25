<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseEnrollment Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int $course_id
 * @property \Cake\I18n\DateTime|null $enrolled_at
 * @property \Cake\I18n\DateTime|null $completed_at
 * @property string|null $progress_percentage
 * @property bool|null $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Course $course
 */
class CourseEnrollment extends Entity
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
        'enrolled_at' => true,
        'completed_at' => true,
        'progress_percentage' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'course' => true,
    ];
}
