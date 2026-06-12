<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::published()->latest()->paginate(9);
        return view('pages.articles.index', compact('articles'));
    }

    public function show(string $slug): View
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();
        
        // Fetch recent articles for sidebar
        $recentArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(5)
            ->get();

        return view('pages.articles.show', compact('article', 'recentArticles'));
    }
}
