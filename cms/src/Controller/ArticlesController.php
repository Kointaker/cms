<?php 
// src/Controller/ArticlesController.php

namespace App\Controller;

class articlesController extends AppController 
{
    public function index()
    {
        $articles = $this->paginate($this->Articles);
        $this->set(compact('articles'));
    }
    // Add to existing src/Controller/ArticlesController.php file

    public function view($slug = null)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }

}

