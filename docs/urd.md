# TÀI LIỆU YÊU CẦU NGƯỜI DÙNG (URD)

- Tên dự án: Dictation Lab (Lách IELTS)
- Phiên bản: 2.2
- Trạng thái: Đã đồng bộ với mã nguồn thực tế

Mục tiêu của tài liệu URD là mô tả chi tiết các yêu cầu chức năng, phi chức năng, mô hình dữ liệu và tiêu chí nghiệm thu cho nền tảng luyện IELTS Dictation.

---

## 1. Bối cảnh và mục tiêu

Dictation Lab là nền tảng luyện nghe chép chính tả (Dictation) và củng cố từ vựng IELTS theo mô hình Freemium:

1. **Luyện nghe chép chính tả từng câu (Dictation)**: Nghe audio, gõ lại văn bản với phản hồi ký tự đúng/sai tức thì, đo tốc độ WPM và độ chính xác.
2. **Trợ lý AI học tập (AI Assistance)**: Tích hợp tính năng AI giải thích ngữ pháp/ngữ cảnh của câu đang gõ và widget AI Chat hỗ trợ hỏi đáp trực tiếp.
3. **Sổ tay từ vựng thông minh (Vocabulary Notebook)**: Lưu từ vựng từ bài học, hỗ trợ dịch nghĩa tự động bằng AI và quản lý tiến độ cá nhân.
4. **Thương mại hóa dịch vụ (Monetization)**: Tích hợp cổng thanh toán SePay tự động hóa quy trình nâng cấp gói Pro.

### Mục tiêu kinh doanh

- Tối ưu tỷ lệ chuyển đổi Free sang Pro thông qua giá trị học tập rõ ràng và giới hạn lượt làm bài hợp lý.
- Tự động hóa 100% quy trình thanh toán và kích hoạt gói học viên qua SePay.

---

## 2. Đối tượng sử dụng và phân quyền

### 2.1 Persona

- **Free Learner**: Người dùng đăng ký miễn phí, luyện các bài Free và bị giới hạn số lượt làm bài Dictation mỗi ngày.
- **Pro Learner**: Người dùng trả phí, mở khóa toàn bộ bài viết Premium và không giới hạn lượt luyện tập.
- **Admin**: Quản trị nội dung bài viết, danh mục, gói học, giao dịch, quản lý tài khoản học viên và cấu hình AI.
- **Superadmin**: Toàn quyền hệ thống, bao gồm phân quyền và quản lý tài khoản Admin.

### 2.2 Role Matrix

| Chức năng | Free | Pro | Admin | Superadmin |
|---|---|---|---|---|
| Xem thư viện bài viết | Có | Có | Có | Có |
| Luyện Dictation bài Free | Có (giới hạn lượt/ngày) | Có (không giới hạn) | Có | Có |
| Luyện Dictation bài Premium | Không | Có | Có | Có |
| AI Explain trong Dictation | Có | Có | Có | Có |
| AI Chat Assistant | Có | Có | Có | Có |
| Sổ tay từ vựng (Vocabulary Notebook) | Có | Có | Có | Có |
| Dashboard & Thống kê học tập | Có | Có | Có | Có |
| Checkout & Nâng cấp Pro qua SePay | Có | Có | Không áp dụng | Không áp dụng |
| Quản lý Categories & Articles | Không | Không | Có | Có |
| Quản lý Plans & Transactions | Không | Không | Có | Có |
| Quản lý Học viên (Clients) | Không | Không | Có | Có |
| Cấu hình AI Assistant & Settings | Không | Không | Có | Có |
| Quản lý tài khoản Admin / Superadmin | Không | Không | Không | Có |

---

## 3. Phạm vi chức năng (Functional Requirements)

### FR-01. Xác thực và quản lý tài khoản

- Hỗ trợ đăng ký và đăng nhập bằng email/password.
- Hỗ trợ đăng nhập nhanh qua Google và Facebook (Laravel Socialite).
- Hỗ trợ gửi email quên mật khẩu và đặt lại mật khẩu.
- Người dùng cập nhật thông tin cá nhân (name, email) và đổi mật khẩu tại trang Profile.

### FR-02. Thư viện bài viết (Article Library)

- Hiển thị danh sách bài viết phân loại theo danh mục (Category) với bộ lọc slug.
- Đánh dấu huy hiệu rõ ràng giữa bài Free và bài Premium.
- Hiển thị tóm tắt, ảnh minh họa, số lượng câu trong bài.

