---
title: "Pivot IELTS Writing → Dictation Lab (song ngữ Anh-Việt)"
description: "Refactor toàn bộ sản phẩm từ nền tảng IELTS Writing (AI chấm điểm/mock exam) sang Dictation Lab chép chính tả song ngữ đa chủ đề."
status: completed
priority: P1
effort: ~5-7 ngày
branch: main
tags: [refactor, pivot, laravel, dictation, database-redesign]
created: 2026-06-29
---

# KẾ HOẠCH TRIỂN KHAI: Pivot sang "Dictation Lab"

> Tài liệu này đã được verify trực tiếp trên codebase (đọc file thực tế). Mọi khác biệt
> so với bản phác thảo ban đầu của chủ dự án được đánh dấu **[ĐÍNH CHÍNH]**.

---

## 0. CÁC ĐÍNH CHÍNH QUAN TRỌNG (khác với mô tả ban đầu — đọc trước khi làm)

1. **[ĐÍNH CHÍNH] KHÔNG CÓ Vite / npm / `package.json` / `resources/js`.**
   - Toàn bộ asset nạp qua **CDN**: Tailwind CDN (`https://cdn.tailwindcss.com`) + Alpine CDN
     (`https://cdn.jsdelivr.net/npm/alpinejs@3.x.x` — chỉ nạp trong `components/client/layout/app.blade.php`)
     + Chart.js CDN (chỉ trong dashboard).
   - **Typing engine hiện tại là ~150 dòng JS thuần inline ngay trong**
     `resources/views/client/learning/study-dictation.blade.php` (hàm `init/renderChars/handleKeydown/...`),
     KHÔNG phải module JS riêng.
   - **KHUYẾN NGHỊ:** giữ nguyên pattern "JS inline trong Blade" (đúng KISS, nhất quán codebase).
     **KHÔNG đưa Vite vào** — đó là scope creep cho một sản phẩm cuối môn. Yêu cầu "load qua Vite"
     trong brief **không áp dụng**; thay bằng "viết engine mới inline trong blade view".

2. **[ĐÍNH CHÍNH] Services nằm trong namespace con `App\Services\Client`.**
   - `MockExamService` và `AnalyzeService` **CÓ tồn tại** tại `app/Services/Client/` (không phải ở
     `app/Services/` gốc). `ReadingMaterialService` và `MiniExerciseService` nằm ở `app/Services/` gốc.

3. **[ĐÍNH CHÍNH] KHÔNG có `Admin/MockExamController`.** Chỉ có `Client/MockExamController`.

4. **[ĐÍNH CHÍNH] Toàn bộ schema chính nằm trong 1 file**
   `database/migrations/0001_01_01_000000_create_users_table.php` (tạo users, plans, transactions,
   lessons, lesson_annotations, lesson_vocabularies, dictation_histories, mock_exams, user_vocabularies
   + bảng hệ thống). File `2026_04_13_023716_init_database_tables.php` là no-op (bỏ qua).
   Các migration phụ: `add_status_to_mock_exams`, `add_indexes_to_lessons`, `create_settings`,
   `create_reading_materials`, `create_mini_exercises`, `create_ai_assistant_settings`,
   `add_payos_fields_to_transactions`.

5. **[ĐÍNH CHÍNH] CHƯA có cơ chế giới hạn lượt/ngày cho Free.** Câu "giới hạn lượt/ngày" hiện chỉ
   là **copywriting** trong `HomeService` + `pricing-section.blade.php`, chưa có middleware/service nào
   enforce. Giai đoạn 5 phải **xây mới** phần enforce này.

6. **DB mặc định = MariaDB/MySQL** (`.env.example`: `DB_CONNECTION=mariadb`; `config/database.php`
   fallback `sqlite`). → Cột `JSON` được hỗ trợ tốt → phương án `content_json` khả thi.

7. `ClientDashboardService` (client) **phụ thuộc nặng** vào `MockExam` (radar TR/CC/LR/GRA, band trend,
   `sample_essay` để ước lượng thời gian) → phải viết lại gần như toàn bộ. Lưu ý còn 1 service
   `app/Services/DashboardService.php` (gốc) phục vụ **admin dashboard** — verify trước khi đụng, mặc
   định **giữ nguyên** (ngoài scope nếu không tham chiếu mock/lesson IELTS).

---

## 1. TÓM TẮT PHẠM VI & QUYẾT ĐỊNH KIẾN TRÚC THEN CHỐT

### 1.1. Phạm vi
Biến nền tảng IELTS Writing (chép chính tả + phân tích Grammarly + mock exam AI) thành **Dictation Lab**:
chép chính tả **song ngữ Anh–Việt**, **gõ từng câu một**, phân loại theo **category (chủ đề)**, có **sổ
tay từ vựng highlight-to-save**, dashboard gọn (tốc độ/độ chính xác/tiến độ), giữ thanh toán SePay và
đổi tính năng AI từ "chấm điểm" → "Giải thích câu này".

### 1.2. Quyết định 1 — Lưu nội dung câu: `content_json` vs bảng `article_sentences`

**→ KHUYẾN NGHỊ: dùng cột `content_json` (JSON) trên bảng `articles`.**

| Tiêu chí | `content_json` (CHỌN) | Bảng `article_sentences` |
|---|---|---|
| Độ phức tạp | Thấp — 1 cột, không join | Cao — thêm model/migration/quan hệ |
| Admin "Sentence Builder" | Map thẳng repeater Alpine ↔ mảng JSON | Phải diff create/update/delete từng row |
| Thứ tự câu | Ngầm theo index mảng | Cần cột `order` + maintain |
| Engine gõ | `@json($article->content_json)` là xong | Phải eager-load + transform |
| Truy vấn từng câu | Không cần (luôn xử lý cả bài) | Có, nhưng YAGNI |
| Từ vựng theo câu | Đã denormalize `sentence_en`/`sentence_vi` ngay trên `user_vocabularies` (không cần FK câu) | FK được nhưng thừa |

Lý do: các câu **luôn được đọc/sửa theo cả bài**, không có nhu cầu query lẻ từng câu; schema
`user_vocabularies` mới đã chủ động lưu `sentence_en`/`sentence_vi` (denormalized) → không cần FK tới câu.
Đúng **KISS + YAGNI**. Cấu trúc: `content_json = [{"en":"...","vi":"..."}, ...]`, cast `array`.

**Ràng buộc dữ liệu cần validate ở ArticleController:** mỗi phần tử phải có `en` (bắt buộc, không rỗng)
và `vi` (bắt buộc); loại bỏ dòng rỗng; tối thiểu 1 câu.

### 1.3. Quyết định 2 — Chiến lược migration: `fresh` vs `incremental`

**→ KHUYẾN NGHỊ: FRESH REBUILD** (sửa trực tiếp file migration gốc + `migrate:fresh --seed`).

- **Giả định (cần chủ dự án xác nhận — xem mục 2):** đây là **sản phẩm cuối môn** (Nhóm 18), môi trường
  DEV, **dữ liệu hiện tại là seed/test, có thể xóa**. Codebase cũng đã theo pattern "sửa thẳng migration
  gốc" (toàn bộ schema gói trong 1 file).
