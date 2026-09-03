# Dictation Lab (Lách IELTS)

Nền tảng luyện nghe chép chính tả (Dictation) và trau dồi từ vựng IELTS theo mô hình Freemium, tích hợp trợ lý AI học tập và cổng thanh toán SePay.

## 1. Mục tiêu sản phẩm

- Biến luyện nghe chép chính tả (Dictation) thành quy trình có phản hồi tức thời theo từng câu.
- Cung cấp tính năng AI giải thích ngữ pháp, từ vựng và cấu trúc câu trực tiếp trong lúc học.
- Quản lý sổ tay từ vựng cá nhân có tính năng dịch nghĩa tự động bằng AI.
- Cung cấp trợ lý học tập AI (Chat Assistant) tương tác ngữ cảnh trong website.
- Vận hành mô hình Free / Pro với chu trình nâng cấp tài khoản tự động qua cổng thanh toán SePay.

## 2. Các module hệ thống

### 2.1 Các module đang hoạt động (Active)

| Module | Mô tả | Quyền truy cập |
|---|---|---|
| **Article Library** | Thư viện bài viết phân theo danh mục, phân loại bài Free và bài Premium | Khách và Người dùng đã đăng nhập |
| **Dictation Mode** | Luyện chép chính tả từng câu với audio/văn bản, phản hồi thời gian thực, đo WPM/Accuracy, lưu lịch sử | Free (có giới hạn số bài/ngày) và Pro (không giới hạn) |
| **AI Explain** | Phân tích và giải thích ngữ pháp, ngữ cảnh của câu đang luyện dictation bằng AI | Free và Pro |
| **AI Chat Assistant** | Widget trợ lý AI nổi hỗ trợ người học giải đáp thắc mắc (giới hạn câu hỏi, quản trị prompt linh hoạt) | Khách và Người dùng đã đăng nhập |
| **Vocabulary Notebook** | Sổ tay lưu từ vựng từ bài học, hỗ trợ dịch tự động bằng AI, chỉnh sửa và quản lý từ cá nhân | Người dùng đã đăng nhập |
| **Dashboard & Analytics** | Thống kê số bài hoàn thành, độ chính xác trung bình, tốc độ gõ (WPM), lịch sử thanh toán | Người dùng đã đăng nhập |
| **Checkout & Subscription** | Chọn gói học Pro, tạo form thanh toán SePay có chữ ký số, xác nhận IPN webhook tự động nâng cấp | Người dùng đã đăng nhập |
| **Admin Panel** | Quản lý Category, Article (nội dung dạng khối song ngữ EN/VI), Plan, Transaction, Client, cấu hình AI và cấu hình chung | Admin và Superadmin |
| **Superadmin User Management** | Quản trị tài khoản admin và superadmin | Superadmin |

### 2.2 Các module trong kế hoạch phát triển (Roadmap)

| Module | Mô tả | Trạng thái |
|---|---|---|
| **Analyze Mode** | Phân tích bài mẫu chi tiết theo các tiêu chí IELTS với chú thích (annotation mapping) | Kế hoạch phiên bản tiếp theo |
| **Mock Exam Mode** | Phòng thi IELTS Writing mô phỏng bấm giờ, chấm điểm tự động theo 4 tiêu chí TR/CC/LR/GRA | Kế hoạch phiên bản tiếp theo |

## 3. Kiến trúc hệ thống

Luồng triển khai bắt buộc của toàn bộ dự án:

`routes/web.php -> app/Models -> app/Services -> app/Http/Controllers -> resources/views`

Đặc điểm kiến trúc:

- **Phân tách ngữ cảnh**: Tách biệt hoàn toàn luồng routing, controller, service, view và blade component giữa Client và Admin.
- **Service Layer**: Toàn bộ nghiệp vụ (SePay, AI Assistant, Article Content, Checkout, Dictation) tập trung tại `app/Services`.
- **Dữ liệu bài viết dạng khối**: Quản lý qua `app/Services/ArticleContentService` với các khối văn bản, tiêu đề và cặp câu song ngữ (EN/VI).
- **Thanh toán tự động**: Tích hợp SePay qua luồng signed POST form và IPN webhook bảo mật bằng secret key.
- **AI Service linh hoạt**: Tích hợp OpenRouter, cho phép cấu hình độc lập model, system prompt và giới hạn lượt tương tác.
- **Frontend độc lập**: Sử dụng Tailwind CSS qua CDN và Vanilla JS trong Blade templates, không phụ thuộc build tool Node.js/Vite.

## 4. Công nghệ và phiên bản

