<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MentorshipModulesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mentorship_modules');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Mentorships', [
            'foreignKey' => 'mentorship_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MentorshipInstances', [
            'foreignKey' => 'instance_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('MentorshipVideos', [
            'foreignKey' => 'module_id',
            'sort' => ['MentorshipVideos.order_position' => 'ASC', 'MentorshipVideos.created' => 'ASC'],
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('mentorship_id')
            ->notEmptyString('mentorship_id');

        $validator
            ->integer('instance_id')
            ->allowEmptyString('instance_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255, 'O título deve ter no máximo 255 caracteres.')
            ->requirePresence('title', 'create', 'O título é obrigatório.')
            ->notEmptyString('title', 'O título é obrigatório.');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['mentorship_id'], 'Mentorships'), ['errorField' => 'mentorship_id']);
        $rules->add($rules->existsIn(['instance_id'], 'MentorshipInstances'), ['errorField' => 'instance_id', 'allowNullableNulls' => true]);

        return $rules;
    }
}
