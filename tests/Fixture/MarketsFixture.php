<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MarketsFixture
 */
class MarketsFixture extends TestFixture
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
                'code' => 'Lorem ipsum dolor ',
                'description' => 'Lorem ipsum dolor sit amet',
                'active' => 1,
                'type' => 'Lorem ipsum dolor sit amet',
                'currency' => 'L',
                'created_at' => '2025-09-24 21:39:01',
                'updated_at' => '2025-09-24 21:39:01',
            ],
        ];
        parent::init();
    }
}
