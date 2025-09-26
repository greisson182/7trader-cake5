<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OperationsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('operations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Studies', [
            'foreignKey' => 'study_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('study_id')
            ->allowEmptyString('study_id');

        $validator
            ->scalar('asset')
            ->allowEmptyString('asset');

        $validator
            ->dateTime('open_time')
            ->allowEmptyDateTime('open_time');

        $validator
            ->dateTime('close_time')
            ->allowEmptyDateTime('close_time');

        $validator
            ->scalar('trade_duration')
            ->allowEmptyString('trade_duration');

        $validator
            ->decimal('buy_quantity')
            ->allowEmptyString('buy_quantity');

        $validator
            ->decimal('sell_quantity')
            ->allowEmptyString('sell_quantity');

        $validator
            ->scalar('side')
            ->allowEmptyString('side');

        $validator
            ->decimal('buy_price')
            ->allowEmptyString('buy_price');

        $validator
            ->decimal('sell_price')
            ->allowEmptyString('sell_price');

        $validator
            ->decimal('market_price')
            ->allowEmptyString('market_price');

        $validator
            ->decimal('mep')
            ->allowEmptyString('mep');

        $validator
            ->decimal('men')
            ->allowEmptyString('men');

        $validator
            ->scalar('buy_agent')
            ->allowEmptyString('buy_agent');

        $validator
            ->scalar('sell_agent')
            ->allowEmptyString('sell_agent');

        $validator
            ->scalar('average_price')
            ->allowEmptyString('average_price');

        $validator
            ->decimal('gross_interval_result')
            ->allowEmptyString('gross_interval_result');

        $validator
            ->decimal('interval_result_percent')
            ->allowEmptyString('interval_result_percent');

        $validator
            ->scalar('operation_number')
            ->allowEmptyString('operation_number');

        $validator
            ->decimal('operation_result')
            ->allowEmptyString('operation_result');

        $validator
            ->decimal('operation_result_percent')
            ->allowEmptyString('operation_result_percent');

        $validator
            ->decimal('drawdown')
            ->allowEmptyString('drawdown');

        $validator
            ->decimal('max_gain')
            ->allowEmptyString('max_gain');

        $validator
            ->decimal('max_loss')
            ->allowEmptyString('max_loss');

        $validator
            ->scalar('tet')
            ->allowEmptyString('tet');

        $validator
            ->decimal('total')
            ->allowEmptyString('total');

        $validator
            ->scalar('conta')
            ->allowEmptyString('conta');

        $validator
            ->scalar('titular')
            ->allowEmptyString('titular');

        $validator
            ->date('data_inicial')
            ->allowEmptyDate('data_inicial');

        $validator
            ->date('data_final')
            ->allowEmptyDate('data_final');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['study_id'], 'Studies'), ['errorField' => 'study_id']);

        return $rules;
    }
}