<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * İade / durum sorgusu yanıtı. isSuccessful(): status = success. isReadable() false ise yanıt okunamadı:
 * işlem gerçekleşmiş olabilir (iade için "belirsiz", otomatik tekrar denenmemeli).
 */
class ApiResponse extends AbstractResponse
{
    public function isReadable(): bool
    {
        return is_array($this->data) && isset($this->data['status']);
    }

    public function isSuccessful(): bool
    {
        return $this->isReadable() && $this->data['status'] === 'success';
    }

    public function getCode(): ?string
    {
        return ($this->data['err_no'] ?? '') !== '' ? (string) $this->data['err_no'] : null;
    }

    public function getMessage(): ?string
    {
        if (!$this->isReadable()) {
            return 'Unreadable response';
        }

        return $this->isSuccessful() ? null : (trim((string) ($this->data['err_msg'] ?? '')) ?: null);
    }
}
