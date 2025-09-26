<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * OperationsCostsFixture
 */
class OperationsCostsFixture extends TestFixture
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
                'cost_per_contract' => 1,
                'date_start' => '2025-09-26 17:25:40',
                'date_end' => '2025-09-26 17:25:40',
                'market_id' => 1,
                'student_id' => 1,
            ],
        ];
        parent::init();
    }
}
