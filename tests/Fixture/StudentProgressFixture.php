<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * StudentProgressFixture
 */
class StudentProgressFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'student_progress';
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
                'video_id' => 1,
                'watched_seconds' => 1,
                'completed' => 1,
                'last_watched' => '2025-09-24 21:43:16',
                'created' => '2025-09-24 21:43:16',
                'modified' => '2025-09-24 21:43:16',
            ],
        ];
        parent::init();
    }
}
