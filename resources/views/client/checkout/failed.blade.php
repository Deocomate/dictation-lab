<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thanh toán thất bại — Dictation Lab</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{colors:{'brand':'#11A683','brand-dark':'#0E8A6D','brand-light':'#E8F8F3','semantic-red':'#FF5E5E','text-primary':'#0E101A','text-secondary':'#6D758D','text-disabled':'#B9BDC5','border-light':'#E1E4E8','app-bg':'#F9F9FA'},fontFamily:{sans:['Inter','Roboto','sans-serif']},boxShadow:{'float':'0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)'}}}}</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>body{font-family:'Inter',sans-serif;color:#0E101A;-webkit-font-smoothing:antialiased;}</style>
</head>
<body class="bg-app-bg min-h-screen flex flex-col">

  <main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full bg-white rounded-2xl border border-border-light p-8 shadow-float text-center">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg class="w-8 h-8 text-semantic-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </div>
      <h1 class="text-2xl font-black text-text-primary">Thanh toán thất bại</h1>
      <p class="text-sm text-text-secondary mt-2">Giao dịch không thành công. Vui lòng thử lại hoặc liên hệ hỗ trợ.</p>

      <div class="mt-6 bg-app-bg rounded-xl p-4 text-left space-y-2">
        <p class="text-xs font-semibold text-text-primary">Một số nguyên nhân phổ biến:</p>
        <ul class="text-xs text-text-secondary space-y-1">
          <li class="flex items-start gap-2">
            <svg class="w-3.5 h-3.5 text-semantic-red flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            Số dư tài khoản không đủ
          </li>
          <li class="flex items-start gap-2">
            <svg class="w-3.5 h-3.5 text-semantic-red flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            Phiên thanh toán đã hết hạn
          </li>
          <li class="flex items-start gap-2">
            <svg class="w-3.5 h-3.5 text-semantic-red flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            Lỗi kết nối mạng
          </li>
        </ul>
      </div>

      <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('client.checkout') }}" class="flex-1 py-3 bg-brand text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors text-center cursor-pointer">Thử lại</a>
        <a href="{{ route('client.dashboard') }}" class="flex-1 py-3 border border-border-light text-sm font-medium text-text-secondary rounded-xl hover:bg-app-bg transition-colors text-center cursor-pointer">Về Dashboard</a>
      </div>
    </div>
  </main>
</body>
</html>
