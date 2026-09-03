<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang chuyển hướng thanh toán...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex flex-col items-center justify-center">
    
    <div class="text-center max-w-md p-8 bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-emerald-100 mb-6">
            <svg class="w-8 h-8 text-emerald-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Đang kết nối bảo mật</h2>
        <p class="text-gray-500 text-sm">Hệ thống đang chuyển hướng bạn đến cổng thanh toán SePay. Vui lòng không đóng trình duyệt...</p>
    </div>

    <!-- Hidden Auto-submit form -->
    <form id="sepay-form" method="POST" action="{{ $actionUrl }}">
        @foreach($formFields as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        // Submit form ngay lập tức khi trang vừa load xong
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.getElementById('sepay-form').submit();
            }, 800); // Delay nhẹ 0.8s để UX trông mượt mà hơn
        });
    </script>
</body>
</html>
