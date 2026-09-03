<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryArticleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Daily Life', 'slug' => 'daily-life', 'description' => 'Cuộc sống hàng ngày'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Kinh doanh & kinh tế'],
            ['name' => 'Travel', 'slug' => 'travel', 'description' => 'Du lịch & khám phá'],
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Công nghệ & đổi mới'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['status' => 'active']));
        }

        $dailyLife = Category::where('slug', 'daily-life')->first();
        $business = Category::where('slug', 'business')->first();
        $travel = Category::where('slug', 'travel')->first();

        $articles = [
            [
                'category_ids' => array_filter([$business?->id]),
                'title' => 'The Growing Economy',
                'excerpt' => 'Một bài ngắn về tăng trưởng kinh tế và lạm phát.',
                'content_json' => [
                    ['en' => 'The economy is growing.', 'vi' => 'Nền kinh tế đang tăng trưởng.'],
                    ['en' => 'However, inflation remains a risk.', 'vi' => 'Tuy nhiên, lạm phát vẫn là một rủi ro.'],
                    ['en' => 'Many families feel the pressure of rising prices.', 'vi' => 'Nhiều gia đình cảm thấy áp lực từ giá cả leo thang.'],
                    ['en' => 'Governments must balance growth and stability.', 'vi' => 'Chính phủ phải cân bằng giữa tăng trưởng và ổn định.'],
                ],
                'is_premium' => false,
                'status' => 'published',
            ],
            [
                'category_ids' => array_filter([$dailyLife?->id]),
                'title' => 'Morning Routine',
                'excerpt' => 'Thói quen buổi sáng giúp bạn khởi đầu ngày mới hiệu quả.',
                'content_json' => [
                    ['en' => 'I wake up at six every morning.', 'vi' => 'Tôi thức dậy lúc sáu giờ mỗi sáng.'],
                    ['en' => 'First, I drink a glass of water.', 'vi' => 'Đầu tiên, tôi uống một cốc nước.'],
                    ['en' => 'Then I spend ten minutes stretching.', 'vi' => 'Sau đó tôi dành mười phút để giãn cơ.'],
                    ['en' => 'A good routine sets the tone for the day.', 'vi' => 'Một thói quen tốt tạo nền tảng cho cả ngày.'],
                ],
                'is_premium' => false,
                'status' => 'published',
            ],
            [
                'category_ids' => array_filter([$travel?->id]),
                'title' => 'Exploring New Cities',
                'excerpt' => 'Trải nghiệm khám phá thành phố mới.',
                'content_json' => [
                    ['en' => 'Travel opens your mind to new cultures.', 'vi' => 'Du lịch mở rộng tâm trí bạn với những nền văn hóa mới.'],
                    ['en' => 'Walking through old streets feels magical.', 'vi' => 'Đi bộ qua những con phố cổ thật kỳ diệu.'],
                    ['en' => 'Local food tells stories words cannot.', 'vi' => 'Ẩm thực địa phương kể những câu chuyện mà lời nói không thể.'],
                ],
                'is_premium' => true,
                'status' => 'published',
            ],
            [
                'category_ids' => array_filter([$business?->id, $dailyLife?->id]),
                'title' => 'Remote Work Today',
                'excerpt' => 'Làm việc từ xa — xu hướng và thách thức.',
                'content_json' => [
                    ['en' => 'Remote work offers flexibility and comfort.', 'vi' => 'Làm việc từ xa mang lại sự linh hoạt và thoải mái.'],
                    ['en' => 'However, it can blur work-life boundaries.', 'vi' => 'Tuy nhiên, nó có thể làm mờ ranh giới giữa công việc và cuộc sống.'],
                ],
                'is_premium' => true,
                'status' => 'draft',
            ],
        ];

        foreach ($articles as $articleData) {
            $categoryIds = $articleData['category_ids'] ?? [];
            unset($articleData['category_ids']);

            $article = Article::updateOrCreate(
                ['title' => $articleData['title']],
                $articleData,
            );

            if ($categoryIds !== []) {
                $article->categories()->sync($categoryIds);
            }
        }
    }
}
