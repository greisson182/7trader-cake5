<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GrouppsFixture
 */
class GrouppsFixture extends TestFixture
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
                'created' => '2025-09-24 21:38:49',
                'modified' => '2025-09-24 21:38:49',
                'type_id' => 1,
            ],
        ];
        parent::init();
    }
}
