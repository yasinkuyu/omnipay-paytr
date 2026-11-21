<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Jeton alındıysa PayTR ödeme sayfasına GET yönlendirmesi. Sayfa iFrame olarak da gösterilebilir: getIframeUrl().
 * Ödemenin sonucu bu yanıtta yoktur; PayTR bildirimiyle (acceptNotification) gelir.
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful(): bool
    {
        return false;
    }

    public function isRedirect(): bool
    {
        return is_array($this->data) && ($this->data['status'] ?? '') === 'success' && !empty($this->data['token']);
    }

    public function getToken(): ?string
    {
        return $this->isRedirect() ? (string) $this->data['token'] : null;
    }

    public function getIframeUrl(): ?string
    {
        return $this->isRedirect() ? AbstractRequest::IFRAME_URL . rawurlencode($this->getToken()) : null;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->getIframeUrl();
    }

    public function getRedirectMethod(): string
    {
        return 'GET';
    }

    public function getRedirectData(): array
    {
        return [];
    }

    public function getMessage(): ?string
    {
        if (!is_array($this->data)) {
            return 'Unreadable response';
        }
        if ($this->isRedirect()) {
            return null;
        }
        $m = trim((string) ($this->data['reason'] ?? ''));
        if (!empty($this->data['errors']) && is_array($this->data['errors'])) {
            $m .= ' (' . implode('; ', array_map('strval', $this->data['errors'])) . ')';
        }

        return $m !== '' ? $m : null;
    }
}
