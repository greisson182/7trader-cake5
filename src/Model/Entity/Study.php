<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Study Entity
 *
 * @property int $id
 * @property int $student_id
 * @property int|null $market_id
 * @property int|null $account_id
 * @property \Cake\I18n\Date $study_date
 * @property int $wins
 * @property int $losses
 * @property string $profit_loss
 * @property string|null $notes
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Market $market
 * @property \App\Model\Entity\Account $account
 */
class Study extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'student_id' => true,
        'market_id' => true,
        'account_id' => true,
        'study_date' => true,
        'wins' => true,
        'losses' => true,
        'profit_loss' => true,
        'notes' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
        'market' => true,
        'account' => true,
    ];
}
