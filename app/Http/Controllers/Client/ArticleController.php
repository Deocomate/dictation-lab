<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Client\ArticleLibraryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleLibraryService $articleLibraryService) {}

    public function library(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'access' => ['nullable', 'in:free,pro,premium'],
            'sort' => ['nullable', 'in:latest,title_asc,title_desc'],
        ]);

        if (($filters['access'] ?? null) === 'premium') {
            $filters['access'] = 'pro';
        }

        return view('client.learning.article-library', [
            'articles' => $this->articleLibraryService->getArticles($filters),
            'totalCount' => $this->articleLibraryService->getTotalCount(),
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }
}
