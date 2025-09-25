<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SettingsFixture
 */
class SettingsFixture extends TestFixture
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
                'maintenance' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet',
                'image' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'face_app_id' => 'Lorem ipsum dolor sit amet',
                'face_author' => 'Lorem ipsum dolor sit amet',
                'face_publisher' => 'Lorem ipsum dolor sit amet',
                'google_publisher' => 'Lorem ipsum dolor sit amet',
                'google_author' => 'Lorem ipsum dolor sit amet',
                'aws_api' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-09-24 21:42:50',
                'modified' => '2025-09-24 21:42:50',
            ],
        ];
        parent::init();
    }
}