### FR-03. Chế độ luyện Dictation

- Tải danh sách câu từ `content_json` của bài viết kèm audio (nếu có).
- Giao diện gõ phím bắt sự kiện thời gian thực, tô màu ký tự đúng/sai ngay lập tức.
- Tính toán tốc độ gõ (WPM) và tỷ lệ chính xác (Accuracy %).
- Middleware `dictation.limit` kiểm soát quyền truy cập bài Premium và số lượt làm bài trong ngày của gói Free.
- Lưu kết quả vào `dictation_histories` phục vụ thống kê cá nhân.

### FR-04. Trợ lý học tập AI (AI Explain & AI Chat Assistant)

- **AI Explain**: Trong lúc luyện Dictation, người học có thể yêu cầu AI phân tích câu đang làm. Hệ thống trích xuất câu tiếng Anh, gọi OpenRouter và trả về phân tích ngữ pháp, từ vựng và ví dụ.
- **AI Chat Assistant**: Widget chat nổi ở góc màn hình, hỗ trợ hỏi đáp nhanh.
- Quản trị viên có thể tùy chỉnh tên bot, lời chào, avatar, system prompt và giới hạn tối đa 5 câu hỏi/phiên trên giao diện Admin.

### FR-05. Sổ tay từ vựng & Dashboard cá nhân

- Lưu từ mới từ bài học vào bảng `user_vocabularies`.
- Tự động tra nghĩa và câu ví dụ song ngữ qua tính năng AI Translate.
- Cho phép người học tự sửa nghĩa và xóa từ khỏi danh sách.
- Dashboard hiển thị KPI: tổng số bài đã hoàn thành, độ chính xác trung bình, WPM trung bình, chuỗi ngày học liên tục (streak) và lịch sử thanh toán.

### FR-06. Thanh toán SePay & Quản lý gói cước (Subscription)

- Hiển thị các gói học Pro (Plan) với giá tiền và thời hạn (duration_days).
- Tạo giao dịch `pending` với mã định danh duy nhất.
- Chuyển hướng người dùng qua cổng thanh toán SePay với form POST đã ký HMAC SHA256.
- Tiếp nhận IPN webhook tại `POST /checkout/sepay/ipn`, xác thực chữ ký số và số tiền.
- Cập nhật trạng thái giao dịch `success` và tự động cộng dồn thời hạn Pro (`subscription_expires_at`).
- Đảm bảo cơ chế xử lý idempotent: không cộng dồn ngày nhiều lần nếu nhận webhook lặp.

### FR-07. Quản trị hệ thống (Admin Panel)

- **Quản lý danh mục**: Thêm, sửa, xóa danh mục bài viết.
- **Quản lý bài viết**: Soạn thảo bài viết với cấu trúc khối đa năng (`ArticleContentService` gồm đoạn văn, tiêu đề, cặp câu song ngữ EN/VI) và tải lên hình ảnh/audio.
- **Quản lý gói cước**: Thiết lập giá, số ngày sử dụng và trạng thái kích hoạt của Plan.
- **Quản lý giao dịch**: Theo dõi danh sách thanh toán, trạng thái thành công/thất bại và cập nhật thủ công khi cần.
- **Quản lý học viên**: Xem danh sách người dùng, kích hoạt/khóa tài khoản, điều chỉnh hạng mức Free/Pro và gia hạn ngày hết hạn.
- **Cấu hình AI & Hệ thống**: Tùy chỉnh thông số trợ lý AI, thông tin website và cấu hình liên hệ.

### FR-08. Quản trị phân quyền Admin (Superadmin)

- Superadmin quản lý danh sách tài khoản Admin và Superadmin.
- Ngăn chặn Admin thường truy cập hoặc chỉnh sửa tài khoản Superadmin.

---

## 4. Yêu cầu phi chức năng (Non-Functional Requirements)

### NFR-01. Hiệu năng & Trải nghiệm

- Phản hồi gõ phím tại Dictation mode chạy 100% bằng JavaScript client-side, độ trễ 0ms.
- Tải trang nhanh nhờ cấu trúc Blade nhẹ và Tailwind CSS CDN, không phụ thuộc bundle JS nặng.

### NFR-02. Độ tin cậy & Idempotency

- Webhook IPN SePay phải được kiểm tra tính hợp lệ bằng khóa bí mật `SEPAY_SECRET_KEY`.
- Các giao dịch đã hoàn tất không bị ghi đè hay xử lý lại khi có request trùng lặp.

