<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Markets Model
 *
 * @property \App\Model\Table\StudiesTable&\Cake\ORM\Association\HasMany $Studies
 *
 * @method \App\Model\Entity\Market newEmptyEntity()
 * @method \App\Model\Entity\Market newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Market> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Market get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Market findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Market patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Market> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Market|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Market saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Market>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Market>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Market>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Market> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Market>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Market>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Market>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Market> deleteManyOrFail(iterable $entities, array $options = [])
 */
class MarketsTable extends Table
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

        $this->setTable('markets');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Studies', [
            'foreignKey' => 'market_id',
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
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('code')
            ->maxLength('code', 20)
            ->requirePresence('code', 'create')
            ->notEmptyString('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->notEmptyString('type');

        $validator
            ->scalar('currency')
            ->maxLength('currency', 3)
            ->notEmptyString('currency');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->notEmptyDateTime('updated_at');

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
        $rules->add($rules->isUnique(['code']), ['errorField' => 'code']);

        return $rules;
    }
}
