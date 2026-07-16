<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MentorshipVideosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mentorship_videos');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Mentorships', [
            'foreignKey' => 'mentorship_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MentorshipInstances', [
            'foreignKey' => 'instance_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('MentorshipModules', [
            'foreignKey' => 'module_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('mentorship_id')
            ->notEmptyString('mentorship_id');

        $validator
            ->integer('instance_id')
            ->allowEmptyString('instance_id');

        $validator
            ->integer('module_id')
            ->allowEmptyString('module_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255, 'O título deve ter no máximo 255 caracteres.')
            ->requirePresence('title', 'create', 'O título é obrigatório.')
            ->notEmptyString('title', 'O título é obrigatório.');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('video_url')
            ->maxLength('video_url', 500, 'A URL deve ter no máximo 500 caracteres.')
            ->requirePresence('video_url', 'create', 'A URL do vídeo é obrigatória.')
            ->notEmptyString('video_url', 'A URL do vídeo é obrigatória.');

        $validator
            ->scalar('video_type')
            ->allowEmptyString('video_type');

        $validator
            ->integer('duration_seconds')
            ->allowEmptyString('duration_seconds');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        $validator
            ->boolean('is_preview')
            ->allowEmptyString('is_preview');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['mentorship_id'], 'Mentorships'), ['errorField' => 'mentorship_id']);
        $rules->add($rules->existsIn(['instance_id'], 'MentorshipInstances'), ['errorField' => 'instance_id', 'allowNullableNulls' => true]);
        $rules->add($rules->existsIn(['module_id'], 'MentorshipModules'), ['errorField' => 'module_id', 'allowNullableNulls' => true]);

        return $rules;
    }
}
