# Quy tắc phát triển dự án

Tài liệu này là chuẩn triển khai bắt buộc cho toàn bộ codebase Dictation Lab (Lách IELTS).

---

## 1. Luồng code bắt buộc

Mọi tính năng mới hoặc chỉnh sửa logic phải đi theo luồng nghiêm ngặt:

`routes/web.php -> app/Models -> app/Services -> app/Http/Controllers -> resources/views`

Ý nghĩa từng lớp:

- **Route**: Khai báo endpoint, áp dụng middleware ngữ cảnh, đặt tên route rõ nghĩa (`client.*`, `admin.*`).
- **Model**: Định nghĩa quan hệ Eloquent, kiểu cast và tương tác cơ sở dữ liệu.
- **Service**: Nơi chứa 100% business logic; Controller không nhồi logic tính toán, xử lý thanh toán hay gọi AI.
- **Controller**: Điều phối request/response, validate input (hoặc qua FormRequest), gọi service và trả về view/json.
- **View**: Chỉ hiển thị dữ liệu đã chuẩn bị từ Controller, tuyệt đối không chứa business logic hoặc query database.

---

## 2. Quy tắc cấu trúc thư mục

- Helper dùng chung đặt tại `app/Helpers`.
- Nghiệp vụ Client đặt tại `app/Services/Client` và Controller Client tại `app/Http/Controllers/Client`.
- Nghiệp vụ Admin đặt tại `app/Services` / `app/Services/Admin` và Controller Admin tại `app/Http/Controllers/Admin`.
- Không tạo thư mục gốc mới khi chưa có phê duyệt kiến trúc.

---

## 3. Quy tắc phân tách Admin và Client

- Component tái sử dụng phải tách riêng cho Admin và Client.
- Không dùng lẫn component giữa hai khu vực nếu chưa có lý do rõ ràng.
- Layout của Admin và Client bắt buộc đặt tại:
  - `resources/views/components/admin`
  - `resources/views/components/client`

---

## 4. Quy tắc đặt tên

### 4.1 View

- `admin.{module}.{action}` (ví dụ: `admin.articles.index`, `admin.plans.create`)
- `client.{module}.{action}` (ví dụ: `client.learning.study-dictation`, `client.checkout.index`)

### 4.2 Blade component

- `components.admin.{group}.{name}`
- `components.client.{group}.{name}`

### 4.3 Route name

- Route client bắt đầu bằng `client.` (hoặc route gốc như `home`, `login`).
- Route admin bắt đầu bằng `admin.`.
- Tên route phải phản ánh đúng ngữ cảnh màn hình và hành động.

---

## 5. Quy tắc tích hợp template

- Trước khi sửa Blade, phải đối chiếu với template HTML tương ứng trong thư mục `templates/` (`templates/learning/`, `templates/dashboard/`, `templates/checkout/`, `templates/admin/`).
- Giữ nguyên cấu trúc DOM, class, spacing và hierarchy của template gốc.
- Chỉ thay thế phần dữ liệu tĩnh bằng Blade bindings và directives.
- Nếu cần tạo giao diện mới mà chưa có template, phải bám sát design tokens trong `docs/style-guidelines.md`.

---

## 6. Quy tắc Frontend

- **Tailwind CSS qua CDN**: Luôn luôn sử dụng link CDN của Tailwind CSS.
- **Không dùng Vite / npm**: Không cài đặt build step Node.js, không thêm các tiện ích bundling phức tạp vào dự án.
- **JavaScript thuần (Vanilla JS)**: Xử lý tương tác client-side trực tiếp trong file view hoặc component Blade tương ứng.

---

## 7. Quy tắc Backend và Laravel

- Tuân thủ cấu trúc chuẩn Laravel 12.
- Validate request đầy đủ trước khi thực thi logic nghiệp vụ.
- Với logic nhạy cảm (xác thực, thanh toán SePay, gọi OpenRouter AI), luôn có cơ chế bắt lỗi và fallback an toàn.
- **Không gọi `env()` ngoài file config**: Luôn truy xuất cấu hình thông qua helper `config('services.sepay.secret_key')`, `config('services.openrouter.key')`, v.v.
- Không hardcode API key, secret, mật khẩu hay endpoint trong source code.

