# Luồng thanh toán Cổng Thanh Toán SePay

## Tìm hiểu luồng thanh toán one-time và recurring trong Cổng thanh toán SePay — chọn mô hình tích hợp phù hợp cho website hoặc app của bạn.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.se    pay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

## Thanh toán một lần (One-time Payment)

Luồng thanh toán một lần cho phép khách hàng thanh toán ngay lập tức cho một đơn hàng cụ thể.

<Mermaid title="Trình tự xử lý thanh toán">
sequenceDiagram
  participant C as Khách hàng
  participant M as Website Merchant
  participant S as SePay Gateway
  participant B as Ngân hàng/Thẻ

  C->>M: 1. Chọn sản phẩm & thanh toán
  M->>M: 2. Tạo đơn hàng
  M->>M: 3. Tạo form checkout với signature
  M->>S: 4. POST /v1/checkout/init
  S->>S: 5. Xác thực signature
  S->>C: 6. Chuyển hướng đến trang thanh toán

  C->>S: 7. Chọn phương thức thanh toán
  S->>B: 8. Xử lý thanh toán
  B->>S: 9. Kết quả thanh toán

  alt Thanh toán thành công
      S->>M: 10a. Callback success_url
      S->>M: 11a. IPN notification
      S->>C: 12a. Chuyển hướng về trang thành công
  else Thanh toán thất bại
      S->>M: 10b. Callback error_url
 S->>C: 12b. Chuyển hướng về trang lỗi
  else Khách hàng hủy
      S->>M: 10c. Callback cancel_url
      S->>C: 11c. Chuyển hướng về trang hủy
  end

  M->>C: 13. Hiển thị kết quả cuối cùng
</Mermaid>

### Các bước chi tiết:

1. **Khách hàng chọn sản phẩm**: Thêm vào giỏ hàng và click "Thanh toán"
2. **Website tạo đơn hàng**: Lưu thông tin đơn hàng vào database
3. **Tạo form checkout**: Chuẩn bị dữ liệu và tạo signature HMAC-SHA256
4. **Gửi request đến SePay**: POST form đến endpoint `/v1/checkout/init`
5. **Xác thực signature**: SePay kiểm tra tính hợp lệ của chữ ký
6. **Chuyển hướng khách hàng**: Đến trang thanh toán của SePay
7. **Chọn phương thức thanh toán**: Thẻ, QR Banking, QR NAPAS
8. **Xử lý thanh toán**: SePay giao tiếp với ngân hàng/thẻ
9. **Nhận kết quả**: Từ hệ thống ngân hàng
10. **Callback về website**: Gọi các URL callback tương ứng
11. **IPN notification**: Thông báo kết quả thanh toán qua IPN
12. **Chuyển hướng khách hàng**: Về trang kết quả trên website
13. **Hiển thị kết quả**: Trang thành công/lỗi/hủy

***

## Thanh toán định kỳ (Recurring Payment)

<Callout type="info" title="Tính năng sắp ra mắt">
Thanh toán định kỳ hiện đang trong giai đoạn hoàn thiện và sẽ được phát hành trong thời gian tới.
Vui lòng theo dõi cập nhật từ SePay để biết thời điểm ra mắt chính thức.
</Callout>

# Bắt đầu nhanh với SePay Cổng Thanh Toán

## Bắt đầu Cổng thanh toán SePay trong vài phút: tạo đơn hàng đầu tiên, xử lý IPN callback, go-live thanh toán chuyển khoản và thẻ ngay hôm nay.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

<iframe width="100%" height="400" src="https://www.youtube.com/embed/RZnw2VU5J9U" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />

## Chức năng chính

* **Xử lý thanh toán**: Tiếp nhận thông tin thanh toán từ khách hàng
* **Bảo mật giao dịch**: Mã hóa và bảo vệ dữ liệu thanh toán
* **Kết nối ngân hàng**: Giao tiếp với các ngân hàng và tổ chức thẻ
* **Thông báo kết quả**: Gửi thông tin giao dịch về hệ thống của bạn

***

## Luồng hoạt động tổng quan

<Mermaid title="Luồng thanh toán">
flowchart LR
A[Khách hàng] -->|1. Chọn sản phẩm| B[Website/App của bạn]
B -->|2. Tạo đơn hàng| C[SePay Gateway]
C -->|3. Hiển thị trang thanh toán| A
A -->|4. Thanh toán| C
C -->|5. Xử lý giao dịch| D[Ngân hàng/Thẻ]
D -->|6. Kết quả| C
C -->|7. Thông báo #40;IPN#41;| B
C -->|8. Chuyển hướng| A

style A fill:#e3f2fd
style B fill:#fff3e0
style C fill:#c8e6c9
style D fill:#f3e5f5
</Mermaid>

***

## Bắt đầu với Quét mã QR chuyển khoản ngân hàng

### Bước 1: Đăng ký tài khoản

