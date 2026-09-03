<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center reveal">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Khám phá hệ thống theo từng trang chi tiết</h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Thay vì dồn toàn bộ nội dung vào một landing page, bạn có thể đi thẳng vào phần mình cần:
        giới thiệu hệ thống, chức năng học tập, hoặc gói dịch vụ và liên hệ.
      </p>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-12">
      <a href="{{ route('client.home.about') }}" class="group bg-app-bg rounded-2xl border border-border-light p-6 shadow-card hover:shadow-card-hover transition-all duration-200 reveal">
        <div class="w-11 h-11 bg-brand-light rounded-xl flex items-center justify-center mb-4">
          <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.06.852l-.708 2.836a.75.75 0 001.06.852l.041-.02M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-text-primary">Giới thiệu hệ thống</h3>
        <p class="mt-2 text-sm text-text-secondary leading-relaxed">
          Hiểu ứng dụng giải quyết vấn đề gì, vì sao nên chọn nền tảng này và câu chuyện người sáng lập.
        </p>
        <p class="mt-4 text-sm font-medium text-brand group-hover:text-brand-dark transition-colors duration-200">Xem chi tiết</p>
      </a>

      <a href="{{ route('client.home.features') }}" class="group bg-app-bg rounded-2xl border border-border-light p-6 shadow-card hover:shadow-card-hover transition-all duration-200 reveal" style="transition-delay: 0.1s">
        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center mb-4">
          <svg class="w-5 h-5 text-semantic-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h9A2.25 2.25 0 0118.75 6v12A2.25 2.25 0 0116.5 20.25h-9A2.25 2.25 0 015.25 18V6A2.25 2.25 0 017.5 3.75zm1.5 4.5h6m-6 3h6m-6 3h4.5" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-text-primary">Chức năng & trải nghiệm</h3>
        <p class="mt-2 text-sm text-text-secondary leading-relaxed">
          Xem trực quan toàn bộ tính năng: chép chính tả song ngữ, thư viện đa chủ đề, sổ từ vựng và AI dịch từ.
        </p>
        <p class="mt-4 text-sm font-medium text-semantic-blue group-hover:text-blue-700 transition-colors duration-200">Xem chi tiết</p>
      </a>

      <a href="{{ route('client.home.pricing-contact') }}" class="group bg-app-bg rounded-2xl border border-border-light p-6 shadow-card hover:shadow-card-hover transition-all duration-200 reveal" style="transition-delay: 0.2s">
        <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center mb-4">
          <svg class="w-5 h-5 text-semantic-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M4.5 4.5h15A2.25 2.25 0 0121.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15A2.25 2.25 0 012.25 17.25V6.75A2.25 2.25 0 014.5 4.5z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-text-primary">Gói & liên hệ</h3>
        <p class="mt-2 text-sm text-text-secondary leading-relaxed">
          So sánh bảng giá, biết gói nào phù hợp và nhận đầy đủ kênh liên hệ: địa chỉ, map, Facebook, Zalo.
        </p>
        <p class="mt-4 text-sm font-medium text-semantic-purple group-hover:text-purple-700 transition-colors duration-200">Xem chi tiết</p>
      </a>
    </div>
  </div>
</section>