| Nhóm | Công nghệ | Chi tiết |
|---|---|---|
| **Backend** | PHP 8.2+, Laravel 12 | Streamlined structure của Laravel 12 |
| **Authentication** | Laravel Auth + Laravel Socialite | Hỗ trợ email/password cùng OAuth Google và Facebook |
| **Frontend** | Blade + Vanilla JS + Tailwind CSS CDN | Giao diện chuẩn template, không dùng Vite/npm build |
| **AI Integration** | OpenRouter API | Mô hình mặc định: Google Gemini Flash / OpenAI-compatible |
| **Payment Gateway** | SePay Gateway | Thanh toán QR Code/chuyển khoản, xác thực IPN webhook |
| **Testing** | Pest v3 + PHPUnit v11 | 22 feature & unit test tự động |
| **Code Style** | Laravel Pint | Chuẩn định dạng PSR-12 của Laravel |

## 5. Cấu trúc thư mục trọng yếu

```text
app/
    Helpers/                # Hàm tiện ích chung
    Http/Controllers/
        Admin/              # Controller quản trị (Article, Category, Client, Plan, Transaction, AI, Setting, User)
        Client/             # Controller học viên (Home, Auth, Dictation, Article, Dashboard, Checkout, AI Chat)
    Models/                 # Eloquent models (Article, Category, User, Plan, Transaction, DictationHistory, ...)
    Services/               # Service nghiệp vụ (AiAssistantService, ArticleContentService)
    Services/Client/        # Nghiệp vụ Client (SePayService, CheckoutService, DictationService, HomeService, ...)

resources/views/
    admin/                  # Giao diện admin theo module
    client/                 # Giao diện học viên (learning, dashboard, checkout, auth, home)
    components/
        admin/              # Blade components cho admin
        client/             # Blade components cho client

routes/web.php            # Toàn bộ định tuyến web client và admin
database/
    migrations/             # Lược đồ cơ sở dữ liệu
    seeders/                # Dữ liệu khởi tạo mẫu
docs/                     # Tài liệu kỹ thuật chi tiết
templates/                # Giao diện HTML tĩnh chuẩn tham chiếu
```

## 6. Hướng dẫn cài đặt môi trường Local

### Yêu cầu hệ thống

- PHP >= 8.2 (với các extension: `pdo_mysql`, `curl`, `mbstring`, `openssl`)
- Composer >= 2.0
- Cơ sở dữ liệu: MariaDB hoặc MySQL

> [!NOTE]
> Dự án sử dụng Tailwind CSS qua link CDN. Không cần cài đặt Node.js hay chạy lệnh build npm/Vite.

### Các bước cài đặt

1. **Clone mã nguồn và cài đặt thư viện PHP:**

   ```bash
   git clone <repo-url>
   cd dictation-lab.minhlong03.site
   composer install
   ```

2. **Cấu hình file môi trường:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Cấu hình cơ sở dữ liệu:**

   Chỉnh sửa thông tin kết nối database trong file `.env`:

   ```env
   DB_CONNECTION=mariadb
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dictation_lab
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Chạy migration và seed dữ liệu:**

   ```bash
   php artisan migrate --seed
   ```

## 7. Biến môi trường quan trọng

### Cấu hình ứng dụng và Database

- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER` (khuyến nghị `database`)

### Cấu hình Social Login (Tùy chọn)

- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET`, `FACEBOOK_REDIRECT_URI`

### Cấu hình AI (OpenRouter)

- `OPENROUTER_API_KEY`: API key cấp từ OpenRouter.
- `OPENROUTER_API_URL`: Mặc định `https://openrouter.ai/api/v1/chat/completions`.
- `OPENROUTER_MODEL`: Mô hình AI sử dụng (ví dụ: `google/gemini-flash-1.5`).
- `OPENROUTER_CHAT_MODEL`: Mô hình chuyên biệt cho chat (mặc định lấy theo `OPENROUTER_MODEL`).
- `OPENROUTER_SITE_URL`, `OPENROUTER_SITE_NAME`: Metadata định danh gửi kèm request.

### Cấu hình Cổng thanh toán SePay

- `SEPAY_MERCHANT_ID`: Mã merchant cấp bởi SePay.
- `SEPAY_SECRET_KEY`: Khóa bí mật dùng để ký form checkout và xác thực IPN webhook.
- `SEPAY_ENVIRONMENT`: Môi trường thanh toán (`sandbox` hoặc `production`).

## 8. Khởi chạy ứng dụng

Chỉ cần một dòng lệnh Artisan để chạy server:

```bash
php artisan serve
```

Truy cập hệ thống tại: `http://localhost:8000`

