  <!-- ============================================================
       SECTION 13: FOOTER
       ============================================================ -->
  <footer class="py-12 bg-app-bg border-t border-border-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Brand -->
        <div>
          <x-client.layout.brand-logo size="sm" variant="subtle" />
          <p class="mt-3 text-sm text-text-secondary leading-relaxed max-w-xs">
            Sản phẩm cuối môn Thiết kế và PTHTTM MOOCS do Nhóm 18 - Bất Cần Đời thực hiện.
          </p>
        </div>

        <!-- Product -->
        <div>
          <h4 class="text-sm font-semibold text-text-primary mb-4">Sản phẩm</h4>
          <ul class="space-y-2.5">
            <li><a href="{{ route('home') }}" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Trang chủ</a></li>
            <li><a href="{{ route('client.home.about') }}" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Giới thiệu hệ thống</a></li>
            <li><a href="{{ route('client.home.features') }}" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Chức năng chi tiết</a></li>
            <li><a href="{{ route('client.home.pricing-contact') }}" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Gói & Liên hệ</a></li>
            <li><a href="{{ route('register') }}" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Đăng ký</a></li>
            <li><a href="{{ route('login') }}" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Đăng nhập</a></li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h4 class="text-sm font-semibold text-text-primary mb-4">Hỗ trợ</h4>
          <ul class="space-y-2.5">
            <li><a href="{{ route('client.home.pricing-contact') }}#contact" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Liên hệ hỗ trợ</a></li>
            <li><a href="mailto:thoer197765@gmail.com" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">thoer197765@gmail.com</a></li>
            <li><a href="tel:0868061598" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">0868061598</a></li>
            <li><a href="https://www.facebook.com/share/14d8fGTUimW/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Facebook</a></li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h4 class="text-sm font-semibold text-text-primary mb-4">Pháp lý</h4>
          <ul class="space-y-2.5">
            <li><a href="/terms" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Điều khoản sử dụng</a></li>
            <li><a href="/privacy" class="text-sm text-text-secondary hover:text-text-primary transition-colors duration-200 cursor-pointer">Chính sách bảo mật</a></li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="mt-10 pt-6 border-t border-border-light flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-text-disabled">&copy; {{ date('Y') }} Dictation Lab. All rights reserved.</p>
        <div class="flex items-center gap-4">
          <!-- Social: Facebook -->
          <a href="https://www.facebook.com/share/14d8fGTUimW/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="text-text-disabled hover:text-text-secondary transition-colors duration-200 cursor-pointer" aria-label="Facebook">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>
          </a>
          <!-- Social: Phone -->
          <a href="tel:0868061598" class="text-text-disabled hover:text-text-secondary transition-colors duration-200 cursor-pointer" aria-label="Phone">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 4.5A2.25 2.25 0 014.5 2.25h2.386a2.25 2.25 0 012.197 1.766l.487 2.194a2.25 2.25 0 01-.648 2.151l-1.054 1.054a13.5 13.5 0 006.363 6.363l1.054-1.054a2.25 2.25 0 012.151-.648l2.194.487A2.25 2.25 0 0121.75 17.114V19.5a2.25 2.25 0 01-2.25 2.25h-.75C9.775 21.75 2.25 14.225 2.25 5.25V4.5z" /></svg>
          </a>
          <!-- Social: Email -->
          <a href="mailto:thoer197765@gmail.com" class="text-text-disabled hover:text-text-secondary transition-colors duration-200 cursor-pointer" aria-label="Email">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
          </a>
        </div>
      </div>
    </div>
  </footer>