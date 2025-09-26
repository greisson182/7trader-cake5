<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class OperationsCost extends Entity
{
    protected array $_accessible = [
        'cost_per_contract' => true,
        'date_start' => true,
        'date_end' => true,
        'market_id' => true,
        'student_id' => true,
        'account_id' => true,
        'market' => true,
        'student' => true,
        'account' => true,
    ];
}
