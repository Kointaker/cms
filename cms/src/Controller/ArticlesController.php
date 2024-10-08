<?php 
// src/Controller/ArticlesController.php

namespace App\Controller;

use App\Controller\AppController;

class ArticlesController extends AppController 
{
    public function index()
    {
        $this->Authorization->skipAuthorization();
        $articles = $this->paginate($this->Articles);
        $this->set(compact('articles'));
        
    }
    // Add to existing src/Controller/ArticlesController.php file

    public function view($slug)
    {
        $this->Authorization->skipAuthorization();
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);
        
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // Hardcoding the user_id is temporary, and will be removed later
            // when we build authentication out.
            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $this->set('article', $article);
    }

    public function edit($slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags') // load associated Tags
            ->firstOrFail();
        $this->Authorization->authorize($article);

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                // Added: Disable modification of user_id
                'accessibleFields' => ['user_id' => false]
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }
        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list')->all();
        
        // Set tags to the view context
        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->Authorization->authorize($article);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
        
            return $this->redirect(['action' => 'index']);
        }
    }

    public function tags(array $tags = [])
    {
        $this->Authorization->skipAuthorization();
        // the 'pass' key is provided by CakePHP and contains all 
        // the passed URL path segments in the request.
        
        
        $tags = $this->request->getParam('pass');
        $articles = $this->Articles->find('tagged', tags: $tags)
            ->all();
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
}

