# Admin Sidebar Backup & Data Replication (Import / Export ZIP)

Status: proposed | Branch: main

## Outcome
1. Thêm một mục menu chuyên biệt "Sao lưu & Dữ liệu" trên sidebar của Admin.
2. Cho phép quản trị viên Export toàn bộ cơ sở dữ liệu và hình ảnh ra file `database.zip` và tải trực tiếp về máy từ trình duyệt.
3. Cho phép quản trị viên Import file `database.zip` từ máy tính lên để tự động khôi phục cơ sở dữ liệu và thư mục ảnh (tự động tạo bản sao lưu dữ liệu hiện tại trước khi import).
4. Xem danh sách lịch sử các bản sao lưu đã tạo trên hệ thống và tải về lại khi cần.

## Related Code Files
- Modify: `routes/web.php`
- Modify: `app/Services/DataReplicationService.php`
- Create: `app/Http/Requests/Admin/ImportBackupRequest.php`
- Create: `app/Http/Controllers/Admin/BackupController.php`
- Modify: `resources/views/components/admin/layout/sidebar.blade.php`
- Create: `resources/views/admin/backups/index.blade.php`
- Create: `tests/Feature/Admin/BackupTest.php`

## Acceptance Criteria
- [ ] Sidebar Admin xuất hiện menu "Sao lưu & Dữ liệu" dưới nhóm "HỆ THỐNG".
- [ ] Giao diện quản lý `/admin/backups` hiển thị thẻ Export, thẻ Import kéo thả file zip và danh sách các bản sao lưu gần nhất.
- [ ] Bấm nút Export tải về file `database.zip` đầy đủ dữ liệu các bảng DB và toàn bộ thư mục hình ảnh.
- [ ] Upload file `database.zip` hợp lệ phục hồi chính xác dữ liệu và hình ảnh, thông báo chi tiết kết quả.
- [ ] Phân quyền bảo mật (chặn tài khoản không có quyền truy cập).
- [ ] Feature tests Pest 3 pass 100%.
