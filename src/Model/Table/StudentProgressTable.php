<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentProgressTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('student_progress');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Videos', [
            'foreignKey' => 'video_id',
            'className' => 'CourseVideos',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('student_id')
            ->notEmptyString('student_id');

        $validator
            ->integer('course_id')
            ->notEmptyString('course_id');

        $validator
            ->integer('video_id')
            ->notEmptyString('video_id');

        $validator
            ->integer('watched_seconds')
            ->allowEmptyString('watched_seconds');

        $validator
            ->boolean('completed')
            ->allowEmptyString('completed');

        $validator
            ->dateTime('last_watched')
            ->allowEmptyDateTime('last_watched');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['student_id', 'video_id']), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);
        $rules->add($rules->existsIn(['video_id'], 'Videos'), ['errorField' => 'video_id']);

        return $rules;
    }
}
