<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PermissionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('permissions');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Profiles', [
            'foreignKey' => 'permission_id',
            'targetForeignKey' => 'profile_id',
            'joinTable' => 'permissions_profiles',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->integer('parent')
            ->allowEmptyString('parent');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        return $validator;
    }
}
