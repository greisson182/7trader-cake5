<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Market Entity
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property bool $active
 * @property string $type
 * @property string $currency
 * @property \Cake\I18n\DateTime $created_at
 * @property \Cake\I18n\DateTime $updated_at
 *
 * @property \App\Model\Entity\Study[] $studies
 */
class Market extends Entity
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
