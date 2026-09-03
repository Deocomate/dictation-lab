# Style Guidelines

Tài liệu này định nghĩa chuẩn giao diện và tương tác cho toàn bộ sản phẩm Lách IELTS.

## 1. Mục tiêu thiết kế

- Content-first: bài viết và phản hồi học thuật là trung tâm.
- Tối giản có chiều sâu: ít nhiễu thị giác nhưng vẫn có phân lớp rõ ràng.
- Nhất quán giữa client và admin: khác ngữ cảnh nhưng cùng hệ quy chiếu chất lượng.
- Dễ mở rộng: ưu tiên component hóa theo module và giữ parity với template gốc.

## 2. Nguyên tắc cốt lõi

1. Giữ cấu trúc template chuẩn, chỉ bind dữ liệu động bằng Blade.
2. Không dùng màu trang trí tùy hứng; màu phải có ý nghĩa ngữ nghĩa.
3. Trạng thái tương tác luôn rõ: default, hover, focus, disabled, loading, error, success.
4. Ưu tiên khả năng đọc dài: line-height thoáng, chiều rộng dòng hợp lý.
5. Chế độ Dictation và Mock Exam là desktop-first, không tối ưu cho thao tác mobile sâu.

## 3. Design tokens

### 3.1 Color tokens

#### Nền và phân lớp

- `--bg-app`: `#F9F9FA`
- `--bg-surface`: `#FFFFFF`
- `--border-subtle`: `#E1E4E8`

#### Typography

- `--text-primary`: `#0E101A`
- `--text-secondary`: `#6D758D`
- `--text-disabled`: `#B9BDC5`

#### Semantic trong học tập

- `--semantic-error`: `#FF5E5E` (lỗi ngữ pháp/chính tả)
- `--semantic-coherence`: `#007AFF` (liên kết, mạch lạc)
- `--semantic-lexical`: `#11A683` (từ vựng tốt)
- `--semantic-grammar-range`: `#8F00FF` (cấu trúc ngữ pháp nâng cao)
- `--semantic-pro`: `#FFD500` (Pro/premium/cảnh báo đặc thù)

### 3.2 Typography tokens

- Font chính: `Inter`, `Roboto`, `Segoe UI`, `system-ui`, `sans-serif`
- Font mono cho vùng gõ dictation (tùy ngữ cảnh): `JetBrains Mono`, `Fira Code`, `monospace`

Scale đề xuất:

- `h1`: 24px, weight 600, line-height 1.35
- `h2`: 18px, weight 600, line-height 1.4
- `body`: 16px, weight 400, line-height 1.65
- `caption`: 14px, weight 400, line-height 1.5

### 3.3 Spacing tokens

- Base spacing: 4px
- Các nấc dùng thường xuyên: 8, 12, 16, 20, 24, 32, 40, 48
- Khoảng cách giữa section lớn: tối thiểu 32px

### 3.4 Radius và shadow

- Radius control: 6px đến 10px
- Shadow card:
	- `0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03)`
- Shadow tooltip/modal:
	- `0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)`

## 4. Component guidelines

### 4.1 Button

- Primary: nền xanh lexical, chữ trắng, radius 8px.
- Secondary: nền xám nhạt hoặc trong suốt, chữ primary.
- Danger: nền semantic-error, chỉ dùng cho tác vụ phá hủy hoặc cảnh báo mạnh.
- Disabled: giảm tương phản, không có hover state giả.

### 4.2 Input/Form

- Label luôn hiển thị rõ ràng, không chỉ dựa vào placeholder.
- Hiển thị lỗi validation sát field, text ngắn và cụ thể.
- Focus ring nhất quán trên toàn hệ thống.

### 4.3 Card/Panel

- Dùng để nhóm thông tin cùng ngữ cảnh.
- Tránh nhồi quá nhiều thông tin trong 1 card.
- Ưu tiên chia block theo hành động của người dùng.

### 4.4 Tooltip/Annotation popup

- Chỉ hiển thị khi hover/focus vào text đã mapping.
- Nội dung ngắn, có cấu trúc: loại nhận xét -> giải thích -> ví dụ.
- Không che kín nội dung đang đọc.

## 5. Quy chuẩn màn hình theo module

### 5.1 Article Library & Dictation

