<?php

namespace Omnipay\PayTR\Tests;

use Omnipay\PayTR\Gateway;
use Omnipay\PayTR\Message\AbstractRequest;
use Omnipay\Tests\GatewayTestCase;
use Symfony\Component\HttpFoundation\Request;

class GatewayTest extends GatewayTestCase
{
    /** @var Gateway */
    protected $gateway;

    private const CREDS = ['merchantId' => '123456', 'merchantKey' => 'KEY123', 'merchantSalt' => 'SALT123', 'testMode' => true];

    public function setUp(): void
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize(self::CREDS);
    }

    private function sent(): array
    {
        parse_str((string) $this->getMockClient()->getLastRequest()->getBody(), $f);

        return $f;
    }

    private function purchase(array $o = []): \Omnipay\PayTR\Message\PurchaseResponse
    {
        return $this->gateway->purchase($o + [
            'amount' => '149.90', 'transactionId' => 'SIP1', 'returnUrl' => 'https://shop.test/r', 'clientIp' => '1.2.3.4',
            'card' => ['email' => 'a@b.test', 'billingFirstName' => 'Ada', 'billingLastName' => 'Kaya', 'billingPhone' => '05551112233'],
            'items' => [['name' => 'Kazak', 'quantity' => 2, 'price' => '50.00'], ['name' => 'Kargo', 'quantity' => 1, 'price' => '49.90']],
            'maxInstallment' => 6,
        ])->send();
    }

    public function testPurchaseGetsTokenAndRedirectsToPaymentPage(): void
    {
        $this->setMockHttpResponse('TokenSuccess.txt');
        $r = $this->purchase();
        $this->assertTrue($r->isRedirect());
        $this->assertSame('GET', $r->getRedirectMethod());
        $this->assertSame('https://www.paytr.com/odeme/guvenli/a1b2c3d4e5', $r->getRedirectUrl());

        $f = $this->sent();
        $this->assertSame(AbstractRequest::TOKEN_URL, (string) $this->getMockClient()->getLastRequest()->getUri());
        $this->assertSame(['14990', 'TL', '0', '6', '1'], [$f['payment_amount'], $f['currency'], $f['no_installment'], $f['max_installment'], $f['test_mode']]);
        $this->assertSame([['Kazak', '50.00', 2], ['Kargo', '49.90', 1]], json_decode(base64_decode($f['user_basket']), true));
        $expected = base64_encode(hash_hmac('sha256', '1234561.2.3.4SIP1a@b.test14990' . $f['user_basket'] . '06TL1SALT123', 'KEY123', true));
        $this->assertSame($expected, $f['paytr_token']);
    }

    public function testNoInstallmentAndBasketFallback(): void
    {
        $this->setMockHttpResponse('TokenSuccess.txt');
        $this->purchase(['maxInstallment' => 0, 'items' => [['name' => 'A', 'quantity' => 1, 'price' => '1.00']]]);
        $f = $this->sent();
        $this->assertSame(['1', '0'], [$f['no_installment'], $f['max_installment']]);
        $this->assertSame([['Siparis SIP1', '149.90', 1]], json_decode(base64_decode($f['user_basket']), true));
    }

    public function testTokenFailure(): void
    {
        $this->setMockHttpResponse('TokenFailure.txt');
        $r = $this->purchase();
        $this->assertFalse($r->isRedirect());
        $this->assertSame('Geçersiz parametre(ler) (email Gerekli ve geçerli olmalı)', $r->getMessage());
    }

    private function notification(array $o = []): array
    {
        $f = array_merge(['merchant_oid' => 'SIP1', 'status' => 'success', 'total_amount' => '15500', 'payment_amount' => '14990',
            'payment_type' => 'card', 'installment_count' => '3', 'currency' => 'TL', 'test_mode' => '1'], $o);
        $f['hash'] = $o['hash'] ?? base64_encode(hash_hmac('sha256', $f['merchant_oid'] . 'SALT123' . $f['status'] . $f['total_amount'], 'KEY123', true));

        return $f;
    }

    public function testNotificationSuccess(): void
    {
        $this->gateway = new Gateway($this->getHttpClient(), Request::create('/n', 'POST', $this->notification()));
        $this->gateway->initialize(self::CREDS);
        $r = $this->gateway->acceptNotification(['transactionId' => 'SIP1', 'amount' => '149.90'])->send();
        $this->assertTrue($r->isVerified());
        $this->assertTrue($r->isSuccessful());
        $this->assertSame(['149.90', '155.00', 3, 'card', 'OK'], [$r->getAmount(), $r->getTotalAmount(), $r->getInstallment(), $r->getPaymentType(), $r->getAcknowledgement()]);
        $this->assertSame('completed', $r->getTransactionStatus());
    }

    public function testNotificationFailedPayment(): void
    {
        $r = $this->gateway->acceptNotification(['fields' => $this->notification(['status' => 'failed', 'total_amount' => '14990', 'failed_reason_code' => '2', 'failed_reason_msg' => 'Yetersiz bakiye'])])->send();
        $this->assertTrue($r->isVerified());
        $this->assertFalse($r->isSuccessful());
        $this->assertSame(['2', 'Yetersiz bakiye', 'failed'], [$r->getCode(), $r->getMessage(), $r->getTransactionStatus()]);
    }

    public function testForgedOrMismatchedNotificationIsRejected(): void
    {
        $f = $this->notification(['status' => 'failed', 'total_amount' => '14990']);
        $f['status'] = 'success';
        $this->assertFalse($this->gateway->acceptNotification(['fields' => $f])->send()->isVerified());
        $this->assertFalse($this->gateway->acceptNotification(['fields' => $this->notification(), 'transactionId' => 'BASKA'])->send()->isVerified());
        $this->assertFalse($this->gateway->acceptNotification(['fields' => $this->notification(), 'amount' => '200.00'])->send()->isVerified());
        $this->gateway->setMerchantSalt('BASKA');
        $this->assertFalse($this->gateway->acceptNotification(['fields' => $this->notification()])->send()->isVerified());
    }

    public function testRefund(): void
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $r = $this->gateway->refund(['transactionId' => 'SIP1', 'amount' => '50.00'])->send();
        $this->assertTrue($r->isSuccessful());
        $f = $this->sent();
        $this->assertSame(base64_encode(hash_hmac('sha256', '123456SIP150.00SALT123', 'KEY123', true)), $f['paytr_token']);

        $this->setMockHttpResponse('RefundError.txt');
        $r = $this->gateway->refund(['transactionId' => 'SIP1', 'amount' => '500.00'])->send();
        $this->assertFalse($r->isSuccessful());
        $this->assertSame(['006', 'Iade tutari odeme tutarindan buyuk olamaz'], [$r->getCode(), $r->getMessage()]);

        $this->setMockHttpResponse('Unreadable.txt');
        $this->assertFalse($this->gateway->refund(['transactionId' => 'SIP1', 'amount' => '1.00'])->send()->isReadable());
    }

    public function testStatusInquiry(): void
    {
        $this->setMockHttpResponse('StatusSuccess.txt');
        $r = $this->gateway->fetchTransaction(['transactionId' => 'SIP1'])->send();
        $this->assertTrue($r->isSuccessful());
        $this->assertSame('14990', $r->getData()['payment_amount']);
        $this->assertSame(base64_encode(hash_hmac('sha256', '123456SIP1SALT123', 'KEY123', true)), $this->sent()['paytr_token']);
        $this->assertSame(AbstractRequest::STATUS_URL, (string) $this->getMockClient()->getLastRequest()->getUri());
    }
}
