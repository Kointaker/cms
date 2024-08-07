<?php
// src/Model/Table/ArticlesTable.php
namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
// the Text class
use Cake\Utility\Text;
// the EventInterface class
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

class ArticlesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        $this->belongsTomany('Tags');
    }
    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            // trim slug to maximum length defined in schema
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title')
            ->minLength('title', 10)
            ->maxLength('title', 255)

            ->notEmptyString('body')
            ->minLength('body', 10);
        return $validator;
        }

        public function findTagged(SelectQuery $query, array $tags = []) : SelectQuery
        {
            $columns = [
                'Articles.id', 'Articles.user_id', 'Articles.title',
                'Articles.body', 'Articles.published', 'Articles.created',
                'Articles.slug',
            ];
            $query = $query 
                ->select($columns)
                ->distinct($columns);

            if (empty($tags)) {
                // If there are no tags provided, find articles that have no tags.
                $query->leftJoinWith('Tags')
                    ->where(['Tags.title IS' => null]);
            } else {
                // Find articles that have one or more of the provided tags.
                $query->innerJoinWith('Tags')
                    ->where(['Tags.title IN' => $tags]);
            }
            return $query->groupBy(['Articles.id']);
        }
    
    }