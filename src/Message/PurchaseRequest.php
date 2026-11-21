<?php

namespace Omnipay\PayTR\Message;

/**
 * Ödeme jetonu (get-token). Parametreler: amount, currency (TL, USD, EUR, GBP, RUB), transactionId (merchant_oid:
 * yalnız harf ve rakam, her denemede benzersiz), returnUrl (merchant_ok_url), cancelUrl (merchant_fail_url, yoksa
 * returnUrl), clientIp (zorunlu), card (e-posta zorunlu; ad, adres, telefon), items (sepet; yoksa tek satır),
 * maxInstallment (0 = taksit yok, 2-12 = en fazla), timeoutLimit (dakika), lang (tr | en).
 *
 * İmza: paytr_token = base64(HMAC-SHA256(merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket +
 * no_installment + max_installment + currency + test_mode + merchant_salt, merchant_key)).
 */
class PurchaseRequest extends AbstractRequest
{
    public function getMaxInstallment() { return $this->getParameter('maxInstallment'); }
    public function setMaxInstallment($value) { return $this->setParameter('maxInstallment', $value); }

    public function getTimeoutLimit() { return $this->getParameter('timeoutLimit'); }
    public function setTimeoutLimit($value) { return $this->setParameter('timeoutLimit', $value); }

    public function getLang() { return $this->getParameter('lang'); }
    public function setLang($value) { return $this->setParameter('lang', $value); }

    public function getData(): array
    {
        $this->validate('merchantId', 'merchantKey', 'merchantSalt', 'amount', 'transactionId', 'returnUrl', 'clientIp', 'card');
        $card = $this->getCard();
        $email = (string) $card->getEmail();
        $max = max(0, min(12, (int) $this->getMaxInstallment()));
        $currency = match (strtoupper((string) ($this->getCurrency() ?: 'TRY'))) {
            'USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'RUB' => 'RUB', default => 'TL',
        };

        $data = [
            'merchant_id' => (string) $this->getMerchantId(),
            'user_ip' => (string) $this->getClientIp(),
            'merchant_oid' => (string) $this->getTransactionId(),
            'email' => $email,
            'payment_amount' => (string) $this->getAmountInteger(),
            'user_basket' => base64_encode((string) json_encode($this->basket(), JSON_UNESCAPED_UNICODE)),
            'no_installment' => $max > 1 ? '0' : '1',
            'max_installment' => $max > 1 ? (string) $max : '0',
            'currency' => $currency,
            'test_mode' => $this->getTestMode() ? '1' : '0',
            'debug_on' => $this->getTestMode() ? '1' : '0',
            'user_name' => mb_substr(trim((string) $card->getBillingName()) ?: 'Musteri', 0, 60),
            'user_address' => mb_substr(trim($card->getBillingAddress1() . ' ' . $card->getBillingAddress2() . ' ' . $card->getBillingCity()) ?: '-', 0, 400),
            'user_phone' => mb_substr((string) ($card->getBillingPhone() ?: '05000000000'), 0, 20),
            'merchant_ok_url' => (string) $this->getReturnUrl(),
            'merchant_fail_url' => (string) ($this->getCancelUrl() ?: $this->getReturnUrl()),
            'timeout_limit' => (string) ((int) ($this->getTimeoutLimit() ?: 30)),
            'lang' => (string) ($this->getLang() ?: 'tr'),
        ];
        $data['paytr_token'] = $this->sign($data['merchant_id'] . $data['user_ip'] . $data['merchant_oid'] . $data['email']
            . $data['payment_amount'] . $data['user_basket'] . $data['no_installment'] . $data['max_installment']
            . $data['currency'] . $data['test_mode'] . $this->getMerchantSalt());

        return $data;
    }

    /** [[ad, birim fiyat "12.50", adet], …]; yoksa ya da toplam tutmuyorsa tek satır */
    private function basket(): array
    {
        $lines = [];
        $sum = 0.0;
        foreach ($this->getItems() ?? [] as $item) {
            $qty = max(1, (int) $item->getQuantity());
            $lines[] = [mb_substr((string) $item->getName(), 0, 120) ?: 'Urun', number_format((float) $item->getPrice(), 2, '.', ''), $qty];
            $sum += round((float) $item->getPrice() * $qty, 2);
        }
        if (!$lines || abs($sum - (float) $this->getAmount()) >= 0.01) {
            return [[(string) ($this->getDescription() ?: 'Siparis ' . $this->getTransactionId()), $this->getAmount(), 1]];
        }

        return $lines;
    }

    public function sendData($data): PurchaseResponse
    {
        return $this->response = new PurchaseResponse($this, $this->post(self::TOKEN_URL, $data));
    }
}
