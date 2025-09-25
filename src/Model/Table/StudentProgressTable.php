<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StudentProgress Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\CourseVideosTable&\Cake\ORM\Association\BelongsTo $Videos
 *
 * @method \App\Model\Entity\StudentProgres newEmptyEntity()
 * @method \App\Model\Entity\StudentProgres newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\StudentProgres> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentProgres get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\StudentProgres findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\StudentProgres patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\StudentProgres> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentProgres|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\StudentProgres saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\StudentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StudentProgres>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\StudentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StudentProgres> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\StudentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StudentProgres>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\StudentProgres>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StudentProgres> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudentProgressTable extends Table
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

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['student_id', 'video_id']), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);
        $rules->add($rules->existsIn(['video_id'], 'Videos'), ['errorField' => 'video_id']);

        return $rules;
    }
}
