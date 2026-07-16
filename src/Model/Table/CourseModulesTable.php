<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseModulesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('course_modules');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CourseVideos', [
            'foreignKey' => 'module_id',
            'sort' => ['CourseVideos.order_position' => 'ASC', 'CourseVideos.created' => 'ASC'],
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('course_id')
            ->notEmptyString('course_id');

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
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);

        return $rules;
    }
}