### NFR-03. An ninh & Bảo mật

- Áp dụng rate limiting (`throttle:20,1`) cho các endpoint gọi AI (`/learning/dictation/explain`, `/ai-chat/message`).
- Mật khẩu mã hóa Bcrypt 12 rounds.
- Không lưu thông tin tài khoản ngân hàng hoặc thẻ của người dùng; giao dịch thực hiện hoàn toàn qua SePay.

### NFR-04. Chuẩn kiến trúc & Tính bảo trì

- Tuân thủ nghiêm ngặt luồng: `routes/web.php -> app/Models -> app/Services -> app/Http/Controllers -> resources/views`.
- Tách biệt rõ component và layout giữa Admin và Client.
- Không đặt logic nghiệp vụ trong Blade templates.

---

## 5. Tích hợp bên ngoài (External Integrations)

### INT-01. OpenRouter API

- Dùng cho tính năng AI Explain câu Dictation, AI Chat Assistant và dịch nghĩa từ vựng tự động.
- Giao thức: HTTP POST (OpenAI-compatible chat completions).
- Mô hình mặc định: `google/gemini-flash-1.5`.

### INT-02. Cổng thanh toán SePay

- Cổng thanh toán chuyên biệt cho thị trường Việt Nam (VietQR, Chuyển khoản ngân hàng tự động).
- Phương thức tích hợp: Signed Form Checkout Redirect và IPN Webhook Server-to-Server.
- Tài liệu chi tiết: `docs/sepay.md`.

### INT-03. Social OAuth

- Đăng nhập nhanh qua Google và Facebook sử dụng Laravel Socialite.

---

## 6. Mô hình dữ liệu chính (Core Data Models)

| Model | Bảng | Ý nghĩa |
|---|---|---|
| `User` | `users` | Tài khoản người dùng (role: superadmin, admin, user; subscription: free, pro) |
| `Category` | `categories` | Danh mục bài viết |
| `Article` | `articles` | Bài viết luyện tập (lưu trữ `content_json` dạng khối song ngữ) |
| `DictationHistory` | `dictation_histories` | Lịch sử luyện dictation (wpm, accuracy, completed_sentences) |
| `UserVocabulary` | `user_vocabularies` | Sổ tay từ vựng của người học |
| `Plan` | `plans` | Gói dịch vụ Pro (price, duration_days, is_active) |
| `Transaction` | `transactions` | Giao dịch thanh toán SePay (transaction_code, gateway_order_code, status) |
| `AiAssistantSetting` | `ai_assistant_settings` | Cấu hình trợ lý ảo AI (bot_name, avatar, prompt, max_questions) |
| `Setting` | `settings` | Cấu hình chung của website (key-value) |

---

## 7. Kế hoạch phát triển tiếp theo (Roadmap / Out of Scope)

Các tính năng sau đây hiện **chưa nằm trong phiên bản hiện tại** và được quy hoạch cho các phiên bản tiếp theo:

1. **Analyze Mode**: Chế độ đọc bài mẫu kèm hệ thống chú thích ngữ pháp (annotation mapping) đa sắc màu theo 4 tiêu chí IELTS.
2. **Mock Exam Mode**: Môi trường phòng thi tính giờ mô phỏng IELTS Writing Task 1 & Task 2 với tiến trình chấm điểm tự động bằng AI qua queue job.
3. Chấm điểm Speaking, Reading, Listening.
4. Ứng dụng di động native độc lập.

---

## 8. Tiêu chí nghiệm thu hệ thống (Acceptance Criteria)

1. Đăng ký, đăng nhập tài khoản và đăng nhập mạng xã hội hoạt động ổn định.
2. Thư viện bài viết lọc theo danh mục chính xác; bài Premium chỉ mở cho tài khoản Pro.
3. Chế độ Dictation đo đạc WPM/Accuracy chính xác và lưu lịch sử đầy đủ.
4. Tính năng AI Explain và AI Chat Widget phản hồi đúng ngữ cảnh và tôn trọng hạn mức câu hỏi.
5. Tạo form thanh toán SePay chuyển hướng thành công, IPN webhook xác thực chữ ký và kích hoạt Pro đúng thời hạn.
6. Admin Panel hỗ trợ đầy đủ thao tác quản lý dữ liệu mà không gây lỗi phân quyền.
7. Toàn bộ 22 automated test pass (`php artisan test`).
