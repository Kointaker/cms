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
}