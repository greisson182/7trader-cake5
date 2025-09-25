<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PermissionsProfiles Model
 *
 * @property \App\Model\Table\PermissionsTable&\Cake\ORM\Association\BelongsTo $Permissions
 * @property \App\Model\Table\ProfilesTable&\Cake\ORM\Association\BelongsTo $Profiles
 *
 * @method \App\Model\Entity\PermissionsProfile newEmptyEntity()
 * @method \App\Model\Entity\PermissionsProfile newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PermissionsProfile> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PermissionsProfile get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PermissionsProfile findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PermissionsProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PermissionsProfile> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PermissionsProfile|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PermissionsProfile saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PermissionsProfile>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PermissionsProfile>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PermissionsProfile>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PermissionsProfile> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PermissionsProfile>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PermissionsProfile>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PermissionsProfile>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PermissionsProfile> deleteManyOrFail(iterable $entities, array $options = [])
 */
class PermissionsProfilesTable extends Table
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

        $this->setTable('permissions_profiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Permissions', [
            'foreignKey' => 'permission_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Profiles', [
            'foreignKey' => 'profile_id',
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
            ->integer('permission_id')
            ->notEmptyString('permission_id');

        $validator
            ->integer('profile_id')
            ->notEmptyString('profile_id');

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
        $rules->add($rules->existsIn(['permission_id'], 'Permissions'), ['errorField' => 'permission_id']);
        $rules->add($rules->existsIn(['profile_id'], 'Profiles'), ['errorField' => 'profile_id']);

        return $rules;
    }
}
