@props([
	'title' => 'Admin',
	'active' => 'dashboard',
])

<!doctype html>
<html lang="vi">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>{{ $title }} — Dictation Lab Admin</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet" />
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						brand: '#11A683',
						'brand-dark': '#0D8A6B',
						'brand-light': '#E8F7F3',
						'text-primary': '#0E101A',
						'text-secondary': '#6D758D',
						'text-disabled': '#B9BDC5',
						'border-light': '#E1E4E8',
						'app-bg': '#F9F9FA',
					},
					fontFamily: {
						sans: ['Inter', 'Roboto', 'ui-sans-serif', 'system-ui', 'sans-serif'],
						brand: ['Plus Jakarta Sans', 'Inter', '-apple-system', 'sans-serif'],
					},
					boxShadow: {
						'card': '0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03)',
						'float': '0 4px 6px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.1)',
					},
				}
			}
		}
	</script>
	<style>
		body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
		[x-cloak] { display: none !important; }
		/* Custom scrollbar for sidebar */
		aside nav::-webkit-scrollbar { width: 4px; }
		aside nav::-webkit-scrollbar-track { background: transparent; }
		aside nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
		/* Table row hover */
		.table-hover tbody tr { transition: background-color 0.15s ease; }
		.table-hover tbody tr:hover { background-color: #f8fafc; }
		/* Input focus */
		.input-admin:focus { border-color: #11A683; box-shadow: 0 0 0 3px rgba(17,166,131,0.12); outline: none; }
		/* Smooth transitions */
		.transition-admin { transition: all 0.15s ease; }
	</style>
</head>
<body class="font-sans bg-app-bg text-text-primary antialiased" x-data="{ sidebarOpen: false }">
	<div class="flex min-h-screen">
		<x-admin.layout.sidebar :active="$active" />

		{{-- Main content --}}
		<div class="flex-1 flex flex-col min-w-0">
			{{-- Header --}}
			<header class="sticky top-0 z-30 bg-white border-b border-border-light px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
				<div class="flex items-center gap-3">
					{{-- Mobile menu toggle --}}
					<button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1.5 rounded-lg hover:bg-gray-100 text-text-secondary cursor-pointer">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
					</button>
					<h1 class="text-base sm:text-lg font-bold text-text-primary truncate">{{ $title }}</h1>
				</div>
				<div class="flex items-center gap-3">
					<a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-brand transition-colors" title="Mở trang chủ trong tab mới">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
						<span class="hidden sm:inline">Trang chủ</span>
					</a>
					<a href="{{ route('client.dashboard') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-brand transition-colors" title="Mở khu học viên trong tab mới">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
						<span class="hidden sm:inline">Khu học viên</span>
					</a>
					<span class="hidden sm:inline text-xs text-text-disabled">{{ now()->format('d/m/Y') }}</span>
				</div>
			</header>

			{{-- Page content --}}
			<main class="flex-1 p-4 sm:p-6">
				{{-- Flash messages --}}
				@if (session('success'))
					<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
						x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
						x-transition:leave-end="opacity-0 -translate-y-2"
						class="mb-5 flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
						<svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
						<span>{{ session('success') }}</span>
					</div>
				@endif

				@if (session('error'))
					<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
						x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
						x-transition:leave-end="opacity-0 -translate-y-2"
						class="mb-5 flex items-center gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
						<svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
						<span>{{ session('error') }}</span>
					</div>
				@endif

				@if ($errors->any())
					<div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
						<ul class="list-disc list-inside space-y-0.5">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				{{ $slot }}
			</main>
		</div>
	</div>
</body>
</html>
