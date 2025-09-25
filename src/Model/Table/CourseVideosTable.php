<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CourseVideos Model
 *
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 *
 * @method \App\Model\Entity\CourseVideo newEmptyEntity()
 * @method \App\Model\Entity\CourseVideo newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CourseVideo> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CourseVideo get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CourseVideo findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CourseVideo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CourseVideo> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CourseVideo|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CourseVideo saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CourseVideo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseVideo>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CourseVideo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseVideo> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CourseVideo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseVideo>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CourseVideo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CourseVideo> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CourseVideosTable extends Table
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

        $this->setTable('course_videos');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->integer('course_id')
            ->notEmptyString('course_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('video_url')
            ->maxLength('video_url', 500)
            ->requirePresence('video_url', 'create')
            ->notEmptyString('video_url');

        $validator
            ->scalar('video_type')
            ->allowEmptyString('video_type');

        $validator
            ->integer('duration_seconds')
            ->allowEmptyString('duration_seconds');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        $validator
            ->boolean('is_preview')
            ->allowEmptyString('is_preview');

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
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);

        return $rules;
    }
}
