<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Services\ArticleContentService;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly ArticleContentService $articleContentService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['nullable', 'in:draft,published'],
        ]);

        $articles = $this->articleService->getArticles($filters);
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('admin.articles.index', compact('articles', 'filters', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $contentJson = $this->articleService->normalizeContent($data['content'] ?? []);

        if (! $this->articleContentService->validateHasSentence($contentJson)) {
            throw ValidationException::withMessages([
                'content' => 'Cần ít nhất một câu song ngữ hợp lệ.',
            ]);
        }

        unset($data['content']);
        $data['content_json'] = $contentJson;
        $data['is_premium'] = $request->boolean('is_premium');

        $this->articleService->createArticle($data, $request->file('image'));

        return redirect()->route('admin.articles.index')
            ->with('success', 'Bài viết đã được tạo thành công.');
    }

    public function edit(Article $article): View
    {
        $article->load('categories');
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $contentJson = $this->articleService->normalizeContent($data['content'] ?? []);

        if (! $this->articleContentService->validateHasSentence($contentJson)) {
            throw ValidationException::withMessages([
                'content' => 'Cần ít nhất một câu song ngữ hợp lệ.',
            ]);
        }

        unset($data['content']);
        $data['content_json'] = $contentJson;
        $data['is_premium'] = $request->boolean('is_premium');

        $this->articleService->updateArticle(
            $article,
            $data,
            $request->file('image'),
            $request->boolean('remove_image'),
        );

        return redirect()->route('admin.articles.index')
            ->with('success', 'Bài viết đã được cập nhật.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->articleService->deleteArticle($article);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Bài viết đã được xóa.');
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_premium' => ['nullable'],
            'status' => ['required', 'in:draft,published'],
            'content' => ['required', 'array', 'min:1'],
            'content.*.type' => ['required', 'in:heading,sentence'],
            'content.*.level' => ['nullable', 'integer', 'between:1,3'],
            'content.*.text' => ['nullable', 'string'],
            'content.*.en' => ['nullable', 'string'],
            'content.*.vi' => ['nullable', 'string'],
        ];
    }
}
