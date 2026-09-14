<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuickStoreCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $categories = $this->categoryService->getCategories($filters, 15);

        return view('admin.categories.index', compact('categories', 'filters'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $category = $this->categoryService->createCategory($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'message' => 'Chủ đề đã được tạo thành công.',
            ], 201);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Chủ đề đã được tạo thành công.');
    }

    public function quickStore(QuickStoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->quickCreateCategory(
            $request->validated('name'),
            $request->validated('slug'),
        );

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'message' => "Danh mục \"{$category->name}\" đã được tạo thành công.",
        ], 201);
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $this->categoryService->updateCategory($category, $data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Chủ đề đã được cập nhật.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Chủ đề đã được xóa.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,'.($category?->id ?? 'NULL')],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug danh mục này đã tồn tại.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);
    }
}
