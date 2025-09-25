<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class User extends Entity
{
    protected array $_accessible = [
        'username' => true,
        'email' => true,
        'password' => true,
        'role' => true,
        'student_id' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'groupp_id' => true,
        'profile_id' => true,
        'student' => true,
        'groupp' => true,
        'profile' => true,
        'salt' => true,
        'last_login' => true,
        'status' => true,
        'blocked' => true,
        'name' => true,
    ];

    protected array $_hidden = [
        'password',
    ];
}
