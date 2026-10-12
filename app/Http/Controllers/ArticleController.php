<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::withCount('activeProducts')
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('article', compact('articles'));
    }

    public function show(Article $article)
    {
        $products = $article->activeProducts()->latest()->get();

        return view('article_show', compact('article', 'products'));
    }
}