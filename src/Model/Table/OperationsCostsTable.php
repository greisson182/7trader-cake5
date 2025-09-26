<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;

class OperationsCostsTable extends Table
{

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('operations_costs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Markets', [
            'foreignKey' => 'market_id',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'account_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('cost_per_contract')
            ->allowEmptyString('cost_per_contract');

        $validator
            ->dateTime('date_start')
            ->allowEmptyDateTime('date_start');

        $validator
            ->dateTime('date_end')
            ->allowEmptyDateTime('date_end');

        $validator
            ->integer('market_id')
            ->allowEmptyString('market_id');

        $validator
            ->integer('student_id')
            ->allowEmptyString('student_id');

        $validator
            ->integer('account_id')
            ->allowEmptyString('account_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['market_id'], 'Markets'), ['errorField' => 'market_id']);
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['account_id'], 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }

    public function getCostForOperation($studentId, $marketId, $accountId, $operationDate)
    {
        
        $cost = $this->find()
            ->where([
                'student_id' => $studentId,
                'market_id' => $marketId,
                'account_id' => $accountId,
                'or' => [
                    'date_start <=' => $operationDate,
                    'date_end IS NULL',
                ]
            ])
            ->orderByDesc('date_start')
            ->first();

        return $cost ? $cost->cost_per_contract * 2 : null;
    }

    public function calculateOperationCost($studentId, $marketId, $accountId, $operationDate, $contracts = 1)
    {
        $costPerContract = $this->getCostForOperation($studentId, $marketId, $accountId, $operationDate);
        
        if ($costPerContract === null) {
            return 0.0;
        }
        
        return -abs($costPerContract * $contracts);
    }
}
