<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Operation extends Entity
{
    protected array $_accessible = [
        'study_id' => true,
        'asset' => true,
        'open_time' => true,
        'close_time' => true,
        'trade_duration' => true,
        'buy_quantity' => true,
        'sell_quantity' => true,
        'side' => true,
        'buy_price' => true,
        'sell_price' => true,
        'market_price' => true,
        'mep' => true,
        'men' => true,
        'buy_agent' => true,
        'sell_agent' => true,
        'average_price' => true,
        'gross_interval_result' => true,
        'interval_result_percent' => true,
        'operation_number' => true,
        'operation_result' => true,
        'operation_result_percent' => true,
        'drawdown' => true,
        'max_gain' => true,
        'max_loss' => true,
        'tet' => true,
        'total' => true,
        'created' => true,
        'modified' => true,
        'study' => true,
        'account' => true,
        'holder' => true,
        'date_start' => true,
        'date_last' => true,
    ];

    protected array $_hidden = [
    ];
}