- Thư viện bài viết hiển thị rõ ràng bộ lọc danh mục và huy hiệu phân biệt bài Free / Pro.
- Màn hình Dictation tập trung vào vùng nhập liệu trung tâm, thanh audio player dễ thao tác và chỉ báo câu đang luyện.
- Trạng thái đúng/sai phản hồi tức thời: ký tự đúng có màu primary/lexical, ký tự sai đổi sang semantic-error.
- Nút AI Explain đặt cạnh câu đang gõ, vùng giải thích phân tách rõ ràng giữa ngữ pháp, từ vựng và câu ví dụ.
- Kết quả cuối phiên luôn hiển thị đầy đủ WPM, Accuracy (%), số câu hoàn thành và nút điều hướng tiếp theo.

### 5.2 AI Chat Assistant Widget

- Widget nổi ở góc dưới bên phải màn hình (`fixed bottom-6 right-6 z-50`), không che khuất nội dung bài đọc hay vùng gõ phím.
- Cửa sổ chat có kích thước tối ưu, hiển thị tên bot, avatar, trạng thái kết nối và số câu hỏi khả dụng còn lại trong phiên.
- Hiệu ứng loading / typing indicator mượt mà, chữ hiển thị rõ ràng, hỗ trợ ngắt dòng chuẩn xác.

### 5.3 Sổ tay từ vựng & Dashboard

- Dashboard bố trí theo thứ tự: Thẻ chỉ số tổng quan (bài đã luyện, WPM, độ chính xác, streak) -> Lịch sử thanh toán.
- Trang sổ tay từ vựng (Vocabulary): Danh sách thẻ hoặc bảng hiển thị từ vựng, phiên âm/nghĩa, câu ví dụ song ngữ EN/VI kèm nút hành động (dịch AI, sửa nghĩa, xóa từ).

### 5.4 Thanh toán SePay (Checkout)

- Màn hình chọn gói: Làm nổi bật lợi ích gói Pro, giá tiền và chu kỳ thời gian.
- Màn hình chuyển hướng SePay (`sepay-redirect`): Hiển thị trạng thái chuyển tiếp mượt mà trong khi tự động POST form đã ký số sang cổng SePay.
- Màn hình trạng thái (Pending, Success, Failed): Hiển thị chi tiết mã giao dịch, số tiền, thông báo hướng dẫn và nút CTA quay về học tiếp hoặc về trang chủ, tránh dead-end UX.

### 5.5 Module quy hoạch tiếp theo (Roadmap: Analyze & Mock Exam)

- Analyze: Sample essay là vùng ưu tiên cao nhất, highlight gạch chân đúng semantic tag, sidebar giải thích band không lấn át bài đọc.
- Mock Exam: Layout 2 cột (đề bài và editor), đồng hồ đếm ngược và bộ đếm từ (word count) luôn hiển thị ở vị trí dễ quan sát.


## 6. Motion và phản hồi tương tác

- Transition chuẩn: 150ms đến 250ms, easing nhẹ.
- Tooltip fade-in có delay ngắn để giảm flicker.
- Tránh animation nặng gây lag trong màn hình có nhiều text.
- Không lạm dụng hiệu ứng trang trí trong ngữ cảnh học tập.

## 7. Responsive và nền tảng

- Desktop-first cho Dictation và Mock Exam.
- Mobile hỗ trợ tốt cho Home, Analyze, Dashboard, Billing.
- Với tính năng không hỗ trợ mobile sâu, hiển thị cảnh báo rõ ràng thay vì cho thao tác lỗi.

Breakpoint tham chiếu:

- `sm`: >= 640px
- `md`: >= 768px
- `lg`: >= 1024px
- `xl`: >= 1280px

## 8. Accessibility baseline

- Độ tương phản text và nền đạt mức dễ đọc.
- Có focus style cho phần tử tương tác bằng bàn phím.
- Icon đơn lẻ cần có label hoặc aria text khi cần.
- Không truyền tải thông tin chỉ bằng màu sắc.

## 9. Template parity policy

- Client views map từ `templates/`, `templates/dashboard/`, `templates/learning/`, `templates/checkout/`.
- Admin views map từ `templates/admin/`.
- Khi refactor UI, giữ parity về cấu trúc và style với template gốc.

## 10. Checklist nghiệm thu UI

1. Màn hình có đúng template nguồn và không phá layout.
2. Trạng thái loading/error/empty/success đã đầy đủ.
3. Màu semantic dùng đúng ngữ cảnh học thuật.
4. Responsive đạt cho các viewport mục tiêu.
5. Điều hướng và CTA chính dễ nhận diện.
6. Không có thành phần khó đọc hoặc nhiễu thị giác.