# Triển khai lên Coolify & Replica dữ liệu

## 1. Kiến trúc Docker

- `Dockerfile`: 1 container `php:8.2-apache`, build code + `composer install
  --no-dev`, không có bước Vite/npm (đúng quy tắc Tailwind CDN + Vanilla JS
  tại `docs/rules.md`).
- `docker-compose.yml`: 2 service — `app` và `mariadb` (MariaDB chạy trong
  cùng compose, có volume riêng, không phụ thuộc DB resource ngoài của
  Coolify).
- `docker/entrypoint.sh`: mỗi lần container khởi động sẽ chạy
  `storage:link`, cache config/route/view, và `migrate --force` (tắt bằng
  env `RUN_MIGRATIONS=false` nếu cần deploy mà không migrate).

### Vì sao dùng `expose` thay vì `ports`

Server Coolify chạy nhiều dự án cùng lúc trên cùng một Docker host. Nếu khai
báo `ports: - "8080:80"`, container sẽ chiếm cứng port `8080` trên host —
dễ đụng port với project khác. `docker-compose.yml` ở đây chỉ khai báo
`expose:` cho cả `app` (80) và `mariadb` (3306): container không publish port
nào ra host, chỉ mở trong internal Docker network của compose. Proxy có sẵn
của Coolify (Traefik) tự route vào `app` qua network đó dựa trên FQDN bạn cấu
hình trong Coolify UI — không cần chọn port thủ công, không thể đụng port với
project khác trên cùng server.

## 2. Cấu hình trên Coolify

1. Tạo Application mới trên Coolify → chọn **Docker Compose** làm build
   pack, trỏ tới repo này (file `docker-compose.yml` ở root).
2. Tab **Environment Variables**: nhập toàn bộ biến trong `.env.example`
   (APP_KEY sinh bằng `php artisan key:generate --show`, `DB_DATABASE`,
   `DB_USERNAME`, `DB_PASSWORD`, `MARIADB_ROOT_PASSWORD`, các key
   SePay/OpenRouter/Google/Facebook...). Coolify sẽ ghi các biến này thành
   file `.env` cạnh `docker-compose.yml` lúc deploy — đúng với những gì
   `env_file: .env` trong compose cần.
3. Đặt `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` là domain thật.
4. Tab **Domains**: gán FQDN cho service `app`. Coolify tự cấu hình Traefik
   + SSL (Let's Encrypt) trỏ vào container qua port 80 nội bộ (không phải
   port publish ra host).
5. Deploy. Lần đầu container sẽ tự `migrate --force` để tạo schema.
6. Volume `mariadb_data` và `storage_app_public` được Coolify giữ lại qua
   các lần deploy tiếp theo (không mất dữ liệu/ảnh khi redeploy).

## 3. Replica dữ liệu (DB + ảnh) giữa local và server

Hai lệnh artisan dùng để đóng gói toàn bộ dữ liệu DB (trừ các bảng hệ thống
tạm thời) + toàn bộ ảnh trong `storage/app/public` thành **1 file zip**, để
đồng bộ nhanh giữa local và server.

### Export

```bash
php artisan replica:export
# hoặc chỉ định nơi lưu:
php artisan replica:export --output=/tmp/replica.zip
```

Kết quả: 1 file zip gồm `db/<table>.json` cho từng bảng dữ liệu, thư mục
`images/` (bản sao `storage/app/public`), và `manifest.json` (thời gian
export, số dòng mỗi bảng).

Các bảng bị loại trừ vì mang tính chất tạm/per-environment (cấu hình tại
`config/replica.php` → `excluded_tables`): `migrations`, `sessions`,
`cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`,
`password_reset_tokens`.

### Import (merge, không ghi đè toàn bộ)

```bash
php artisan replica:import /path/to/replica.zip
```

- Dòng DB có primary key trùng với dữ liệu đích → bị **ghi đè** bằng dữ liệu
  trong file import.
- Dòng DB / file ảnh chỉ tồn tại ở phía đích (không có trong file import) →
  **giữ nguyên**, không bị xoá.
- File ảnh trùng đường dẫn tương đối → bị ghi đè bởi file trong gói import;
  file khác đường dẫn → giữ nguyên.
- Trước khi ghi đè bất kỳ gì, lệnh **tự động backup** dữ liệu hiện tại (đúng
  quy tắc "luôn backup trước khi thay đổi hàng loạt") vào
  `storage/app/private/replica-backups/pre-import-*.zip`. Bỏ qua bằng
  `--skip-backup` nếu chắc chắn không cần.
- Chạy trên `APP_ENV=production` sẽ hỏi xác nhận trừ khi thêm `--force`
  (giống hành vi `migrate --force`).

### Ví dụ luồng đồng bộ server → local

```bash
# Trên server (qua SSH vào container, hoặc Coolify's terminal):
php artisan replica:export --output=storage/app/private/replica-exports/server.zip
# Tải file zip đó về máy local, rồi:
php artisan replica:import /path/to/server.zip
```

Chiều ngược lại (local → server) làm tương tự.
