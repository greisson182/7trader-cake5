<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property string $item_type
 * @property int $item_id
 * @property string $buyer_name
 * @property string $buyer_email
 * @property string|null $buyer_phone
 * @property string $buyer_password_hash
 * @property string $amount
 * @property string $status
 * @property string|null $mp_preference_id
 * @property string|null $mp_payment_id
 * @property string|null $payment_method
 * @property int|null $student_id
 * @property int|null $user_id
 * @property \Cake\I18n\DateTime|null $fulfilled_at
 * @property string|null $notes
 * @property string|null $ip_address
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Student|null $student
 */
class Order extends Entity
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
        'item_type' => true,
        'item_id' => true,
        'buyer_name' => true,
        'buyer_email' => true,
        'buyer_phone' => true,
        'buyer_password_hash' => true,
        'amount' => true,
        'status' => true,
        'mp_preference_id' => true,
        'mp_payment_id' => true,
        'payment_method' => true,
        'student_id' => true,
        'user_id' => true,
        'fulfilled_at' => true,
        'notes' => true,
        'ip_address' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
