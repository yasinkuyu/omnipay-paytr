<?php

namespace Omnipay\PayTR;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayTR\Message\CompletePurchaseRequest;
use Omnipay\PayTR\Message\FetchTransactionRequest;
use Omnipay\PayTR\Message\PurchaseRequest;
use Omnipay\PayTR\Message\RefundRequest;

/**
 * PayTR (iFrame API).
 *
 * purchase()            PayTR'den ödeme jetonu alır; yanıt müşteriyi PayTR ödeme sayfasına (iFrame adresi) götürür.
 * acceptNotification()  PayTR'nin sunucudan sunucuya bildirimi (Bildirim URL): imza doğrulanır; ödemenin sonucu YALNIZ
 *                       bununla kesinleşir. PayTR'ye düz "OK" yanıtı verilmelidir. completePurchase() aynısı.
 * refund()              tam ya da kısmi iade; fetchTransaction() durum sorgusu.
 *
 * (c) Yasin Kuyu — https://github.com/yasinkuyu/omnipay-paytr
 */
class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'PayTR';
    }

    public function getDefaultParameters(): array
    {
        return [
            'merchantId' => '',
            'merchantKey' => '',
            'merchantSalt' => '',
            'testMode' => false,
        ];
    }

    /** Mağaza numarası */
    public function getMerchantId() { return $this->getParameter('merchantId'); }
    public function setMerchantId($value): self { return $this->setParameter('merchantId', $value); }

    /** Mağaza parolası (merchant_key) */
    public function getMerchantKey() { return $this->getParameter('merchantKey'); }
    public function setMerchantKey($value): self { return $this->setParameter('merchantKey', $value); }

    /** Mağaza gizli anahtarı (merchant_salt) */
    public function getMerchantSalt() { return $this->getParameter('merchantSalt'); }
    public function setMerchantSalt($value): self { return $this->setParameter('merchantSalt', $value); }

    public function purchase(array $options = []): PurchaseRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    public function acceptNotification(array $options = []): CompletePurchaseRequest
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    public function completePurchase(array $options = []): CompletePurchaseRequest
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    public function refund(array $options = []): RefundRequest
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    public function fetchTransaction(array $options = []): FetchTransactionRequest
    {
        return $this->createRequest(FetchTransactionRequest::class, $options);
    }
}
