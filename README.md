# Omnipay: PayTR

**PayTR için [Omnipay](https://github.com/thephpleague/omnipay) sürücüsü**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paytr/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paytr/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paytr/license)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)

**Türkçe** | [English](README_EN.md)

## Bu paket ne yapar?

Mağazanızda PayTR ile kartla ödeme almanızı sağlar. Sunucunuz PayTR'den bir **ödeme jetonu** alır, müşteri kartını
**PayTR'nin ödeme sayfasında** (sitenize gömülü iFrame ya da tam sayfa) girer. Kart bilgisi sizin sunucunuzdan geçmez.

```
Sunucunuz ──(jeton iste)──▶ PayTR ──▶ jeton
Müşteri ──▶ PayTR ödeme sayfası (kart + taksit + 3D Secure)
PayTR ──(imzalı bildirim)──▶ Sunucunuz (Bildirim URL)   ← ödemenin sonucu YALNIZ burada kesinleşir
```

**Özellikler**

| | |
|---|---|
| Ödeme | PayTR iFrame API; kart, taksit ve 3D Secure PayTR'nin sayfasında |
| Taksit | En fazla taksit sayısını siz belirlersiniz (`maxInstallment`) |
| İade | `refund()`, tam ya da kısmi |
| Sorgu | `fetchTransaction()` |
| Güvenlik | HMAC-SHA256; bildirimde imza, sipariş no ve tutar denetimi |

## Kurulum

```bash
composer require yasinkuyu/omnipay-paytr php-http/guzzle7-adapter
```

PHP 8.1 ya da üstü gerekir.

## PayTR'den almanız gereken bilgiler

PayTR Mağaza Paneli → **Destek & Kurulum → Entegrasyon Bilgileri**: `merchantId` (Mağaza no), `merchantKey`
(Mağaza parola), `merchantSalt` (Mağaza gizli anahtar). Aynı ekranda **Bildirim URL** olarak bildirim sayfanızın
adresini girin.

## Hızlı başlangıç

### 1. Gateway'i hazırlayın

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('PayTR');
$gateway->initialize([
    'merchantId'   => '123456',
    'merchantKey'  => '********',
    'merchantSalt' => '********',
    'testMode'     => true,   // canlıya geçerken false
]);
```

### 2. Ödemeyi başlatın (odeme.php)

```php
$response = $gateway->purchase([
    'amount'         => '149.90',
    'currency'       => 'TRY',
    'transactionId'  => 'SIP10045',                         // yalnız harf ve rakam, her denemede benzersiz
    'returnUrl'      => 'https://magaza.com/odeme-tamam.php',
    'cancelUrl'      => 'https://magaza.com/odeme-hata.php',
    'clientIp'       => $_SERVER['REMOTE_ADDR'],            // zorunlu
    'maxInstallment' => 6,                                  // 0 = taksit yok
    'card'  => ['email' => 'ali@ornek.com', 'billingFirstName' => 'Ali', 'billingLastName' => 'Kaya', 'billingPhone' => '05551112233'],
    'items' => [['name' => 'Kazak', 'quantity' => 2, 'price' => '50.00'], ['name' => 'Kargo', 'quantity' => 1, 'price' => '49.90']],
])->send();

if ($response->isRedirect()) {
    // sayfanıza gömün:  <iframe src="<?= $response->getIframeUrl() ?>"></iframe>
    // ya da tam sayfa:  $response->redirect();
} else {
    echo $response->getMessage();
}
```

### 3. Bildirimi işleyin (odeme-bildirim.php)

```php
$result = $gateway->acceptNotification([
    'transactionId' => $_POST['merchant_oid'] ?? '', // isteğe bağlı: kendi kaydınızla karşılaştırmak için
])->send();

if ($result->isVerified()) {
    if ($result->isSuccessful()) {
        // ödendi: $result->getAmount(), $result->getTotalAmount() (vade farkı dahil), $result->getInstallment()
    } else {
        // başarısız: $result->getMessage()
    }
}
echo 'OK'; // PayTR düz "OK" bekler; aksi halde bildirimi yineler
```

> `returnUrl` / `cancelUrl` yalnız müşteriyi geri getirir; **ödemenin sonucuna orada karar vermeyin**. Sonuç imzalı
> bildirimle gelir. Aynı sipariş için bildirim birden çok kez gelebilir: yinelenen bildirimi işlemeyin.

## İade ve sorgu

```php
$gateway->refund(['transactionId' => 'SIP10045', 'amount' => '50.00'])->send(); // tam ya da kısmi
$gateway->fetchTransaction(['transactionId' => 'SIP10045'])->send()->getData();  // payment_amount, returns …
```

**`isReadable()` false dönerse** yanıt okunamamıştır: iade gerçekleşmiş olabilir. Otomatik olarak yeniden denemeyin;
PayTR Mağaza Paneli'nden kontrol edin.

## Test

`testMode => true` iken PayTR test modunda çalışır, para çekilmez. Test için mağazanızın kendi bilgileri gerekir; test
kartları PayTR Mağaza Paneli'ndeki entegrasyon belgesindedir.

## Sık karşılaşılan durumlar

| Belirti | Neden |
|---|---|
| "Gecersiz istek veya magaza aktif degil" | `merchantId` / `merchantKey` / `merchantSalt` yanlış ya da mağaza onaylanmamış |
| "Geçersiz parametre(ler)" | Hata ayrıntısı `getMessage()` içinde (ör. e-posta, tutar) |
| Bildirim gelmiyor | Bildirim URL PayTR panelinde tanımlı değil ya da sayfanız "OK" yazmıyor |
| `isVerified()` hep false | `merchantKey` / `merchantSalt` farklı |

## 2.x'ten geçiş

3.0 Omnipay v3 için baştan yazıldı: kart numarasıyla doğrudan satış kaldırıldı, ödeme PayTR'nin sayfasında alınır;
sonuç `acceptNotification()` ile doğrulanır; iade ve durum sorgusu eklendi. Eski sürümü (2.x) kullanmaya devam etmek
isterseniz: `composer require yasinkuyu/omnipay-paytr:^2.0`

## Testler

```bash
composer install
vendor/bin/phpunit
```

## Diğer Omnipay paketleri

- **[Omnipay: NestPay (EST / Payten)](https://github.com/yasinkuyu/omnipay-nestpay)** — İş Bankası, Akbank, Halkbank, Ziraat, TEB, QNB, ING, Şekerbank, Anadolubank sanal POS'u; 3D Pay Hosting
- **[Omnipay: GVP (Garanti BBVA)](https://github.com/yasinkuyu/omnipay-gvp)** — Garanti BBVA sanal POS'u; Ortak Ödeme Sayfası
- **[Omnipay: Akbank](https://github.com/yasinkuyu/omnipay-akbank)** — Akbank sanal POS'u (yeni altyapı); ortak ödeme sayfası
- **[Omnipay: QNB](https://github.com/yasinkuyu/omnipay-qnb)** — QNB sanal POS'u (PayFor); ortak ödeme sayfası
- **[Omnipay: Paratika](https://github.com/yasinkuyu/omnipay-paratika)** — Paratika, VakıfPayS ve ZiraatPay ortak ödeme sayfası
- **[Omnipay: Paycell SDK](https://github.com/yasinkuyu/omnipay-paycell-sdk)** — Turkcell Paycell ödeme sayfası (Web SDK)
- **[Omnipay: Paycell API](https://github.com/yasinkuyu/omnipay-paycell)** — Turkcell Paycell ödeme API'si
- **[Omnipay: iyzico](https://github.com/yasinkuyu/omnipay-iyzico)** — iyzico ödeme formu
- **[Omnipay: Posnet](https://github.com/yasinkuyu/omnipay-posnet)** — Yapı Kredi Posnet sanal POS'u

## Lisans

MIT — [Yasin Kuyu](https://github.com/yasinkuyu)
