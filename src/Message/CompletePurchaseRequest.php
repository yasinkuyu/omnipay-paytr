<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * PayTR Complete Purchase Request
 * 
 * (c) Yasin Kuyu
 * 2015, insya.com
 * http://www.github.com/yasinkuyu/omnipay-paytr
 */
class CompletePurchaseRequest extends BasePurchaseRequest {
     

    public function getData() {
 
    }

    public function sendData($data) {
 
  
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
   

}
