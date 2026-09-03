<x-client.layout.dashboard title="Sổ tay từ vựng" activePage="vocabulary">
  <x-slot:headerContent>
    <div>
      <h1 class="text-lg font-bold text-text-primary">Sổ tay từ vựng</h1>
      <p class="text-xs text-text-secondary">{{ $totalCount }} từ/cụm từ đã lưu</p>
    </div>
  </x-slot:headerContent>
  <x-slot:headerActions>
    <form method="GET" action="{{ route('client.vocabulary') }}" class="hidden sm:flex items-center gap-2">
      <input name="search" type="text" value="{{ request('search') }}" placeholder="Tìm từ vựng..."
        class="px-3 py-1.5 bg-app-bg border border-border-light rounded-lg text-xs text-text-primary placeholder-text-disabled w-48" />
      <button type="submit" class="px-3 py-1.5 bg-brand text-white text-xs font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">Tìm</button>
    </form>
  </x-slot:headerActions>

  {{-- Mobile search --}}
  <form method="GET" action="{{ route('client.vocabulary') }}" class="sm:hidden flex items-center gap-2 mb-4">
    <input name="search" type="text" value="{{ request('search') }}" placeholder="Tìm từ vựng..."
      class="flex-1 px-3 py-2 bg-white border border-border-light rounded-lg text-sm text-text-primary placeholder-text-disabled" />
    <button type="submit" class="px-3 py-2 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors cursor-pointer">Tìm</button>
  </form>

  @if($vocabularies->isEmpty())
    <div class="bg-white rounded-xl border border-border-light p-10 text-center">
      <div class="w-14 h-14 bg-brand-light rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
      </div>
      <h3 class="font-semibold text-text-primary mb-1">Chưa có từ vựng nào</h3>
      <p class="text-sm text-text-secondary mb-4">Bôi đen từ trong lịch sử câu khi luyện bài để lưu vào đây.</p>
      <a href="{{ route('client.articles.library') }}" class="inline-flex px-4 py-2 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-dark transition-colors">Bắt đầu học ngay</a>
    </div>
  @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($vocabularies as $vocab)
        <div class="bg-white rounded-xl border border-border-light p-4 hover:shadow-card transition-shadow" id="vocab-card-{{ $vocab->id }}">
          {{-- Header: word + actions --}}
          <div class="flex items-start justify-between mb-2 gap-2">
            <h3 class="font-semibold text-text-primary text-sm leading-tight">{{ $vocab->word }}</h3>
            <div class="flex items-center gap-1.5 flex-shrink-0">
              @if($isPro)
                <button
                  id="ai-btn-{{ $vocab->id }}"
                  onclick="aiTranslate({{ $vocab->id }}, '{{ route('client.vocabulary.translate', $vocab->id) }}')"
                  class="text-[11px] text-semantic-purple hover:text-purple-700 font-medium transition-colors cursor-pointer whitespace-nowrap"
                  title="Dịch bằng AI">
                  ✨ AI dịch
                </button>
              @endif
              <form method="POST" action="{{ route('client.vocabulary.destroy', $vocab->id) }}" onsubmit="return confirm('Xóa từ này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-text-disabled hover:text-semantic-red transition-colors cursor-pointer" title="Xóa">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </form>
            </div>
          </div>

          {{-- Meaning (inline editable) --}}
          <div id="meaning-display-{{ $vocab->id }}" class="flex items-start gap-1.5 mb-2">
            <p class="text-xs text-text-secondary flex-1" id="meaning-text-{{ $vocab->id }}">{{ $vocab->meaning ?: '—' }}</p>
            <button
              onclick="startEditMeaning({{ $vocab->id }})"
              class="text-[10px] text-text-disabled hover:text-brand transition-colors cursor-pointer flex-shrink-0 mt-0.5"
              title="Sửa nghĩa">
              Sửa
            </button>
          </div>
          <div id="meaning-edit-{{ $vocab->id }}" class="hidden mb-2">
            <input
              type="text"
              id="meaning-input-{{ $vocab->id }}"
              value="{{ $vocab->meaning }}"
              placeholder="Nhập nghĩa..."
              class="w-full text-xs border border-border-light rounded-lg px-2.5 py-1.5 text-text-primary placeholder-text-disabled focus:border-brand focus:ring-1 focus:ring-brand/20 outline-none transition-all"
              onkeydown="if(event.key==='Enter'){saveMeaning({{ $vocab->id }},'{{ route('client.vocabulary.update', $vocab->id) }}')}else if(event.key==='Escape'){cancelEditMeaning({{ $vocab->id }})}"
            />
            <div class="flex gap-1.5 mt-1.5">
              <button
                onclick="saveMeaning({{ $vocab->id }}, '{{ route('client.vocabulary.update', $vocab->id) }}')"
                class="text-[11px] text-white bg-brand px-2.5 py-1 rounded-md hover:bg-brand-dark transition-colors cursor-pointer font-medium">
                Lưu
              </button>
              <button
                onclick="cancelEditMeaning({{ $vocab->id }})"
                class="text-[11px] text-text-secondary hover:text-text-primary transition-colors cursor-pointer">
                Hủy
              </button>
            </div>
          </div>

          {{-- Sentence context: sentence_en / sentence_vi --}}
          @if($vocab->sentence_en)
            <div class="border-l-2 border-brand-light pl-2.5 mt-2">
              <p class="text-xs text-text-primary italic leading-relaxed">{{ $vocab->sentence_en }}</p>
              @if($vocab->sentence_vi)
                <p class="text-xs text-text-secondary mt-0.5 leading-relaxed">{{ $vocab->sentence_vi }}</p>
              @endif
            </div>
          @endif

          {{-- Source article --}}
          @if($vocab->article)
            <p class="text-[10px] text-text-disabled mt-2 truncate">Từ bài: {{ $vocab->article->title }}</p>
          @endif
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $vocabularies->links() }}
    </div>
  @endif

  <script>
    const VOCAB_CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function startEditMeaning(id) {
      document.getElementById('meaning-display-' + id).classList.add('hidden');
      const editEl = document.getElementById('meaning-edit-' + id);
      editEl.classList.remove('hidden');
      document.getElementById('meaning-input-' + id).focus();
    }

    function cancelEditMeaning(id) {
      document.getElementById('meaning-edit-' + id).classList.add('hidden');
      document.getElementById('meaning-display-' + id).classList.remove('hidden');
    }

    function saveMeaning(id, url) {
      const input = document.getElementById('meaning-input-' + id);
      const value = input.value.trim();

      fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': VOCAB_CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ meaning: value }),
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          document.getElementById('meaning-text-' + id).textContent = data.meaning || '—';
          cancelEditMeaning(id);
        }
      })
      .catch(() => cancelEditMeaning(id));
    }

    function aiTranslate(id, url) {
      const btn = document.getElementById('ai-btn-' + id);
      btn.disabled = true;
      btn.textContent = '⏳';

      fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': VOCAB_CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = '✨ AI dịch';
        if (data.success && data.meaning) {
          document.getElementById('meaning-text-' + id).textContent = data.meaning;
          document.getElementById('meaning-input-' + id).value = data.meaning;
        } else if (data.message) {
          alert(data.message);
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.textContent = '✨ AI dịch';
      });
    }
  </script>
</x-client.layout.dashboard>
