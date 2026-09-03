@props([
	'title' => 'Admin Auth',
])

<!doctype html>
<html lang="vi">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>{{ $title }} — Dictation Lab</title>
	<script src="https://cdn.tailwindcss.com"></script>
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
					},
				}
			}
		}
	</script>
	<style>
		body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
		.input-auth:focus { border-color: #11A683; box-shadow: 0 0 0 3px rgba(17,166,131,0.15); outline: none; }
	</style>
</head>
<body class="font-sans bg-app-bg text-text-primary min-h-screen flex items-center justify-center px-4 py-8">
	<div class="w-full max-w-sm">
		{{-- Logo --}}
		<div class="text-center mb-8 flex flex-col items-center">
			<x-admin.layout.brand-logo theme="light" size="lg" badgeText="PORTAL" />
		</div>

		{{-- Flash messages --}}
		@if (session('success'))
			<div class="mb-4 flex items-center gap-2 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
				<svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				{{ session('success') }}
			</div>
		@endif

		@if ($errors->any())
			<div class="mb-4 flex items-center gap-2 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
				<svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				{{ $errors->first() }}
			</div>
		@endif

		{{-- Form card --}}
		<div class="bg-white border border-border-light rounded-2xl p-6 shadow-card">
			{{ $slot }}
		</div>

		<p class="text-[11px] text-text-disabled text-center mt-6">Trang quản trị nội bộ. Không dành cho người dùng phổ thông.</p>
	</div>
</body>
</html>
