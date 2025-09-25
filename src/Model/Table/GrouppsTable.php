<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Groupps Model
 *
 * @property \App\Model\Table\TypesTable&\Cake\ORM\Association\BelongsTo $Types
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Groupp newEmptyEntity()
 * @method \App\Model\Entity\Groupp newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Groupp> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Groupp get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Groupp findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Groupp patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Groupp> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Groupp|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Groupp saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Groupp>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Groupp>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Groupp>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Groupp> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Groupp>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Groupp>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Groupp>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Groupp> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GrouppsTable extends Table
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

        $this->setTable('groupps');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Types', [
            'foreignKey' => 'type_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'groupp_id',
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
            ->integer('type_id')
            ->allowEmptyString('type_id');

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
        $rules->add($rules->existsIn(['type_id'], 'Types'), ['errorField' => 'type_id']);

        return $rules;
    }
}
