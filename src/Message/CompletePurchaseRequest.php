<?php

namespace Omnipay\PayTR\Message;

/**
 * PayTR bildirimi (Bildirim URL'ye sunucudan POST). Ağ isteği yapmaz; imzayı doğrular.
 *
 * İmza: hash = base64(HMAC-SHA256(merchant_oid + merchant_salt + status + total_amount, merchant_key)).
 * Verilmişse merchant_oid transactionId ile ve ödenen tutar (payment_amount) amount ile karşılaştırılır.
 * PayTR yanıt olarak düz "OK" bekler; gelmezse bildirimi yineler.
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function getFields() { return $this->getParameter('fields'); }
    public function setFields($value) { return $this->setParameter('fields', $value); }

    public function getData(): array
    {
        $this->validate('merchantKey', 'merchantSalt');

        return is_array($this->getFields()) ? $this->getFields() : $this->httpRequest->request->all();
    }

    public function sendData($data): CompletePurchaseResponse
    {
        $given = (string) ($data['hash'] ?? '');
        $valid = $given !== '' && hash_equals($this->sign((string) ($data['merchant_oid'] ?? '') . $this->getMerchantSalt()
            . (string) ($data['status'] ?? '') . (string) ($data['total_amount'] ?? '')), $given);
        if ($valid && $this->getTransactionId() !== null && (string) ($data['merchant_oid'] ?? '') !== (string) $this->getTransactionId()) {
            $valid = false;
        }
        if ($valid && $this->getParameter('amount') !== null && ($data['status'] ?? '') === 'success'
            && (int) ($data['payment_amount'] ?? 0) !== $this->getAmountInteger()) {
            $valid = false;
        }

        return $this->response = new CompletePurchaseResponse($this, $data, $valid);
    }
}
