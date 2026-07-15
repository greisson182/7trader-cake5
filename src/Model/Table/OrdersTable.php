<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class OrdersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('item_type')
            ->inList('item_type', ['course', 'mentorship'])
            ->requirePresence('item_type', 'create')
            ->notEmptyString('item_type');

        $validator
            ->integer('item_id')
            ->requirePresence('item_id', 'create')
            ->notEmptyString('item_id');

        $validator
            ->scalar('buyer_name')
            ->maxLength('buyer_name', 255)
            ->requirePresence('buyer_name', 'create')
            ->notEmptyString('buyer_name');

        $validator
            ->email('buyer_email')
            ->requirePresence('buyer_email', 'create')
            ->notEmptyString('buyer_email');

        $validator
            ->scalar('buyer_phone')
            ->maxLength('buyer_phone', 20)
            ->allowEmptyString('buyer_phone');

        $validator
            ->scalar('buyer_password_hash')
            ->requirePresence('buyer_password_hash', 'create')
            ->notEmptyString('buyer_password_hash');

        $validator
            ->decimal('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('status')
            ->inList('status', ['pending', 'approved', 'rejected', 'cancelled', 'refunded'])
            ->notEmptyString('status');

        $validator
            ->scalar('mp_preference_id')
            ->maxLength('mp_preference_id', 191)
            ->allowEmptyString('mp_preference_id');

        $validator
            ->scalar('mp_payment_id')
            ->maxLength('mp_payment_id', 191)
            ->allowEmptyString('mp_payment_id');

        $validator
            ->scalar('payment_method')
            ->maxLength('payment_method', 50)
            ->allowEmptyString('payment_method');

        $validator
            ->integer('student_id')
            ->allowEmptyString('student_id');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        $validator
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->allowEmptyString('ip_address');

        return $validator;
    }
}
