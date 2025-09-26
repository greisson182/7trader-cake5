<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Cache\Cache;


class PostsTable extends Table
{

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('posts');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Categories', [
            'foreignKey' => 'categorie_id',
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
            ->scalar('summary')
            ->allowEmptyString('summary');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

        $validator
            ->scalar('images')
            ->maxLength('images', 300)
            ->allowEmptyFile('images');

        $validator
            ->scalar('identificator')
            ->maxLength('identificator', 255)
            ->allowEmptyString('identificator');

        $validator
            ->scalar('autor')
            ->maxLength('autor', 255)
            ->allowEmptyString('autor');

        $validator
            ->integer('creator')
            ->allowEmptyString('creator');

        $validator
            ->integer('view')
            ->allowEmptyString('view');

        $validator
            ->integer('order')
            ->allowEmptyString('order');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        $validator
            ->integer('categorie_id')
            ->allowEmptyString('categorie_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('categorie_id', 'Categories'), ['errorField' => 'categorie_id']);

        return $rules;
    }

    public function getPostBySlug($slug)
    {					
        $post = Cache::read('post_'.$slug);

        if ($post === false) {
			
			$slug2 = $slug;
			
			$slug2 = explode("--",$slug2);
	
			if (sizeof($slug2) > 1) {
				$conditions = ['Posts.slug'=> $slug2[0],'Posts.id'=> $slug2[1]];
			} else {
				$conditions = ['Posts.slug'=> $slug];
			} 
			
            $post = $this->find()
                ->where($conditions)
                ->first();

            Cache::write('post'.$slug, $post, 'short');
        }
        
        return $post;
    }
	
    function beforeFind( $event, $query, $options, $primary) {

        $query->where(function ($exp, $q) {
            return $exp->in('Posts.status', ['a', 'i']);
        });

    }
}
