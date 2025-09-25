<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CoursesFixture
 */
class CoursesFixture extends TestFixture
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
                'title' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'thumbnail' => 'Lorem ipsum dolor sit amet',
                'duration_minutes' => 1,
                'difficulty' => 'Lorem ipsum dolor sit amet',
                'category' => 'Lorem ipsum dolor sit amet',
                'instructor' => 'Lorem ipsum dolor sit amet',
                'price' => 1.5,
                'is_free' => 1,
                'is_active' => 1,
                'order_position' => 1,
                'created' => '2025-09-24 21:38:12',
                'modified' => '2025-09-24 21:38:12',
            ],
        ];
        parent::init();
    }
}
