<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use App\Services\Client\ClientDashboardService;
use App\Services\Client\ProfileService;
use App\Services\Client\VocabularyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ClientDashboardService $dashboardService,
        private readonly ProfileService $profileService,
        private readonly VocabularyService $vocabularyService,
        private readonly AiAssistantService $aiAssistantService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        return view('client.dashboard.index', [
            'stats' => $this->dashboardService->getStats($user),
            'weeklyWpm' => $this->dashboardService->getWeeklyWpm($user),
            'recentActivity' => $this->dashboardService->getRecentActivity($user),
            'recommendedArticles' => $this->dashboardService->getRecommendedArticles($user),
            'resumeList' => $this->dashboardService->getResumeList($user),
            'categories' => $this->dashboardService->getCategoriesWithCounts(),
        ]);
    }

    public function profile(): View
    {
        return view('client.dashboard.profile');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
        ]);

        $this->profileService->updateProfile(auth()->user(), $data);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->profileService->updatePassword(auth()->user(), $request->input('password'));

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function vocabulary(Request $request): View
    {
        $user = auth()->user();

        return view('client.dashboard.vocabulary', [
            'vocabularies' => $this->vocabularyService->getVocabularies($user, $request->all()),
            'totalCount' => $this->vocabularyService->getCount($user),
            'isPro' => $user->isPro(),
        ]);
    }

    public function saveVocabulary(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'max:255'],
            'meaning' => ['nullable', 'string', 'max:500'],
            'article_id' => ['nullable', 'exists:articles,id'],
            'sentence_en' => ['nullable', 'string', 'max:2000'],
            'sentence_vi' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $vocabulary = $this->vocabularyService->saveVocabulary(auth()->user(), $data);
        } catch (RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
            }

            return back()->with('error', $exception->getMessage());
        }

        $isCreated = $vocabulary->wasRecentlyCreated;
        $message = $isCreated ? 'Đã lưu từ vựng!' : 'Từ vựng đã có trong sổ tay.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $isCreated ? 'created' : 'already_exists',
                'message' => $message,
                'vocabulary' => [
                    'id' => $vocabulary->id,
                    'word' => $vocabulary->word,
                    'meaning' => $vocabulary->meaning,
                ],
            ], $isCreated ? 201 : 200);
        }

        return back()->with('success', $message);
    }

    public function updateVocabularyMeaning(int $id, Request $request): JsonResponse
    {
        $data = $request->validate(['meaning' => ['required', 'string', 'max:500']]);
        $vocab = $this->vocabularyService->updateMeaning(auth()->user(), $id, $data['meaning']);

        return response()->json(['success' => true, 'meaning' => $vocab->meaning]);
    }

    public function translateVocabulary(int $id): JsonResponse
    {
        abort_unless(auth()->user()->isPro(), 403);

        $vocab = auth()->user()->userVocabularies()->findOrFail($id);

        try {
            $meaning = $this->aiAssistantService->translateWord(
                $vocab->word,
                $vocab->sentence_en,
            );
            $vocab = $this->vocabularyService->updateMeaning(auth()->user(), $id, $meaning);
        } catch (RuntimeException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'meaning' => $vocab->meaning,
        ]);
    }

    public function deleteVocabulary(int $id): RedirectResponse
    {
        $this->vocabularyService->deleteVocabulary(auth()->user(), $id);

        return back()->with('success', 'Đã xóa từ vựng.');
    }

    public function billing(): View
    {
        $user = auth()->user();

        return view('client.dashboard.billing', [
            'user' => $user,
            'transactions' => $user->transactions()->with('plan')->latest()->paginate(10),
        ]);
    }
}
