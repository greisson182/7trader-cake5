<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class FilesTable extends Table
{
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

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
