<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MentorshipsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mentorships');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('MentorshipModules', [
            'foreignKey' => 'mentorship_id',
            'sort' => ['MentorshipModules.order_position' => 'ASC'],
        ]);
        $this->hasMany('MentorshipVideos', [
            'foreignKey' => 'mentorship_id',
            'sort' => ['MentorshipVideos.order_position' => 'ASC'],
        ]);
        $this->hasMany('MentorshipInstances', [
            'foreignKey' => 'mentorship_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('type')
            ->inList('type', ['grupo', 'particular'])
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->decimal('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        return $validator;
    }
}
