<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'Đăng nhập — Dictation Lab' }}</title>
  <meta name="description" content="Đăng nhập vào Dictation Lab để tiếp tục luyện chép chính tả song ngữ." />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'brand': '#11A683','brand-dark': '#0D8A6B','brand-light': '#E8F7F3',
            'text-primary': '#0E101A','text-secondary': '#6D758D','text-disabled': '#B9BDC5',
            'border-light': '#E1E4E8','app-bg': '#F9F9FA',
          },
          fontFamily: {
            sans: ['Inter', 'Roboto', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
            brand: ['Plus Jakarta Sans', 'Inter', '-apple-system', 'sans-serif'],
          },
          boxShadow: {
            'card': '0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03)',
            'float': '0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)',
          },
        },
      },
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #0E101A; -webkit-font-smoothing: antialiased; }
    .input-field { transition: border-color 0.2s, box-shadow 0.2s; }
    .input-field:focus { border-color: #11A683; box-shadow: 0 0 0 3px rgba(17,166,131,0.15); outline: none; }
    .btn-social { transition: background-color 0.2s, border-color 0.2s, transform 0.15s; }
    .btn-social:hover { background-color: #F9F9FA; transform: translateY(-1px); }
  </style>
</head>
<body class="min-h-screen bg-app-bg flex flex-col">

  <header class="py-5 px-6 flex items-center justify-between max-w-7xl mx-auto w-full">
    <x-client.layout.brand-logo size="md" />
    {{ $headerRight ?? '' }}
  </header>

  <main class="flex-1 flex items-center justify-center px-4 py-10">
    {{ $slot }}
  </main>

  <footer class="py-6 text-center text-sm text-text-secondary">
    <p>&copy; {{ date('Y') }} Dictation Lab. All rights reserved.</p>
  </footer>

  <script>
    // Toggle password visibility
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
      togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle icon
        if (type === 'text') {
          togglePassword.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';
        } else {
          togglePassword.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        }
      });
    }
  </script>
</body>
</html>
