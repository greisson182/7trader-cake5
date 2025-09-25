<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
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
                'username' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ipsum dolor sit amet',
                'role' => 'Lorem ipsum dolor ',
                'student_id' => 1,
                'active' => 1,
                'created' => '2025-09-24 21:43:49',
                'modified' => '2025-09-24 21:43:49',
                'groupp_id' => 1,
                'profile_id' => 1,
            ],
        ];
        parent::init();
    }
}
