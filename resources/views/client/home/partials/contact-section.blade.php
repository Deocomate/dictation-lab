<section id="contact" class="py-16 sm:py-24 bg-app-bg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Liên hệ hỗ trợ</h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Cần trao đổi thêm về sản phẩm cuối môn? Kết nối trực tiếp với Nhóm 18 - Bất Cần Đời qua các kênh dưới đây.
      </p>
    </div>

    <div class="grid lg:grid-cols-5 gap-6 mt-10">
      <div class="lg:col-span-2 bg-white border border-border-light rounded-2xl p-6 shadow-card reveal">
        <h3 class="text-lg font-semibold text-text-primary">Thông tin liên hệ</h3>
        <ul class="mt-5 space-y-3 text-sm text-text-secondary">
          <li><strong class="text-text-primary">Số điện thoại:</strong> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['hotline'] ?? '') }}" class="hover:text-text-primary transition-colors">{{ $contact['hotline'] ?? '-' }}</a></li>
          <li><strong class="text-text-primary">Email:</strong> <a href="mailto:{{ $contact['email'] ?? '' }}" class="hover:text-text-primary transition-colors">{{ $contact['email'] ?? '-' }}</a></li>
          <li>
            <strong class="text-text-primary">Facebook:</strong>
            <a href="{{ $contact['facebook_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="hover:text-text-primary transition-colors">
              Trang Facebook nhóm
            </a>
          </li>
          <li><strong class="text-text-primary">Nhóm thực hiện:</strong> {{ $contact['team_name'] ?? '-' }}</li>
        </ul>

        <div class="mt-6 pt-5 border-t border-border-light grid grid-cols-2 gap-3">
          <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['hotline'] ?? '') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-app-bg border border-border-light rounded-lg text-sm font-medium text-text-primary hover:bg-white transition-colors">Gọi điện</a>
          <a href="mailto:{{ $contact['email'] ?? '' }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-app-bg border border-border-light rounded-lg text-sm font-medium text-text-primary hover:bg-white transition-colors">Gửi email</a>
        </div>
      </div>

      <div class="lg:col-span-3 bg-white border border-border-light rounded-2xl p-6 shadow-card reveal" style="transition-delay: 0.1s">
        <h3 class="text-lg font-semibold text-text-primary">Thông tin sản phẩm</h3>
        <ul class="mt-5 space-y-3 text-sm text-text-secondary">
          <li><strong class="text-text-primary">Tên nhóm:</strong> {{ $contact['team_name'] ?? '-' }}</li>
          <li><strong class="text-text-primary">Founder:</strong> {{ $contact['founder'] ?? '-' }}</li>
          <li><strong class="text-text-primary">Co-Founder:</strong> {{ $contact['co_founder'] ?? '-' }}</li>
          <li><strong class="text-text-primary">Môn:</strong> {{ $contact['course'] ?? '-' }}</li>
          <li><strong class="text-text-primary">Hạng mục:</strong> {{ $contact['project_type'] ?? '-' }}</li>
        </ul>
      </div>
    </div>
  </div>
</section>
