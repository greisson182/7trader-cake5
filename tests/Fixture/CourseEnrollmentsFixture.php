<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CourseEnrollmentsFixture
 */
class CourseEnrollmentsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'student_id' => 1,
                'course_id' => 1,
                'enrolled_at' => '2025-09-24 21:38:23',
                'completed_at' => '2025-09-24 21:38:23',
                'progress_percentage' => 1.5,
                'is_active' => 1,
                'created' => '2025-09-24 21:38:23',
                'modified' => '2025-09-24 21:38:23',
            ],
        ];
        parent::init();
    }
}
