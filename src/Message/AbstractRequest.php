<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

abstract class AbstractRequest extends BaseAbstractRequest
{
    public const TOKEN_URL = 'https://www.paytr.com/odeme/api/get-token';
    public const IFRAME_URL = 'https://www.paytr.com/odeme/guvenli/';
    public const REFUND_URL = 'https://www.paytr.com/odeme/iade';
    public const STATUS_URL = 'https://www.paytr.com/odeme/durum-sorgu';

    public function getMerchantId() { return $this->getParameter('merchantId'); }
    public function setMerchantId($value) { return $this->setParameter('merchantId', $value); }

    public function getMerchantKey() { return $this->getParameter('merchantKey'); }
    public function setMerchantKey($value) { return $this->setParameter('merchantKey', $value); }

    public function getMerchantSalt() { return $this->getParameter('merchantSalt'); }
    public function setMerchantSalt($value) { return $this->setParameter('merchantSalt', $value); }

    /** base64(HMAC-SHA256(metin, merchant_key)) */
    protected function sign(string $data): string
    {
        return base64_encode(hash_hmac('sha256', $data, (string) $this->getMerchantKey(), true));
    }

    /** Form POST → JSON. Okunamayan yanıtta null; bağlantı hatasında Omnipay'in NetworkException'ı fırlar. */
    protected function post(string $url, array $fields): ?array
    {
        $res = $this->httpClient->request('POST', $url, ['Content-Type' => 'application/x-www-form-urlencoded'], http_build_query($fields));
        $json = json_decode((string) $res->getBody(), true);

        return is_array($json) ? $json : null;
    }
}
