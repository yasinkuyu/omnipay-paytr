<?php

namespace Omnipay\PayTR;

use Omnipay\Common\AbstractGateway;

/**
 * PayTR Gateway
 * 
 * (c) Yasin Kuyu
 * 2016, insya.com
 * http://www.github.com/yasinkuyu/omnipay-paytr
 */
class Gateway extends AbstractGateway {

    public function getName() {
        return 'PayTR';
    }

    public function getDefaultParameters() {
        return array(
            'merchantNo'   => '',
            'merchantKey'  => '',
            'merchantSalt' => '',
            'installment'  => '9',
            'ip' 		   => $_SERVER['REMOTE_ADDR'],
            'currency' 	   => 'TRY',
            'testMode'     => false
        );
    }

    public function authorize(array $parameters = array()) {
        return $this->createRequest('\Omnipay\PayTR\Message\AuthorizeRequest', $parameters);
    }

    public function capture(array $parameters = array()) {
        return $this->createRequest('\Omnipay\PayTR\Message\CaptureRequest', $parameters);
    }

    public function purchase(array $parameters = array()) {
        return $this->createRequest('\Omnipay\PayTR\Message\PurchaseRequest', $parameters);
    }

    public function refund(array $parameters = array()) {
        return $this->createRequest('\Omnipay\PayTR\Message\RefundRequest', $parameters);
    }

    public function void(array $parameters = array()) {
        return $this->createRequest('\Omnipay\PayTR\Message\VoidRequest', $parameters);
    }

    public function getMerchantNo() {
        return $this->getParameter('merchantNo');
    }

    public function setMerchantNo($value) {
        return $this->setParameter('merchantNo', $value);
    }

    public function getMerchantKey() {
        return $this->getParameter('merchantKey');
    }

    public function setMerchantKey($value) {
        return $this->setParameter('merchantKey', $value);
    }

    public function getMerchantSalt() {
        return $this->getParameter('merchantSalt');
    }

    public function setMerchantSalt($value) {
        return $this->setParameter('merchantSalt', $value);
    }

    public function getInstallment() {
        return $this->getParameter('installment');
    }

    public function setInstallment($value) {
        return $this->setParameter('installment', $value);
    }

    public function getIp() {
        return $this->getParameter('ip');
    }

    public function setIp($value) {
        return $this->setParameter('ip', $value);
    }

    public function getOrderId() {
        return $this->getParameter('orderid');
    }

    public function setOrderId($value) {
        return $this->setParameter('orderid', $value);
    }

    public function getTransId() {
        return $this->getParameter('transId');
    }

    public function setTransId($value) {
        return $this->setParameter('transId', $value);
    }

}
