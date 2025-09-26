<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PagesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Models', [
            'foreignKey' => 'model_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmptyString('title');

        $validator
            ->scalar('subtitle')
            ->maxLength('subtitle', 255)
            ->allowEmptyString('subtitle');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 255)
            ->allowEmptyString('slug');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

        $validator
            ->scalar('summary')
            ->allowEmptyString('summary');

        $validator
            ->scalar('images')
            ->maxLength('images', 300)
            ->allowEmptyFile('images');

        $validator
            ->integer('creator')
            ->allowEmptyString('creator');

        $validator
            ->integer('link')
            ->allowEmptyString('link');

        $validator
            ->integer('order')
            ->allowEmptyString('order');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        $validator
            ->integer('model_id')
            ->allowEmptyString('model_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('model_id', 'Models'), ['errorField' => 'model_id']);

        return $rules;
    }

    public function getPaginaBySlug($url)
    {
        $query = $this->find()
            ->where([
                'Pages.slug' => $url
            ]);
        return $query->first();
    }

    public function getPaginaById($id)
    {
        $query = $this->find()
            ->where([
                'Pages.id' => $id
            ]);
        return $query->first();
    }

    public function getMenu()
    {
        return $this->find()
            ->select(['title', 'slug', 'link'])
            ->order(['Pages.order' => 'asc'])
            ->all();
    }

    function beforeFind($event, $query, $options, $primary)
    {

        $query->where(function ($exp, $q) {
            return $exp->in('Pages.status', ['a', 'i']);
        });
    }
}
