@props([
	'active' => 'dashboard',
])

@php
	$user = auth()->user();
	$isSuperAdmin = $user?->role === 'superadmin';
	$initials = strtoupper(substr($user->name ?? 'A', 0, 1));
@endphp

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200"
	x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
	x-transition:leave="transition-opacity ease-linear duration-200"
	x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
	class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

{{-- Sidebar --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
	class="fixed inset-y-0 left-0 z-50 flex w-60 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto">

	{{-- Logo --}}
	<div class="px-4 py-4.5 border-b border-slate-700/60 flex items-center">
		<x-admin.layout.brand-logo theme="dark" size="md" />
	</div>

	{{-- Navigation --}}
	<nav class="flex-1 px-2.5 py-3 space-y-0.5 overflow-y-auto">
		{{-- Dashboard --}}
		<a href="{{ route('admin.dashboard') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'dashboard' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
			Tổng quan
		</a>

		{{-- NỘI DUNG --}}
		<div class="pt-4 pb-1 px-3">
			<p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider">Nội dung</p>
		</div>
		<a href="{{ route('admin.articles.index') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'articles' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
			Quản lý bài viết
		</a>
		<a href="{{ route('admin.categories.index') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'categories' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"/></svg>
			Danh mục
		</a>

		{{-- NGƯỜI DÙNG --}}
		<div class="pt-4 pb-1 px-3">
			<p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider">Người dùng</p>
		</div>
		<a href="{{ route('admin.clients.index') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'clients' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
			Học viên
		</a>
		@if($isSuperAdmin)
		<a href="{{ route('admin.users.index') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'users' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
			Quản trị viên
		</a>
		@endif

		{{-- DOANH THU --}}
		<div class="pt-4 pb-1 px-3">
			<p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider">Doanh thu</p>
		</div>
		<a href="{{ route('admin.transactions.index') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'transactions' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
			Giao dịch
		</a>
		<a href="{{ route('admin.plans.index') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'plans' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
			Gói cước
		</a>

		{{-- HỆ THỐNG --}}
		<div class="pt-4 pb-1 px-3">
			<p class="text-slate-500 text-[10px] font-semibold uppercase tracking-wider">Hệ thống</p>
		</div>
		<a href="{{ route('admin.ai-assistant.edit') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'ai-assistant' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18l-1.813-2.096A5.968 5.968 0 014.5 12c0-3.314 2.686-6 6-6s6 2.686 6 6-2.686 6-6 6a5.973 5.973 0 01-1.687-.244z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75h.008v.008h-.008V9.75zM9.75 9.75h.008v.008H9.75V9.75zM12 12.75a2.25 2.25 0 002.25-2.25h-4.5A2.25 2.25 0 0012 12.75z"/></svg>
			Trợ lý AI
		</a>
		<a href="{{ route('admin.settings.general.edit') }}"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer {{ $active === 'general-settings' ? 'bg-slate-700/80 text-white' : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-200' }}">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
			Cấu hình chung
		</a>
		<a href="{{ route('client.dashboard') }}" target="_blank" rel="noopener noreferrer"
			class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 cursor-pointer hover:bg-slate-800/60 text-slate-400 hover:text-slate-200">
			<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
			Khu học viên
		</a>
	</nav>

	{{-- User profile --}}
	<div class="px-3 py-3 border-t border-slate-700/60">
		<div class="flex items-center gap-2.5">
			<div class="w-8 h-8 rounded-full bg-brand flex items-center justify-center text-white text-xs font-bold shrink-0">{{ $initials }}</div>
			<div class="flex-1 min-w-0">
				<p class="text-[13px] text-white font-medium truncate">{{ $user->name ?? 'Admin' }}</p>
				<p class="text-[11px] text-slate-500 truncate">{{ $isSuperAdmin ? 'Super Admin' : 'Admin' }}</p>
			</div>
			<form action="{{ route('admin.auth.logout') }}" method="POST">
				@csrf
				<button type="submit" class="text-slate-500 hover:text-slate-300 transition-colors cursor-pointer" title="Đăng xuất">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
				</button>
			</form>
		</div>
	</div>
</aside>