---

## 8. Quy tắc nghiệp vụ theo module

### 8.1 Bài viết & Khối nội dung (Articles & Content Blocks)

- Bài viết lưu nội dung linh hoạt dưới dạng `content_json` qua `ArticleContentService`.
- Cấu trúc hỗ trợ các khối: đoạn văn (`paragraph`), tiêu đề (`heading`), và các cặp câu luyện tập song ngữ (`sentences` gồm `en` và `vi`).
- Quản lý danh mục nhiều-nhiều qua bảng pivot `article_category`.

### 8.2 Chế độ luyện Dictation

- Kiểm tra quyền truy cập bài viết thông qua middleware `dictation.limit`.
- Tài khoản Free bị giới hạn số lượt hoàn thành bài mỗi ngày; tài khoản Pro không giới hạn.
- Dữ liệu kết quả lưu vào `dictation_histories` bắt buộc có: `article_id`, `wpm`, `accuracy`, `completed_sentences`.

### 8.3 Trợ lý AI (AI Assistance & Chat Widget)

- Tích hợp qua `AiAssistantService` kết nối OpenRouter API.
- Áp dụng rate limiting `throttle:20,1` cho các request gọi AI từ phía client.
- Giới hạn tối đa 5 câu hỏi/phiên đối với AI Chat Assistant để tối ưu chi phí và tránh lạm dụng.
- Cấu hình trợ lý (prompt, model, bot name) có thể cập nhật linh hoạt từ trang quản trị Admin.

### 8.4 Sổ tay từ vựng (Vocabulary Notebook)

- Mỗi người dùng không được lưu trùng lặp cùng một từ vựng (`unique(['user_id', 'word'])`).
- Tính năng AI Translate tự động tra nghĩa tiếng Việt và câu ví dụ khi người dùng yêu cầu.

### 8.5 Thanh toán & Gói cước (SePay Checkout & Subscription)

- Luồng thanh toán chính thức sử dụng **SePay** (tham khảo đặc tả tại `docs/sepay.md`).
- Form thanh toán gửi sang SePay phải có chữ ký HMAC SHA256 hợp lệ.
- Webhook IPN (`POST /checkout/sepay/ipn`) phải xác thực secret key và kiểm tra khớp số tiền đơn hàng.
- Xử lý cập nhật subscription theo cơ chế idempotent: nếu IPN đã xử lý thành công thì không cộng dồn ngày thêm lần nữa.

### 8.6 Quản trị hệ thống (Admin & Superadmin)

- Phân quyền nghiêm ngặt giữa Superadmin (`role:superadmin`) và Admin thường (`role:superadmin,admin`).
- Chức năng quản lý tài khoản người quản trị (`/admin/users`) chỉ dành riêng cho Superadmin.
- Quản trị viên có quyền cập nhật trạng thái học viên (`active`/`locked`) và điều chỉnh thời hạn gói Pro trực tiếp.

---

## 9. Quy tắc bảo mật

- Bảo vệ route bằng middleware phù hợp (`auth`, `guest`, `role`, `throttle`, `dictation.limit`).
- Không trả thông tin nhạy cảm, exception trace hay khóa bí mật ra response hoặc client-side log.
- Dữ liệu đầu vào từ người dùng luôn phải validate và sanitize kỹ lưỡng.

---

## 10. Quy tắc kiểm thử và chất lượng mã

- Chạy toàn bộ test suite để đảm bảo không hồi quy:
  ```bash
  php artisan test
  ```
- Định dạng code PHP bằng Laravel Pint trước khi hoàn tất:
  ```bash
  vendor/bin/pint --dirty
  ```

---

## 11. Anti-pattern cần tránh

- Nhúng trực tiếp câu lệnh Eloquent query hoặc logic tính toán trong Blade view.
- Bỏ qua bước xác thực chữ ký số webhook IPN của SePay.
- Gọi trực tiếp hàm `env()` trong Controller hoặc Service.
- Tự ý bổ sung file `package.json` hoặc npm dependencies trái với quy tắc Tailwind CDN của dự án.
- Sửa đổi cấu trúc layout làm mất tính đồng nhất với template tĩnh trong thư mục `templates/`.