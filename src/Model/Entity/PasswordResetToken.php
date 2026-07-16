<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PasswordResetToken Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $token_hash
 * @property \Cake\I18n\DateTime $expires_at
 * @property \Cake\I18n\DateTime|null $used_at
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\User $user
 */
class PasswordResetToken extends Entity
{
    protected array $_accessible = [
        'user_id' => true,
        'token_hash' => true,
        'expires_at' => true,
        'used_at' => true,
        'created' => true,
        'user' => true,
    ];
}