- **Cách làm:** sửa `0001_01_01_000000_create_users_table.php` (bỏ bảng chết, đổi `lessons`→`articles`
  với cột mới, thêm `categories`), xoá các migration của bảng chết, rồi `php artisan migrate:fresh --seed`.
- **Phương án dự phòng (nếu PROD có user thật):** giữ migration cũ, **thêm migration mới incremental**
  (rename table, drop/add column, backfill). Plan có nêu các lệnh ở Phụ lục. Nhưng với rename
  `lessons→articles` + bỏ nhiều cột + đổi FK `lesson_id→article_id` ở 2 bảng, đường incremental phức tạp
  hơn nhiều và **không khuyến nghị** cho dự án này.

### 1.4. Nguyên tắc kỹ thuật xuyên suốt
- Giữ pattern **Controller → Service** mỏng (controller validate + gọi service).
- Đổi tên route/view/model **đồng bộ một lượt** để tránh reference gãy.
- Mọi JS engine viết inline trong blade (nhất quán).
- PowerShell: **không dùng `&&`**; chạy từng lệnh hoặc nối bằng `;` (xem `.agents/skills/powershell-windows`).

---

## 2. RỦI RO / ĐIỂM CẦN CHỦ DỰ ÁN XÁC NHẬN

| # | Câu hỏi | Vì sao quan trọng | Mặc định nếu không phản hồi |
|---|---|---|---|
| R1 | Có user/dữ liệu THẬT trên prod không? | Quyết định fresh vs incremental | Giả định DEV → **fresh** |
| R2 | "Đa chủ đề" = quản lý bằng bảng `categories` phẳng (1 cấp) đủ chưa, hay cần cây phân cấp? | Ảnh hưởng schema categories | **1 cấp phẳng** (KISS) |
| R3 | Free "3 bài/ngày" tính theo **bài bắt đầu** hay **bài hoàn thành**? Reset theo ngày lịch hay rolling 24h? | Quyết định nơi & cách enforce | **Bài hoàn thành/ngày lịch** (đếm `dictation_histories` theo `completed_at` hôm nay) |
| R4 | Free "tối đa 50 từ" = giới hạn **tổng số từ trong sổ tay** đúng không? | Enforce ở VocabularyService | **Tổng số bản ghi `user_vocabularies` ≤ 50** |
| R5 | Âm "ting" khi gõ đúng câu: bật mặc định hay tắt mặc định? | UX + asset âm thanh | **Tắt mặc định**, toggle bật; tạo bằng WebAudio (không cần file) |
| R6 | Giữ lịch sử WPM/accuracy cũ để hiển thị dashboard không? | Nếu fresh sẽ mất | Chấp nhận mất (seed lại) |
| R7 | "AI dịch từ vựng (Pro)" có làm ngay GĐ4 hay để sau? | Ảnh hưởng khối lượng GĐ4 | **Làm tối giản**: nút "AI dịch" gọi OpenRouter, chỉ Pro |
| R8 | Giữ trang marketing phụ (about, features, pricing-contact) hay gộp? | Ảnh hưởng GĐ6 | **Giữ**, chỉ đổi copy |

---

## 3. KẾ HOẠCH THEO TỪNG GIAI ĐOẠN

> Thứ tự bắt buộc: **GĐ1 (DB+dọn dẹp) → GĐ2 (Admin) → GĐ3 (Dictation Room) → GĐ4 (Vocab) → GĐ5
> (Dashboard/Pricing) → GĐ6 (Branding)**. GĐ4–6 có thể song song một phần (xem mục 4).

---

### GIAI ĐOẠN 1 — Dọn dẹp codebase & thiết kế lại DB

#### 1A. XÓA file (Models / Controllers / Services / Jobs / Views / Migrations)

**Models** (`app/Models/`):
- `MockExam.php`, `LessonAnnotation.php`, `LessonVocabulary.php`, `MiniExercise.php`, `ReadingMaterial.php`

**Controllers**:
- Admin: `LessonAnnotationController.php`, `LessonVocabularyController.php`, `MiniExerciseController.php`, `ReadingMaterialController.php`
- Client: `MockExamController.php`, `AnalyzeController.php`, `MiniExerciseController.php`, `ReadingMaterialController.php`
- *(KHÔNG có Admin/MockExamController.)*

**Services**:
- `app/Services/Client/MockExamService.php`
- `app/Services/Client/AnalyzeService.php`
- `app/Services/MiniExerciseService.php`
- `app/Services/ReadingMaterialService.php`
- *(GIỮ `app/Services/DashboardService.php` gốc — verify không tham chiếu mock; nếu có thì sửa, không xóa.)*

**Jobs**: `app/Jobs/GradeMockExamJob.php`

**Views** (xóa cả thư mục/ file):
- `resources/views/client/learning/study-analyze.blade.php`
- `resources/views/client/learning/mock-exam-room.blade.php`, `mock-exam-report.blade.php`, `mock-exam-intro.blade.php`
- `resources/views/client/reading-materials/` (cả thư mục)
- `resources/views/client/mini-exercises/` (cả thư mục)
- `resources/views/admin/reading-materials/` (cả thư mục)
- `resources/views/admin/mini-exercises/` (cả thư mục)
- `resources/views/admin/lessons/mapping.blade.php`
- `resources/views/client/home/partials/demo-video-modal.blade.php` (nếu demo cũ là mock — xem GĐ6; tạm giữ, xử lý ở GĐ6)
- *(Cân nhắc `client/learning/dictation-report.blade.php`: route report đang trỏ tới; GĐ3 sẽ quyết định giữ hay bỏ — xem 1E.)*

**Migrations** (nếu theo phương án FRESH):
- Xóa `2026_05_24_074352_create_reading_materials_table.php`
- Xóa `2026_05_24_074352_create_mini_exercises_table.php`
- Xóa `2026_04_13_033732_add_status_to_mock_exams_table.php`
- Sửa `2026_04_13_160000_add_indexes_to_lessons_table.php` → đổi sang index cho bảng `articles` (hoặc gộp index vào migration gốc rồi xóa file này).

#### 1B. Thiết kế DB mới (sửa `0001_01_01_000000_create_users_table.php`)

**(1) Bỏ các `Schema::create` chết:** `lesson_annotations`, `lesson_vocabularies`, `mock_exams`
(và bỏ các `dropIfExists` tương ứng trong `down()`).

