<?php

namespace Omnipay\PayTR\Message;

/**
 * Durum sorgusu: transactionId (merchant_oid).
 * paytr_token = base64(HMAC-SHA256(merchant_id + merchant_oid + merchant_salt, merchant_key)).
 */
class FetchTransactionRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('merchantId', 'merchantKey', 'merchantSalt', 'transactionId');
        $data = ['merchant_id' => (string) $this->getMerchantId(), 'merchant_oid' => (string) $this->getTransactionId()];
        $data['paytr_token'] = $this->sign($data['merchant_id'] . $data['merchant_oid'] . $this->getMerchantSalt());

        return $data;
    }

    public function sendData($data): ApiResponse
    {
        return $this->response = new ApiResponse($this, $this->post(self::STATUS_URL, $data));
    }
}
