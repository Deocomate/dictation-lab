<?php

namespace App\Http\Middleware;

use App\Models\DictationHistory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDailyDictationLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isPro()) {
            return $next($request);
        }

        $completedToday = DictationHistory::query()
            ->where('user_id', $user->id)
            ->whereDate('completed_at', today())
            ->distinct('article_id')
            ->count('article_id');

        if ($completedToday >= 3) {
            return redirect()
                ->route('client.checkout')
                ->with('error', 'Bạn đã hoàn thành 3 bài chép chính tả hôm nay. Nâng cấp Pro để chép không giới hạn.');
        }

        return $next($request);
    }
}
