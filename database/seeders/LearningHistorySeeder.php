<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningHistorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $clients = User::query()->where('role', 'user')->orderBy('id')->get();
        $articles = Article::query()->orderBy('id')->get();

        DB::table('dictation_histories')->truncate();
        DB::table('user_vocabularies')->truncate();

        foreach ($clients as $index => $client) {
            foreach ($articles->where('status', 'published')->take(3) as $lessonIndex => $article) {
                $sentenceCount = $article->sentenceCount();

                DB::table('dictation_histories')->insert([
                    'user_id' => $client->id,
                    'article_id' => $article->id,
                    'wpm' => 32 + (($index + $lessonIndex) % 45),
                    'accuracy' => 78 + (($index + $lessonIndex) % 20),
                    'completed_sentences' => $sentenceCount,
                    'completed_at' => now()->subDays(rand(1, 40)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $firstArticle = $articles->first();
            if ($firstArticle) {
                $firstSentence = $firstArticle->sentences()[0] ?? null;

                DB::table('user_vocabularies')->insert([
                    'user_id' => $client->id,
                    'article_id' => $firstArticle->id,
                    'word' => 'economy-'.$client->id,
                    'meaning' => null,
                    'sentence_en' => $firstSentence['en'] ?? 'The economy is growing.',
                    'sentence_vi' => $firstSentence['vi'] ?? 'Nền kinh tế đang tăng trưởng.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