Tài khoản mặc định từ Seeder (tham khảo `database/seeders`):
- **Superadmin**: Quản trị toàn hệ thống.
- **Admin**: Quản trị nội dung và vận hành.
- **User Pro / Free**: Trải nghiệm các luồng học tập và thanh toán.

## 9. Luồng nghiệp vụ cốt lõi

### 9.1 Luyện tập Dictation & AI Giải thích

1. Người học vào Thư viện bài viết (`/articles`), chọn bài học phù hợp.
2. Hệ thống kiểm tra quyền truy cập qua middleware `dictation.limit` (bài Premium hoặc giới hạn bài trong ngày đối với tài khoản Free).
3. Màn hình `/learning/dictation/{article}` tải danh sách câu từ `content_json`.
4. Người học nghe audio và gõ phím; giao diện so khớp ký tự đúng/sai tức thì.
5. Khi gặp câu khó, người học bấm nút nhờ AI giải thích: API `/learning/dictation/explain` gọi OpenRouter phân tích ngữ pháp, từ vựng và cấu trúc của chính câu đó.
6. Kết thúc bài, hệ thống gửi kết quả về `/learning/dictation/save`, ghi nhận WPM, độ chính xác (%) và cập nhật streak trên Dashboard.

### 9.2 Thanh toán SePay & Nâng cấp Pro

1. Người học chọn gói dịch vụ tại trang `/checkout`.
2. Hệ thống tạo giao dịch trạng thái `pending` với mã hóa đơn duy nhất qua `CheckoutService`.
3. `SePayService` tạo form thanh toán đã ký số (`sepay-redirect.blade.php`) và tự động chuyển hướng người dùng sang cổng SePay.
4. Người học thực hiện chuyển khoản bằng mã QR hoặc ứng dụng ngân hàng.
5. SePay gửi thông báo IPN đến webhook `POST /checkout/sepay/ipn`.
6. Hệ thống xác thực `secret_key`, kiểm tra tính toàn vẹn số tiền, cập nhật trạng thái `success` và gia hạn Pro cho User (`subscription_expires_at`).
7. Cơ chế xử lý idempotent đảm bảo IPN trùng lặp không cộng dồn ngày quá số lần quy định.

### 9.3 Trợ lý AI Chat Assistant

1. Widget AI hiển thị ở góc màn hình của các trang được kích hoạt.
2. Endpoint `/ai-chat/config` trả về cấu hình công khai (lời chào, avatar, trạng thái kích hoạt) do Admin cài đặt.
3. Người học gửi tin nhắn đến `/ai-chat/message` (có rate limiting).
4. `AiAssistantService` kết hợp context bài học hiện tại và lịch sử hội thoại (giới hạn tối đa 5 câu hỏi/phiên) để phản hồi chuẩn xác.

## 10. Kiểm thử và chất lượng mã

Chạy toàn bộ test suite (Pest / PHPUnit):

```bash
php artisan test
```

Định dạng mã nguồn theo chuẩn PSR-12:

```bash
vendor/bin/pint --dirty
```

## 11. Xử lý sự cố thường gặp (Troubleshooting)

- **Lỗi AI Chat hoặc AI Explain không phản hồi**:
  - Kiểm tra `OPENROUTER_API_KEY` trong file `.env`.
  - Đảm bảo model được cấu hình khả dụng trên tài khoản OpenRouter.
- **Giao dịch SePay không tự động kích hoạt Pro**:
  - Kiểm tra log tại `storage/logs/laravel.log` với tiền tố `sepay.ipn.*`.
  - Đảm bảo `SEPAY_SECRET_KEY` khớp chính xác với cấu hình trên SePay Dashboard.
  - Khi test local, sử dụng ngrok hoặc webhook relay để SePay có thể gửi request đến URL webhook `POST /checkout/sepay/ipn`.
- **Giao diện không nhận CSS Tailwind**:
  - Kiểm tra kết nối mạng internet để đảm bảo trình duyệt tải được thư viện Tailwind từ CDN.

## 12. Tài liệu kỹ thuật chi tiết

- [docs/urd.md](docs/urd.md): Tài liệu đặc tả yêu cầu người dùng và phạm vi chức năng hệ thống.
- [docs/rules.md](docs/rules.md): Quy tắc kiến trúc, quy chuẩn luồng mã và coding standards.
- [docs/style-guidelines.md](docs/style-guidelines.md): Quy chuẩn UI/UX, Design tokens và Component guidelines.
- [docs/sepay.md](docs/sepay.md): Tài liệu hướng dẫn và đặc tả chi tiết tích hợp cổng thanh toán SePay.

## 13. License

Dự án được phát hành theo giấy phép MIT.

