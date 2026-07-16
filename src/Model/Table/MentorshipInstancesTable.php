<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MentorshipInstancesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mentorship_instances');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Mentorships', [
            'foreignKey' => 'mentorship_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('MentorshipModules', [
            'foreignKey' => 'instance_id',
            'sort' => ['MentorshipModules.order_position' => 'ASC'],
        ]);
        $this->hasMany('MentorshipVideos', [
            'foreignKey' => 'instance_id',
            'sort' => ['MentorshipVideos.order_position' => 'ASC'],
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('mentorship_id')
            ->notEmptyString('mentorship_id');

        $validator
            ->integer('student_id')
            ->notEmptyString('student_id');

        $validator
            ->integer('order_id')
            ->notEmptyString('order_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['mentorship_id'], 'Mentorships'), ['errorField' => 'mentorship_id']);
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['order_id'], 'Orders'), ['errorField' => 'order_id']);
        $rules->add($rules->isUnique(['order_id']), ['errorField' => 'order_id']);

        return $rules;
    }
}