Truy cập [https://my.sepay.vn/register](https://my.sepay.vn/register?onboarding=payment-gateway) và đăng ký tài khoản SePay. Chọn gói dịch vụ phù hợp sau khi đăng ký.

Nếu đã có tài khoản, truy cập [https://my.sepay.vn/pg/payment-methods](https://my.sepay.vn/pg/payment-methods) để kích hoạt Cổng thanh toán.

**Kích hoạt Cổng thanh toán:**

Tại mục "CỔNG THANH TOÁN" vào "Đăng ký". Tại màn hình "Phương thức thanh toán" chọn "Bắt đầu ngay":

<Image src="/images/quick_start/step_1_1.png" alt="Payment Flow Diagram" caption="Màn hình phương thức thanh toán" />

Bạn có thể chọn bắt đầu với Sandbox và bấm vào "Bắt đầu hướng dẫn tích hợp":

<Image src="/images/quick_start/step_1_8.png" alt="Payment Flow Diagram" caption="Bắt đầu tích hợp cổng thanh toán SePay" />

SePay hỗ trợ phương thức tích hợp bằng API với SDK PHP và SDK NodeJS. Bấm tiếp tục:

<Image src="/images/quick_start/step_1_9.png" alt="Payment Flow Diagram" caption="Phương thức tích hợp" />

Bạn sẽ nhận được thông tin tích hợp (sao chép lại thông tin `MERCHANT ID` và `SECRET KEY` để sử dụng cho các bước sau), giữ lại màn hình này và thực hiện tiếp các bước sau:

<Image src="/images/quick_start/step_1_10.png" alt="Payment Flow Diagram" caption="Thông tin tích hợp" />

***

### Bước 2: Tạo form thanh toán trên hệ thống của bạn

**Cài đặt SDK (tùy chọn PHP hoặc NodeJS)**

<CodeTabs>
  <Code label="PHP">
    ```php
    composer require sepay/sepay-pg
    ```
  </Code>
  <Code label="NodeJS">
    ```js
    npm i sepay-pg-node
    ```
  </Code>
</CodeTabs>

<Callout type="info" title="Ghi chú">
Xem chi tiết hơn tích hợp bằng SDK PHP 
Tại đây
 hoặc SDK NodeJS 
Tại đây
</Callout>

**Khởi tạo form thanh toán với các thông tin đơn hàng và chữ ký bảo mật:**

* **YOUR\_MERCHANT\_ID**: MERCHANT ID bạn đã sao chép trên thông tin tích hợp ở **bước 1**
* **YOUR\_MERCHANT\_SECRET\_KEY**: SECRET KEY bạn đã sao chép trên thông tin tích hợp ở **bước 1**

<CodeTabs>
  <Code label="SDK PHP">
    ```php
    <?php
    
    require_once 'vendor/autoload.php';
    
    use SePay\SePayClient;
    use SePay\Builders\CheckoutBuilder;
    
    // Initialize client
    $sepay = new SePayClient('YOUR_MERCHANT_ID', 'YOUR_MERCHANT_SECRET_KEY', 'sandbox');
    
    // Create checkout data
    $checkoutData = CheckoutBuilder::make()
        ->currency('VND')
        ->orderInvoiceNumber('INV-' . time())
        ->orderAmount(100000)
        ->operation('PURCHASE')
        ->orderDescription('Test payment')
        ->successUrl('https://example.com/order/DH123?payment=success')
        ->errorUrl('https://example.com/order/DH123?payment=error')
        ->cancelUrl('https://example.com/order/DH123?payment=cancel')
        ->build();
    
    // Render checkout form to UI
    echo $sepay->checkout()->generateFormHtml($checkoutData);
    ```
  </Code>
  <Code label="SDK NodeJS">
    ```js
    import { SePayPgClient } from 'sepay-pg-node-sdk';
    
    const client = new SePayPgClient({
      env: 'sandbox',
      merchant_id: 'YOUR_MERCHANT_ID',
      secret_key: 'YOUR_MERCHANT_SECRET_KEY'
    });
    
    const checkoutURL = client.checkout.initCheckoutUrl();
    
    const checkoutFormfields = client.checkout.initOneTimePaymentFields({
      operation: 'PURCHASE',
      payment_method: 'BANK_TRANSFER',
      order_invoice_number: 'DH123',
      order_amount: 10000,
      currency: 'VND',
      order_description: 'Thanh toan don hang DH123',
      success_url: 'https://example.com/order/DH123?payment=success',
      error_url: 'https://example.com/order/DH123?payment=error',
      cancel_url: 'https://example.com/order/DH123?payment=cancel',
    });
    
    return (
      <form action={checkoutURL} method="POST">
        {Object.keys(checkoutFormfields).map(field => (
          <input type="hidden" name={field} value={checkoutFormfields[field]} />
        ))}
        <button type="submit">Pay now</button>
      </form>
    );
    ```
  </Code>
  <Code label="PHP">
    ```php
    <?php
    
    $merchantId = 'YOUR_MERCHANT_ID';
    $secretKey = 'YOUR_MERCHANT_SECRET_KEY';
    
    $fields = [
        'merchant' => $merchantId,
        'currency' => 'VND',
        'order_amount' => '100000',
        'operation' => 'PURCHASE',
        'order_description' => 'Payment for order #12345',
        'order_invoice_number' => 'INV_' . time(),
        'customer_id' => 'CUST_001',
        'success_url' => 'https://example.com/order/DH123?payment=success',
        'error_url' => 'https://example.com/order/DH123?payment=error',
        'cancel_url' => 'https://example.com/order/DH123?payment=cancel',
    ];
    
    // Generate signature
    $signature = signFields($fields, $secretKey);
    $fields['signature'] = $signature;
    
    // Render form
    echo '<form method="POST" action="https://pay-sandbox.sepay.vn/v1/checkout/init">';
    foreach ($fields as $key => $value) {
        echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
    }
    echo '<button type="submit">Pay now</button>';
    echo '</form>';
    
    // Signature generation function
    function signFields(array $fields, string $secretKey): string {
        $signed = [];
        $signedFields = array_values(array_filter(array_keys($fields), fn ($field) => in_array($field, [
            'merchant','operation','payment_method','order_amount','currency',
            'order_invoice_number','order_description','customer_id',
           'success_url','error_url','cancel_url'
        ])));
    
        foreach ($signedFields as $field) {
            if (! isset($fields[$field])) continue;
            $signed[] = $field . '=' . ($fields[$field] ?? '');
        }
    
        return base64_encode(hash_hmac('sha256', implode(',', $signed), $secretKey, true));
    }
    ```
  </Code>
</CodeTabs>

**Kết quả nhận được form thanh toán** (Tùy chỉnh giao diện phù hợp với hệ thống của bạn):

<Image src="/images/quick_start/step_1_6.png" alt="Payment Flow Diagram" caption="Ví dụ form thanh toán được tạo" />

Khi submit form thanh toán sẽ chuyển sang cổng thanh toán của SePay:

<Image src="/images/quick_start/step_1_7.png" alt="Payment Flow Diagram" caption="Công thanh toán của SePay sau khi bạn submit form" />

<Callout type="warn" title="Ghi chú">
Khi kết thúc thanh toán SePay sẽ trả về các kết quả: 
Thành công (success_url)
, 
Thất bại (error_url)
 và 
Khách hàng hủy (cancel_url)
. Cần tạo các endpoint để xử lý callback từ SePay.
</Callout>

**Tạo các endpoint để nhận các callback từ SePay:**

<Php title="PHP">
```php
// success_url - Handle successful payment
Route::get('/payment/success', function() {
  // Show success page to customer
  return view('payment.success');
});

// error_url - Handle failed payment
Route::get('/payment/error', function() {
  // Show error page to customer
  return view('payment.error');
});

// cancel_url - Handle canceled payment
Route::get('/payment/cancel', function() {
  // Show cancel page to customer
  return view('payment.cancel');
});
```
</Php>

Đưa các endpoint bạn đã tạo vào `success_url`, `error_url`, `cancel_url` lúc tạo form thanh toán.

***

### Bước 3: Cấu hình IPN

<Callout type="info" title="IPN (Instant Payment Notification) là gì?">
IPN là một endpoint trên hệ thống của bạn dùng để nhận thông báo giao dịch theo thời gian thực từ cổng thanh toán SePay. 
Tìm hiểu thêm về IPN
</Callout>

Tại màn hình thông tin tích hợp đang giữ ở **bước 1**, điền vào endpoint IPN của bạn:

<Image src="/images/quick_start/step_1_4.png" alt="Payment Flow Diagram" caption="Tạo cấu hình IPN" />

Lưu cấu hình IPN.

<Callout type="light" title="Ghi chú">
Khi có giao dịch thành công SePay sẽ trả về JSON qua IPN của bạn:
</Callout>

<Response title="IPN JSON">
```json
{
  "timestamp": 1759134682,
  "notification_type": "ORDER_PAID",
  "order": {
      "id": "e2c195be-c721-47eb-b323-99ab24e52d85",
      "order_id": "NQD-68DA43D73C1A5",
      "order_status": "CAPTURED",
      "order_currency": "VND",
      "order_amount": "100000.00",
      "order_invoice_number": "INV-1759134677",
      "custom_data": [],
      "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36",
      "ip_address": "14.186.39.212",
      "order_description": "Test payment"
  },
  "transaction": {
      "id": "384c66dd-41e6-4316-a544-b4141682595c",
      "payment_method": "BANK_TRANSFER",
      "transaction_id": "68da43da2d9de",
      "transaction_type": "PAYMENT",
      "transaction_date": "2025-09-29 15:31:22",
      "transaction_status": "APPROVED",
      "transaction_amount": "100000",
      "transaction_currency": "VND",
      "authentication_status": "AUTHENTICATION_SUCCESSFUL",
      "card_number": null,
      "card_holder_name": null,
      "card_expiry": null,
      "card_funding_method": null,
      "card_brand": null
  },
  "customer": null,
  "agreement": null
}
```
</Response>

**Tạo endpoint IPN để nhận JSON data từ SePay**

Endpoint là endpoint bạn đã cấu hình trên IPN:

<Php title="PHP">
```php
Route::post('/payment/ipn', function(Request $request) {
  $data = $request->json()->all();

  if ($data['notification_type'] === 'ORDER_PAID') {
      $order = Order::where('invoice_number', $data['order']['order_invoice_number'])->first();
      $order->status = 'paid';
      $order->save();
  }

  // Return 200 to acknowledge receipt
  return response()->json(['success' => true], 200);
});
```
</Php>

***

### Bước 4: Kiểm thử

Bây giờ bạn có thể kiểm thử bằng cách tạo một đơn hàng trên form vừa tích hợp ở **bước 2**.

Sau đó quay lại màn hình thông tin tích hợp và bấm tiếp tục để kiểm tra kết quả:

<Image src="/images/quick_start/step_1_12.png" alt="Payment Flow Diagram" caption="Kiểm tra kết quả" />

**Kịch bản:**

* Khi người dùng gửi form thanh toán trên website của bạn, hệ thống sẽ chuyển hướng đến trang thanh toán của SePay.
* Khi thanh toán thành công: SePay chuyển hướng về endpoint `/payment/success` của bạn và gửi dữ liệu cho endpoint IPN bạn đã cấu hình
* Khi thanh toán thất bại: SePay chuyển hướng về endpoint `/payment/error`
* Khi hủy thanh toán: SePay chuyển hướng về endpoint `/payment/cancel`

***

### Bước 5: Go live

<Callout type="info" title="Yêu cầu">
Có tài khoản ngân hàng cá nhân/doanh nghiệp và đã hoàn thành tích hợp và test ở Sandbox.
</Callout>

**Các bước cần thực hiện:**

1. Liên kết tài khoản ngân hàng thật
2. Từ **[https://my.sepay.vn/](https://my.sepay.vn/)** vào mục **Cổng thanh toán** chọn **Đăng ký** → Tại mục "Quét mã QR chuyển khoản ngân hàng" chọn "Bắt đầu ngay" và tiếp tục cho đến màn hình như ảnh bên dưới và chọn "Chuyển sang Production"

<Image src="/images/quick_start/step_1_11.png" alt="Payment Flow Diagram" caption="Chuyển sang Production" />

3. Sau khi **Chuyển sang Production** sẽ nhận được "MERCHANT ID" và "SECRET KEY" chính thức

<Image src="/images/quick_start/step_1_2.png" alt="Payment Flow Diagram" caption="Thông tin tích hợp" />

4. Cập nhật endpoint sang Production: **`https://pay.sepay.vn/v1/checkout/init`**
5. Đối với trường hợp dùng SDK: cập nhật các biến môi trường từ **Sandbox** sang **Production** (khi khởi tạo client)
6. Cập nhật "MERCHANT ID" và "SECRET KEY" của Sandbox thành "MERCHANT ID" và "SECRET KEY" chính thức
7. Cập nhật **IPN URL** sang **Production**
8. Cập nhật các **Callback URL** sang **Production**

<Callout type="light" title="Đối với Quét mã QR NAPAS chuyển khoản ngân hàng">
Cần gửi hồ sơ - 
Xem chi tiết tại đây
</Callout>

<iframe width="100%" height="400" src="https://www.youtube.com/embed/uThfz1cmwAE" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />

<Callout type="light" title="Đối với Thanh toán bằng thẻ">
Cần gửi hồ sơ - 
Xem chi tiết tại đây
</Callout>

<iframe width="100%" height="400" src="https://www.youtube.com/embed/-I4t9VKqkLM" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen />

# Tổng quan API Cổng Thanh Toán

## Xác thực Basic Auth (merchant_id:secret_key) cho API Cổng thanh toán SePay — kèm base URL sandbox/production và bộ mã lỗi HTTP chuẩn.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

## Base URLs

Cổng thanh toán SePay cung cấp hai môi trường để tích hợp — Sandbox dùng cho kiểm thử và Production cho giao dịch thật:

| Môi trường     | Base URL                         |
| -------------- | -------------------------------- |
| **Production** | `https://pgapi.sepay.vn`         |
| **Sandbox**    | `https://pgapi-sandbox.sepay.vn` |

***

## Xác thực API

Tất cả các API của SePay đều sử dụng **Basic Authentication** để xác thực.

<TextBlock title="Headers">
```text
Authorization: Basic base64(merchant_id:secret_key)
Content-Type: application/json
```
</TextBlock>

***

## Mã lỗi chung

<ErrorCodes
  hiddenHead={true}
  rows={[
  { code: 200, name: "Thành công",                 description: "Request được xử lý thành công",                                              action: "—" },
  { code: 400, name: "Bad Request",                 description: "Dữ liệu request không hợp lệ",                                              action: "Kiểm tra lại các tham số" },
  { code: 401, name: "Unauthorized",                description: "Xác thực thất bại",                                                         action: "Kiểm tra lại merchant_id và secret_key" },
  { code: 403, name: "Forbidden",                   description: "Không có quyền truy cập API này",                                           action: "Xác nhận quyền truy cập/whitelist nếu cần" },
  { code: 404, name: "Not Found",                   description: "Không tìm thấy tài nguyên yêu cầu",                                         action: "Kiểm tra URL/path hoặc id" },
  { code: 422, name: "Unprocessable Entity",        description: "Dữ liệu hợp lệ nhưng không thể xử lý (validation errors)",                  action: "Sửa các lỗi validation theo thông báo" },
  { code: 429, name: "Too Many Requests",           description: "Vượt quá giới hạn rate limit",                                              action: "Giảm tần suất, áp dụng retry/backoff" },
  { code: 500, name: "Internal Server Error",       description: "Lỗi server",                                                                action: "Thử lại sau; liên hệ SePay để được hỗ trợ" }
]}
/>

***

## Phân trang

Các API trả về danh sách đều hỗ trợ phân trang:

<ParamsTable
  rows={[
  { "name": "per_page", "type": "integer", "required": false, "description": "Số lượng kết quả mỗi trang (mặc định: 20, tối đa: 100)" },
  { "name": "page", "type": "integer", "required": false, "description": "Trang hiện tại (mặc định: 1)" }
]}
/>

***

## Định dạng trả về

<Response title="RESPONSE">
```json
{
  "data": "[...]",
  "meta": {
    "per_page": 20,
    "total": 100,
    "has_more": false,
    "current_page": 1,
    "page_count": 5
  }
}
```
</Response>

# API tạo đơn hàng thanh toán

## Tạo đơn hàng one-time checkout qua API Cổng thanh toán SePay — hỗ trợ chuyển khoản QR, thẻ tín dụng/ghi nợ quốc tế và cổng NAPAS.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

<Callout type="info" title="Đơn hàng là gì?">
Trong cổng thanh toán SePay, đơn hàng (order) là gói thông tin mô tả yêu cầu thanh toán với các thuộc tính chính như số tiền, mô tả giao dịch, mã hóa đơn, khách hàng và các URL callback để hệ thống xử lý. API khởi tạo form thanh toán sử dụng gói thông tin này để tạo giao dịch một lần; bạn chỉ cần tạo form HTML hợp lệ và submit tới endpoint 
`checkout/init`
 để chuyển hướng khách hàng đến trang thanh toán.
</Callout>

## Luồng xử lý tạo đơn hàng

<Mermaid title="Luồng tạo form thanh toán và xác thực chữ ký">
flowchart TD
  A[Khách hàng chọn thanh toán] --> B[Website tạo form HTML]
  B --> C[Thu thập thông tin đơn hàng]
  C --> D[Chuẩn bị dữ liệu form]
  D --> E[Tạo signature HMAC-SHA256]
  E --> F[Thêm signature vào form]
  F --> G[Submit form POST đến checkout/init]
  G --> H{SePay xác thực signature}
  H -->|Thành công| I[Chuyển hướng đến trang thanh toán]
  H -->|Thất bại| J[Trả về lỗi xác thực]
  I --> K[Khách hàng chọn phương thức thanh toán]
  K --> L[Thực hiện thanh toán]
  L --> M[Callback về success/error/cancel URL]

  style A fill:#e1f5fe
  style I fill:#c8e6c9
  style J fill:#ffcdd2
  style M fill:#fff3e0
</Mermaid>

1. **Khách hàng chọn thanh toán**: Người dùng click nút thanh toán trên website
2. **Website tạo form HTML**: Server tạo form HTML với các tham số cần thiết
3. **Thu thập thông tin đơn hàng**: Lấy thông tin từ database hoặc session
4. **Chuẩn bị dữ liệu form**: Sắp xếp các tham số theo đúng format
5. **Tạo signature**: Sử dụng thuật toán HMAC-SHA256 để tạo chữ ký
6. **Thêm signature vào form**: Đưa chữ ký vào form như một hidden field
7. **Submit form**: Gửi POST request đến endpoint `checkout/init`
8. **Xác thực signature**: SePay kiểm tra tính hợp lệ của chữ ký
9. **Chuyển hướng**: Nếu hợp lệ, chuyển hướng đến trang thanh toán
10. **Thanh toán**: Khách hàng thực hiện thanh toán trên trang SePay
11. **Callback**: SePay gọi về URL IPN với kết quả

***

## Endpoint

<Endpoint>
  <Method>POST</Method>

  <Path>https://pgapi.sepay.vn/v1/checkout/init</Path>

  <Description>
    Tạo form thanh toán
  </Description>
</Endpoint>

<Callout type="warn" title="Lưu ý">
Đây là endpoint để submit form, không phải endpoint để gọi API.
</Callout>

***

## Danh sách tham số

<ParamsTable rows={[{ "name": "merchant", "type": "string", "required": true, "description": "ID merchant của bạn (Ví dụ: MERCHANT_123)" }, { "name": "currency", "type": "string", "required": true, "description": "Mã tiền tệ (chỉ hỗ trợ VND)" }, { "name": "order_amount", "type": "string", "required": true, "description": "Số tiền đơn hàng (đơn vị nhỏ nhất)" }, { "name": "operation", "type": "string", "required": true, "description": "Loại giao dịch (PURCHASE hoặc VERIFY)" }, { "name": "order_description", "type": "string", "required": true, "description": "Mô tả đơn hàng" }, { "name": "order_invoice_number", "type": "string", "required": true, "description": "Mã hóa đơn (bắt buộc cho PURCHASE, ví dụ: INV_20231201_001)" }, { "name": "payment_method", "type": "string", "required": false, "description": "Phương thức thanh toán (CARD, BANK_TRANSFER, NAPAS_BANK_TRANSFER)" }, { "name": "customer_id", "type": "string", "required": false, "description": "ID khách hàng" }, { "name": "success_url", "type": "string", "required": false, "description": "URL chuyển hướng khi thành công (Ví dụ: https://yoursite.com/success)" }, { "name": "error_url", "type": "string", "required": false, "description": "URL chuyển hướng khi lỗi (Ví dụ: https://yoursite.com/error)" }, { "name": "cancel_url", "type": "string", "required": false, "description": "URL chuyển hướng khi hủy (Ví dụ: https://yoursite.com/cancel)" }]} />

<Callout type="warn" title="Lưu ý">
Các tham số success_url, error_url, và cancel_url 
chỉ hoạt động khi ứng dụng của bạn đang chạy trên domain hoặc IP có thể truy cập công khai (public)
. Nếu bạn đang phát triển trên môi trường 
localhost
, hãy sử dụng các công cụ giúp public môi trường cục bộ như 
ngrok
, 
localtunnel
, hoặc tương tự.
</Callout>

***

## Ví dụ tạo đơn hàng cơ bản

**Tạo form HTML**

<Callout type="danger" title="Lưu ý quan trọng về thứ tự input trong HTML">
Khi tự dựng form HTML, hãy giữ đúng thứ tự các input như form mẫu ngay bên dưới để quá trình ký và xử lý phía SePay khớp tuyệt đối; đổi vị trí trường có thể khiến signature sai.
</Callout>

<Html title="Form thanh toán">
  {`<form action="https://pay-sandbox.sepay.vn/v1/checkout/init" method="POST">
    <input type="hidden" name="order_amount" value="100000" />
    <input type="hidden" name="merchant" value="MERCHANT_123" />
    <input type="hidden" name="currency" value="VND" />
    <input type="hidden" name="operation" value="PURCHASE" />
    <input type="hidden" name="order_description" value="Thanh toán đơn hàng #12345" />
    <input type="hidden" name="order_invoice_number" value="INV_20231201_001" />
    <input type="hidden" name="success_url" value="https://yoursite.com/payment/success" />
    <input type="hidden" name="error_url" value="https://yoursite.com/payment/error" />
    <input type="hidden" name="cancel_url" value="https://yoursite.com/payment/cancel" />
    <input type="hidden" name="signature" value="a1b2c3d4e5f6..." />
    <button type="submit">Thanh toán</button>
    </form>`}
</Html>

**Response:**

Sau khi submit form, hệ thống sẽ chuyển hướng người dùng đến trang thanh toán của SePay:

`https://pgapi-sandbox.sepay.vn?merchant=MERCHANT_123&currency=VND&order_amount=100000&operation=PURCHASE&order_description=Thanh%20toán%20đơn%20hàng%20%2312345&order_invoice_number=INV_20231201_001&customer_id=CUST_001&success_url=https%3A%2F%2Fyoursite.com%2Fpayment%2Fsuccess&error_url=https%3A%2F%2Fyoursite.com%2Fpayment%2Ferror&cancel_url=https%3A%2F%2Fyoursite.com%2Fpayment%2Fcancel&signature=a1b2c3d4e5f6...`

<Callout type="warn" title="Lưu ý">
Trang thanh toán sẽ hiển thị các phương thức thanh toán khả dụng dựa trên cấu hình merchant của bạn.
</Callout>

***

## Xác thực chữ ký

<Callout type="danger" title="Lưu ý quan trọng về các trường khi tạo chữ ký">
Khi tạo signature, hãy giữ nguyên thứ tự các field trong 
`signedFields`
 đúng như đoạn code mẫu (không sắp xếp lại) để chuỗi ký trùng với phía SePay.
</Callout>

**Signature được tạo từ các tham số form theo quy tắc sau:**

1. **Lọc các trường cần ký**: Chỉ ký các trường được phép trong danh sách: `merchant, operation, payment_method, order_amount, currency, order_invoice_number, order_description, customer_id, success_url, error_url, cancel_url`
2. **Tạo chuỗi ký**: `field1=value1,field2=value2,field3=value3...`
3. **Mã hóa**: `base64_encode(hash_hmac('sha256', $signedString, $secretKey, true))`

**Ví dụ tạo chữ ký:**

<Php title="Hàm ký dữ liệu PHP">
```php
function signFields(array $fields, string $secretKey): string {
  $signed = [];
  $allowedFields = [
      'order_amount', 'merchant', 'currency', 'operation',
      'order_description', 'order_invoice_number', 'customer_id',
      'payment_method', 'success_url', 'error_url', 'cancel_url',
  ];

  foreach ($allowedFields as $field) {
      if (! isset($fields[$field])) continue;
      $signed[] = $field . '=' . $fields[$field];
  }

  return base64_encode(hash_hmac('sha256', implode(',', $signed), $secretKey, true));
}
```
</Php>

**Ví dụ chuỗi chữ ký:**

`order_amount=100000,merchant=MERCHANT_123,currency=VND,operation=PURCHASE,order_description=Thanh toán đơn hàng #12345,order_invoice_number=INV_20231201_001,success_url=https://yoursite.com/payment/success,error_url=https://yoursite.com/payment/error,cancel_url=https://yoursite.com/payment/cancel`

***

<Callout type="warn" title="Lưu ý quan trọng">
Mã hóa đơn hàng:
 
`order_invoice_number`
 phải là duy nhất và không được trùng lặp. 2. 
Số tiền:
 Chỉ hỗ trợ VND, số tiền phải lớn hơn 0 cho giao dịch 
`PURCHASE`
. 3. 
URL callback:
 Phải là URL công khai có thể truy cập từ internet. 4. 
Chữ ký:
 Luôn kiểm tra chữ ký để đảm bảo tính toàn vẹn dữ liệu. 5. 
Môi trường:
 Sử dụng sandbox cho testing, production cho giao dịch thực.
</Callout>

# API lấy chi tiết đơn hàng

## Lấy chi tiết đơn hàng qua API getOrderDetails của Cổng thanh toán SePay — trả về trạng thái, số tiền, khách hàng và lịch sử giao dịch.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

## API Endpoint

<Endpoint>
  <Method>GET</Method>

  <Path>https://pgapi.sepay.vn/v1/order/detail/{order_id}</Path>

  <Description>
    Chi tiết đơn hàng
  </Description>

  <Authentication>
    basicAuth
  </Authentication>
</Endpoint>

## API Request

<Params>
  <PathParams>
    <Param name="order_id" type="string" required="true">
      ID đơn hàng cần truy vấn (Ví dụ SEPAY-68BA83CE637C1)
    </Param>
  </PathParams>

</Params>

## API Response

<Responses>
  <Response status="200">
    <Description>
      Chi tiết đơn hàng
    </Description>

    <Example>
      {
        "data": {
          "id": "1",
          "customer_id": null,
          "order_id": "SEPAY-68B01673A77FF",
          "order_invoice_number": "DH1756370479",
          "order_status": "CAPTURED",
          "order_amount": "300000.00",
          "order_currency": "VND",
          "order_description": "Đơn hàng #1756370479",
          "authentication_status": "AUTHENTICATION_SUCCESSFUL",
          "created_at": "2025-08-28 15:43:48",
          "updated_at": "2025-08-28 15:43:48",
          "transactions": [
            {
              "id": "1",
              "payment_method": "CARD",
              "transaction_type": "PAYMENT",
              "transaction_amount": "300000",
              "transaction_currency": "VND",
              "transaction_status": "APPROVED",
              "authentication_status": "AUTHENTICATION_SUCCESSFUL",
              "card_number": "512345xxxxxx0008",
              "card_holder_name": "NGO QUOC DAT",
              "card_expiry": "1230",
              "card_funding_method": "DEBIT",
              "card_brand": "MASTERCARD",
              "transaction_date": "2025-08-28 15:43:41",
              "transaction_last_updated_date": "2025-08-28 15:43:41"
            }
          ]
        }
      }
    </Example>
  </Response>

</Responses>

<ResponseDescriptionFields>
  <ResponseSchema status="200">
    <Fields>
      <Field name="data" type="object" required="false">
        <Fields>
          <Field name="id" type="string" required="false">
            ID nội bộ của đơn hàng
          </Field>
          <Field name="customer_id" type="string" required="false">
            ID khách hàng (có thể null)
          </Field>
          <Field name="order_id" type="string" required="false">
            Mã đơn hàng duy nhất
          </Field>
          <Field name="order_invoice_number" type="string" required="false">
            Mã hóa đơn
          </Field>
          <Field name="order_status" type="enum: CAPTURED, CANCELLED, AUTHENTICATION_NOT_NEEDED" required="false">
            Trạng thái đơn hàng:
- CAPTURED: Đã thanh toán
- CANCELLED: Đã hủy
- AUTHENTICATION_NOT_NEEDED: Đang đợi thanh toán

          </Field>
          <Field name="order_amount" type="string" required="false">
            Số tiền đơn hàng (VND)
          </Field>
          <Field name="order_currency" type="string" required="false">
            Mã tiền tệ
          </Field>
          <Field name="order_description" type="string" required="false">
            Mô tả đơn hàng
          </Field>
          <Field name="authentication_status" type="string" required="false">
            Trạng thái xác thực
          </Field>
          <Field name="created_at" type="string" required="false">
            Thời gian tạo (YYYY-MM-DD HH:mm:ss)
          </Field>
          <Field name="updated_at" type="string" required="false">
            Thời gian cập nhật cuối (YYYY-MM-DD HH:mm:ss)
          </Field>
          <Field name="transactions" type="array" required="false">
            <Description>Danh sách giao dịch</Description>
            <ArrayItems>
              <Fields>
                <Field name="id" type="string" required="false">
                  ID giao dịch
                </Field>
                <Field name="payment_method" type="enum: CARD, BANK_TRANSFER, NAPAS_BANK_TRANSFER" required="false">
                  Phương thức thanh toán:
- CARD: Thẻ quốc tế
- BANK_TRANSFER: Chuyển khoản QR
- NAPAS_BANK_TRANSFER: Chuyển khoản NAPAS QR

                </Field>
                <Field name="transaction_type" type="enum: PAYMENT, REFUND, VOID" required="false">
                  Loại giao dịch:
- PAYMENT: Thanh toán
- REFUND: Hoàn tiền
- VOID: Hủy giao dịch

                </Field>
                <Field name="transaction_amount" type="string" required="false">
                  Số tiền giao dịch
                </Field>
                <Field name="transaction_currency" type="string" required="false">
                  Mã tiền tệ
                </Field>
                <Field name="transaction_status" type="enum: APPROVED, DECLINED, PENDING" required="false">
                  Trạng thái giao dịch:
- APPROVED: Đã duyệt
- DECLINED: Bị từ chối
- PENDING: Đang xử lý

                </Field>
                <Field name="authentication_status" type="string" required="false">
                  Trạng thái xác thực giao dịch
                </Field>
                <Field name="card_number" type="string" required="false">
                  Số thẻ đã mask
                </Field>
                <Field name="card_holder_name" type="string" required="false">
                  Tên chủ thẻ
                </Field>
                <Field name="card_expiry" type="string" required="false">
                  Hạn thẻ (MMYY)
                </Field>
                <Field name="card_funding_method" type="enum: DEBIT, CREDIT" required="false">
                  Loại thẻ:
- DEBIT: Thẻ ghi nợ
- CREDIT: Thẻ tín dụng

                </Field>
                <Field name="card_brand" type="enum: VISA, MASTERCARD, JCB" required="false">
                  Thương hiệu thẻ
                </Field>
                <Field name="transaction_date" type="string" required="false">
                  Thời gian giao dịch
                </Field>
                <Field name="transaction_last_updated_date" type="string" required="false">
                  Thời gian cập nhật cuối
                </Field>
              </Fields>
            </ArrayItems>
          </Field>
        </Fields>
      </Field>
    </Fields>
  </ResponseSchema>

</ResponseDescriptionFields>

## Code mẫu

<CodeSamples>
  <CodeSamplesList>
    <CodeSamplesTrigger value="shell_curl">
      cURL
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="php_curl">
      PHP
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="python_python3">
      Python
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="node_native">
      NodeJS
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="java_okhttp">
      Java
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="ruby_native">
      Ruby
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="go_native">
      Go
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="csharp_httpclient">
      .NET
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="swift_nsurlsession">
      Swift
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="kotlin_okhttp">
      Kotlin
    </CodeSamplesTrigger>

  </CodeSamplesList>

  <CodeSample value="shell_curl" lang="bash">
    ```bash
    curl --request GET \
      --url https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1 \
      --header 'Authorization: Basic REPLACE_BASIC_AUTH'
    ```
  </CodeSample>

  <CodeSample value="php_curl" lang="php">
    ```php
    <?php
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Authorization: Basic REPLACE_BASIC_AUTH"
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
    ```
  </CodeSample>

  <CodeSample value="python_python3" lang="python">
    ```python
    import http.client
    
    conn = http.client.HTTPSConnection("pgapi.sepay.vn")
    
    headers = { 'Authorization': "Basic REPLACE_BASIC_AUTH" }
    
    conn.request("GET", "/v1/order/detail/SEPAY-68BA83CE637C1", headers=headers)
    
    res = conn.getresponse()
    data = res.read()
    
    print(data.decode("utf-8"))
    ```
  </CodeSample>

  <CodeSample value="node_native" lang="javascript">
    ```javascript
    const http = require("https");
    
    const options = {
      "method": "GET",
      "hostname": "pgapi.sepay.vn",
      "port": null,
      "path": "/v1/order/detail/SEPAY-68BA83CE637C1",
      "headers": {
        "Authorization": "Basic REPLACE_BASIC_AUTH"
      }
    };
    
    const req = http.request(options, function (res) {
      const chunks = [];
    
      res.on("data", function (chunk) {
        chunks.push(chunk);
      });
    
      res.on("end", function () {
        const body = Buffer.concat(chunks);
        console.log(body.toString());
      });
    });
    
    req.end();
    ```
  </CodeSample>

  <CodeSample value="java_okhttp" lang="java">
    ```java
    OkHttpClient client = new OkHttpClient();
    
    Request request = new Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1")
      .get()
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .build();
    
    Response response = client.newCall(request).execute();
    ```
  </CodeSample>

  <CodeSample value="ruby_native" lang="ruby">
    ```ruby
    require 'uri'
    require 'net/http'
    require 'openssl'
    
    url = URI("https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1")
    
    http = Net::HTTP.new(url.host, url.port)
    http.use_ssl = true
    http.verify_mode = OpenSSL::SSL::VERIFY_NONE
    
    request = Net::HTTP::Get.new(url)
    request["Authorization"] = 'Basic REPLACE_BASIC_AUTH'
    
    response = http.request(request)
    puts response.read_body
    ```
  </CodeSample>

  <CodeSample value="go_native" lang="go">
    ```go
    package main
    
    import (
    	"fmt"
    	"net/http"
    	"io/ioutil"
    )
    
    func main() {
    
    	url := "https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1"
    
    	req, _ := http.NewRequest("GET", url, nil)
    
    	req.Header.Add("Authorization", "Basic REPLACE_BASIC_AUTH")
    
    	res, _ := http.DefaultClient.Do(req)
    
    	defer res.Body.Close()
    	body, _ := ioutil.ReadAll(res.Body)
    
    	fmt.Println(res)
    	fmt.Println(string(body))
    
    }
    ```
  </CodeSample>

  <CodeSample value="csharp_httpclient" lang="csharp">
    ```csharp
    var client = new HttpClient();
    var request = new HttpRequestMessage
    {
        Method = HttpMethod.Get,
        RequestUri = new Uri("https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1"),
        Headers =
        {
            { "Authorization", "Basic REPLACE_BASIC_AUTH" },
        },
    };
    using (var response = await client.SendAsync(request))
    {
        response.EnsureSuccessStatusCode();
        var body = await response.Content.ReadAsStringAsync();
        Console.WriteLine(body);
    }
    ```
  </CodeSample>

  <CodeSample value="swift_nsurlsession" lang="swift">
    ```swift
    import Foundation
    
    let headers = ["Authorization": "Basic REPLACE_BASIC_AUTH"]
    
    let request = NSMutableURLRequest(url: NSURL(string: "https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1")! as URL,
                                            cachePolicy: .useProtocolCachePolicy,
                                        timeoutInterval: 10.0)
    request.httpMethod = "GET"
    request.allHTTPHeaderFields = headers
    
    let session = URLSession.shared
    let dataTask = session.dataTask(with: request as URLRequest, completionHandler: { (data, response, error) -> Void in
      if (error != nil) {
        print(error)
      } else {
        let httpResponse = response as? HTTPURLResponse
        print(httpResponse)
      }
    })
    
    dataTask.resume()
    ```
  </CodeSample>

  <CodeSample value="kotlin_okhttp" lang="kotlin">
    ```kotlin
    val client = OkHttpClient()
    
    val request = Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order/detail/SEPAY-68BA83CE637C1")
      .get()
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .build()
    
    val response = client.newCall(request).execute()
    ```
  </CodeSample>

</CodeSamples>

## Ghi chú

<Callout type="tip" title="Định nghĩa trạng thái đơn hàng">
`CAPTURED: `
Đã thanh toán
`CANCELLED: `
Đã hủy
`AUTHENTICATION_NOT_NEEDED: `
Đang đợi thanh toán
</Callout>

# API lấy danh sách đơn hàng

## Lấy danh sách đơn hàng từ Cổng thanh toán SePay qua API listOrders — hỗ trợ phân trang, lọc theo ngày và trạng thái cho mọi merchant.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

## API Endpoint

<Endpoint>
  <Method>GET</Method>

  <Path>https://pgapi.sepay.vn/v1/order</Path>

  <Description>
    Danh sách đơn hàng
  </Description>

  <Authentication>
    basicAuth
  </Authentication>
</Endpoint>

## API Request

<Params>
  <QueryParams>
    <Param name="per_page" type="integer" required="false">
      Số đơn hàng mỗi trang (mặc định 20)
    </Param>
    <Param name="page" type="integer" required="false">
      Trang hiện tại (mặc định 1)
    </Param>
    <Param name="q" type="string" required="false">
      Tìm kiếm theo từ khóa
    </Param>
    <Param name="order_status" type="enum: CAPTURED, CANCELLED, AUTHENTICATION_NOT_NEEDED" required="false">
      Lọc theo trạng thái đơn hàng:
- CAPTURED: Đã thanh toán
- CANCELLED: Đã hủy
- AUTHENTICATION_NOT_NEEDED: Đang đợi thanh toán

    </Param>
    <Param name="customer_id" type="string" required="false">
      Lọc theo ID khách hàng
    </Param>
    <Param name="created_at" type="string" required="false">
      Lọc theo ngày tạo (YYYY-MM-DD)
    </Param>
    <Param name="from_created_at" type="string" required="false">
      Ngày bắt đầu (YYYY-MM-DD)
    </Param>
    <Param name="end_created_at" type="string" required="false">
      Ngày kết thúc (YYYY-MM-DD)
    </Param>
    <Param name="sort" type="string" required="false">
      Sắp xếp (created_at:asc, created_at:desc)
    </Param>
  </QueryParams>

</Params>

## API Response

<Responses>
  <Response status="200">
    <Description>
      Danh sách đơn hàng
    </Description>

    <Example>
      {
        "data": [
          {
            "id": "9",
            "customer_id": "2427",
            "order_id": "SEPAY-68BA83CE637C1",
            "order_invoice_number": "DH1757053857",
            "order_status": "AUTHENTICATION_NOT_NEEDED",
            "order_amount": "300000.00",
            "order_currency": "VND",
            "order_description": "Đơn hàng #1757053857",
            "authentication_status": null,
            "created_at": "2025-09-05 13:31:42",
            "updated_at": "2025-09-05 13:31:42"
          }
        ],
        "meta": {
          "per_page": 20,
          "total": 1,
          "has_more": false,
          "current_page": 1,
          "page_count": 1
        }
      }
    </Example>
  </Response>

</Responses>

<ResponseDescriptionFields>
  <ResponseSchema status="200">
    <Fields>
      <Field name="data" type="array" required="false">
        <ArrayItems>
          <Fields>
            <Field name="id" type="string" required="false">
              ID nội bộ của đơn hàng
            </Field>
            <Field name="customer_id" type="string" required="false">
              ID khách hàng (có thể null)
            </Field>
            <Field name="order_id" type="string" required="false">
              Mã đơn hàng duy nhất
            </Field>
            <Field name="order_invoice_number" type="string" required="false">
              Mã hóa đơn
            </Field>
            <Field name="order_status" type="enum: CAPTURED, CANCELLED, AUTHENTICATION_NOT_NEEDED" required="false">
              Trạng thái đơn hàng:
- CAPTURED: Đã thanh toán
- CANCELLED: Đã hủy
- AUTHENTICATION_NOT_NEEDED: Đang đợi thanh toán

            </Field>
            <Field name="order_amount" type="string" required="false">
              Số tiền đơn hàng (VND)
            </Field>
            <Field name="order_currency" type="string" required="false">
              Mã tiền tệ
            </Field>
            <Field name="order_description" type="string" required="false">
              Mô tả đơn hàng
            </Field>
            <Field name="authentication_status" type="string" required="false">
              Trạng thái xác thực
            </Field>
            <Field name="created_at" type="string" required="false">
              Thời gian tạo (YYYY-MM-DD HH:mm:ss)
            </Field>
            <Field name="updated_at" type="string" required="false">
              Thời gian cập nhật cuối (YYYY-MM-DD HH:mm:ss)
            </Field>
          </Fields>
        </ArrayItems>
      </Field>
      <Field name="meta" type="object" required="false">
        <Fields>
          <Field name="per_page" type="integer" required="false">
            Số bản ghi mỗi trang
          </Field>
          <Field name="total" type="integer" required="false">
            Tổng số bản ghi
          </Field>
          <Field name="has_more" type="boolean" required="false">
            Còn dữ liệu ở trang tiếp theo hay không
          </Field>
          <Field name="current_page" type="integer" required="false">
            Trang hiện tại
          </Field>
          <Field name="page_count" type="integer" required="false">
            Tổng số trang
          </Field>
        </Fields>
      </Field>
    </Fields>
  </ResponseSchema>

</ResponseDescriptionFields>

## Code mẫu

<CodeSamples>
  <CodeSamplesList>
    <CodeSamplesTrigger value="shell_curl">
      cURL
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="php_curl">
      PHP
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="python_python3">
      Python
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="node_native">
      NodeJS
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="java_okhttp">
      Java
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="ruby_native">
      Ruby
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="go_native">
      Go
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="csharp_httpclient">
      .NET
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="swift_nsurlsession">
      Swift
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="kotlin_okhttp">
      Kotlin
    </CodeSamplesTrigger>

  </CodeSamplesList>

  <CodeSample value="shell_curl" lang="bash">
    ```bash
    curl --request GET \
      --url 'https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc' \
      --header 'Authorization: Basic REPLACE_BASIC_AUTH'
    ```
  </CodeSample>

  <CodeSample value="php_curl" lang="php">
    ```php
    <?php
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Authorization: Basic REPLACE_BASIC_AUTH"
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
    ```
  </CodeSample>

  <CodeSample value="python_python3" lang="python">
    ```python
    import http.client
    
    conn = http.client.HTTPSConnection("pgapi.sepay.vn")
    
    headers = { 'Authorization': "Basic REPLACE_BASIC_AUTH" }
    
    conn.request("GET", "/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc", headers=headers)
    
    res = conn.getresponse()
    data = res.read()
    
    print(data.decode("utf-8"))
    ```
  </CodeSample>

  <CodeSample value="node_native" lang="javascript">
    ```javascript
    const http = require("https");
    
    const options = {
      "method": "GET",
      "hostname": "pgapi.sepay.vn",
      "port": null,
      "path": "/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc",
      "headers": {
        "Authorization": "Basic REPLACE_BASIC_AUTH"
      }
    };
    
    const req = http.request(options, function (res) {
      const chunks = [];
    
      res.on("data", function (chunk) {
        chunks.push(chunk);
      });
    
      res.on("end", function () {
        const body = Buffer.concat(chunks);
        console.log(body.toString());
      });
    });
    
    req.end();
    ```
  </CodeSample>

  <CodeSample value="java_okhttp" lang="java">
    ```java
    OkHttpClient client = new OkHttpClient();
    
    Request request = new Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc")
      .get()
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .build();
    
    Response response = client.newCall(request).execute();
    ```
  </CodeSample>

  <CodeSample value="ruby_native" lang="ruby">
    ```ruby
    require 'uri'
    require 'net/http'
    require 'openssl'
    
    url = URI("https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc")
    
    http = Net::HTTP.new(url.host, url.port)
    http.use_ssl = true
    http.verify_mode = OpenSSL::SSL::VERIFY_NONE
    
    request = Net::HTTP::Get.new(url)
    request["Authorization"] = 'Basic REPLACE_BASIC_AUTH'
    
    response = http.request(request)
    puts response.read_body
    ```
  </CodeSample>

  <CodeSample value="go_native" lang="go">
    ```go
    package main
    
    import (
    	"fmt"
    	"net/http"
    	"io/ioutil"
    )
    
    func main() {
    
    	url := "https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc"
    
    	req, _ := http.NewRequest("GET", url, nil)
    
    	req.Header.Add("Authorization", "Basic REPLACE_BASIC_AUTH")
    
    	res, _ := http.DefaultClient.Do(req)
    
    	defer res.Body.Close()
    	body, _ := ioutil.ReadAll(res.Body)
    
    	fmt.Println(res)
    	fmt.Println(string(body))
    
    }
    ```
  </CodeSample>

  <CodeSample value="csharp_httpclient" lang="csharp">
    ```csharp
    var client = new HttpClient();
    var request = new HttpRequestMessage
    {
        Method = HttpMethod.Get,
        RequestUri = new Uri("https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc"),
        Headers =
        {
            { "Authorization", "Basic REPLACE_BASIC_AUTH" },
        },
    };
    using (var response = await client.SendAsync(request))
    {
        response.EnsureSuccessStatusCode();
        var body = await response.Content.ReadAsStringAsync();
        Console.WriteLine(body);
    }
    ```
  </CodeSample>

  <CodeSample value="swift_nsurlsession" lang="swift">
    ```swift
    import Foundation
    
    let headers = ["Authorization": "Basic REPLACE_BASIC_AUTH"]
    
    let request = NSMutableURLRequest(url: NSURL(string: "https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc")! as URL,
                                            cachePolicy: .useProtocolCachePolicy,
                                        timeoutInterval: 10.0)
    request.httpMethod = "GET"
    request.allHTTPHeaderFields = headers
    
    let session = URLSession.shared
    let dataTask = session.dataTask(with: request as URLRequest, completionHandler: { (data, response, error) -> Void in
      if (error != nil) {
        print(error)
      } else {
        let httpResponse = response as? HTTPURLResponse
        print(httpResponse)
      }
    })
    
    dataTask.resume()
    ```
  </CodeSample>

  <CodeSample value="kotlin_okhttp" lang="kotlin">
    ```kotlin
    val client = OkHttpClient()
    
    val request = Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order?per_page=50&page=1&q=INV_20231201&order_status=CAPTURED&customer_id=CUST_001&created_at=2023-12-01&from_created_at=2023-12-01&end_created_at=2023-12-31&sort=created_at%3Adesc")
      .get()
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .build()
    
    val response = client.newCall(request).execute()
    ```
  </CodeSample>

</CodeSamples>

# API hủy đơn hàng

## Hủy đơn hàng chưa thanh toán qua API cancelOrder của Cổng thanh toán SePay — dừng đơn pending trước khi capture, trả về trạng thái mới ngay.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

<Callout type="info" title="Ghi chú">
Áp dụng cho Payment_method=BANK_TRANSFER hoặc Payment_method=NAPAS_BANK_TRANSFER
Chỉ được hủy khi order_status khác CAPTURED và CANCELED
</Callout>

## API Endpoint

<Endpoint>
  <Method>POST</Method>

  <Path>https://pgapi.sepay.vn/v1/order/cancel</Path>

  <Description>
    Hủy đơn hàng
  </Description>

  <Authentication>
    basicAuth
  </Authentication>
</Endpoint>

## API Request

<Params>
  <RequestBody>
    <Fields>
      <Field name="order_invoice_number" type="string" required="true">
        Mã hóa đơn đơn hàng cần hủy
      </Field>
    </Fields>

    <Example>
      {
        "order_invoice_number": "DH1757053857"
      }
    </Example>
  </RequestBody>
</Params>

## API Response

<Responses>
  <Response status="200">
    <Description>
      Hủy đơn hàng thành công
    </Description>

    <Example>
      {
        "message": "Đã hủy đơn hàng thành công"
      }
    </Example>
  </Response>

</Responses>

<ResponseDescriptionFields>
  <ResponseSchema status="200">
    <Fields>
      <Field name="message" type="string" required="false">
        Thông báo kết quả
      </Field>
    </Fields>
  </ResponseSchema>

</ResponseDescriptionFields>

## Code mẫu

<CodeSamples>
  <CodeSamplesList>
    <CodeSamplesTrigger value="shell_curl">
      cURL
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="php_curl">
      PHP
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="python_python3">
      Python
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="node_native">
      NodeJS
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="java_okhttp">
      Java
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="ruby_native">
      Ruby
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="go_native">
      Go
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="csharp_httpclient">
      .NET
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="swift_nsurlsession">
      Swift
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="kotlin_okhttp">
      Kotlin
    </CodeSamplesTrigger>

  </CodeSamplesList>

  <CodeSample value="shell_curl" lang="bash">
    ```bash
    curl --request POST \
      --url https://pgapi.sepay.vn/v1/order/cancel \
      --header 'Authorization: Basic REPLACE_BASIC_AUTH' \
      --header 'content-type: application/json' \
      --data '{"order_invoice_number":"DH1757053857"}'
    ```
  </CodeSample>

  <CodeSample value="php_curl" lang="php">
    ```php
    <?php
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://pgapi.sepay.vn/v1/order/cancel",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"order_invoice_number\":\"DH1757053857\"}",
      CURLOPT_HTTPHEADER => [
        "Authorization: Basic REPLACE_BASIC_AUTH",
        "content-type: application/json"
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
    ```
  </CodeSample>

  <CodeSample value="python_python3" lang="python">
    ```python
    import http.client
    
    conn = http.client.HTTPSConnection("pgapi.sepay.vn")
    
    payload = "{\"order_invoice_number\":\"DH1757053857\"}"
    
    headers = {
        'Authorization': "Basic REPLACE_BASIC_AUTH",
        'content-type': "application/json"
        }
    
    conn.request("POST", "/v1/order/cancel", payload, headers)
    
    res = conn.getresponse()
    data = res.read()
    
    print(data.decode("utf-8"))
    ```
  </CodeSample>

  <CodeSample value="node_native" lang="javascript">
    ```javascript
    const http = require("https");
    
    const options = {
      "method": "POST",
      "hostname": "pgapi.sepay.vn",
      "port": null,
      "path": "/v1/order/cancel",
      "headers": {
        "Authorization": "Basic REPLACE_BASIC_AUTH",
        "content-type": "application/json"
      }
    };
    
    const req = http.request(options, function (res) {
      const chunks = [];
    
      res.on("data", function (chunk) {
        chunks.push(chunk);
      });
    
      res.on("end", function () {
        const body = Buffer.concat(chunks);
        console.log(body.toString());
      });
    });
    
    req.write(JSON.stringify({order_invoice_number: 'DH1757053857'}));
    req.end();
    ```
  </CodeSample>

  <CodeSample value="java_okhttp" lang="java">
    ```java
    OkHttpClient client = new OkHttpClient();
    
    MediaType mediaType = MediaType.parse("application/json");
    RequestBody body = RequestBody.create(mediaType, "{\"order_invoice_number\":\"DH1757053857\"}");
    Request request = new Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order/cancel")
      .post(body)
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .addHeader("content-type", "application/json")
      .build();
    
    Response response = client.newCall(request).execute();
    ```
  </CodeSample>

  <CodeSample value="ruby_native" lang="ruby">
    ```ruby
    require 'uri'
    require 'net/http'
    require 'openssl'
    
    url = URI("https://pgapi.sepay.vn/v1/order/cancel")
    
    http = Net::HTTP.new(url.host, url.port)
    http.use_ssl = true
    http.verify_mode = OpenSSL::SSL::VERIFY_NONE
    
    request = Net::HTTP::Post.new(url)
    request["Authorization"] = 'Basic REPLACE_BASIC_AUTH'
    request["content-type"] = 'application/json'
    request.body = "{\"order_invoice_number\":\"DH1757053857\"}"
    
    response = http.request(request)
    puts response.read_body
    ```
  </CodeSample>

  <CodeSample value="go_native" lang="go">
    ```go
    package main
    
    import (
    	"fmt"
    	"strings"
    	"net/http"
    	"io/ioutil"
    )
    
    func main() {
    
    	url := "https://pgapi.sepay.vn/v1/order/cancel"
    
    	payload := strings.NewReader("{\"order_invoice_number\":\"DH1757053857\"}")
    
    	req, _ := http.NewRequest("POST", url, payload)
    
    	req.Header.Add("Authorization", "Basic REPLACE_BASIC_AUTH")
    	req.Header.Add("content-type", "application/json")
    
    	res, _ := http.DefaultClient.Do(req)
    
    	defer res.Body.Close()
    	body, _ := ioutil.ReadAll(res.Body)
    
    	fmt.Println(res)
    	fmt.Println(string(body))
    
    }
    ```
  </CodeSample>

  <CodeSample value="csharp_httpclient" lang="csharp">
    ```csharp
    var client = new HttpClient();
    var request = new HttpRequestMessage
    {
        Method = HttpMethod.Post,
        RequestUri = new Uri("https://pgapi.sepay.vn/v1/order/cancel"),
        Headers =
        {
            { "Authorization", "Basic REPLACE_BASIC_AUTH" },
        },
        Content = new StringContent("{\"order_invoice_number\":\"DH1757053857\"}")
        {
            Headers =
            {
                ContentType = new MediaTypeHeaderValue("application/json")
            }
        }
    };
    using (var response = await client.SendAsync(request))
    {
        response.EnsureSuccessStatusCode();
        var body = await response.Content.ReadAsStringAsync();
        Console.WriteLine(body);
    }
    ```
  </CodeSample>

  <CodeSample value="swift_nsurlsession" lang="swift">
    ```swift
    import Foundation
    
    let headers = [
      "Authorization": "Basic REPLACE_BASIC_AUTH",
      "content-type": "application/json"
    ]
    let parameters = ["order_invoice_number": "DH1757053857"] as [String : Any]
    
    let postData = JSONSerialization.data(withJSONObject: parameters, options: [])
    
    let request = NSMutableURLRequest(url: NSURL(string: "https://pgapi.sepay.vn/v1/order/cancel")! as URL,
                                            cachePolicy: .useProtocolCachePolicy,
                                        timeoutInterval: 10.0)
    request.httpMethod = "POST"
    request.allHTTPHeaderFields = headers
    request.httpBody = postData as Data
    
    let session = URLSession.shared
    let dataTask = session.dataTask(with: request as URLRequest, completionHandler: { (data, response, error) -> Void in
      if (error != nil) {
        print(error)
      } else {
        let httpResponse = response as? HTTPURLResponse
        print(httpResponse)
      }
    })
    
    dataTask.resume()
    ```
  </CodeSample>

  <CodeSample value="kotlin_okhttp" lang="kotlin">
    ```kotlin
    val client = OkHttpClient()
    
    val mediaType = MediaType.parse("application/json")
    val body = RequestBody.create(mediaType, "{\"order_invoice_number\":\"DH1757053857\"}")
    val request = Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order/cancel")
      .post(body)
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .addHeader("content-type", "application/json")
      .build()
    
    val response = client.newCall(request).execute()
    ```
  </CodeSample>

</CodeSamples>

# API Void giao dịch thẻ

## Hủy (void) giao dịch thẻ qua API voidTransaction của Cổng thanh toán SePay — đảo giao dịch đã CAPTURED trước thời điểm quyết toán nhanh chóng.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

## Điều kiện hủy giao dịch

<Callout type="info" title="Chỉ cho phép hủy giao dịch khi thỏa mãn các điều kiện sau:">
Chỉ cho hủy giao dịch khi thanh toán bằng thẻ (
`payment_method=CARD`
).
Chỉ cho hủy giao dịch khi trạng thái đơn hàng là 
`CAPTURED`
 (
`order_status=CAPTURED`
).
Chỉ cho hủy giao dịch khi chưa đến thời điểm quyết toán. Lịch quyết toán:
Trước 16:00 (4 giờ chiều): Tiền về ngày làm việc kế tiếp (T + 1).
Ví dụ: Giao dịch lúc 
15:30 Thứ 3 (T3)
 → Tiền về 
Thứ 4 (T4)
.
Sau 16:00 (4 giờ chiều): Tiền về sau 2 ngày làm việc (T + 2).
Ví dụ: Giao dịch lúc 
17:00 Thứ 3 (T3)
 → Tiền về 
Thứ 5 (T5)
.
Sau 16:00 Thứ 6 (T6): Tiền về Thứ 2 tuần kế tiếp (T2).
</Callout>

## API Endpoint

<Endpoint>
  <Method>POST</Method>

  <Path>https://pgapi.sepay.vn/v1/order/voidTransaction</Path>

  <Description>
    Hủy giao dịch
  </Description>

  <Authentication>
    basicAuth
  </Authentication>
</Endpoint>

## API Request

<Params>
  <RequestBody>
    <Fields>
      <Field name="order_invoice_number" type="string" required="true">
        Mã hóa đơn của giao dịch thanh toán cần hủy
      </Field>
    </Fields>

    <Example>
      {
        "order_invoice_number": "DH1756370479"
      }
    </Example>
  </RequestBody>
</Params>

## API Response

<Responses>
  <Response status="200">
    <Description>
      Hủy giao dịch thành công
    </Description>

    <Example>
      {
        "message": "Đã hủy giao dịch thành công"
      }
    </Example>
  </Response>

</Responses>

<ResponseDescriptionFields>
  <ResponseSchema status="200">
    <Fields>
      <Field name="message" type="string" required="false">
        Thông báo kết quả
      </Field>
    </Fields>
  </ResponseSchema>

</ResponseDescriptionFields>

## Code mẫu

<CodeSamples>
  <CodeSamplesList>
    <CodeSamplesTrigger value="shell_curl">
      cURL
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="php_curl">
      PHP
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="python_python3">
      Python
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="node_native">
      NodeJS
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="java_okhttp">
      Java
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="ruby_native">
      Ruby
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="go_native">
      Go
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="csharp_httpclient">
      .NET
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="swift_nsurlsession">
      Swift
    </CodeSamplesTrigger>

    <CodeSamplesTrigger value="kotlin_okhttp">
      Kotlin
    </CodeSamplesTrigger>

  </CodeSamplesList>

  <CodeSample value="shell_curl" lang="bash">
    ```bash
    curl --request POST \
      --url https://pgapi.sepay.vn/v1/order/voidTransaction \
      --header 'Authorization: Basic REPLACE_BASIC_AUTH' \
      --header 'content-type: application/json' \
      --data '{"order_invoice_number":"DH1756370479"}'
    ```
  </CodeSample>

  <CodeSample value="php_curl" lang="php">
    ```php
    <?php
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://pgapi.sepay.vn/v1/order/voidTransaction",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"order_invoice_number\":\"DH1756370479\"}",
      CURLOPT_HTTPHEADER => [
        "Authorization: Basic REPLACE_BASIC_AUTH",
        "content-type: application/json"
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
    ```
  </CodeSample>

  <CodeSample value="python_python3" lang="python">
    ```python
    import http.client
    
    conn = http.client.HTTPSConnection("pgapi.sepay.vn")
    
    payload = "{\"order_invoice_number\":\"DH1756370479\"}"
    
    headers = {
        'Authorization': "Basic REPLACE_BASIC_AUTH",
        'content-type': "application/json"
        }
    
    conn.request("POST", "/v1/order/voidTransaction", payload, headers)
    
    res = conn.getresponse()
    data = res.read()
    
    print(data.decode("utf-8"))
    ```
  </CodeSample>

  <CodeSample value="node_native" lang="javascript">
    ```javascript
    const http = require("https");
    
    const options = {
      "method": "POST",
      "hostname": "pgapi.sepay.vn",
      "port": null,
      "path": "/v1/order/voidTransaction",
      "headers": {
        "Authorization": "Basic REPLACE_BASIC_AUTH",
        "content-type": "application/json"
      }
    };
    
    const req = http.request(options, function (res) {
      const chunks = [];
    
      res.on("data", function (chunk) {
        chunks.push(chunk);
      });
    
      res.on("end", function () {
        const body = Buffer.concat(chunks);
        console.log(body.toString());
      });
    });
    
    req.write(JSON.stringify({order_invoice_number: 'DH1756370479'}));
    req.end();
    ```
  </CodeSample>

  <CodeSample value="java_okhttp" lang="java">
    ```java
    OkHttpClient client = new OkHttpClient();
    
    MediaType mediaType = MediaType.parse("application/json");
    RequestBody body = RequestBody.create(mediaType, "{\"order_invoice_number\":\"DH1756370479\"}");
    Request request = new Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order/voidTransaction")
      .post(body)
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .addHeader("content-type", "application/json")
      .build();
    
    Response response = client.newCall(request).execute();
    ```
  </CodeSample>

  <CodeSample value="ruby_native" lang="ruby">
    ```ruby
    require 'uri'
    require 'net/http'
    require 'openssl'
    
    url = URI("https://pgapi.sepay.vn/v1/order/voidTransaction")
    
    http = Net::HTTP.new(url.host, url.port)
    http.use_ssl = true
    http.verify_mode = OpenSSL::SSL::VERIFY_NONE
    
    request = Net::HTTP::Post.new(url)
    request["Authorization"] = 'Basic REPLACE_BASIC_AUTH'
    request["content-type"] = 'application/json'
    request.body = "{\"order_invoice_number\":\"DH1756370479\"}"
    
    response = http.request(request)
    puts response.read_body
    ```
  </CodeSample>

  <CodeSample value="go_native" lang="go">
    ```go
    package main
    
    import (
    	"fmt"
    	"strings"
    	"net/http"
    	"io/ioutil"
    )
    
    func main() {
    
    	url := "https://pgapi.sepay.vn/v1/order/voidTransaction"
    
    	payload := strings.NewReader("{\"order_invoice_number\":\"DH1756370479\"}")
    
    	req, _ := http.NewRequest("POST", url, payload)
    
    	req.Header.Add("Authorization", "Basic REPLACE_BASIC_AUTH")
    	req.Header.Add("content-type", "application/json")
    
    	res, _ := http.DefaultClient.Do(req)
    
    	defer res.Body.Close()
    	body, _ := ioutil.ReadAll(res.Body)
    
    	fmt.Println(res)
    	fmt.Println(string(body))
    
    }
    ```
  </CodeSample>

  <CodeSample value="csharp_httpclient" lang="csharp">
    ```csharp
    var client = new HttpClient();
    var request = new HttpRequestMessage
    {
        Method = HttpMethod.Post,
        RequestUri = new Uri("https://pgapi.sepay.vn/v1/order/voidTransaction"),
        Headers =
        {
            { "Authorization", "Basic REPLACE_BASIC_AUTH" },
        },
        Content = new StringContent("{\"order_invoice_number\":\"DH1756370479\"}")
        {
            Headers =
            {
                ContentType = new MediaTypeHeaderValue("application/json")
            }
        }
    };
    using (var response = await client.SendAsync(request))
    {
        response.EnsureSuccessStatusCode();
        var body = await response.Content.ReadAsStringAsync();
        Console.WriteLine(body);
    }
    ```
  </CodeSample>

  <CodeSample value="swift_nsurlsession" lang="swift">
    ```swift
    import Foundation
    
    let headers = [
      "Authorization": "Basic REPLACE_BASIC_AUTH",
      "content-type": "application/json"
    ]
    let parameters = ["order_invoice_number": "DH1756370479"] as [String : Any]
    
    let postData = JSONSerialization.data(withJSONObject: parameters, options: [])
    
    let request = NSMutableURLRequest(url: NSURL(string: "https://pgapi.sepay.vn/v1/order/voidTransaction")! as URL,
                                            cachePolicy: .useProtocolCachePolicy,
                                        timeoutInterval: 10.0)
    request.httpMethod = "POST"
    request.allHTTPHeaderFields = headers
    request.httpBody = postData as Data
    
    let session = URLSession.shared
    let dataTask = session.dataTask(with: request as URLRequest, completionHandler: { (data, response, error) -> Void in
      if (error != nil) {
        print(error)
      } else {
        let httpResponse = response as? HTTPURLResponse
        print(httpResponse)
      }
    })
    
    dataTask.resume()
    ```
  </CodeSample>

  <CodeSample value="kotlin_okhttp" lang="kotlin">
    ```kotlin
    val client = OkHttpClient()
    
    val mediaType = MediaType.parse("application/json")
    val body = RequestBody.create(mediaType, "{\"order_invoice_number\":\"DH1756370479\"}")
    val request = Request.Builder()
      .url("https://pgapi.sepay.vn/v1/order/voidTransaction")
      .post(body)
      .addHeader("Authorization", "Basic REPLACE_BASIC_AUTH")
      .addHeader("content-type", "application/json")
      .build()
    
    val response = client.newCall(request).execute()
    ```
  </CodeSample>

</CodeSamples>

# IPN xác nhận thanh toán Cổng Thanh Toán

## Cấu hình IPN endpoint trong Cổng thanh toán SePay để nhận callback xác nhận thanh toán tức thì, an toàn và đúng theo thời gian thực.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

## Cấu hình IPN URL

IPN URL được cấu hình tại trang quản lý merchant trên SePay:

1. Đăng nhập vào [SePay](https://sepay.vn)
2. Vào **Cổng thanh toán → Cấu hình → IPN**
3. Nhập URL endpoint của bạn để nhận IPN
4. Lưu cấu hình

<Callout type="warn" title="Lưu ý quan trọng">
IPN URL phải là 
HTTPS
 và endpoint phải trả về HTTP status code 
200
 để xác nhận đã nhận thành công.
</Callout>

***

## Request từ SePay đến Merchant

<Endpoint method="POST" path="https://your-url (url bạn cấu hình trong IPN)" />

**Headers:**

```http
X-Secret-Key: <secret_key>
Content-Type: application/json
```

<Callout type="tip" title="Ghi chú">
X-Secret-Key:
 Secret key để xác thực (chỉ có khi merchant cấu hình auth type = SECRET_KEY)
</Callout>

**Danh sách tham số**

<ParamsTable rows={[{ "name": "timestamp", "type": "integer", "required": true, "description": "Unix timestamp khi gửi thông báo" }, { "name": "notification_type", "type": "string", "required": true, "description": "Loại thông báo: ORDER_PAID (thanh toán thành công), TRANSACTION_VOID (hủy giao dịch)" }, { "name": "order", "type": "object", "required": true, "description": "Thông tin đơn hàng", "children": [{ "name": "id", "type": "uuidv4", "required": true, "description": "ID đơn hàng nội bộ SePay" }, { "name": "order_id", "type": "string", "required": true, "description": "Mã đơn hàng duy nhất" }, { "name": "order_status", "type": "string", "required": true, "description": "Trạng thái: CAPTURED (đã thanh toán), CANCELLED (đã hủy), AUTHENTICATION_NOT_NEEDED (đang đợi thanh toán)" }, { "name": "order_currency", "type": "string", "required": true, "description": "Mã tiền tệ (VND)" }, { "name": "order_amount", "type": "string", "required": true, "description": "Số tiền đơn hàng" }, { "name": "order_invoice_number", "type": "string", "required": true, "description": "Mã hóa đơn" }, { "name": "custom_data", "type": "array", "required": true, "description": "Dữ liệu tùy chỉnh" }, { "name": "user_agent", "type": "string", "required": true, "description": "User agent của khách hàng" }, { "name": "ip_address", "type": "string", "required": true, "description": "IP address của khách hàng" }, { "name": "order_description", "type": "string", "required": true, "description": "Mô tả đơn hàng" }] }, { "name": "transaction", "type": "object", "required": true, "description": "Thông tin giao dịch", "children": [{ "name": "id", "type": "uuidv4", "required": true, "description": "ID giao dịch nội bộ" }, { "name": "payment_method", "type": "string", "required": true, "description": "Phương thức thanh toán" }, { "name": "transaction_id", "type": "string", "required": true, "description": "Mã giao dịch duy nhất" }, { "name": "transaction_type", "type": "string", "required": true, "description": "Loại giao dịch: PAYMENT, REFUND" }, { "name": "transaction_date", "type": "string", "required": true, "description": "Ngày giờ giao dịch" }, { "name": "transaction_status", "type": "string", "required": true, "description": "Trạng thái: APPROVED, DECLINED" }, { "name": "transaction_amount", "type": "string", "required": true, "description": "Số tiền giao dịch" }, { "name": "transaction_currency", "type": "string", "required": true, "description": "Mã tiền tệ" }] }, { "name": "customer", "type": "object", "required": true, "description": "Thông tin khách hàng", "children": [{ "name": "id", "type": "uuidv4", "required": true, "description": "ID khách hàng nội bộ" }, { "name": "customer_id", "type": "string", "required": true, "description": "ID khách hàng của merchant" }] }]} />

**Ví dụ request body:**

<Response title="REQUEST">
```json
{
  "timestamp": 1757058220,
  "notification_type": "ORDER_PAID",
  "order": {
    "id": "e2c195be-c721-47eb-b323-99ab24e52d85",
    "order_id": "NPSETVI00101000042R",
    "order_status": "CAPTURED",
    "order_currency": "VND",
    "order_amount": "50000.00",
    "order_invoice_number": "SUB_202509_001",
    "custom_data": [],
    "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
    "ip_address": "14.xxx.xxx.xxx",
    "order_description": "Thanh toán định kỳ gói Premium tháng 9/2025"
  },
  "transaction": {
    "id": "384c66dd-41e6-4316-a544-b4141682595c",
    "payment_method": "CARD",
    "transaction_id": "68ba94ac80123",
    "transaction_type": "PAYMENT",
    "transaction_date": "2025-09-01 00:00:15",
    "transaction_status": "APPROVED",
    "transaction_amount": "50000",
    "transaction_currency": "VND",
    "authentication_status": "AUTHENTICATION_SUCCESSFUL",
    "card_number": "4111XXXXXXXX1111",
    "card_holder_name": "NGUYEN VAN A",
    "card_expiry": "12/26",
    "card_funding_method": "CREDIT",
    "card_brand": "VISA"
  },
  "customer": {
    "id": "bae12d2f-0580-4669-8841-cc35cf671613",
    "customer_id": "CUST_001"
  }
}
```
</Response>

**Xử lý IPN endpoint:**

<Php title="PHP">
```php
Route::post('/payment/ipn', function(Request $request) {
  // Verify secret key
  if ($request->header('X-Secret-Key') !== $secretKey) {
      return response()->json(['error' => 'Unauthorized'], 401);
  }

  $data = $request->json()->all();

  if ($data['notification_type'] === 'ORDER_PAID') {
      $order = Order::where('invoice_number', $data['order']['order_invoice_number'])->first();
      $order->status = 'paid';
      $order->save();
  }

  // Return 200 to acknowledge receipt
  return response()->json(['success' => true], 200);
});
```
</Php>

# SePay SDK cho PHP

## Tích hợp Cổng thanh toán SePay trong PHP qua SDK chính thức — tạo thanh toán, xử lý chuyển khoản, QR code và callback IPN nhanh chóng.

---

**API Overview:**

API cổng thanh toán SePay hỗ trợ nhiều phương thức thanh toán bao gồm chuyển khoản ngân hàng qua QR code, NAPAS QR và thẻ quốc tế.

**Base URLs:**
- Production API: `https://pgapi.sepay.vn`
- Sandbox API: `https://pgapi-sandbox.sepay.vn`
- Production Checkout: `https://pay.sepay.vn`
- Sandbox Checkout: `https://pay-sandbox.sepay.vn`

**Xác thực:** Tất cả API sử dụng Basic Authentication với `merchant_id` và `secret_key`.


---

<Callout type="warn" title="Yêu cầu">
PHP 7.4 trở lên, ext-json, ext-curl, Guzzle HTTP client
</Callout>

## Cài đặt

<Node title="Cài đặt">
```js
composer require sepay/sepay-pg
```
</Node>

***

## Khởi tạo Client

<Php title="Khởi tạo">
```php
use SePay\\SePayClient;
use SePay\\Builders\\CheckoutBuilder;

// Initialize client
$sepay = new SePayClient(
  'SP-TEST-XXXXXXX',
  'spsk_live_xxxxxxxxxxxo99PoE7RsBpss3EFH5nV',
  SePayClient::ENVIRONMENT_SANDBOX, // or ENVIRONMENT_PRODUCTION
 []
);
```
</Php>

**Giải thích tham số**

<ParamsTable rows={[{ "name": "SP-TEST-XXXXXXX", "type": "string", "required": true, "description": "Mã đơn vị merchant" }, { "name": "spsk_live_x...", "type": "string", "required": true, "description": "Khóa bảo mật merchant" }, { "name": "SePayClient::ENVIRONMENT_...", "type": "string", "required": true, "description": "Biến môi trường (ENVIRONMENT_SANDBOX hoặc ENVIRONMENT_PRODUCTION)" }, { "name": "[]", "type": "array", "required": false, "description": "Mảng các giá trị cấu hình khác" }]} />

**Ví dụ các cấu hình khác:**

<Php title="PHP">
```php
[
  'timeout' => 60,           // Thời gian chờ của request (tính bằng giây)
  'retry_attempts' => 5,     // Số lần thử lại khi request thất bại
  'retry_delay' => 2000,     // Thời gian trễ giữa các lần thử lại (tính bằng mili giây)
  'debug' => true,           // Bật chế độ debug (ghi log chi tiết)
  'user_agent' => 'MyApp/1.0 SePay-PHP-SDK/1.0.0', // Chuỗi định danh ứng dụng gửi đi trong request
  'logger' => $customLogger, // Logger tương thích chuẩn PSR-3
];
```
</Php>

***

## Khởi tạo đối tượng cho biểu mẫu thanh toán (Đơn hàng thanh toán 1 lần)

<Php title="Khởi tạo thanh toán một lần">
```php
$checkoutData = CheckoutBuilder::make()
  ->currency('VND')
  ->orderAmount(100000) // 100,000 VND
  ->operation('PURCHASE')
  ->orderDescription('Test payment')
  ->orderInvoiceNumber('INV_001')
  ->successUrl('https://yoursite.com/success')
  ->build();

// Create form fields with signature
$formFields = $sepay->checkout()->generateFormFields($checkoutData);
```
</Php>

**Giải thích thuộc tính**

<ParamsTable rows={[{ "name": "operation", "type": "string", "required": true, "description": "Loại giao dịch, hiện chỉ hỗ trợ: PURCHASE" }, { "name": "orderInvoiceNumber", "type": "string", "required": true, "description": "Mã đơn hàng/hoá đơn (duy nhất)" }, { "name": "orderAmount", "type": "string", "required": true, "description": "Số tiền giao dịch" }, { "name": "currency", "type": "string", "required": true, "description": "Đơn vị tiền tệ (VD: VND, USD)" }, { "name": "paymentMethod", "type": "string", "required": false, "description": "Phương thức thanh toán: CARD, BANK_TRANSFER" }, { "name": "orderDescription", "type": "string", "required": false, "description": "Mô tả đơn hàng" }, { "name": "customerId", "type": "string", "required": false, "description": "Mã khách hàng (nếu có)" }, { "name": "successUrl", "type": "string", "required": false, "description": "URL callback khi thanh toán thành công" }, { "name": "errorUrl", "type": "string", "required": false, "description": "URL callback khi xảy ra lỗi" }, { "name": "cancelUrl", "type": "string", "required": false, "description": "URL callback khi người dùng hủy thanh toán" }]} />

**Tạo form xử lý thanh toán**

<Callout type="danger" title="Lưu ý quan trọng về tạo form HTML và chữ ký">
Nếu bạn tự dựng form HTML và xây dựng hàm tạo chữ ký không theo code mẫu thì cần phải đảm bảo thứ tự các trường giống như danh sách tham số ở trên để quá trình ký khớp tuyệt đối phía SePay; Nếu bạn hoán đổi vị trí các trường thì có thể dẫn đến chữ ký bị sai và SePay sẽ xem yêu cầu này là không hợp lệ
</Callout>

<Node title="Form thanh toán">
```js
<form method="POST" action="https://pay.sepay.vn/checkout/init">
  <?php foreach ($formFields as $name => $value): ?>
      <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>">
  <?php endforeach; ?>

  <button type="submit">Thanh toán ngay</button>
</form>
```
</Node>

<Callout type="info" title="Ghi chú">
Bật chế độ debug: 
`$sepay->enableDebugMode();`
 - Cấu hình hành vi thử lại: 
`$sepay->setRetryAttempts(3)->setRetryDelay(1000);`
</Callout>

***

## API

<Callout type="tip">
SDK cung cấp các phương thức để gọi Open API cho cổng thanh toán SePay.
</Callout>

<Php title="Tra cứu danh sách đơn hàng">
```php
$orders = $sepay->orders()->list([
  'per_page' => 10,
  'order_status' => 'CAPTURED',
  'from_created_at' => '2025-01-01',
  'to_created_at' => '2025-12-31',
]);
```
</Php>

<Php title="Xem chi tiết đơn hàng">
```php
$order = $sepay->orders()->retrieve('ORDER_INVOICE_NUMBER');
```
</Php>

<Node title="Hủy giao dịch đơn hàng">
```js
$result = $sepay->orders()->voidTransaction('ORDER_INVOICE_NUMBER');
```
</Node>

<Callout type="warning" title="Ghi chú">
Đơn hàng được tạo khi khách hàng hoàn tất thanh toán, không phải trực tiếp thông qua API.
</Callout>

***

## Xử lý lỗi

<Callout type="tip">
The SDK has different exception types for different errors
</Callout>

<Php title="PHP">
```php
use SePay\\Exceptions\\AuthenticationException;
use SePay\\Exceptions\\ValidationException;
use SePay\\Exceptions\\NotFoundException;
use SePay\\Exceptions\\RateLimitException;
use SePay\\Exceptions\\ServerException;

try {
  $order = $sepay->orders()->retrieve('ORDER_INVOICE_NUMBER');
} catch (AuthenticationException $e) {
  // Invalid credentials or signature
  echo "Authentication failed: " . $e->getMessage();
} catch (ValidationException $e) {
  // Invalid request data
  echo "Validation error: " . $e->getMessage();

  // Get field-specific errors
  if ($e->hasFieldError('amount')) {
      $errors = $e->getFieldErrors('amount');
      echo "Amount errors: " . implode(', ', $errors);
  }
} catch (NotFoundException $e) {
  // Resource not found
  echo "Not found: " . $e->getMessage();
} catch (RateLimitException $e) {
  // Rate limit exceeded
  echo "Rate limited. Retry after: " . $e->getRetryAfter() . " seconds";
} catch (ServerException $e) {
  // Server error (5xx)
  echo "Server error: " . $e->getMessage();
}
```
</Php>

***

## Kiểm thử

<TextBlock title="Lệnh kiểm tra">
```text
# Chạy toàn bộ test
composer test

# Chạy test kèm báo cáo độ bao phủ mã (coverage)
composer test-coverage

# Phân tích tĩnh mã nguồn (static analysis)
composer phpstan

# Sửa định dạng mã nguồn (code style)
composer cs-fix
```
</TextBlock>

***

<Callout type="tip">
Xem chi tiết hướng dẫn cài đặt và sử dụng tại 
GitHub Repository
</Callout>

