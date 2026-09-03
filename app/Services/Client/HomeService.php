<?php

namespace App\Services\Client;

use App\Helpers\FormatHelper;
use App\Models\Article;
use App\Models\Plan;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class HomeService
{
    public function getHomePageData(): array
    {
        $featuredArticles = $this->getFeaturedArticlesFromDatabase();
        $activePlans = $this->getActivePlansFromDatabase();

        return [
            'seo' => [
                'title' => 'Dictation Lab — Chép chính tả song ngữ đa chủ đề',
                'description' => 'Học tiếng Anh qua chép chính tả song ngữ Anh-Việt: báo chí, truyện ngắn, TED Talks. Tăng phản xạ qua muscle memory, sổ từ vựng cá nhân.',
            ],
            'demo_video_url' => app(SettingService::class)->getDemoVideoUrl(),
            'hero' => [
                'badge' => 'Bilingual Dictation',
                'title_lines' => ['Chép chính tả song ngữ,', 'tăng phản xạ tiếng Anh', 'qua muscle memory.'],
                'description' => 'Gõ từng câu tiếng Anh theo nghĩa tiếng Việt — qua báo chí, truyện ngắn, TED Talks và hàng trăm chủ đề. Không cần AI chấm điểm, chỉ cần luyện đều mỗi ngày.',
                'trust_signals' => [
                    'Miễn phí trọn đời',
                    'Không cần thẻ tín dụng',
                    '1000+ bài song ngữ',
                ],
            ],
            'social_proof' => $this->getSocialProof(),
            'problem_section' => [
                'title' => 'Tại sao đọc thụ động không đủ?',
                'description' => 'Bạn đọc hàng chục bài nhưng vẫn không nhớ cấu trúc câu? Bạn biết nghĩa từng từ nhưng không gõ được cả câu? Dictation Lab giúp bạn biến kiến thức thành phản xạ thực sự.',
                'items' => [
                    [
                        'title' => 'Đọc mà không gõ',
                        'description' => 'Đọc qua loa, mắt quen nhưng tay không quen — khi cần viết/nói thì vẫn vướng.',
                        'bg_class' => 'bg-red-50',
                    ],
                    [
                        'title' => 'Thiếu ngữ cảnh song ngữ',
                        'description' => 'Học từ đơn lẻ, không gắn với câu hoàn chỉnh có nghĩa tiếng Việt tương ứng.',
                        'bg_class' => 'bg-yellow-50',
                    ],
                    [
                        'title' => 'Không theo dõi tiến độ',
                        'description' => 'Không biết mình đã chép bao nhiêu câu, tốc độ gõ cải thiện ra sao.',
                        'bg_class' => 'bg-blue-50',
                    ],
                ],
            ],
            'features' => [
                [
                    'title' => 'Chép chính tả song ngữ',
                    'badge' => 'Miễn phí',
                    'description' => 'Nhìn nghĩa tiếng Việt, gõ lại câu tiếng Anh từng ký tự — phản hồi đúng/sai tức thì, ghi nhớ qua muscle memory.',
                    'highlights' => [
                        'Gõ từng câu, không bị ngợp',
                        'Progress bar theo số câu hoàn thành',
                        'Thống kê WPM & Accuracy sau mỗi bài',
                    ],
                ],
                [
                    'title' => 'Đa chủ đề',
                    'badge' => 'Miễn phí',
                    'description' => 'Báo chí, kinh tế, du lịch, công nghệ, truyện ngắn… Chọn chủ đề bạn thích để học lâu dài.',
                    'highlights' => [
                        'Phân loại theo category',
                        'Bài Free và Pro',
                        'Nội dung cập nhật thường xuyên',
                    ],
                ],
                [
                    'title' => 'Sổ từ vựng cá nhân',
                    'badge' => 'PRO',
                    'description' => 'Bôi đen từ hay trong câu đã chép, lưu vào sổ tay kèm ngữ cảnh song ngữ. Pro có AI dịch từ vựng.',
                    'highlights' => [
                        'Highlight-to-save trực tiếp khi chép',
                        'Tự ghi nghĩa theo cách hiểu của bạn',
                        'AI dịch từ vựng (Pro)',
                    ],
                ],
            ],
            'how_it_works' => [
                [
                    'title' => 'Chọn bài viết',
                    'description' => 'Duyệt thư viện theo chủ đề — Daily Life, Business, Travel, Technology…',
                ],
                [
                    'title' => 'Chép từng câu',
                    'description' => 'Đọc nghĩa tiếng Việt, gõ lại câu tiếng Anh. Sai thì sửa, đúng thì sang câu tiếp.',
                ],
                [
                    'title' => 'Lưu từ & theo dõi',
                    'description' => 'Bôi đen từ hay, lưu sổ tay. Xem WPM, accuracy và tiến độ trên dashboard.',
                ],
            ],
            'featured_articles' => $featuredArticles->map(function (Article $article): array {
                return [
                    'title' => $article->title,
                    'category' => $article->categories->pluck('name')->join(', '),
                    'excerpt' => $article->excerpt,
                    'sentence_count' => $article->sentenceCount(),
                    'is_premium' => (bool) $article->is_premium,
                ];
            })->values()->all(),
            'pricing' => [
                'free' => [
                    'name' => 'Free',
                    'price_label' => '0đ',
                    'duration_label' => '/mãi mãi',
                    'features' => [
                        ['label' => 'Chép chính tả 3 bài/ngày', 'included' => true],
                        ['label' => 'Lưu tối đa 50 từ vựng', 'included' => true],
                        ['label' => 'Truy cập bài Free', 'included' => true],
                        ['label' => 'AI dịch từ vựng', 'included' => false],
                    ],
                ],
                'pro' => $activePlans->map(function (Plan $plan): array {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'price_label' => FormatHelper::money($plan->price),
                        'duration_label' => '/'.$plan->duration_days.' ngày',
                        'features' => [
                            '1000+ bài báo/truyện song ngữ',
                            'Chép chính tả không giới hạn',
                            'Lưu từ vựng không giới hạn',
                            'AI dịch từ vựng & giải thích câu',
                        ],
                    ];
                })->values()->all(),
            ],
            'testimonials' => [
                [
                    'name' => 'Thu Hương',
                    'role' => 'Sinh viên, TP.HCM',
                    'content' => 'Chép từng câu song ngữ giúp mình nhớ cấu trúc nhanh hơn đọc thụ động gấp nhiều lần. Sau 1 tháng gõ đều, mình tự tin viết email tiếng Anh hơn hẳn.',
                    'initial' => 'TH',
                    'avatar_bg' => 'bg-brand-light',
                    'avatar_text' => 'text-brand',
                ],
                [
                    'name' => 'Minh Khôi',
                    'role' => 'Kỹ sư IT, Hà Nội',
                    'content' => 'Mình thích học qua bài báo công nghệ. Nhìn câu tiếng Việt rồi gõ tiếng Anh — cảm giác như game, không chán.',
                    'initial' => 'MK',
                    'avatar_bg' => 'bg-blue-50',
                    'avatar_text' => 'text-semantic-blue',
                ],
                [
                    'name' => 'Lan Anh',
                    'role' => 'Giáo viên, Đà Nẵng',
                    'content' => 'Sổ từ vựng highlight-to-save rất hay. Học sinh tự lưu từ trong ngữ cảnh, nhớ lâu hơn flashcard.',
                    'initial' => 'LA',
                    'avatar_bg' => 'bg-purple-50',
                    'avatar_text' => 'text-semantic-purple',
                ],
            ],
            'faqs' => [
                [
                    'question' => 'Tôi có thể dùng miễn phí mãi mãi không?',
                    'answer' => 'Có! Gói Free cho phép chép 3 bài/ngày, lưu tối đa 50 từ vựng và truy cập các bài Free. Không cần thẻ tín dụng.',
                ],
                [
                    'question' => 'Dictation Lab khác gì app chép chính tả thông thường?',
                    'answer' => 'Mỗi câu có nghĩa tiếng Việt kèm theo, bạn gõ tiếng Anh theo nghĩa — học song ngữ chủ động thay vì chỉ nghe audio.',
                ],
                [
                    'question' => 'Tôi có thể dùng trên điện thoại không?',
                    'answer' => 'Chép chính tả hoạt động tốt nhất trên máy tính (bàn phím vật lý). Dashboard và sổ từ vựng dùng được trên mobile.',
                ],
                [
                    'question' => 'Thanh toán bằng phương thức nào?',
                    'answer' => 'Hỗ trợ chuyển khoản ngân hàng qua SePay. Thanh toán an toàn, kích hoạt Pro tự động sau khi xác nhận.',
                ],
                [
                    'question' => 'Pro có những gì?',
                    'answer' => 'Truy cập toàn bộ bài Premium, chép không giới hạn, lưu từ vựng không giới hạn, AI dịch từ và giải thích ngữ pháp câu.',
                ],
            ],
            'final_cta' => [
                'title_line1' => 'Sẵn sàng tăng phản xạ',
                'title_line2' => 'tiếng Anh mỗi ngày?',
                'description' => 'Đăng ký miễn phí. Bắt đầu chép câu đầu tiên trong 30 giây — không cần thẻ tín dụng.',
            ],
            'about_page' => [
                'application_intro' => [
                    'title' => 'Nhóm 18 - Bất Cần Đời thực hiện sản phẩm cuối môn cho học phần Thiết kế và PTHTTM MOOCS.',
                    'description' => 'Dictation Lab là nền tảng chép chính tả song ngữ Anh-Việt đa chủ đề, giúp người học tăng phản xạ tiếng Anh qua muscle memory.',
                    'highlights' => [
                        'Founder: Nguyễn Lê Khánh Linh',
                        'Co-Founder: Đỗ Ngọc Hà',
                        'Môn: Thiết kế và PTHTTM MOOCS',
                    ],
                ],
                'why_choose' => [
                    [
                        'title' => 'Học qua ngữ cảnh thật',
                        'description' => 'Bài báo, truyện ngắn, TED Talks — không phải câu ví dụ nhàm chán.',
                    ],
                    [
                        'title' => 'Song ngữ có chủ đích',
                        'description' => 'Mỗi câu có nghĩa tiếng Việt, bạn gõ tiếng Anh — liên kết hai ngôn ngữ trong não.',
                    ],
                    [
                        'title' => 'Theo dõi tiến độ rõ ràng',
                        'description' => 'Số câu đã chép, WPM, accuracy — thấy mình tiến bộ từng ngày.',
                    ],
                ],
                'founder' => [
                    'team_name' => 'Nhóm 18 - Bất Cần Đời',
                    'name' => 'Nguyễn Lê Khánh Linh',
                    'role' => 'Founder',
                    'co_founder' => 'Đỗ Ngọc Hà',
                    'course' => 'Thiết kế và PTHTTM MOOCS',
                    'project_type' => 'Sản phẩm cuối môn',
                    'quote' => 'Dictation Lab ra đời từ niềm tin rằng học tiếng Anh hiệu quả nhất là gõ lại những câu có nghĩa — không phải đọc thụ động hay thi thử liên tục.',
                    'story' => [
                        'Nhóm thực hiện: Nhóm 18 - Bất Cần Đời.',
                        'Founder: Nguyễn Lê Khánh Linh.',
                        'Co-Founder: Đỗ Ngọc Hà.',
                        'Môn học: Thiết kế và PTHTTM MOOCS.',
                    ],
                ],
            ],
            'feature_experience' => [
                [
                    'title' => 'Chép chính tả song ngữ',
                    'description' => 'Gõ từng câu tiếng Anh theo nghĩa tiếng Việt, với phản hồi đúng/sai tức thì.',
                    'experience' => [
                        'Highlight lỗi ngay khi gõ sai.',
                        'Theo dõi WPM, Accuracy theo từng bài.',
                    ],
                ],
                [
                    'title' => 'Thư viện đa chủ đề',
                    'description' => 'Bài viết phân loại theo category — chọn chủ đề bạn quan tâm.',
                    'experience' => [
                        'Daily Life, Business, Travel, Technology…',
                        'Bài Free và Pro.',
                    ],
                ],
                [
                    'title' => 'Sổ từ vựng',
                    'description' => 'Bôi đen từ hay khi chép, lưu kèm câu ngữ cảnh song ngữ.',
                    'experience' => [
                        'Tự ghi nghĩa theo cách hiểu của bạn.',
                        'AI dịch từ (Pro).',
                    ],
                ],
                [
                    'title' => 'Giải thích câu (Pro)',
                    'description' => 'AI phân tích ngữ pháp câu vừa chép xong — hiểu sâu hơn, không chỉ gõ máy.',
                    'experience' => [
                        'Giải thích ngắn gọn bằng tiếng Việt.',
                        'Chỉ dành cho tài khoản Pro.',
                    ],
                ],
            ],
            'plan_fit' => [
                [
                    'plan' => 'Free',
                    'fit_for' => 'Người mới muốn thử phương pháp chép chính tả song ngữ.',
                    'best_when' => '3 bài/ngày, 50 từ vựng — đủ để làm quen hệ thống.',
                ],
                [
                    'plan' => 'Pro',
                    'fit_for' => 'Người học nghiêm túc, muốn chép không giới hạn và dùng AI.',
                    'best_when' => 'Bạn cần truy cập toàn bộ bài Premium và sổ từ vựng không giới hạn.',
                ],
            ],
            'contact' => [
                'hotline' => '0868061598',
                'email' => 'thoer197765@gmail.com',
                'address' => 'Nhóm 18 - Bất Cần Đời',
                'map_embed_url' => '',
                'facebook_url' => 'https://www.facebook.com/share/14d8fGTUimW/?mibextid=wwXIfr',
                'zalo_url' => '',
                'support_hours' => 'Sản phẩm cuối môn - Thiết kế và PTHTTM MOOCS',
                'team_name' => 'Nhóm 18 - Bất Cần Đời',
                'founder' => 'Nguyễn Lê Khánh Linh',
                'co_founder' => 'Đỗ Ngọc Hà',
                'course' => 'Thiết kế và PTHTTM MOOCS',
                'project_type' => 'Sản phẩm cuối môn',
            ],
        ];
    }

    private function getSocialProof(): array
    {
        $userCount = 0;
        $publishedArticles = 0;

        try {
            $userCount = User::query()->where('role', 'user')->count();
            $publishedArticles = Article::query()->where('status', 'published')->count();
        } catch (QueryException) {
        }

        return [
            [
                'value' => number_format($userCount).'+',
                'label' => 'Học viên đang luyện tập',
            ],
            [
                'value' => number_format($publishedArticles).'+',
                'label' => 'Bài viết đã xuất bản',
            ],
            [
                'value' => '1000+',
                'label' => 'Câu song ngữ',
            ],
            [
                'value' => '4.8/5',
                'label' => 'Đánh giá trung bình',
            ],
        ];
    }

    private function getFeaturedArticlesFromDatabase(): Collection
    {
        try {
            return Article::query()
                ->with('categories:id,name')
                ->where('status', 'published')
                ->latest()
                ->take(3)
                ->get(['id', 'title', 'excerpt', 'content_json', 'is_premium']);
        } catch (QueryException) {
            return collect();
        }
    }

    private function getActivePlansFromDatabase(): Collection
    {
        try {
            return Plan::query()
                ->where('is_active', true)
                ->orderBy('price')
                ->get(['id', 'name', 'duration_days', 'price']);
        } catch (QueryException) {
            return collect();
        }
    }
}
