<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('articles')
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->each(function (object $article) {
                DB::table('article_category')->insertOrIgnore([
                    'article_id' => $article->id,
                    'category_id' => $article->category_id,
                ]);
            });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['status', 'category_id']);
            $table->dropConstrainedForeignId('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index(['status', 'category_id']);
        });

        DB::table('article_category')
            ->select('article_id', DB::raw('MIN(category_id) as category_id'))
            ->groupBy('article_id')
            ->orderBy('article_id')
            ->each(function (object $row) {
                DB::table('articles')
                    ->where('id', $row->article_id)
                    ->update(['category_id' => $row->category_id]);
            });

        Schema::dropIfExists('article_category');
    }
};
