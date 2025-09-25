<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FilesFixture
 */
class FilesFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'name_simple' => 'Lorem ipsum dolor sit amet',
                'slug' => 'Lorem ipsum dolor sit amet',
                'url' => 'Lorem ipsum dolor sit amet',
                'realpeth' => 'Lorem ipsum dolor sit amet',
                'size' => 1,
                'extension' => 'Lorem ipsum dolor sit amet',
                'content_type' => 'Lorem ipsum dolor sit amet',
                'type' => 'Lorem ipsum dolor sit amet',
                'file_key' => 'Lorem ipsum dolor sit amet',
                'file_log' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'relationship' => 'Lorem ipsum dolor sit amet',
                'others_info' => 'Lorem ipsum dolor sit amet',
                'phase' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-09-24 21:46:54',
                'modified' => '2025-09-24 21:46:54',
                'occult' => 1,
                'user_id' => 1,
                'status' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
