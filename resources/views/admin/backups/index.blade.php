<x-admin.layout.app title="Sao lưu & Phục hồi dữ liệu" active="backups">
	<div class="w-full max-w-full space-y-6" x-data="{
		isExporting: false,
		exportSuccess: false,
		isImporting: false,
		selectedFile: null,
		selectedFileSize: '',
		extractedTime: null,
		autoBackup: true,
		confirmModalOpen: false,
		async exportDatabase() {
			if (this.isExporting) return;
			this.isExporting = true;
			this.exportSuccess = false;
			try {
				const response = await fetch('{{ route('admin.backups.export') }}', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});

				if (!response.ok) {
					throw new Error('Máy chủ phản hồi mã lỗi: ' + response.status);
				}

				const disposition = response.headers.get('Content-Disposition');
				let filename = 'database.zip';
				if (disposition && disposition.indexOf('filename=') !== -1) {
					const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
					if (matches && matches[1]) {
						filename = matches[1].replace(/['"]/g, '');
					}
				}

				const blob = await response.blob();
				const downloadUrl = window.URL.createObjectURL(blob);
				const link = document.createElement('a');
				link.href = downloadUrl;
				link.download = filename;
				document.body.appendChild(link);
				link.click();
				window.URL.revokeObjectURL(downloadUrl);
				link.remove();

				this.isExporting = false;
				this.exportSuccess = true;
				setTimeout(() => {
					this.exportSuccess = false;
					window.location.reload();
				}, 1500);
			} catch (err) {
				alert('Có lỗi xảy ra khi xuất dữ liệu: ' + err.message);
				this.isExporting = false;
				this.exportSuccess = false;
			}
		},
		extractExportTime(filename) {
			if (!filename) return null;
			let m = filename.match(/(\d{4})-(\d{2})-(\d{2})[_T-](\d{2})[-:]?(\d{2})[-:]?(\d{2})?/);
			if (m) {
				return `${m[4]}:${m[5]}:${m[6] || '00'} ngày ${m[3]}/${m[2]}/${m[1]}`;
			}
			m = filename.match(/(\d{4})(\d{2})(\d{2})[-_](\d{2})(\d{2})(\d{2})/);
			if (m) {
				return `${m[4]}:${m[5]}:${m[6]} ngày ${m[3]}/${m[2]}/${m[1]}`;
			}
			m = filename.match(/(\d{2})-(\d{2})-(\d{4})[_T-](\d{2})[-:]?(\d{2})[-:]?(\d{2})?/);
			if (m) {
				return `${m[4]}:${m[5]}:${m[6] || '00'} ngày ${m[1]}/${m[2]}/${m[3]}`;
			}
			return null;
		},
		handleFileSelect(e) {
			const files = e.target.files || e.dataTransfer.files;
			if (!files.length) return;
			const file = files[0];
			if (!file.name.toLowerCase().endsWith('.zip')) {
				alert('Vui lòng chỉ chọn tệp định dạng .zip');
				return;
			}
			this.selectedFile = file.name;
			this.selectedFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
			this.extractedTime = this.extractExportTime(file.name);
		},
		clearFile() {
			this.selectedFile = null;
			this.selectedFileSize = '';
			this.extractedTime = null;
			const input = document.getElementById('backup_file_input');
			if (input) input.value = '';
		}
	}">

		{{-- Stats banner --}}
		<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
			<div class="bg-white border border-border-light rounded-xl p-5 shadow-card flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-brand-light flex items-center justify-center text-brand shrink-0">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Bảng dữ liệu</p>
					<p class="text-2xl font-bold text-text-primary mt-0.5">{{ $stats['table_count'] }} <span class="text-xs font-normal text-text-secondary">bảng</span></p>
				</div>
			</div>

			<div class="bg-white border border-border-light rounded-xl p-5 shadow-card flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Hình ảnh lưu trữ</p>
					<p class="text-2xl font-bold text-text-primary mt-0.5">{{ number_format($stats['image_count']) }} <span class="text-xs font-normal text-text-secondary">tệp</span></p>
				</div>
			</div>

			<div class="bg-white border border-border-light rounded-xl p-5 shadow-card flex items-center gap-4">
				<div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Bản sao lưu trên server</p>
					<p class="text-2xl font-bold text-text-primary mt-0.5">{{ count($recentBackups) }} <span class="text-xs font-normal text-text-secondary">bản</span></p>
				</div>
			</div>
		</div>

		{{-- Main Actions: 2 Cards --}}
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

			{{-- CARD 1: EXPORT --}}
			<div class="bg-white border border-border-light rounded-xl shadow-card p-6 flex flex-col justify-between">
				<div>
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
						</div>
						<div>
							<h2 class="text-base font-bold text-text-primary">Xuất dữ liệu hệ thống (Export)</h2>
							<p class="text-xs text-text-secondary">Đóng gói toàn bộ cơ sở dữ liệu và hình ảnh ra file ZIP</p>
						</div>
					</div>

					<div class="mt-5 space-y-3 text-sm text-text-secondary">
						<p>Chức năng tự động đóng gói toàn bộ nội dung của ứng dụng thành tệp zip kèm mã ngày giờ (ví dụ: <code class="text-xs bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded font-mono">database_{{ now()->format('Y-m-d_H-i-s') }}.zip</code>):</p>
						<ul class="space-y-2 text-xs text-slate-600 bg-slate-50 border border-slate-200/80 rounded-lg p-3.5">
							<li class="flex items-start gap-2">
								<svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
								<span><strong>Cơ sở dữ liệu:</strong> Toàn bộ {{ $stats['table_count'] }} bảng (bài viết, người dùng, từ vựng, giao dịch, v.v.) được lưu dưới dạng JSON chuẩn.</span>
							</li>
							<li class="flex items-start gap-2">
								<svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
								<span><strong>Tệp đính kèm:</strong> Toàn bộ {{ number_format($stats['image_count']) }} hình ảnh bài viết và tài nguyên trong thư mục lưu trữ.</span>
							</li>
							<li class="flex items-start gap-2">
								<svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
								<span><strong>Mã thời gian:</strong> Tên tệp mang ngày tháng năm và giờ phút giây giúp dễ dàng nhận diện và kiểm tra khi khôi phục.</span>
							</li>
						</ul>
					</div>
				</div>

				<div class="mt-6 pt-5 border-t border-border-light">
					<button type="button" @click="exportDatabase()" :disabled="isExporting"
						:class="exportSuccess ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-brand hover:bg-brand-dark'"
						class="w-full inline-flex items-center justify-center gap-2 text-white rounded-lg px-4 py-2.5 text-sm font-semibold transition-all shadow-sm cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
						<template x-if="!isExporting && !exportSuccess">
							<span class="inline-flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
								Tải về tệp sao lưu (.zip)
							</span>
						</template>
						<template x-if="isExporting">
							<span class="inline-flex items-center gap-2">
								<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
								Đang nén dữ liệu & chuẩn bị tải về...
							</span>
						</template>
						<template x-if="exportSuccess">
							<span class="inline-flex items-center gap-2">
								<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
								Đã xuất và tải về thành công!
							</span>
						</template>
					</button>
				</div>
			</div>

			{{-- CARD 2: IMPORT --}}
			<div class="bg-white border border-border-light rounded-xl shadow-card p-6 flex flex-col justify-between">
				<div>
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
						</div>
						<div>
							<h2 class="text-base font-bold text-text-primary">Khôi phục dữ liệu (Import)</h2>
							<p class="text-xs text-text-secondary">Nhập cơ sở dữ liệu và hình ảnh từ file zip lên hệ thống</p>
						</div>
					</div>

					<form id="import-form" method="POST" action="{{ route('admin.backups.import') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
						@csrf

						{{-- Dropzone / File picker --}}
						<div class="relative border-2 border-dashed rounded-xl p-5 text-center transition-all cursor-pointer hover:border-brand/60 bg-slate-50/50"
							:class="selectedFile ? 'border-brand bg-emerald-50/30' : 'border-slate-300'"
							@dragover.prevent=""
							@drop.prevent="handleFileSelect($event)"
							@click="$refs.fileInput.click()">

							<input type="file" id="backup_file_input" x-ref="fileInput" name="file" accept=".zip" class="hidden" @change="handleFileSelect($event)" />

							<template x-if="!selectedFile">
								<div class="space-y-1">
									<svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/></svg>
									<p class="text-xs font-semibold text-text-primary">Kéo thả tệp sao lưu <span class="text-brand">.zip</span> vào đây</p>
									<p class="text-[11px] text-text-secondary">hoặc nhấp chuột để chọn tệp từ máy tính (Tối đa 500MB)</p>
								</div>
							</template>

							<template x-if="selectedFile">
								<div class="space-y-2.5 text-left">
									<div class="flex items-center justify-between gap-3">
										<div class="flex items-center gap-2.5 min-w-0">
											<div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
											</div>
											<div class="min-w-0">
												<p class="text-xs font-bold text-text-primary truncate" x-text="selectedFile"></p>
												<p class="text-[11px] text-text-secondary" x-text="selectedFileSize"></p>
											</div>
										</div>
										<button type="button" @click.stop="clearFile()" class="p-1 rounded-md text-slate-400 hover:text-red-500 hover:bg-white transition-colors" title="Bỏ chọn tệp">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
										</button>
									</div>

									{{-- Timestamp extraction badge --}}
									<template x-if="extractedTime">
										<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-800 font-medium">
											<svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
											<span>Thời điểm xuất bản: <strong x-text="extractedTime"></strong></span>
										</div>
									</template>
									<template x-if="!extractedTime">
										<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 border border-amber-200 text-[11px] text-amber-800">
											<svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
											<span>Tên tệp không chứa mã thời gian tiêu chuẩn.</span>
										</div>
									</template>
								</div>
							</template>
						</div>

						@error('file')
							<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
						@enderror

						{{-- Backup option --}}
						<div class="flex items-center gap-2.5 px-1">
							<input type="checkbox" id="auto_backup_checkbox" x-model="autoBackup"
								class="w-4 h-4 rounded text-brand focus:ring-brand border-slate-300 cursor-pointer" />
							<input type="hidden" name="skip_backup" :value="autoBackup ? '0' : '1'" />
							<label for="auto_backup_checkbox" class="text-xs text-text-primary font-medium cursor-pointer select-none">
								Tự động sao lưu dữ liệu hiện tại trước khi nhập <span class="text-emerald-600 font-semibold">(Khuyên dùng)</span>
							</label>
						</div>

						<p class="text-[11px] text-text-secondary px-1">
							Dữ liệu trong tệp zip sẽ được hợp nhất (upsert): các bản ghi trùng mã sẽ được cập nhật mới, các dữ liệu chỉ có riêng ở hệ thống này sẽ được giữ nguyên an toàn.
						</p>
					</form>
				</div>

				<div class="mt-6 pt-5 border-t border-border-light">
					<button type="button" :disabled="!selectedFile || isImporting" @click="confirmModalOpen = true"
						class="w-full inline-flex items-center justify-center gap-2 bg-amber-600 text-white rounded-lg px-4 py-2.5 text-sm font-semibold hover:bg-amber-700 transition-all shadow-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
						<template x-if="!isImporting">
							<span class="inline-flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
								Khôi phục dữ liệu từ file
							</span>
						</template>
						<template x-if="isImporting">
							<span class="inline-flex items-center gap-2">
								<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
								Đang xử lý khôi phục dữ liệu...
							</span>
						</template>
					</button>
				</div>
			</div>
		</div>

		{{-- RECENT BACKUPS TABLE --}}
		<div class="bg-white border border-border-light rounded-xl shadow-card overflow-hidden">
			<div class="px-5 py-4 border-b border-border-light flex items-center justify-between">
				<div>
					<h3 class="text-sm font-bold text-text-primary">Lịch sử bản sao lưu & tệp xuất trên Server</h3>
					<p class="text-xs text-text-secondary mt-0.5">Các bản sao lưu tự động trước khi import và các tệp xuất dữ liệu gần đây</p>
				</div>
				<span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full">
					{{ count($recentBackups) }} tệp
				</span>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse table-hover">
					<thead>
						<tr class="bg-slate-50 border-b border-border-light text-[11px] uppercase tracking-wider text-slate-500 font-semibold">
							<th class="py-3 px-5">Tên tệp</th>
							<th class="py-3 px-4">Loại bản ghi</th>
							<th class="py-3 px-4">Dung lượng</th>
							<th class="py-3 px-4">Thời gian tạo</th>
							<th class="py-3 px-5 text-right">Thao tác</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-border-light text-xs">
						@forelse($recentBackups as $backup)
							<tr class="transition-colors">
								<td class="py-3 px-5 font-mono text-slate-800 font-medium">
									<div class="flex items-center gap-2">
										<svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
										<span class="truncate max-w-xs sm:max-w-md" title="{{ $backup['filename'] }}">{{ $backup['filename'] }}</span>
									</div>
								</td>
								<td class="py-3 px-4 whitespace-nowrap">
									@if($backup['type'] === 'backup')
										<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
											<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
											Sao lưu tự động
										</span>
									@else
										<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
											<span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
											Tệp xuất thủ công
										</span>
									@endif
								</td>
								<td class="py-3 px-4 whitespace-nowrap font-medium text-slate-600">
									{{ $backup['size_formatted'] }}
								</td>
								<td class="py-3 px-4 whitespace-nowrap text-slate-500">
									{{ \App\Helpers\FormatHelper::dateTime($backup['created_at']) }}
								</td>
								<td class="py-3 px-5 text-right whitespace-nowrap">
									<div class="inline-flex items-center gap-2">
										{{-- Download button --}}
										<a href="{{ route('admin.backups.download', ['filename' => $backup['filename']]) }}"
											class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-brand hover:text-white border border-brand hover:bg-brand rounded-lg transition-colors"
											title="Tải tệp này về máy">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
											Tải về
										</a>

										{{-- Delete button --}}
										<form method="POST" action="{{ route('admin.backups.destroy', ['filename' => $backup['filename']]) }}"
											onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản sao lưu này khỏi server không?');" class="inline">
											@csrf
											@method('DELETE')
											<button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors cursor-pointer" title="Xóa tệp">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
											</button>
										</form>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="py-10 text-center text-slate-400">
									<div class="flex flex-col items-center justify-center gap-2">
										<svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
										<p class="text-sm font-medium">Chưa có bản sao lưu nào được lưu trên máy chủ.</p>
										<p class="text-xs text-slate-400">Các bản sao lưu tự động hoặc tệp xuất sẽ hiển thị tại đây.</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		{{-- CONFIRMATION MODAL --}}
		<div x-show="confirmModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
			x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
			x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

			<div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-float border border-border-light space-y-4" @click.outside="confirmModalOpen = false">
				<div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
				</div>

				<div class="text-center">
					<h3 class="text-base font-bold text-text-primary">Xác nhận khôi phục dữ liệu?</h3>
					<p class="text-xs text-text-secondary mt-1.5 leading-relaxed">
						Hệ thống sẽ đồng bộ các bản ghi cơ sở dữ liệu và thư mục hình ảnh theo tệp:
						<br><span class="inline-block mt-1 font-mono font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px]" x-text="selectedFile"></span>
					</p>

					{{-- Exported timestamp notification --}}
					<template x-if="extractedTime">
						<div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-200 text-left space-y-1">
							<div class="flex items-center gap-1.5 text-blue-900 font-bold text-xs">
								<svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
								<span>Thời điểm tệp được xuất bản:</span>
							</div>
							<p class="text-xs text-blue-800 font-bold pl-5" x-text="extractedTime"></p>
							<p class="text-[11px] text-blue-700/80 pl-5">Vui lòng kiểm tra ngày giờ để tránh khôi phục nhầm dữ liệu cũ.</p>
						</div>
					</template>

					<template x-if="!extractedTime">
						<div class="mt-3 p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[11px] text-left flex items-start gap-2">
							<svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
							<span>Tên tệp không chứa mã ngày giờ. Hệ thống sẽ đọc toàn bộ dữ liệu có trong tệp zip này.</span>
						</div>
					</template>

					<template x-if="autoBackup">
						<div class="mt-2.5 p-2.5 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] text-left flex items-start gap-2 border border-emerald-100">
							<svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
							<span>Hệ thống sẽ tự động tạo một bản sao lưu an toàn của dữ liệu hiện tại trước khi thực hiện.</span>
						</div>
					</template>
				</div>

				<div class="flex items-center gap-3 pt-2">
					<button type="button" @click="confirmModalOpen = false"
						class="flex-1 py-2 px-4 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
						Hủy bỏ
					</button>
					<button type="button" @click="confirmModalOpen = false; isImporting = true; document.getElementById('import-form').submit()"
						class="flex-1 py-2 px-4 rounded-lg bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 transition-colors cursor-pointer">
						Tôi chắc chắn, tiến hành
					</button>
				</div>
			</div>
		</div>

	</div>
</x-admin.layout.app>
