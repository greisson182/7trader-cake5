<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Studies Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\MarketsTable&\Cake\ORM\Association\BelongsTo $Markets
 * @property \App\Model\Table\AccountsTable&\Cake\ORM\Association\BelongsTo $Accounts
 *
 * @method \App\Model\Entity\Study newEmptyEntity()
 * @method \App\Model\Entity\Study newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Study> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Study get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Study findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Study patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Study> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Study|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Study saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Study>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Study>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Study>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Study> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Study>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Study>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Study>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Study> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudiesTable extends Table
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

        $this->setTable('studies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Markets', [
            'foreignKey' => 'market_id',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
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
            ->integer('market_id')
            ->allowEmptyString('market_id');

        $validator
            ->integer('account_id')
            ->allowEmptyString('account_id');

        $validator
            ->date('study_date')
            ->requirePresence('study_date', 'create')
            ->notEmptyDate('study_date');

        $validator
            ->integer('wins')
            ->notEmptyString('wins');

        $validator
            ->integer('losses')
            ->notEmptyString('losses');

        $validator
            ->decimal('profit_loss')
            ->notEmptyString('profit_loss');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

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
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['market_id'], 'Markets'), ['errorField' => 'market_id']);
        $rules->add($rules->existsIn(['account_id'], 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }
}
