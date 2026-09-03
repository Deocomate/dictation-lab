@props(['title' => 'Dashboard', 'activePage' => 'dashboard'])

<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }} — Dictation Lab</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'app-bg': '#F9F9FA', 'surface': '#FFFFFF', 'border-light': '#E1E4E8',
            'text-primary': '#0E101A', 'text-secondary': '#6D758D', 'text-disabled': '#B9BDC5',
            'brand': '#11A683', 'brand-dark': '#0E8A6D', 'brand-light': '#E8F8F3',
            'semantic-red': '#FF5E5E', 'semantic-blue': '#007AFF', 'semantic-purple': '#8F00FF', 'semantic-yellow': '#FFD500',
          },
          fontFamily: {
            sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
            brand: ['Plus Jakarta Sans', 'Inter', '-apple-system', 'sans-serif'],
            mono: ['JetBrains Mono', 'Fira Code', 'Consolas', 'monospace'],
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <style>
    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', sans-serif; color: #0E101A; background-color: #F9F9FA; -webkit-font-smoothing: antialiased; }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 3px; }
    .sidebar-link { transition: all 0.15s; }
    .sidebar-link.active { background: #E8F8F3; color: #11A683; font-weight: 600; }
    .sidebar-link.active svg { color: #11A683; }
    .sidebar-link:hover:not(.active) { background: #F9F9FA; }
    .mobile-overlay { display: none; } .mobile-overlay.open { display: block; }
    .mobile-sidebar { transform: translateX(-100%); transition: transform 0.25s ease; } .mobile-sidebar.open { transform: translateX(0); }
    input:focus, select:focus, textarea:focus { border-color: #11A683; box-shadow: 0 0 0 3px rgba(17,166,131,0.15); outline: none; }
    @media (prefers-reduced-motion: reduce) { .sidebar-link { transition: none; } .mobile-sidebar { transition: none; } }
  </style>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  {{ $head ?? '' }}
</head>
<body class="bg-app-bg flex h-screen overflow-hidden">

  {{-- Mobile sidebar overlay --}}
  <div id="mobileOverlay" class="mobile-overlay fixed inset-0 bg-black/40 z-40 lg:hidden" onclick="closeMobileMenu()"></div>

  {{-- Mobile sidebar --}}
  <aside id="mobileSidebar" class="mobile-sidebar fixed inset-y-0 left-0 w-64 bg-white z-50 lg:hidden flex flex-col shadow-float">
    @include('components.client.layout.partials.sidebar-content', ['activePage' => $activePage])
  </aside>

  {{-- Desktop sidebar --}}
  <aside class="hidden lg:flex flex-col w-60 bg-white border-r border-border-light flex-shrink-0">
    @include('components.client.layout.partials.sidebar-content', ['activePage' => $activePage])
  </aside>

  {{-- Main content --}}
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <header class="bg-white border-b border-border-light px-6 py-3.5 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-3">
        <button onclick="openMobileMenu()" class="lg:hidden text-text-secondary hover:text-text-primary cursor-pointer">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        {{ $headerContent ?? '' }}
      </div>
      <div class="flex items-center gap-3">
        {{ $headerActions ?? '' }}
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6">
      @if(session('success'))
        <div class="mb-4 p-4 bg-brand-light border border-brand/20 text-brand rounded-xl text-sm" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false, 4000)">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-semantic-red rounded-xl text-sm" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false, 4000)">
          {{ session('error') }}
        </div>
      @endif
      {{ $slot }}
    </main>
  </div>

  <script>
    function openMobileMenu(){ document.getElementById('mobileSidebar').classList.add('open'); document.getElementById('mobileOverlay').classList.add('open'); }
    function closeMobileMenu(){ document.getElementById('mobileSidebar').classList.remove('open'); document.getElementById('mobileOverlay').classList.remove('open'); }
  </script>
  <x-client.layout.ai-chat-widget />
  {{ $scripts ?? '' }}
</body>
</html>
