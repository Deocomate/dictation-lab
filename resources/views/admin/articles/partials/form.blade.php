@php
    $selectedCategoryIds = old('category_ids', $article?->categories->pluck('id')->all() ?? []);
    $initialBlocks = old('content', $article ? $article->contentBlocks() : []);
    $categoryOptions = $categories->map(fn ($cat) => [
        'id' => $cat->id,
        'name' => $cat->name,
        'slug' => $cat->slug,
    ])->values();
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6" @if(!$article) x-data="articleAiToolkit({{ Js::from($categoryOptions) }})" @endif>
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    @if(!$article)
        {{-- AI Import --}}
        <div class="rounded-xl border border-dashed border-purple-300 bg-purple-50/50 p-4 sm:p-5 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-text-primary flex items-center gap-2">
                        <span class="text-base">✨</span> Tạo bài bằng AI
                    </h3>
                    <p class="text-xs text-text-secondary mt-1 max-w-2xl">
                        Copy prompt gửi cho ChatGPT/Claude → nhận JSON → paste vào đây để tự điền form. Bạn vẫn có thể chỉnh sửa trước khi lưu.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <button type="button" @click="copyPrompt()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-purple-300 text-purple-700 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy AI Prompt
                    </button>
                    <button type="button" @click="pasteOpen = !pasteOpen"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-dashed border-purple-400 text-purple-700 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-text="pasteOpen ? 'Ẩn paste JSON' : 'Paste JSON AI'"></span>
                    </button>
                </div>
            </div>

            <p x-show="promptCopied" x-transition class="text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                ✓ Đã copy prompt vào clipboard — dán vào AI chat của bạn.
            </p>
            <p x-show="importSuccess" x-transition class="text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2" x-text="importSuccess"></p>
            <p x-show="importError" x-transition class="text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2" x-text="importError"></p>

            <div x-show="pasteOpen" x-cloak class="space-y-2">
                <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide">Dán JSON từ AI</label>
                <textarea x-model="pasteText" rows="8" placeholder='Dán JSON tại đây... (có thể bọc trong ```json)'
                    class="input-admin w-full border border-purple-200 rounded-lg px-3 py-2 text-xs font-mono leading-relaxed bg-white"></textarea>
                <button type="button" @click="applyImport()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors cursor-pointer">
                    Áp dụng vào form
                </button>
            </div>
        </div>

        <hr class="border-border-light" />
    @endif

    {{-- Thông tin cơ bản --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Thông tin cơ bản
        </legend>

        <div>
            <label for="title" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Tiêu đề bài viết <span class="text-red-500">*</span></label>
            <input id="title" name="title" value="{{ old('title', $article?->title) }}" placeholder="VD: Từ vựng chủ đề Công nghệ thông tin" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all" required />
            @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <span class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Danh mục <span class="text-red-500">*</span></span>
            <fieldset class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($categories as $cat)
                    <label class="flex items-center gap-2.5 p-3 rounded-lg border border-border-light hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                            data-category-slug="{{ $cat->slug }}"
                            data-category-name="{{ $cat->name }}"
                            @checked(in_array($cat->id, $selectedCategoryIds)) />
                        <span class="text-sm font-medium">{{ $cat->name }}</span>
                    </label>
                @endforeach
            </fieldset>
            @error('category_ids')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            @error('category_ids.*')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="excerpt" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Tóm tắt <span class="font-normal text-text-disabled">(tùy chọn)</span></label>
            <textarea id="excerpt" name="excerpt" rows="2" placeholder="Mô tả ngắn để hiển thị trên thẻ bài viết..." class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm transition-all leading-relaxed">{{ old('excerpt', $article?->excerpt) }}</textarea>
            @error('excerpt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
    </fieldset>

    <hr class="border-border-light" />

    {{-- Hình ảnh --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
            Hình ảnh bìa
        </legend>

        <div x-data="imageUpload()" x-init="init()">
            @if($article?->image_path)
                <div x-show="!removed && !newPreview" class="mb-3 relative inline-block group">
                    <img src="{{ Storage::disk('public')->url($article->image_path) }}" alt="Ảnh bìa" class="max-h-40 rounded-lg border border-border-light shadow-sm" />
                    <button type="button" @click="removeExisting()" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition-colors cursor-pointer opacity-0 group-hover:opacity-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <p class="text-xs text-text-disabled mt-1">Di chuột vào ảnh để xóa</p>
                </div>
                <input type="hidden" name="remove_image" :value="removed ? '1' : '0'" />
            @endif

            <div x-show="newPreview" class="mb-3 relative inline-block group">
                <img :src="newPreview" alt="Ảnh mới" class="max-h-40 rounded-lg border border-brand shadow-sm" />
                <button type="button" @click="clearNew()" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div x-show="!newPreview"
                 @dragover.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @drop.prevent="handleDrop($event)"
                 :class="dragging ? 'border-brand bg-brand-light' : 'border-border-light bg-gray-50/50'"
                 class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer hover:border-brand/50"
                 @click="$refs.fileInput.click()">
                <input type="file" name="image" accept="image/*" x-ref="fileInput" @change="handleFile($event)" class="hidden" />
                <svg class="w-8 h-8 mx-auto text-text-disabled mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                <p class="text-sm text-text-secondary">Kéo thả ảnh vào đây hoặc <span class="text-brand font-medium">nhấn để chọn</span></p>
                <p class="text-xs text-text-disabled mt-1">JPG, PNG, GIF, WebP — Tối đa 5MB</p>
            </div>
        </div>

        <script>
            function imageUpload() {
                return {
                    removed: false,
                    dragging: false,
                    newPreview: null,
                    init() {},
                    removeExisting() { this.removed = true; },
                    handleFile(e) {
                        const file = e.target.files[0];
                        if (file && file.type.startsWith('image/')) {
                            this.newPreview = URL.createObjectURL(file);
                            this.removed = false;
                        }
                    },
                    handleDrop(e) {
                        this.dragging = false;
                        const file = e.dataTransfer.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            this.$refs.fileInput.files = dt.files;
                            this.newPreview = URL.createObjectURL(file);
                            this.removed = false;
                        }
                    },
                    clearNew() {
                        this.newPreview = null;
                        this.$refs.fileInput.value = '';
                    }
                }
            }
        </script>
    </fieldset>

    <hr class="border-border-light" />

    {{-- Block Content Builder --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Nội dung bài (Block Builder)
        </legend>

        <p class="text-xs text-text-secondary">Thêm tiêu đề và câu song ngữ. Kéo thả biểu tượng ≡ để sắp xếp lại thứ tự block.</p>

        <div x-data="blockBuilder({{ Js::from($initialBlocks) }})"
             x-init="$nextTick(() => initSortable())"
             @article-blocks-import.window="importBlocks($event.detail.blocks)">
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" @click="addBlock('heading', 1)" class="inline-flex items-center gap-1.5 px-3 py-2 border border-dashed border-brand text-brand text-sm font-medium rounded-lg hover:bg-brand-light transition-colors cursor-pointer">
                    + Tiêu đề chính
                </button>
                <button type="button" @click="addBlock('heading', 2)" class="inline-flex items-center gap-1.5 px-3 py-2 border border-dashed border-brand text-brand text-sm font-medium rounded-lg hover:bg-brand-light transition-colors cursor-pointer">
                    + Tiêu đề phụ
                </button>
                <button type="button" @click="addBlock('heading', 3)" class="inline-flex items-center gap-1.5 px-3 py-2 border border-dashed border-brand text-brand text-sm font-medium rounded-lg hover:bg-brand-light transition-colors cursor-pointer">
                    + Tiêu đề nhỏ
                </button>
                <button type="button" @click="addBlock('sentence')" class="inline-flex items-center gap-1.5 px-3 py-2 border border-dashed border-brand text-brand text-sm font-medium rounded-lg hover:bg-brand-light transition-colors cursor-pointer">
                    + Câu song ngữ
                </button>
            </div>

            <div class="space-y-3" x-ref="list">
                <template x-for="(block, index) in blocks" :key="block.id">
                    <div class="rounded-xl border border-border-light bg-gray-50/40 p-4" :data-block-id="block.id">
                        <div class="flex items-start gap-3">
                            <button type="button" class="drag-handle mt-2 p-1 text-text-disabled hover:text-text-secondary cursor-grab active:cursor-grabbing shrink-0" title="Kéo để sắp xếp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 11-.001 4.001A2 2 0 017 2zm0 6a2 2 0 11-.001 4.001A2 2 0 017 8zm0 6a2 2 0 11-.001 4.001A2 2 0 017 14zm6-8a2 2 0 11-.001 4.001A2 2 0 0113 6zm0 2a2 2 0 11-.001 4.001A2 2 0 0113 8zm0 6a2 2 0 11-.001 4.001A2 2 0 0113 14z"/></svg>
                            </button>

                            <div class="flex-1 min-w-0 space-y-3">
                                <input type="hidden" :name="'content[' + index + '][type]'" :value="block.type" />

                                <template x-if="block.type === 'heading'">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-brand-light text-brand text-xs font-bold shrink-0" x-text="'H' + block.level"></span>
                                        <input type="hidden" :name="'content[' + index + '][level]'" :value="block.level" />
                                        <input type="text" :name="'content[' + index + '][text]'" x-model="block.text" placeholder="Nhập tiêu đề..." class="input-admin flex-1 border border-border-light rounded-lg px-3 py-2 text-sm transition-all" required />
                                    </div>
                                </template>

                                <template x-if="block.type === 'sentence'">
                                    <div class="grid md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-text-secondary mb-1">Tiếng Anh (EN) <span class="text-red-400">*</span></label>
                                            <textarea :name="'content[' + index + '][en]'" x-model="block.en" rows="2" placeholder="The quick brown fox..." class="input-admin w-full border border-border-light rounded-lg px-3 py-2 text-sm transition-all leading-relaxed" required></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-text-secondary mb-1">Tiếng Việt (VI) <span class="text-red-400">*</span></label>
                                            <textarea :name="'content[' + index + '][vi]'" x-model="block.vi" rows="2" placeholder="Con cáo nâu nhanh nhẹn..." class="input-admin w-full border border-border-light rounded-lg px-3 py-2 text-sm transition-all leading-relaxed" required></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <button type="button" @click="removeBlock(block.id)" class="mt-2 p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors cursor-pointer shrink-0" title="Xóa block">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <p x-show="blocks.length === 0" class="mt-2 text-xs text-text-disabled">Chưa có block nào. Nhấn nút phía trên để thêm nội dung.</p>
        </div>

        @error('content')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

        <script>
            function blockBuilder(initial) {
                return {
                    blocks: [],
                    sortable: null,
                    initFromInitial(raw) {
                        const items = Array.isArray(raw) && raw.length > 0 ? raw : [{ type: 'sentence', en: '', vi: '' }];
                        this.blocks = items.map(item => this.normalizeBlock(item));
                    },
                    normalizeBlock(item) {
                        if (item.type === 'heading') {
                            return {
                                id: crypto.randomUUID(),
                                type: 'heading',
                                level: parseInt(item.level, 10) || 1,
                                text: item.text ?? '',
                            };
                        }
                        return {
                            id: crypto.randomUUID(),
                            type: 'sentence',
                            en: item.en ?? '',
                            vi: item.vi ?? '',
                        };
                    },
                    addBlock(type, level) {
                        if (type === 'heading') {
                            this.blocks.push({
                                id: crypto.randomUUID(),
                                type: 'heading',
                                level: level || 1,
                                text: '',
                            });
                        } else {
                            this.blocks.push({
                                id: crypto.randomUUID(),
                                type: 'sentence',
                                en: '',
                                vi: '',
                            });
                        }
                        this.$nextTick(() => this.initSortable());
                    },
                    removeBlock(id) {
                        this.blocks = this.blocks.filter(b => b.id !== id);
                        this.$nextTick(() => this.initSortable());
                    },
                    importBlocks(raw) {
                        const items = Array.isArray(raw) && raw.length > 0 ? raw : [{ type: 'sentence', en: '', vi: '' }];
                        this.blocks = items.map(item => this.normalizeBlock(item));
                        this.$nextTick(() => this.initSortable());
                    },
                    initSortable() {
                        if (typeof Sortable === 'undefined' || !this.$refs.list) return;
                        if (this.sortable) {
                            this.sortable.destroy();
                            this.sortable = null;
                        }
                        this.sortable = Sortable.create(this.$refs.list, {
                            handle: '.drag-handle',
                            animation: 150,
                            onEnd: (evt) => {
                                const moved = this.blocks.splice(evt.oldIndex, 1)[0];
                                this.blocks.splice(evt.newIndex, 0, moved);
                            },
                        });
                    },
                    init() {
                        this.initFromInitial(initial);
                    },
                };
            }
        </script>
    </fieldset>

    @if(!$article)
        <script>
            function articleAiToolkit(categories) {
                return {
                    categories,
                    pasteOpen: false,
                    pasteText: '',
                    promptCopied: false,
                    importSuccess: '',
                    importError: '',
                    buildPrompt() {
                        const categoryLines = this.categories
                            .map(c => `  - ${c.name} → slug: "${c.slug}"`)
                            .join('\n');
                        const example = {
                            title: 'Morning Routine',
                            excerpt: 'Thói quen buổi sáng giúp bạn khởi đầu ngày mới hiệu quả.',
                            category_slugs: ['daily-life'],
                            is_premium: false,
                            status: 'draft',
                            content: [
                                { type: 'heading', level: 1, text: 'Morning Habits' },
                                { type: 'heading', level: 2, text: 'Wake up early' },
                                { type: 'sentence', en: 'I wake up at six every morning.', vi: 'Tôi thức dậy lúc sáu giờ mỗi sáng.' },
                                { type: 'sentence', en: 'First, I drink a glass of water.', vi: 'Đầu tiên, tôi uống một cốc nước.' },
                            ],
                        };
                        return `Bạn là trợ lý tạo nội dung bài viết cho **Dictation Lab** — ứng dụng luyện chép chính tả tiếng Anh (đọc nghĩa tiếng Việt, gõ lại câu tiếng Anh).

## Nhiệm vụ
Tạo MỘT bài viết theo chủ đề tôi mô tả ở cuối prompt. Trả về **CHỈ một object JSON hợp lệ** — không markdown, không giải thích, không \`\`\`json fence.

## Schema JSON (bắt buộc)
{
  "title": "string — tiêu đề bài (tiếng Anh hoặc song ngữ, max 255 ký tự)",
  "excerpt": "string — tóm tắt ngắn tiếng Việt (max 500 ký tự, tùy chọn)",
  "category_slugs": ["slug1", "slug2"] — ít nhất 1 slug từ danh sách bên dưới,
  "is_premium": false | true,
  "status": "draft" | "published",
  "content": [ ... blocks theo thứ tự đọc ... ]
}

## Block types trong content[]
1. **heading** — tiêu đề phân đoạn:
   { "type": "heading", "level": 1|2|3, "text": "Tieng Anh hoac Viet" }
   - level 1 = tiêu đề chính, 2 = tiêu đề phụ, 3 = tiêu đề nhỏ

2. **sentence** — câu chép chính tả (BAT BUOC co en + vi):
   { "type": "sentence", "en": "Cau tieng Anh.", "vi": "Ban dich tieng Viet." }

## Quy tắc nội dung
- Ít nhất 1 block type "sentence" với en và vi đều không rỗng
- Mỗi câu EN nên 8–25 từ, phù hợp luyện chép chính tả
- Dùng heading để chia section (H1 mở đầu, H2 cho từng phần)
- Viết tự nhiên, đúng ngữ pháp; dấu câu chuẩn tiếng Anh
- category_slugs: chọn 1–2 slug phù hợp nhất

## Danh mục có sẵn
${categoryLines}

## Ví dụ output
${JSON.stringify(example, null, 2)}

---
Chủ đề bài viết: [MO TA CHU DE CUA BAN TAI DAY — VD: "Du lich Da Nang, 6 cau, level B1"]`;
                    },
                    async copyPrompt() {
                        try {
                            await navigator.clipboard.writeText(this.buildPrompt());
                            this.promptCopied = true;
                            this.importError = '';
                            setTimeout(() => { this.promptCopied = false; }, 3500);
                        } catch (_) {
                            this.importError = 'Không copy được — hãy cho phép truy cập clipboard.';
                        }
                    },
                    parseJson(raw) {
                        let text = raw.trim();
                        text = text.replace(/^```(?:json)?\s*\n?/i, '').replace(/\n?```\s*$/i, '').trim();
                        return JSON.parse(text);
                    },
                    normalizeContentBlocks(content) {
                        return content.map(item => {
                            if (!item || typeof item !== 'object') return null;
                            if (!item.type && item.en !== undefined) {
                                return { type: 'sentence', en: String(item.en ?? ''), vi: String(item.vi ?? '') };
                            }
                            if (item.type === 'heading') {
                                return { type: 'heading', level: parseInt(item.level, 10) || 1, text: String(item.text ?? '') };
                            }
                            if (item.type === 'sentence') {
                                return { type: 'sentence', en: String(item.en ?? ''), vi: String(item.vi ?? '') };
                            }
                            return null;
                        }).filter(Boolean);
                    },
                    matchCategories(slugs) {
                        const values = (Array.isArray(slugs) ? slugs : []).map(s => String(s).toLowerCase().trim());
                        document.querySelectorAll('input[name="category_ids[]"]').forEach(cb => {
                            const slug = (cb.dataset.categorySlug || '').toLowerCase();
                            const name = (cb.dataset.categoryName || '').toLowerCase();
                            cb.checked = values.some(v => v === slug || v === name || slug.includes(v) || name.includes(v));
                        });
                    },
                    applyImport() {
                        this.importSuccess = '';
                        this.importError = '';
                        try {
                            const data = this.parseJson(this.pasteText);
                            if (!data.title || typeof data.title !== 'string') {
                                throw new Error('JSON thiếu trường "title".');
                            }
                            if (!Array.isArray(data.content) || data.content.length === 0) {
                                throw new Error('JSON thiếu mảng "content" hoặc rỗng.');
                            }
                            const blocks = this.normalizeContentBlocks(data.content);
                            const hasSentence = blocks.some(b => b.type === 'sentence' && b.en.trim() && b.vi.trim());
                            if (!hasSentence) {
                                throw new Error('Cần ít nhất 1 block sentence có en và vi.');
                            }

                            document.getElementById('title').value = data.title.trim();
                            document.getElementById('excerpt').value = (data.excerpt ?? '').trim();

                            if (data.status === 'published' || data.status === 'draft') {
                                document.getElementById('status').value = data.status;
                            }
                            if (data.is_premium !== undefined) {
                                document.getElementById('is_premium').value = data.is_premium ? '1' : '0';
                            }

                            const slugs = data.category_slugs ?? data.categories ?? [];
                            if (slugs.length > 0) {
                                this.matchCategories(slugs);
                            }

                            window.dispatchEvent(new CustomEvent('article-blocks-import', { detail: { blocks } }));

                            this.importSuccess = '✓ Đã điền form — kiểm tra danh mục, nội dung và lưu khi sẵn sàng.';
                            this.pasteOpen = false;
                            document.getElementById('title').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        } catch (e) {
                            this.importError = e instanceof SyntaxError
                                ? 'JSON không hợp lệ — kiểm tra dấu ngoặc và dấu phẩy.'
                                : (e.message || 'Import thất bại.');
                        }
                    },
                };
            }
        </script>
    @endif

    <hr class="border-border-light" />

    {{-- Phát hành --}}
    <fieldset class="space-y-4">
        <legend class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-2 mb-1">
            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Phát hành
        </legend>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label for="is_premium" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Phân loại truy cập</label>
                <select id="is_premium" name="is_premium" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm cursor-pointer transition-all" required>
                    <option value="0" @selected((string) old('is_premium', (int) ($article?->is_premium ?? 0)) === '0')>Free — Miễn phí</option>
                    <option value="1" @selected((string) old('is_premium', (int) ($article?->is_premium ?? 0)) === '1')>Pro — Trả phí</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Trạng thái</label>
                <select id="status" name="status" class="input-admin w-full border border-border-light rounded-lg px-3.5 py-2.5 text-sm cursor-pointer transition-all" required>
                    <option value="draft" @selected(old('status', $article?->status ?? 'draft') === 'draft')>Draft — Bản nháp</option>
                    <option value="published" @selected(old('status', $article?->status) === 'published')>Published — Đã xuất bản</option>
                </select>
            </div>
        </div>
    </fieldset>

    <div class="flex items-center gap-3 pt-4 border-t border-border-light">
        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand text-white text-sm font-medium rounded-lg hover:bg-brand-dark transition-colors cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Lưu bài viết
        </button>
        <a href="{{ route('admin.articles.index') }}" class="px-4 py-2.5 border border-border-light rounded-lg text-sm text-text-secondary hover:bg-gray-50 transition-colors cursor-pointer">Hủy</a>
    </div>
</form>
