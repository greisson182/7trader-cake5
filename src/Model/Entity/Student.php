<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Student extends Entity
{
    protected array $_accessible = [
        'name' => true,
        'phone' => true,
        'created' => true,
        'modified' => true,
        'course_enrollments' => true,
        'student_progress' => true,
        'studies' => true,
        'users' => true,
    ];
}
