<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('settings');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('maintenance')
            ->notEmptyString('maintenance');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmptyString('title');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        $validator
            ->scalar('image')
            ->maxLength('image', 255)
            ->allowEmptyFile('image');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('face_app_id')
            ->maxLength('face_app_id', 255)
            ->allowEmptyString('face_app_id');

        $validator
            ->scalar('face_author')
            ->maxLength('face_author', 255)
            ->allowEmptyString('face_author');

        $validator
            ->scalar('face_publisher')
            ->maxLength('face_publisher', 255)
            ->allowEmptyString('face_publisher');

        $validator
            ->scalar('google_publisher')
            ->maxLength('google_publisher', 255)
            ->allowEmptyString('google_publisher');

        $validator
            ->scalar('google_author')
            ->maxLength('google_author', 255)
            ->allowEmptyString('google_author');

        $validator
            ->scalar('aws_api')
            ->maxLength('aws_api', 255)
            ->allowEmptyString('aws_api');

        $validator
            ->scalar('mp_access_token')
            ->maxLength('mp_access_token', 255)
            ->allowEmptyString('mp_access_token');

        $validator
            ->scalar('mp_public_key')
            ->maxLength('mp_public_key', 255)
            ->allowEmptyString('mp_public_key');

        $validator
            ->scalar('mp_webhook_secret')
            ->maxLength('mp_webhook_secret', 255)
            ->allowEmptyString('mp_webhook_secret');

        $validator
            ->boolean('mp_sandbox')
            ->notEmptyString('mp_sandbox');

        return $validator;
    }
}
