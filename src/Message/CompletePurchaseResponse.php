<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Bildirimin sonucu. isSuccessful(): imza geçerli + status = success. isVerified() false ise kaydedilmemeli.
 * total_amount müşterinin ödediği (vade farkı dahil), payment_amount sipariş tutarıdır (kuruş).
 */
class CompletePurchaseResponse extends AbstractResponse implements NotificationInterface
{
    public function __construct(RequestInterface $request, $data, protected bool $verified)
    {
        parent::__construct($request, $data);
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function isSuccessful(): bool
    {
        return $this->verified && ($this->data['status'] ?? '') === 'success';
    }

    public function getTransactionStatus(): string
    {
        if (!$this->verified) {
            return self::STATUS_PENDING;
        }

        return $this->isSuccessful() ? self::STATUS_COMPLETED : self::STATUS_FAILED;
    }

    public function getTransactionId(): ?string
    {
        return ($this->data['merchant_oid'] ?? '') !== '' ? (string) $this->data['merchant_oid'] : null;
    }

    /** PayTR'de ayrı işlem no yoktur; iade ve sorgu merchant_oid ile yapılır */
    public function getTransactionReference(): ?string
    {
        return $this->getTransactionId();
    }

    public function getCode(): ?string
    {
        return ($this->data['failed_reason_code'] ?? '') !== '' ? (string) $this->data['failed_reason_code'] : null;
    }

    public function getMessage(): ?string
    {
        if (!$this->verified) {
            return 'Invalid signature';
        }

        return $this->isSuccessful() ? null : (trim((string) ($this->data['failed_reason_msg'] ?? '')) ?: null);
    }

    /** Sipariş tutarı (payment_amount, TL) */
    public function getAmount(): ?string
    {
        return isset($this->data['payment_amount']) ? number_format(((int) $this->data['payment_amount']) / 100, 2, '.', '') : null;
    }

    /** Müşterinin ödediği toplam (vade farkı dahil, TL) */
    public function getTotalAmount(): ?string
    {
        return isset($this->data['total_amount']) ? number_format(((int) $this->data['total_amount']) / 100, 2, '.', '') : null;
    }

    public function getInstallment(): int
    {
        return max(1, (int) ($this->data['installment_count'] ?? 1));
    }

    /** Ödeme türü: card | eft */
    public function getPaymentType(): ?string
    {
        return ($this->data['payment_type'] ?? '') !== '' ? (string) $this->data['payment_type'] : null;
    }

    /** PayTR'ye verilecek yanıt metni */
    public function getAcknowledgement(): string
    {
        return 'OK';
    }
}
