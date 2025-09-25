<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CourseEnrollments Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 *
 * @method \App\Model\Entity\CourseEnrollment newEmptyEntity()
 * @method \App\Model\Entity\CourseEnrollment newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CourseEnrollment> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CourseEnrollment get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CourseEnrollment findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CourseEnrollment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CourseEnrollment> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CourseEnrollment|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CourseEnrollment saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CourseEnrollment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseEnrollment>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CourseEnrollment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseEnrollment> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CourseEnrollment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseEnrollment>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CourseEnrollment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseEnrollment> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CourseEnrollmentsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('course_enrollments');
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
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('student_id')
            ->notEmptyString('student_id');

        $validator
            ->integer('course_id')
            ->notEmptyString('course_id');

        $validator
            ->dateTime('enrolled_at')
            ->allowEmptyDateTime('enrolled_at');

        $validator
            ->dateTime('completed_at')
            ->allowEmptyDateTime('completed_at');

        $validator
            ->decimal('progress_percentage')
            ->allowEmptyString('progress_percentage');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['student_id', 'course_id']), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);

        return $rules;
    }
}
