# Omnipay: PayTR

**[Omnipay](https://github.com/thephpleague/omnipay) driver for PayTR**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paytr/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paytr/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paytr/license)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)

[Türkçe](README.md) | **English**

## What does this package do?

It lets your shop take card payments with PayTR. Your server gets a **payment token** from PayTR, and the customer
enters the card on **PayTR's payment page** (embedded as an iFrame or full page). Card data never touches your server.

```
Your server ──(request token)──▶ PayTR ──▶ token
Customer ──▶ PayTR payment page (card + installments + 3D Secure)
PayTR ──(signed notification)──▶ Your server (Notification URL)   ← the result is final ONLY here
```

**Features**

| | |
|---|---|
| Payment | PayTR iFrame API; card, installments and 3D Secure on PayTR's page |
| Installments | You set the maximum (`maxInstallment`) |
| Refund | `refund()`, full or partial |
| Status | `fetchTransaction()` |
| Security | HMAC-SHA256; the notification is checked for signature, order number and amount |

## Installation

```bash
composer require yasinkuyu/omnipay-paytr php-http/guzzle7-adapter
```

Requires PHP 8.1 or later.

## What you need from PayTR

PayTR merchant panel → **Destek & Kurulum → Entegrasyon Bilgileri** (Support & Setup → Integration details):
`merchantId`, `merchantKey`, `merchantSalt`. On the same screen, set your notification page's address as the
**Notification URL**.

## Quick start

### 1. Set up the gateway

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('PayTR');
$gateway->initialize([
    'merchantId'   => '123456',
    'merchantKey'  => '********',
    'merchantSalt' => '********',
    'testMode'     => true,   // false in production
]);
```

### 2. Start the payment (pay.php)

```php
$response = $gateway->purchase([
    'amount'         => '149.90',
    'currency'       => 'TRY',
    'transactionId'  => 'ORD10045',                        // letters and digits only, unique per attempt
    'returnUrl'      => 'https://shop.com/pay-ok.php',
    'cancelUrl'      => 'https://shop.com/pay-fail.php',
    'clientIp'       => $_SERVER['REMOTE_ADDR'],           // required
    'maxInstallment' => 6,                                 // 0 = no installments
    'card'  => ['email' => 'ali@example.com', 'billingFirstName' => 'Ali', 'billingLastName' => 'Kaya', 'billingPhone' => '05551112233'],
    'items' => [['name' => 'Sweater', 'quantity' => 2, 'price' => '50.00'], ['name' => 'Shipping', 'quantity' => 1, 'price' => '49.90']],
])->send();

if ($response->isRedirect()) {
    // embed in your page:  <iframe src="<?= $response->getIframeUrl() ?>"></iframe>
    // or full page:        $response->redirect();
} else {
    echo $response->getMessage();
}
```

### 3. Handle the notification (pay-notify.php)

```php
$result = $gateway->acceptNotification([
    'transactionId' => $_POST['merchant_oid'] ?? '', // optional: to compare with your own record
])->send();

if ($result->isVerified()) {
    if ($result->isSuccessful()) {
        // paid: $result->getAmount(), $result->getTotalAmount() (incl. installment interest), $result->getInstallment()
    } else {
        // failed: $result->getMessage()
    }
}
echo 'OK'; // PayTR expects a plain "OK"; otherwise it retries the notification
```

> `returnUrl` / `cancelUrl` only bring the customer back; **don't decide the payment result there**. The result comes
> with the signed notification. A notification may arrive more than once for the same order: don't process duplicates.

## Refund and status

```php
$gateway->refund(['transactionId' => 'ORD10045', 'amount' => '50.00'])->send(); // full or partial
$gateway->fetchTransaction(['transactionId' => 'ORD10045'])->send()->getData();  // payment_amount, returns …
```

**If `isReadable()` returns false**, the response could not be read: the refund may have gone through. Do not retry
automatically; check the PayTR merchant panel.

## Testing

With `testMode => true` PayTR runs in test mode; no money is charged. Testing needs your own store's credentials; test
cards are in the integration document in the PayTR merchant panel.

## Troubleshooting

| Symptom | Cause |
|---|---|
| "Gecersiz istek veya magaza aktif degil" (invalid request or store not active) | Wrong `merchantId` / `merchantKey` / `merchantSalt`, or store not approved |
| "Geçersiz parametre(ler)" (invalid parameters) | Details are in `getMessage()` (e.g. e-mail, amount) |
| No notification arrives | Notification URL not set in the PayTR panel, or your page doesn't print "OK" |
| `isVerified()` always false | Different `merchantKey` / `merchantSalt` |

## Upgrading from 2.x

3.0 is a rewrite for Omnipay v3: direct sales with a card number were removed and payment is taken on PayTR's page;
the result is verified with `acceptNotification()`; refund and status inquiry were added. To keep using the old version
(2.x): `composer require yasinkuyu/omnipay-paytr:^2.0`

## Tests

```bash
composer install
vendor/bin/phpunit
```

## Other Omnipay packages

- **[Omnipay: NestPay (EST / Payten)](https://github.com/yasinkuyu/omnipay-nestpay)** — İş Bankası, Akbank, Halkbank, Ziraat, TEB, QNB, ING, Şekerbank, Anadolubank virtual POS; 3D Pay Hosting
- **[Omnipay: GVP (Garanti BBVA)](https://github.com/yasinkuyu/omnipay-gvp)** — Garanti BBVA virtual POS; hosted payment page
- **[Omnipay: Akbank](https://github.com/yasinkuyu/omnipay-akbank)** — Akbank virtual POS (new platform); hosted payment page
- **[Omnipay: QNB](https://github.com/yasinkuyu/omnipay-qnb)** — QNB virtual POS (PayFor); hosted payment page
- **[Omnipay: Paratika](https://github.com/yasinkuyu/omnipay-paratika)** — Paratika, VakıfPayS and ZiraatPay hosted payment page
- **[Omnipay: Paycell SDK](https://github.com/yasinkuyu/omnipay-paycell-sdk)** — Turkcell Paycell payment page (Web SDK)
- **[Omnipay: Paycell API](https://github.com/yasinkuyu/omnipay-paycell)** — Turkcell Paycell payment API
- **[Omnipay: iyzico](https://github.com/yasinkuyu/omnipay-iyzico)** — iyzico checkout form
- **[Omnipay: Posnet](https://github.com/yasinkuyu/omnipay-posnet)** — Yapı Kredi Posnet virtual POS

## License

MIT — [Yasin Kuyu](https://github.com/yasinkuyu)
