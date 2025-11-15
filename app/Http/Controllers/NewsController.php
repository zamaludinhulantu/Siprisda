<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        abort_if($news->status !== 'published', 404);
        return view('news.show', compact('news'));
    }
}

