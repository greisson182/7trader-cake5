<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Market extends Entity
{
    protected array $_accessible = [
        'name' => true,
        'code' => true,
        'description' => true,
        'active' => true,
        'type' => true,
        'currency' => true,
        'created_at' => true,
        'updated_at' => true,
        'studies' => true,
    ];
}
