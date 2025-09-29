<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('students');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CourseEnrollments', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('StudentProgress', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Studies', [
            'foreignKey' => 'student_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'student_id',
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
            ->scalar('phone')
            ->maxLength('phone', 20)
            ->allowEmptyString('phone');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {

        return $rules;
    }
}
