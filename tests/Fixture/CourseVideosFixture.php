<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CourseVideosFixture
 */
class CourseVideosFixture extends TestFixture
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
                'course_id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'video_url' => 'Lorem ipsum dolor sit amet',
                'video_type' => 'Lorem ipsum dolor sit amet',
                'duration_seconds' => 1,
                'order_position' => 1,
                'is_preview' => 1,
                'is_active' => 1,
                'created' => '2025-09-24 21:38:37',
                'modified' => '2025-09-24 21:38:37',
            ],
        ];
        parent::init();
    }
}
