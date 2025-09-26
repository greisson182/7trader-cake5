<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class StudiesTable extends Table
{
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
        $this->hasMany('Operations', [
            'foreignKey' => 'study_id',
        ]);
    }

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

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['student_id'], 'Students'), ['errorField' => 'student_id']);
        $rules->add($rules->existsIn(['market_id'], 'Markets'), ['errorField' => 'market_id']);
        $rules->add($rules->existsIn(['account_id'], 'Accounts'), ['errorField' => 'account_id']);

        return $rules;
    }

    public function getCostTrades($study)
    {
        $this->Operations = TableRegistry::getTableLocator()->get('Operations');
        $this->OperationsCosts = TableRegistry::getTableLocator()->get('OperationsCosts');

        $operations = $this->Operations->find('all')->where([
            'study_id' => $study['id'],
            'market_id' => $study['market_id'],
        ])->toArray();

        $amount_cost = 0;
        if (count($operations) > 0) {
            foreach ($operations as &$operation) {
                $amount_cost += $this->OperationsCosts->calculateOperationCost(
                    $study['student_id'],
                    $study['market_id'],
                    $study['account_id'],
                    $study['study_date'],
                    $operation['buy_quantity'],
                );
            }
        }

        if ($amount_cost > 0) {
            $amount_cost = $amount_cost + (float)$study['profit_loss'];
        }

        return $amount_cost;
    }
}
