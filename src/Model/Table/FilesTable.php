<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Files Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\File newEmptyEntity()
 * @method \App\Model\Entity\File newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\File> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\File get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\File findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\File patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\File> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\File|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\File saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\File>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\File>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\File>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\File> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\File>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\File>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\File>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\File> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FilesTable extends Table
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

        $this->setTable('files');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('name_simple')
            ->maxLength('name_simple', 255)
            ->allowEmptyString('name_simple');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 300)
            ->allowEmptyString('slug');

        $validator
            ->scalar('url')
            ->maxLength('url', 1500)
            ->allowEmptyString('url');

        $validator
            ->scalar('realpeth')
            ->maxLength('realpeth', 1500)
            ->allowEmptyString('realpeth');

        $validator
            ->numeric('size')
            ->allowEmptyString('size');

        $validator
            ->scalar('extension')
            ->maxLength('extension', 255)
            ->allowEmptyString('extension');

        $validator
            ->scalar('content_type')
            ->maxLength('content_type', 255)
            ->allowEmptyString('content_type');

        $validator
            ->scalar('type')
            ->maxLength('type', 255)
            ->allowEmptyString('type');

        $validator
            ->scalar('file_key')
            ->maxLength('file_key', 255)
            ->allowEmptyString('file_key');

        $validator
            ->scalar('file_log')
            ->allowEmptyString('file_log');

        $validator
            ->scalar('relationship')
            ->maxLength('relationship', 255)
            ->allowEmptyString('relationship');

        $validator
            ->scalar('others_info')
            ->maxLength('others_info', 255)
            ->allowEmptyString('others_info');

        $validator
            ->scalar('phase')
            ->maxLength('phase', 255)
            ->allowEmptyString('phase');

        $validator
            ->integer('occult')
            ->allowEmptyString('occult');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
