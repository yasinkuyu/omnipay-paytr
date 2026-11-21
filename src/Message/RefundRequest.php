<?php

namespace Omnipay\PayTR\Message;

/**
 * İade (tam ya da kısmi): transactionId (merchant_oid), amount. PayTR aynı gün iptali ayrı sunmaz; iade aynı uçtan gider.
 * paytr_token = base64(HMAC-SHA256(merchant_id + merchant_oid + return_amount + merchant_salt, merchant_key)).
 */
class RefundRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('merchantId', 'merchantKey', 'merchantSalt', 'transactionId', 'amount');
        $data = ['merchant_id' => (string) $this->getMerchantId(), 'merchant_oid' => (string) $this->getTransactionId(), 'return_amount' => $this->getAmount()];
        $data['paytr_token'] = $this->sign($data['merchant_id'] . $data['merchant_oid'] . $data['return_amount'] . $this->getMerchantSalt());

        return $data;
    }

    public function sendData($data): ApiResponse
    {
        return $this->response = new ApiResponse($this, $this->post(self::REFUND_URL, $data));
    }
}
