<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Page extends Entity
{
    protected array $_accessible = [
        'title' => true,
        'content' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
    ];
}