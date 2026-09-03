<style>
  [x-cloak] { display: none !important; }

  .ai-chat-markdown p { margin: 0.35rem 0; }
  .ai-chat-markdown p:first-child { margin-top: 0; }
  .ai-chat-markdown p:last-child { margin-bottom: 0; }
  .ai-chat-markdown ul,
  .ai-chat-markdown ol {
    margin: 0.35rem 0;
    padding-left: 1.1rem;
  }
  .ai-chat-markdown li { margin: 0.2rem 0; }
  .ai-chat-markdown strong { font-weight: 700; }
  .ai-chat-markdown a {
    color: #0e8a6d;
    text-decoration: underline;
  }
  .ai-chat-markdown code {
    background: #f1f5f9;
    border-radius: 0.3rem;
    padding: 0.1rem 0.3rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.85em;
  }
  .ai-chat-markdown pre {
    background: #0f172a;
    color: #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.6rem 0.75rem;
    overflow-x: auto;
  }
  .ai-chat-markdown pre code {
    background: transparent;
    color: inherit;
    padding: 0;
  }
</style>

<script>
  function loadExternalScriptOnce(selector, src) {
    if (document.querySelector(selector)) {
      return;
    }

    const script = document.createElement('script');
    script.defer = true;
    script.src = src;
    document.head.appendChild(script);
  }

  loadExternalScriptOnce('script[src*="alpinejs"]', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js');
  loadExternalScriptOnce('script[src*="marked.min.js"]', 'https://cdn.jsdelivr.net/npm/marked/marked.min.js');
  loadExternalScriptOnce('script[src*="purify.min.js"]', 'https://cdn.jsdelivr.net/npm/dompurify@3.1.7/dist/purify.min.js');
</script>

<div
  x-data="aiMiniChatWidget()"
  x-init="initialize()"
  x-cloak
  class="fixed bottom-5 right-5 z-50"
  x-show="enabled"
>
  <button
    type="button"
    @click="toggle()"
    class="w-14 h-14 rounded-full bg-brand text-white shadow-float hover:bg-brand-dark transition-colors flex items-center justify-center"
    :aria-label="open ? 'Đóng AI chat' : 'Mở AI chat'"
  >
    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v9a2 2 0 01-2 2h-4l-4 4v-4z"/></svg>
    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>

  <div
    x-show="open"
    x-transition
    class="absolute bottom-16 right-0 w-lg max-w-[calc(100vw-1.5rem)] bg-white border border-border-light rounded-2xl shadow-float overflow-hidden"
  >
    <div class="px-4 py-3 border-b border-border-light flex items-center justify-between gap-2 bg-app-bg">
      <div>
        <p class="text-sm font-semibold text-text-primary">AI trợ lý học tập</p>
        <p class="text-xs text-text-secondary" x-text="`Còn ${remainingQuestions}/${maxQuestions} câu hỏi trong cuộc hội thoại`"></p>
      </div>
      <button type="button" @click="startNewConversation()" class="text-xs font-medium text-brand hover:text-brand-dark transition-colors">
        Cuộc mới
      </button>
    </div>

    <div class="h-80 overflow-y-auto p-3 space-y-2" x-ref="messagesWrapper">
      <template x-for="(item, index) in messages" :key="index">
        <div :class="item.role === 'assistant' ? 'mr-8' : 'ml-8'">
          <div
            :class="item.role === 'assistant' ? 'bg-app-bg text-text-primary border border-border-light ai-chat-markdown' : 'bg-brand text-white'"
            class="rounded-xl px-3 py-2 text-sm leading-relaxed wrap-break-word"
            x-html="renderMessage(item)"
          ></div>
        </div>
      </template>

      <div x-show="loading" class="mr-8">
        <div class="bg-app-bg text-text-secondary border border-border-light rounded-xl px-3 py-2 text-sm">
          AI đang suy nghĩ...
        </div>
      </div>
    </div>

    <form @submit.prevent="sendMessage" class="p-3 border-t border-border-light">
      <div class="flex items-end gap-2">
        <textarea
          x-model="draft"
          :maxlength="maxInputChars"
          rows="2"
          class="flex-1 border border-border-light rounded-xl px-3 py-2 text-sm resize-none focus:border-brand focus:ring-2 focus:ring-brand/20"
          :placeholder="remainingQuestions > 0 ? 'Hỏi AI về bài học, cách tăng band...' : 'Bạn đã hết số câu hỏi cho cuộc hội thoại này'"
          :disabled="loading || remainingQuestions <= 0"
        ></textarea>
        <button
          type="submit"
          class="px-3 py-2 bg-brand text-white rounded-xl text-sm font-semibold hover:bg-brand-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="loading || remainingQuestions <= 0 || draft.trim().length === 0"
        >
          Gửi
        </button>
      </div>

      <div class="mt-1.5 flex items-center justify-between">
        <p class="text-[11px] text-text-secondary" x-text="statusText"></p>
        <p class="text-[11px] text-text-disabled" x-text="`${draft.length}/${maxInputChars}`"></p>
      </div>
    </form>
  </div>

  <script>
    function aiMiniChatWidget() {
      return {
        open: false,
        enabled: false,
        loading: false,
        maxQuestions: 5,
        maxInputChars: 500,
        welcomeMessage: '',
        remainingQuestions: 5,
        questionCount: 0,
        conversationId: '',
        draft: '',
        statusText: 'Sẵn sàng hỗ trợ bạn.',
        messages: [],

        csrfToken() {
          return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        async initialize() {
          this.conversationId = localStorage.getItem('ai_chat_conversation_id') || this.generateConversationId();
          localStorage.setItem('ai_chat_conversation_id', this.conversationId);

          await this.loadConfig();
          this.restoreConversation();
          this.scrollToBottom();
        },

        toggle() {
          this.open = !this.open;
          if (this.open) {
            this.$nextTick(() => this.scrollToBottom());
          }
        },

        async loadConfig() {
          try {
            const response = await fetch(@js(route('client.ai-chat.config')));
            const payload = await response.json();

            if (!response.ok || !payload?.success) {
              this.enabled = false;
              return;
            }

            this.enabled = !!payload.data.enabled;
            this.maxQuestions = Number(payload.data.max_questions || 5);
            this.maxInputChars = Number(payload.data.max_input_chars || 500);

            if (!this.enabled) {
              return;
            }

            this.welcomeMessage = String(payload.data.welcome_message || '').trim();
            if (!localStorage.getItem(this.storageKey('messages')) && this.welcomeMessage !== '') {
              this.messages = [{ role: 'assistant', content: this.welcomeMessage }];
              this.persistConversation();
            }
          } catch (error) {
            this.enabled = false;
          }
        },

        restoreConversation() {
          const storedQuestionCount = Number(localStorage.getItem(this.storageKey('question_count')) || 0);
          const storedMessages = localStorage.getItem(this.storageKey('messages'));

          this.questionCount = Number.isFinite(storedQuestionCount) ? storedQuestionCount : 0;
          this.remainingQuestions = Math.max(0, this.maxQuestions - this.questionCount);

          if (storedMessages) {
            try {
              const parsedMessages = JSON.parse(storedMessages);
              this.messages = Array.isArray(parsedMessages) ? parsedMessages : this.messages;
            } catch (error) {
              this.messages = this.messages;
            }
          }
        },

        async sendMessage() {
          const question = this.draft.trim();
          if (!question || this.loading || this.remainingQuestions <= 0) {
            return;
          }

          this.loading = true;
          this.statusText = 'Đang gửi câu hỏi...';
          this.messages.push({ role: 'user', content: question });
          this.draft = '';
          this.scrollToBottom();

          try {
            const response = await fetch(@js(route('client.ai-chat.message')), {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken(),
                'Accept': 'application/json',
              },
              body: JSON.stringify({
                conversation_id: this.conversationId,
                message: question,
              }),
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
              if (payload.limit_reached) {
                this.remainingQuestions = 0;
              }

              this.messages.push({
                role: 'assistant',
                content: payload.message || 'AI hiện không phản hồi được. Vui lòng thử lại.',
              });
              this.statusText = payload.message || 'AI hiện không phản hồi được.';
              this.persistConversation();
              this.scrollToBottom();
              return;
            }

            this.messages.push({ role: 'assistant', content: payload.assistant_message });
            this.questionCount = Number(payload.question_count || this.questionCount + 1);
            this.remainingQuestions = Number(payload.remaining_questions || 0);
            this.statusText = 'Sẵn sàng cho câu hỏi tiếp theo.';

            this.persistConversation();
            this.scrollToBottom();
          } catch (error) {
            this.messages.push({
              role: 'assistant',
              content: 'Lỗi kết nối. Vui lòng kiểm tra mạng và thử lại.',
            });
            this.statusText = 'Lỗi kết nối tới AI.';
            this.scrollToBottom();
          } finally {
            this.loading = false;
          }
        },

        async startNewConversation() {
          const oldConversationId = this.conversationId;

          try {
            await fetch(@js(route('client.ai-chat.reset')), {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken(),
                'Accept': 'application/json',
              },
              body: JSON.stringify({ conversation_id: oldConversationId }),
            });
          } catch (error) {
            // No-op: chat state will still be reset locally.
          }

          this.clearStorageKeys(oldConversationId);

          this.conversationId = this.generateConversationId();
          localStorage.setItem('ai_chat_conversation_id', this.conversationId);

          this.messages = this.welcomeMessage !== ''
            ? [{ role: 'assistant', content: this.welcomeMessage }]
            : [];
          this.questionCount = 0;
          this.remainingQuestions = this.maxQuestions;
          this.statusText = 'Đã tạo cuộc hội thoại mới.';
          this.persistConversation();
          this.scrollToBottom();
        },

        renderMessage(item) {
          const text = String(item?.content || '');

          if (item?.role === 'assistant') {
            return this.renderMarkdown(text);
          }

          return this.escapeHtml(text).replace(/\n/g, '<br>');
        },

        renderMarkdown(text) {
          if (window.marked?.parse && window.DOMPurify?.sanitize) {
            const parsed = window.marked.parse(text, {
              gfm: true,
              breaks: true,
            });

            return window.DOMPurify.sanitize(parsed, {
              USE_PROFILES: { html: true },
            });
          }

          return this.escapeHtml(text).replace(/\n/g, '<br>');
        },

        escapeHtml(text) {
          return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        },

        generateConversationId() {
          return `chat_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
        },

        storageKey(field) {
          return `ai_chat_${this.conversationId}_${field}`;
        },

        persistConversation() {
          localStorage.setItem(this.storageKey('messages'), JSON.stringify(this.messages.slice(-12)));
          localStorage.setItem(this.storageKey('question_count'), String(this.questionCount));
        },

        clearStorageKeys(conversationId) {
          localStorage.removeItem(`ai_chat_${conversationId}_messages`);
          localStorage.removeItem(`ai_chat_${conversationId}_question_count`);
        },

        scrollToBottom() {
          this.$nextTick(() => {
            if (this.$refs.messagesWrapper) {
              this.$refs.messagesWrapper.scrollTop = this.$refs.messagesWrapper.scrollHeight;
            }
          });
        },
      };
    }
  </script>
</div>
