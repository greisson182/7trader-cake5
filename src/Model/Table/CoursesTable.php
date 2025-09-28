<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CoursesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('courses');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CourseEnrollments', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('CourseVideos', [
            'foreignKey' => 'course_id',
        ]);
        $this->hasMany('StudentProgress', [
            'foreignKey' => 'course_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('thumbnail')
            ->maxLength('thumbnail', 500)
            ->allowEmptyString('thumbnail');

        $validator
            ->integer('duration_minutes')
            ->allowEmptyString('duration_minutes');

        $validator
            ->scalar('difficulty')
            ->allowEmptyString('difficulty');

        $validator
            ->scalar('category')
            ->maxLength('category', 100)
            ->allowEmptyString('category');

        $validator
            ->scalar('instructor')
            ->maxLength('instructor', 255)
            ->allowEmptyString('instructor');

        $validator
            ->decimal('price')
            ->allowEmptyString('price');

        $validator
            ->boolean('is_free')
            ->allowEmptyString('is_free');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        return $validator;
    }
}