**(2) Thêm bảng `categories`** (đặt TRƯỚC `articles` vì FK):
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('description')->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
});
```

**(3) Đổi `lessons` → `articles`** (đổi tên bảng + bỏ cột IELTS + thêm cột mới):
```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->string('excerpt')->nullable();
    $table->string('image_path')->nullable();
    $table->json('content_json');            // [{"en":"...","vi":"..."}]
    $table->boolean('is_premium')->default(false);
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamps();
    $table->index(['status', 'category_id']);
});
// BỎ: task_type, question_type, prompt_text, sample_essay,
//     band_score, tr_score, cc_score, lr_score, gra_score
```

**(4) `dictation_histories`** — đổi `lesson_id`→`article_id`, thêm `completed_sentences`:
```php
Schema::create('dictation_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('article_id')->constrained()->cascadeOnDelete();
    $table->integer('wpm');
    $table->decimal('accuracy', 5, 2);
    $table->unsignedInteger('completed_sentences')->default(0);
    $table->timestamp('completed_at')->useCurrent();
    $table->timestamps();
    $table->index(['user_id', 'completed_at']);
});
```

**(5) `user_vocabularies`** — `lesson_id`→`article_id`, thêm `sentence_en`/`sentence_vi`:
```php
Schema::create('user_vocabularies', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
    $table->string('word');
    $table->string('meaning')->nullable();   // để trống ban đầu, user tự điền
    $table->text('sentence_en')->nullable();
    $table->text('sentence_vi')->nullable();
    $table->timestamps();
    $table->unique(['user_id', 'word']);
});
// BỎ: context_sentence (thay bằng sentence_en/sentence_vi)
```

> Lưu ý: cột `meaning` đổi sang `nullable()` (brief: "nghĩa để trống ban đầu"). Cần đồng bộ logic
> `VocabularyService` (đang ép "Chưa có nghĩa") — xem GĐ4.

#### 1C. Models mới / sửa
- **Tạo** `app/Models/Category.php`: fillable `name, slug, description, status`; `articles(): HasMany`.
- **Tạo** `app/Models/Article.php` (rename từ `Lesson.php`):
  - fillable: `category_id, title, excerpt, image_path, content_json, is_premium, status`
  - casts: `content_json => 'array'`, `is_premium => 'boolean'`
  - quan hệ: `category(): BelongsTo`, `dictationHistories(): HasMany`
  - **bỏ** `annotations()`, `vocabularies()`
- **Xóa** `app/Models/Lesson.php`.
- **Sửa** `DictationHistory.php`: fillable đổi `lesson_id→article_id`, thêm `completed_sentences`;
  quan hệ `lesson()` → `article(): BelongsTo(Article::class)`.
- **Sửa** `UserVocabulary.php`: fillable `lesson_id→article_id`, bỏ `context_sentence`, thêm
  `sentence_en, sentence_vi`; quan hệ `lesson()` → `article()`.
- **Sửa** `User.php`: **xóa** `mockExams(): HasMany`; đổi tên quan hệ `dictationHistories` (giữ tên,
  trỏ model cũ vẫn ok). Không còn tham chiếu MockExam.

#### 1D. Cập nhật `routes/web.php`
- **Xóa imports** (đầu file) các controller đã xóa: `Admin\LessonAnnotationController`,
  `Admin\LessonVocabularyController`, `Admin\MiniExerciseController`, `Admin\ReadingMaterialController`,
  `Client\AnalyzeController`, `Client\MiniExerciseController`, `Client\MockExamController`,
  `Client\ReadingMaterialController`.
- **Xóa routes:**
  - `learning/analyze/{lesson}` (analyze)
  - `learning/mock-exam/*` (intro/room/submit/report/status)
  - `reading-materials/{readingMaterial}`, `mini-exercises/{miniExercise}` (client)
  - Admin: `Route::resource('reading-materials'...)`, `Route::resource('mini-exercises'...)`,
    `lessons/{lesson}/mapping`, `lessons/{lesson}/annotations*`, `lessons/{lesson}/vocabularies*`
- **Đổi tên:** `Route::resource('lessons', LessonController)` → `Route::resource('articles', ArticleController)`;
  route client `learning/dictation/{lesson}` → `learning/dictation/{article}` (đổi binding + tên route
  `client.learning.dictation`), `client.lessons.library` → `client.articles.library`.
- **Thêm (đặt nền cho GĐ2/4/AI):**
  - `Route::resource('categories', CategoryController)` trong nhóm admin
  - (GĐ4) route lưu từ vựng đã có sẵn: `dashboard/vocabulary` (store) — **tái sử dụng**, chỉ mở rộng payload
  - (AI) `POST learning/dictation/explain` → `DictationController@explain` (throttle, chỉ Pro)

> ⚠️ Sau khi sửa routes, chạy `php artisan route:list` để chắc không còn reference controller đã xóa
> (nếu còn import thừa Laravel sẽ fatal khi boot).

#### 1E. Xử lý `DictationController` + `DictationService` (chuẩn bị, hoàn thiện ở GĐ3)
- `DictationController@show`: đổi `getLessonForDictation(int $lesson)` → `getArticleForDictation(int $article)`;
  bỏ `->with('vocabularies')`; check `is_premium` giữ nguyên.
- `saveResult`: validate đổi `lesson_id`→`article_id` (`exists:articles,id`), thêm `completed_sentences`.
- `report`/view `dictation-report`: quyết định **giữ** (đổi field) hay **bỏ** (chuyển sang completion modal).
  → KHUYẾN NGHỊ: **bỏ** route+view report, dùng completion modal tại chỗ (đã có sẵn modal trong view) để
  giảm bề mặt. Nếu giữ, phải bỏ tham chiếu `band_score`, `task_type` trong `getResult`.
- `DictationService`: bỏ `use App\Models\Lesson`, dùng `Article`; `saveResult` lưu `article_id`,
  `completed_sentences`.

#### 1F. Seeders
- **Sửa `LessonSeeder.php` → `CategoryArticleSeeder.php`:** tạo vài `categories` (VD: "Daily Life",
  "Business", "Travel", "Technology") + vài `articles` có `content_json` mảng câu song ngữ thực tế.
  Bỏ toàn bộ annotations/vocabularies seeding.
- **Sửa `LearningHistorySeeder.php`:** bỏ `mock_exams` truncate/insert; đổi `dictation_histories` dùng
  `article_id` + `completed_sentences`; `user_vocabularies` dùng `article_id` + `sentence_en/vi`.
- **Sửa `DatabaseSeeder.php`:** thay `LessonSeeder::class` → `CategoryArticleSeeder::class`.

#### Checklist xác minh GĐ1
- [ ] `php artisan migrate:fresh --seed` chạy không lỗi.
- [ ] `php artisan route:list` không lỗi, không còn route mock/analyze/reading/mini.
- [ ] `php artisan tinker` → `App\Models\Article::first()->content_json` trả về mảng câu.
- [ ] Grep toàn repo `MockExam|LessonAnnotation|LessonVocabulary|ReadingMaterial|MiniExercise|sample_essay|band_score`
      → chỉ còn ở chỗ đã chủ ý (lý tưởng: 0).
- [ ] `php artisan about` / mở trang chủ `/` không 500.

---

### GIAI ĐOẠN 2 — Admin panel đơn giản hóa

#### Tạo
- `app/Http/Controllers/Admin/CategoryController.php` (resource: index/create/store/edit/update/destroy):
  validate `name` (required), `slug` (nullable→auto `Str::slug(name)`, unique), `status`.
  Chặn xóa category còn `articles` (hoặc set null — đã `nullOnDelete`).
- `app/Http/Controllers/Admin/ArticleController.php` (rename từ `LessonController`):
  - bỏ method `mapping()`; bỏ filter `task_type` trong `index`.
  - `rules()`: `title` (required), `category_id` (nullable, exists), `image` (nullable image),
    `is_premium` (boolean), `status` (in draft,published), **`content` (array, min:1)** với
    `content.*.en` (required, string), `content.*.vi` (required, string).
  - store/update: build `content_json` từ `content[]` (lọc dòng rỗng) rồi gọi service.
- `app/Services/ArticleService.php` (rename từ `LessonService`):
  - giữ `getArticles/createArticle/updateArticle/deleteArticle` + xử lý ảnh (FileUploadService giữ nguyên).
  - **xóa** toàn bộ method annotation: `createAnnotation/updateAnnotation/deleteAnnotation/`
    `renderEssayWithAnnotations/getNormalizedEssay/normalizeAnnotationPayload/findTextOccurrences`.
- (tùy chọn) `app/Services/CategoryService.php` nếu muốn tách logic; với CRUD đơn giản có thể để trong controller.

#### Views
- **Tạo** `resources/views/admin/categories/` : `index.blade.php`, `create.blade.php`, `edit.blade.php`,
  `partials/form.blade.php` (theo style admin hiện có).
- **Sửa/đổi tên** `resources/views/admin/lessons/` → `resources/views/admin/articles/`:
  - `index.blade.php`: cột Tiêu đề, Category, Free/Pro, Status; bỏ cột Task type/Band.
  - `partials/form.blade.php`: **viết lại** — giữ block upload ảnh Alpine (`imageUpload()`) đang có;
    **bỏ** block `task_type/question_type`, `sample_essay`, band scores; **thêm**:
    - select `category_id`
    - input `excerpt`
    - **"Sentence Builder" repeater Alpine** (component mới, xem dưới)
  - `create.blade.php` / `edit.blade.php`: trỏ tới form mới, truyền `$categories` + `$article`.
  - **Xóa** `mapping.blade.php`, `clients/` nếu thuộc lesson-mapping (verify `clients/` — đây là trang
    admin xem học viên theo lesson, có thể cần đổi sang article hoặc bỏ).
- **Sửa sidebar** `resources/views/components/admin/layout/sidebar.blade.php`:
  - đổi "Quản lý bài học" route `admin.lessons.index` → `admin.articles.index`
  - **thêm** mục "Chủ đề (Categories)" → `admin.categories.index`
  - **xóa** mục "Học liệu mở rộng" (`admin.reading-materials.index`) và "Trạm sửa lỗi"
    (`admin.mini-exercises.index`)
  - đổi text "IELTS Type & Learn" → "Dictation Lab"

#### Sentence Builder (Alpine, inline trong `admin/articles/partials/form.blade.php`)
Pattern giống `imageUpload()` đã có. Phác thảo:
```html
<div x-data="sentenceBuilder({{ Js::from(old('content', $article?->content_json ?? [['en'=>'','vi'=>'']])) }})">
  <template x-for="(row, i) in rows" :key="i">
    <div class="grid grid-cols-[1fr,1fr,auto] gap-2 mb-2">
      <input :name="`content[${i}][en]`" x-model="row.en" placeholder="English sentence" />
      <input :name="`content[${i}][vi]`" x-model="row.vi" placeholder="Nghĩa tiếng Việt" />
      <button type="button" @click="remove(i)">✕</button>
    </div>
  </template>
  <button type="button" @click="add()">+ Thêm câu</button>
</div>
<script>
function sentenceBuilder(initial){return{
  rows: initial.length ? initial : [{en:'',vi:''}],
  add(){this.rows.push({en:'',vi:''})},
  remove(i){this.rows.splice(i,1); if(!this.rows.length)this.add()},
}}
</script>
```
(Có thể bổ sung: kéo-thả sắp xếp, paste nhiều dòng → tự tách câu. YAGNI ở v1.)

#### Checklist xác minh GĐ2
- [ ] Tạo Category mới → hiện trong list + dropdown khi tạo Article.
- [ ] Tạo Article với 3 câu song ngữ → DB lưu `content_json` đúng mảng 3 phần tử.
- [ ] Sửa Article: thêm/xóa câu, đổi ảnh, đổi Free/Pro → lưu đúng.
- [ ] Sidebar không còn link 404 (reading-materials/mini-exercises).
- [ ] `php artisan route:list | findstr articles` (PowerShell) thấy resource articles.

---

### GIAI ĐOẠN 3 — Viết lại Dictation Room (core UX)

> Viết lại hoàn toàn `resources/views/client/learning/study-dictation.blade.php`. Giữ khung header/
> Tailwind config inline, **thay engine cũ** (render toàn bộ `SOURCE_TEXT`) bằng engine **từng-câu-một**.

#### Dữ liệu vào view
- Controller truyền `$article` (có `content_json`). Trong view:
  `const SENTENCES = @json($article->content_json);` // [{en,vi},...]
  `const ARTICLE_ID = {{ $article->id }};`
  `const SAVE_URL = @json(route('client.learning.dictation.save'));`

#### Layout mới (3 vùng)
1. **Trên:** ảnh (`image_path` nếu có) + tiêu đề + category + progress bar **theo số câu**
   (`câu hiện tại / tổng câu`).
2. **Giữa (khu gõ):**
   - Dòng **nghĩa tiếng Việt** của câu hiện tại (to, rõ).
   - Khu "ghost characters": render các ký tự tiếng Anh của **chỉ câu hiện tại** dạng mờ để gõ đè
     (tái dùng CSS `.char-ghost/.char-correct/.char-wrong/.char-cursor` đã có sẵn trong file cũ).
3. **Dưới:** lịch sử các câu đã hoàn thành (push lên trên), mỗi câu hiển thị English + nghĩa VI
   (vùng này là nơi GĐ4 gắn highlight-to-save).

#### Engine JS mới (inline, viết lại từ đầu — đơn giản hơn engine cũ)
Trạng thái: `currentSentenceIndex`, `cursorPos`, `charStates[]`, `totalCorrect`, `totalKeystrokes`,
`startTime`, `completedSentences[]`.
- Render **1 câu** (`SENTENCES[currentSentenceIndex].en`) thành các span ghost.
- `keydown`:
  - ký tự thường → so khớp ký tự kỳ vọng (giữ `CHAR_EQUIVALENCE` cho dấu nháy cong/– từ file cũ);
    đúng → `char-correct`, sai → `char-wrong` (đỏ) + đứng yên chờ Backspace.
  - `Backspace` → lùi cursor, xóa state.
  - Khi `cursorPos === câu.length` **và không còn ký tự sai** → câu hoàn thành:
    - (tùy chọn R5) phát âm "ting" qua WebAudio.
    - push câu vào vùng history (English + VI).
    - `currentSentenceIndex++`; nếu hết → `completeSession()`.
- Cập nhật WPM (tổng từ đã gõ / phút), accuracy (`totalCorrect/totalKeystrokes`), progress
  (`currentSentenceIndex / SENTENCES.length`).
- `completeSession()`: tính WPM/accuracy/`completed_sentences`, `fetch(SAVE_URL, {article_id, wpm,
  accuracy, completed_sentences})`, hiện completion modal (tái dùng modal có sẵn, **bỏ link analyze/band**).

**Âm "ting" (không cần file asset):**
```js
function playTing(){ if(!soundOn) return;
  const ctx = new (window.AudioContext||window.webkitAudioContext)();
  const o=ctx.createOscillator(), g=ctx.createGain();
  o.frequency.value=880; o.connect(g); g.connect(ctx.destination);
  g.gain.setValueAtTime(0.001,ctx.currentTime);
  g.gain.exponentialRampToValueAtTime(0.2,ctx.currentTime+0.01);
  g.gain.exponentialRampToValueAtTime(0.001,ctx.currentTime+0.25);
  o.start(); o.stop(ctx.currentTime+0.26);
}
```

#### File đụng
- **Viết lại:** `resources/views/client/learning/study-dictation.blade.php`
- **Sửa:** `DictationController@show` (đã nêu GĐ1E), `DictationService::getArticleForDictation`,
  `saveResult` (thêm `completed_sentences`).

#### Checklist xác minh GĐ3
- [ ] Mở `/learning/dictation/{id}` của 1 article: thấy ảnh + tiêu đề + nghĩa VI câu 1 + ghost EN.
- [ ] Gõ đúng hết câu 1 → câu đẩy lên history, hiện câu 2; progress tăng theo câu.
- [ ] Gõ sai → đỏ, phải Backspace mới đi tiếp.
- [ ] Hoàn thành bài cuối → modal hiện WPM/accuracy; `dictation_histories` có bản ghi
      `article_id`+`completed_sentences` đúng.
- [ ] Bài `is_premium=true` + user Free → 403.

---

### GIAI ĐOẠN 4 — Sổ tay từ vựng tự do (highlight-to-save)

#### Cơ chế
- Trong vùng **history câu đã chép** (GĐ3), bắt `mouseup` → `window.getSelection()`:
  - lấy `word = selection.toString().trim()` (≥1 ký tự, ≤ vài từ).
  - xác định câu chứa selection (qua `data-sentence-index` trên phần tử history) → lấy `sentence_en`,
    `sentence_vi` tương ứng từ `SENTENCES`.
  - hiện tooltip nhỏ "📌 Thêm vào sổ từ vựng" tại vị trí selection.
- Click tooltip → `POST` tới route lưu từ vựng.

#### Backend — tái sử dụng route có sẵn `dashboard/vocabulary` (store)
- Route đã tồn tại: `Route::post('dashboard/vocabulary', [DashboardController, 'saveVocabulary'])
  ->name('client.vocabulary.store')`. **Tái dùng**, mở rộng payload.
- **Sửa `DashboardController@saveVocabulary`** validate:
  - `word` (required), `article_id` (nullable, `exists:articles,id`), `sentence_en` (nullable),
    `sentence_vi` (nullable), `meaning` (nullable).
  - bỏ `lesson_id`/`context_sentence`.
- **Sửa `VocabularyService::saveVocabulary`:**
  - lưu `article_id, sentence_en, sentence_vi`; `meaning` để **null/rỗng** (brief: trống ban đầu) —
    bỏ logic ép "Chưa có nghĩa" (hoặc đổi thành cho phép null; cập nhật cả `getVocabularies` search
    không lỗi khi meaning null).
  - **Enforce giới hạn Free (R4):** nếu `!$user->isPro()` và `getCount($user) >= 50` → throw/trả lỗi
    "Đạt giới hạn 50 từ của gói Free".
- (R7) **AI dịch (Pro):** nút "AI dịch" trong **Dashboard > Sổ tay** (hoặc tooltip) →
  `POST dashboard/vocabulary/{id}/translate` → gọi `AiAssistantService` (xem mục AI) điền `meaning`.
  Chỉ cho Pro.

#### File đụng
- **Sửa:** `resources/views/client/learning/study-dictation.blade.php` (thêm tooltip + JS highlight).
- **Sửa:** `app/Http/Controllers/Client/DashboardController.php` (`saveVocabulary`).
- **Sửa:** `app/Services/Client/VocabularyService.php` (payload mới + enforce 50 từ).
- **Sửa:** `resources/views/client/dashboard/vocabulary.blade.php` (hiển thị `sentence_en/vi`, ô điền
  nghĩa, nút AI dịch cho Pro).

#### Checklist xác minh GĐ4
- [ ] Bôi đen 1 từ trong câu history → tooltip hiện → click → toast "Đã lưu".
- [ ] `user_vocabularies` có `word, article_id, sentence_en, sentence_vi`, `meaning` null.
- [ ] User Free lưu tới từ thứ 51 → bị chặn với thông báo.
- [ ] Vào Dashboard > Sổ tay: thấy từ + câu ngữ cảnh; điền nghĩa lưu được.
- [ ] (Pro) bấm "AI dịch" → `meaning` được điền.

---

### GIAI ĐOẠN 5 — Dashboard & Pricing

#### 5A. Dashboard client — viết lại (bỏ IELTS analytics)
- **Sửa `ClientDashboardService`:** **bỏ toàn bộ** logic MockExam/radar/band:
  - `getStats`: trả `total_articles_completed` (distinct `article_id` trong `dictation_histories`),
    `total_sentences` (sum `completed_sentences`), `avg_wpm`, `avg_accuracy`, `total_vocab`
    (`userVocabularies()->count()`).
  - **bỏ** `getAnalytics/buildSummary/buildCharts/getWeeklyWpm`-mock; có thể giữ một chart đơn giản
    "WPM theo ngày" (tái dùng ý tưởng `getWeeklyWpm`, đổi `lesson`→`article`, bỏ `sample_essay`).
  - `getRecentActivity`: **bỏ nhánh mockExams**, chỉ dictation; `with('article:id,title')`.
  - `getRecommendedLessons` → `getRecommendedArticles`: `Article::published()` + theo category.
  - **mới:** `getResumeList($user)`: bài đang chép dở = article có history nhưng
    `completed_sentences < tổng câu` (so `count(content_json)`), để "resume".
  - **mới:** danh sách category + số bài mỗi category.
- **Sửa `DashboardController@index`:** bỏ `period_days`/`analytics`; truyền `stats`, `recentActivity`,
  `recommendedArticles`, `resumeList`, `categories`.
- **Sửa `resources/views/client/dashboard/index.blade.php`:**
  - **bỏ** 5 `<canvas>` mock/radar + toàn bộ block `renderScoreRadarChart/renderMockBandTrendChart/...`
    + có thể bỏ luôn Chart.js CDN nếu không giữ chart nào (KISS); hoặc giữ 1 chart WPM.
  - hiển thị KPI: số bài hoàn thành, tổng số câu đã chép, WPM TB + accuracy, số từ vựng đã lưu.
  - danh sách category + bài đang chép dở (resume → link `client.learning.dictation`).

#### 5B. Pricing & enforce giới hạn Free
- **Copy (Pricing):** sửa `HomeService` mảng `pricing` + `pricing-section.blade.php`:
  - Free: "3 bài/ngày", "Tối đa 50 từ trong sổ tay", bỏ "Phòng thi & AI chấm điểm".
  - Pro: "1000+ bài", "Chép chính tả không giới hạn", "AI dịch từ vựng", bỏ "Phòng thi & AI chấm điểm".
- **Enforce (R3) — Free 3 bài/ngày:** tạo **middleware** `EnsureDailyDictationLimit`:
  - nếu `!user->isPro()` và `DictationHistory::where('user_id',...)->whereDate('completed_at', today())
    ->distinct('article_id')->count('article_id') >= 3` → chặn (redirect pricing + thông báo).
  - Gắn vào route `client.learning.dictation` (GET show) — quyết định: chặn khi **bắt đầu bài thứ 4**.
    (Hoặc enforce ở `DictationController@show`.) Đăng ký alias trong `bootstrap/app.php`.
- **Enforce 50 từ:** đã làm ở GĐ4 (VocabularyService).
- **Giữ nguyên** logic SePay (`CheckoutController`, `Client\SePayService`, `Client\CheckoutService`,
  `config/services.php sepay`) — không đụng.

#### Checklist xác minh GĐ5
- [ ] Dashboard không còn radar/mock; hiển thị đúng KPI dictation + resume list.
- [ ] `grep MockExam app/` = 0 (đặc biệt trong ClientDashboardService).
- [ ] User Free hoàn thành 3 bài hôm nay → bài thứ 4 bị chặn, hiện CTA nâng cấp.
- [ ] Trang Pricing hiển thị wording mới; nút nâng cấp SePay vẫn hoạt động.

#### 5C. Cập nhật `HomeService` (dữ liệu trang chủ)
- Bỏ/sửa `featured_lessons` (đang dùng band/tr/cc...) → `featured_articles` (title, category, is_premium).
- Sửa `features`, `how_it_works`, `feature_experience`, `plan_fit`, `faqs`, `final_cta` bỏ nội dung
  mock exam/AI chấm/band (chi tiết ở GĐ6).

---

### GIAI ĐOẠN 6 — Rebranding & marketing

#### Đổi copywriting (bỏ "Band 8.0+", "Mock Exam", "Grammarly", "AI chấm điểm", TR/CC/LR/GRA)
Các view/khối cần sửa:
- `app/Services/Client/HomeService.php` — **nguồn copy chính** (seo, hero, problem_section, features,
  how_it_works, pricing, testimonials, faqs, final_cta, about_page, feature_experience, plan_fit, contact).
- `resources/views/client/home/index.blade.php` + `partials/`:
  - `hero-section.blade.php` — đổi tiêu đề + demo.
  - `pricing-section.blade.php` — wording (GĐ5B), bỏ dòng "Phòng thi & AI chấm điểm", "Band 8.0–9.0+".
  - `features.blade.php`, `partials/founder-section.blade.php`, `partials/contact-section.blade.php`,
    `about.blade.php`, `pricing-contact.blade.php` — rà soát text IELTS.
  - `partials/demo-video-modal.blade.php` — đổi/bỏ; demo mới = cảnh **gõ đè câu tiếng Anh dưới câu
    tiếng Việt** (mini typing loop tự động, inline JS, hoặc ảnh/gif).
  - `social-proof-section`, `overview-pages-section`, `final-cta-section` (được include ở index) — rà text.
- `resources/views/components/client/layout/app.blade.php` — `<title>`, `<meta description>`, bỏ CSS
  `.underline-*` / `.band-pill` không dùng (tùy chọn dọn).
- `resources/views/components/client/layout/header.blade.php`, `footer.blade.php` — đổi brand
  "IELTS Type & Learn" → "Dictation Lab", menu (bỏ link analyze/mock nếu có).
- `resources/views/components/admin/layout/sidebar.blade.php` — brand (đã nêu GĐ2).
- `config/app.php` / `.env` `APP_NAME` → "Dictation Lab" (tùy chọn).
- `AiAssistantService::defaultInstruction()` + `welcome_message` mặc định — đổi sang ngữ cảnh Dictation.

#### Checklist xác minh GĐ6
- [ ] Grep toàn repo (view + HomeService): `Band|Mock|Grammarly|chấm điểm|TR/CC/LR/GRA` → 0 (trừ chỗ
      cố ý).
- [ ] Trang chủ hiển thị demo gõ đè song ngữ; không còn nhắc IELTS chấm điểm.
- [ ] Brand "Dictation Lab" đồng nhất ở header/footer/admin/title.

---

### TẬN DỤNG AI — "Giải thích câu này" (thay cho "chấm điểm")

- **Tái sử dụng** `app/Services/AiAssistantService.php` (đang gọi OpenRouter qua
  `config('services.openrouter.*')`, dùng `chat_model`). **Giữ API key, không đổi config.**
- **Thêm method** `AiAssistantService::explainSentence(string $english, ?string $vietnamese): string`:
  build messages với system prompt kiểu "Bạn là giáo viên tiếng Anh, phân tích ngắn gọn ngữ pháp/cấu
  trúc/từ vựng của câu sau, giải thích bằng tiếng Việt", gọi `requestAssistantMessage()` (đã có).
- **Controller:** thêm `DictationController@explain` (hoặc dùng `AiChatController` mới) — route
  `POST learning/dictation/explain`, `throttle:20,1`, **chỉ Pro** (`abort_unless(auth()->user()->isPro(), 403)`).
- **UI:** trong vùng history câu đã chép, nút nhỏ "💡 Giải thích câu" (chỉ hiện cho Pro) → gọi API →
  hiện kết quả trong tooltip/panel.
- **AI dịch từ vựng (GĐ4):** dùng cùng service, method `translateWord(string $word, ?string $context)`.

---

## 4. THỨ TỰ TRIỂN KHAI TỔNG THỂ & PHẦN SONG SONG

```
GĐ1 (DB + dọn dẹp)          ── BẮT BUỘC TRƯỚC, làm tuần tự, 1 người
   │  (sau khi migrate:fresh --seed xanh)
   ├── GĐ2 (Admin: Category + Article + Sentence Builder)
   │       │ (cần có Article CRUD để tạo dữ liệu test cho GĐ3)
   │       └── GĐ3 (Dictation Room engine)  ← lõi, ưu tiên cao
   │               └── GĐ4 (Highlight-to-save vocab)  [phụ thuộc GĐ3 history UI]
   │
   ├── GĐ5A (Dashboard rewrite)   ── song song được sau GĐ1 (chỉ cần schema mới)
   ├── GĐ5B (Pricing + middleware giới hạn)  ── song song sau GĐ1
   └── GĐ6 (Rebranding copy)      ── song song bất kỳ lúc nào sau GĐ1 (chỉ sửa text/view)
```

**Có thể song song (nhiều người):**
- Sau khi GĐ1 xong: **GĐ2/3/4** (luồng core) và **GĐ5/GĐ6** (dashboard/branding) đi song song —
  ít đụng file chung (core ở `learning/`, branding ở `home/`).
- **Tránh đụng file chung:** `routes/web.php`, `sidebar.blade.php`, `HomeService.php`,
  `DictationController.php` — phân công 1 người sở hữu mỗi file để tránh conflict.

**Đường găng (critical path):** GĐ1 → GĐ2(Article CRUD) → GĐ3(engine) → GĐ4.

---

## 5. CÁC LỆNH ARTISAN (PowerShell — KHÔNG dùng `&&`)

> Chạy trong thư mục dự án. PowerShell: mỗi lệnh 1 dòng, hoặc nối bằng `;`.

```powershell
# ── GĐ1: Models / Migration (nếu muốn artisan tạo khung; có thể sửa tay file gốc) ──
php artisan make:model Category
php artisan make:model Article
# (Khuyến nghị: sửa thẳng migration gốc 0001_01_01_000000 thay vì tạo mới — xem mục 1.3)

# ── GĐ1: Rebuild DB (FRESH) ──
php artisan migrate:fresh --seed

# ── GĐ2: Controllers admin ──
php artisan make:controller Admin/CategoryController --resource
php artisan make:controller Admin/ArticleController --resource
# (ArticleService/CategoryService tạo tay trong app/Services/ — không có generator service)

# ── GĐ5: Middleware giới hạn Free 3 bài/ngày ──
php artisan make:middleware EnsureDailyDictationLimit
# → đăng ký alias trong bootstrap/app.php (->withMiddleware) rồi gắn vào route dictation

# ── Kiểm tra sau mỗi giai đoạn ──
php artisan route:list
php artisan optimize:clear
php artisan config:clear

# ── Chạy dev (KHÔNG dùng `composer run dev` vì script đó gọi npm/Vite không tồn tại) ──
php artisan serve
# (queue chỉ cần nếu còn job; GradeMockExamJob bị xóa nên thường không cần queue:listen)

# ── Lệnh make sẽ KHÔNG dùng (đã loại bỏ tính năng) ──
# KHÔNG: make:job, make:controller cho MockExam/Analyze/Reading/Mini
```

> Lưu ý: xóa file bằng thao tác xóa thủ công / `Remove-Item` (PowerShell), KHÔNG có lệnh artisan xóa
> model/controller. Ví dụ: `Remove-Item app\Models\MockExam.php`.

---

## 6. MA TRẬN KIỂM THỬ (test matrix)

| Hạng mục | Unit/Service | Thủ công (E2E) |
|---|---|---|
| Article CRUD + content_json | `ArticleService` lưu/đọc JSON | Tạo/sửa bài, reload thấy đúng câu |
| Category CRUD | validate slug unique | Tạo/sửa/xóa, dropdown cập nhật |
| Dictation engine | (JS, test thủ công) | Gõ đúng/sai/backspace, hoàn thành, lưu history |
| Premium gate | `isPro()` | Free vào bài Pro → 403 |
| Free 3 bài/ngày | middleware đếm theo `completed_at` today | Hoàn thành 3 → bài 4 bị chặn |
| Vocab highlight-save | `VocabularyService` 50-word cap | Bôi đen → lưu; từ 51 bị chặn |
| AI giải thích/dịch | `AiAssistantService::explainSentence` | Pro bấm → có kết quả; Free → 403 |
| Dashboard KPI | `ClientDashboardService::getStats` | Số liệu khớp DB |
| Routes sạch | `php artisan route:list` không lỗi | Không 404/500 ở mọi trang |

---

## 7. KẾ HOẠCH ROLLBACK
- Làm trên nhánh riêng (VD `feat/dictation-lab-pivot`), commit theo từng giai đoạn (GĐ1…GĐ6) để revert
  từng phần.
- Vì chọn FRESH: **backup DB hiện tại** trước khi `migrate:fresh` (mysqldump) nếu cần khôi phục.
- Nếu một giai đoạn hỏng: `git revert` các commit của giai đoạn đó; schema GĐ1 là nền tảng nên nếu revert
  GĐ1 phải revert toàn bộ.

---

## 8. CÂU HỎI CHƯA GIẢI QUYẾT — ĐÃ ĐƯỢC CHỦ DỰ ÁN CHỐT (2026-06-29)
- **R1 → FRESH.** Dữ liệu hiện tại là DEV/seed, được phép xóa → dùng `migrate:fresh --seed`.
- **R3 → Bài HOÀN THÀNH / ngày lịch.** Free tối đa 3 bài hoàn thành mỗi ngày, reset 0h
  (đếm distinct `article_id` trong `dictation_histories` theo `whereDate('completed_at', today())`).
- **R4 → Tổng số bản ghi `user_vocabularies` ≤ 50** cho Free.
- **R7 → Làm AI dịch từ vựng NGAY ở GĐ4** (nút "AI dịch" gọi OpenRouter, chỉ Pro).
- **Report → BỎ** route/view `dictation-report`, dùng completion modal tại chỗ.
- (Các câu còn lại R2/R5/R6/R8 dùng default trong bảng mục 2.)

---

## 9. ĐIỂM MÙ PHÁT HIỆN KHI REVIEW (BỔ SUNG BẮT BUỘC — đã verify file thực tế)

> Phần này vá các thiếu sót của plan gốc. **Phải gộp vào các giai đoạn tương ứng.** Đây là các file
> sẽ gây **fatal error khi boot/test** nếu bỏ sót, vì chúng `use` các class bị xóa.

### 9.1. [GĐ1 — NGHIÊM TRỌNG] Stack "Admin xem học viên" phụ thuộc nặng MockExam
Plan gốc **bỏ sót hoàn toàn** nhóm này. Khi xóa model `MockExam`, các file sau sẽ vỡ:

- **`app/Services/ClientUserService.php`** — `use App\Models\MockExam`; `getClientDetail` gọi
  `$client->mockExams()`, `buildSummary/buildCharts/buildAttempts` đầy `MockExam::STATUS_*`,
  `overall_band/tr/cc/lr/gra`, và `estimateDictationDurationSeconds()` đọc `lesson->sample_essay`
  (cột sẽ bị xóa). → **PHẢI VIẾT LẠI:** bỏ mọi nhánh mock; chỉ còn dictation; đổi `lesson`→`article`;
  thời lượng học không còn ước lượng từ `sample_essay` (article không có cột này) — thay bằng dùng
  `completed_sentences` hoặc bỏ cột thời lượng. Bỏ tham số filter `source` (chỉ còn dictation).
- **`app/Http/Controllers/Admin/ClientController.php`** — bỏ `'source' => in:all,mock_exam,dictation`
  trong validate (`show` + `sanitizeShowFilters`). View trả về đổi theo 9.2.
- **`resources/views/admin/lessons/clients/index.blade.php`** và
  **`resources/views/admin/lessons/clients/show.blade.php`** — đây **KHÔNG phải** trang lesson-mapping
  mà là **trang quản lý/chi tiết học viên**. `show.blade.php` render radar band + mock charts → phải
  viết lại (chỉ KPI dictation). **Lưu ý vị trí:** khi đổi thư mục `admin/lessons/` → `admin/articles/`,
  hai view này nên **chuyển sang `resources/views/admin/clients/`** và cập nhật `ClientController`
  (`view('admin.clients.index')`, `view('admin.clients.show')`). (Hiện route đã là `admin.clients.*`,
  chỉ path view bị "lạc" trong thư mục lessons.)

### 9.2. [GĐ5 — NGHIÊM TRỌNG] Admin DashboardService (root) phụ thuộc MockExam
- **`app/Services/DashboardService.php`** (admin, KHÁC `ClientDashboardService`) — `use App\Models\MockExam`
  + `use App\Models\Lesson`; `getSummary()` có `total_lessons = Lesson::count()`;
  `buildLearningActivityChart()` query `MockExam` + `MockExam::STATUS_COMPLETED`.
  → **PHẢI SỬA** (nâng từ "có thể giữ" của plan gốc lên "bắt buộc sửa"): đổi `Lesson`→`Article`
  (`total_lessons`→`total_articles`), bỏ nhánh MockExam trong learning activity chart (chỉ còn dictation).
- **`resources/views/admin/dashboard.blade.php`** — rà bỏ phần hiển thị mock exam trong learning chart.

### 9.3. [GĐ1/GĐ5 — NGHIÊM TRỌNG] Client LessonController + thư viện bài học
- **`app/Http/Controllers/Client/LessonController.php`** — constructor inject `ReadingMaterialService`
  **và** `MiniExerciseService` (sẽ bị xóa) → **fatal khi resolve**. `library()` có tabs
  `ielts/materials/exercises` + filter `task_type/question_type/band_min`. → **VIẾT LẠI** thành thư viện
  Article theo **category**: bỏ 2 service đã xóa, bỏ tabs materials/exercises, đổi filter sang
  `category` + `search` + `access`. (Cân nhắc đổi tên file → `ArticleController` cho nhất quán; route
  `client.lessons.library` → `client.articles.library`.)
- **`app/Services/Client/LessonLibraryService.php`** — `use App\Models\Lesson`; lọc theo
  `task_type/question_type/band_score`; `getLesson()` dùng `->with(['annotations','vocabularies'])`
  (quan hệ sẽ bị xóa khỏi model) → **VIẾT LẠI** dùng `Article`, lọc theo `category_id`, bỏ eager-load
  annotations/vocabularies. (Đổi tên → `ArticleLibraryService` tùy chọn.)
- **`resources/views/client/learning/lesson-library.blade.php`** — UI tabs IELTS/Materials/Exercises +
  filter band/task → **VIẾT LẠI** thành lưới Article lọc theo category (đổi tên → `article-library.blade.php`).

### 9.4. [GĐ1 — bắt buộc] Tests sẽ vỡ khi xóa class
`php artisan test` sẽ fatal vì test `use` class đã xóa. Xử lý trong GĐ1:
- **XÓA** `tests/Feature/ResourceCenterFeatureTest.php` (reading-materials + mini-exercises).
- **XÓA** `tests/Feature/MockExamQueueFlowTest.php` (MockExam + GradeMockExamJob).
- **KIỂM TRA/GIỮ** `tests/Feature/AiAssistantModuleTest.php` (AI vẫn còn — sửa nếu tham chiếu copy IELTS),
  `tests/Feature/SePayCheckoutTest.php` (giữ), `tests/Feature/ExampleTest.php`,
  `tests/Unit/ExampleTest.php` (giữ).
- (Tùy chọn) thêm test mới cho Article CRUD / Dictation save / giới hạn Free — không bắt buộc cho cuối môn.

### 9.5. [GĐ1 — bắt buộc] Form Requests & Factories mồ côi
- **XÓA Form Requests** (`app/Http/Requests/Admin/`): `StoreMiniExerciseRequest.php`,
  `UpdateMiniExerciseRequest.php`, `StoreReadingMaterialRequest.php`, `UpdateReadingMaterialRequest.php`.
  (Giữ `UpdateGeneralSettingRequest.php`.)
- **XÓA Factories** (`database/factories/`): `MiniExerciseFactory.php`, `ReadingMaterialFactory.php`.
  (Giữ `UserFactory.php`.)

### 9.6. [GĐ4 — chi tiết đã verify] VocabularyService hiện trạng
`app/Services/Client/VocabularyService.php` hiện: ép `meaning='Chưa có nghĩa'` khi rỗng; dùng
`lesson_id` + `context_sentence`; `firstOrCreate` theo `(user_id, word)`; có `getCount()` sẵn (dùng cho
cap 50). → Khi sửa GĐ4: cho phép `meaning` null (bỏ ép "Chưa có nghĩa"), đổi `lesson_id`→`article_id`,
thay `context_sentence` bằng `sentence_en`+`sentence_vi`, thêm chặn 50 từ (tái dùng `getCount()`),
sửa `getVocabularies` search vẫn an toàn khi `meaning` null (LIKE trên null là OK).

### 9.7. [GĐ4/AI — đã verify] AiAssistantService
`requestAssistantMessage()` là **private** nhưng `explainSentence()`/`translateWord()` đặt **cùng class**
nên gọi nội bộ được — OK. Đã có sẵn cấu hình OpenRouter (`config('services.openrouter.*')`,
`chat_model`). `defaultInstruction()` + `welcome_message` + (trong `getPublicConfig`) đang ghi
"IELTS Writing" → đổi copy ở GĐ6. Khi thêm method mới, tự build `messages` rồi gọi
`requestAssistantMessage($messages)`.

### 9.8. [GĐ6 — tùy chọn] Docs / templates / snapshots còn chữ IELTS (không gây lỗi runtime)
- `docs/urd.md`, `docs/rules.md`, `README.md` — tài liệu mô tả IELTS; cập nhật nếu cần nộp kèm.
- `templates/` (`admin/admin-lesson-*.html`, `learning/lesson-library.html`) — HTML thiết kế tĩnh, không
  chạy runtime; cập nhật/để lại tùy nhu cầu trình bày.
- `_codebase/` — snapshot tự sinh, bỏ qua.

### 9.9. Checklist "không còn tham chiếu chết" (chạy cuối GĐ1, bổ sung cho mục 1F)
- [ ] `php artisan test` không fatal vì thiếu class (sau khi xóa test ở 9.4).
- [ ] `php artisan tinker` resolve được `Admin\ClientController`, `Client\LessonController`,
      `App\Services\DashboardService`, `App\Services\ClientUserService` (không lỗi DI/`use` class đã xóa).
- [ ] Mở `/admin/clients` và `/admin/clients/{id}` không 500.
- [ ] Mở `/lessons` (thư viện client) không 500.
- [ ] Grep lại toàn repo (trừ `vendor/`, `_codebase/`, `docs/`, `templates/`):
      `MockExam|LessonAnnotation|LessonVocabulary|ReadingMaterial|MiniExercise|sample_essay|band_score|task_type`
      → 0 kết quả trong `app/`, `routes/`, `resources/views/`, `database/`, `tests/`.

### 9.10. Tổng hợp danh sách XÓA bổ sung (gộp vào 1A của GĐ1)
```
app/Http/Requests/Admin/StoreMiniExerciseRequest.php
app/Http/Requests/Admin/UpdateMiniExerciseRequest.php
app/Http/Requests/Admin/StoreReadingMaterialRequest.php
app/Http/Requests/Admin/UpdateReadingMaterialRequest.php
database/factories/MiniExerciseFactory.php
database/factories/ReadingMaterialFactory.php
tests/Feature/ResourceCenterFeatureTest.php
tests/Feature/MockExamQueueFlowTest.php
```
### 9.11. Tổng hợp danh sách SỬA bổ sung (ngoài danh sách plan gốc)
```
app/Services/ClientUserService.php              (viết lại, bỏ MockExam)
app/Http/Controllers/Admin/ClientController.php (bỏ filter source, đổi path view)
resources/views/admin/lessons/clients/index.blade.php → chuyển admin/clients/index.blade.php
resources/views/admin/lessons/clients/show.blade.php  → chuyển admin/clients/show.blade.php (bỏ radar/mock)
app/Services/DashboardService.php               (admin root: Lesson→Article, bỏ MockExam chart)
resources/views/admin/dashboard.blade.php       (bỏ mock trong learning chart)
app/Http/Controllers/Client/LessonController.php(bỏ 2 service đã xóa, library theo category)
app/Services/Client/LessonLibraryService.php    (Lesson→Article, lọc category, bỏ annotations/vocab)
resources/views/client/learning/lesson-library.blade.php (viết lại theo category)
tests/Feature/AiAssistantModuleTest.php         (kiểm tra/giữ, sửa copy nếu cần)
```
