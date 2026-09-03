{{-- SECTION 8: SEMANTIC COLOR SYSTEM SHOWCASE --}}
<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
    <div class="text-center">
      <h2 class="text-2xl sm:text-3xl font-bold text-text-primary">Hệ thống phân tích trực quan</h2>
      <p class="mt-3 text-base text-text-secondary max-w-2xl mx-auto leading-relaxed">
        Mỗi yếu tố trong bài mẫu được mã hóa bằng 4 màu sắc — giúp bạn nhìn ra ngay đâu là từ vựng ăn điểm,
        đâu là cấu trúc câu phức tạp, và đâu là lỗi cần tránh.
      </p>
    </div>

    <div class="grid sm:grid-cols-2 gap-5 mt-10">
      <!-- Green: Lexical -->
      <div class="flex items-start gap-4 bg-green-50 rounded-xl p-5">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-card flex-shrink-0">
          <span class="w-4 h-1 bg-semantic-green rounded-full" aria-hidden="true"></span>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-semantic-green">Từ vựng ăn điểm (Lexical Resource)</h3>
          <p class="text-sm text-text-secondary mt-1 leading-relaxed">Highlight các collocations, idioms, academic vocabulary đạt Band 7.0+.</p>
          <p class="text-xs text-text-disabled mt-2 italic">"fluctuated wildly", "a marked decline", "engaging in"</p>
        </div>
      </div>

      <!-- Purple: GRA -->
      <div class="flex items-start gap-4 bg-purple-50 rounded-xl p-5">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-card flex-shrink-0">
          <span class="w-4 h-1 bg-semantic-purple rounded-full" aria-hidden="true"></span>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-semantic-purple">Cấu trúc câu phức (Grammar Range)</h3>
          <p class="text-sm text-text-secondary mt-1 leading-relaxed">Câu điều kiện, mệnh đề quan hệ, câu ghép nâng cao giúp tăng GRA.</p>
          <p class="text-xs text-text-disabled mt-2 italic">"while sedentary behaviour, which had been predominant..."</p>
        </div>
      </div>

      <!-- Blue: CC -->
      <div class="flex items-start gap-4 bg-blue-50 rounded-xl p-5">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-card flex-shrink-0">
          <span class="w-4 h-1 bg-semantic-blue rounded-full" aria-hidden="true"></span>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-semantic-blue">Từ nối & Mạch lạc (Coherence)</h3>
          <p class="text-sm text-text-secondary mt-1 leading-relaxed">Linking words, signposting language, paragraph transitions giúp tăng CC.</p>
          <p class="text-xs text-text-disabled mt-2 italic">"Overall, it is evident that", "In addition", "Furthermore"</p>
        </div>
      </div>

      <!-- Red: Errors -->
      <div class="flex items-start gap-4 bg-red-50 rounded-xl p-5">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-card flex-shrink-0">
          <span class="w-4 h-1 bg-semantic-red rounded-full" aria-hidden="true"></span>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-semantic-red">Lỗi ngữ pháp & Chính tả</h3>
          <p class="text-sm text-text-secondary mt-1 leading-relaxed">Gạch chân đỏ các lỗi khi gõ sai — phản hồi tức thì trong phòng chép chính tả.</p>
          <p class="text-xs text-text-disabled mt-2 italic">"informations" → "information", "peoples" → "people"</p>
        </div>
      </div>
    </div>
  </div>
</section>
