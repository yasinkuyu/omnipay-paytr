<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * PayTR Purchase Request
 * 
 * (c) Yasin Kuyu
 * 2015, insya.com
 * http://www.github.com/yasinkuyu/omnipay-paytr
 */
class PurchaseRequest extends AbstractRequest {
    
    protected $endpoint = '';
    protected $endpoints = array(
        'purchase'   => 'https://www.paytr.com/odeme/api/get-token'
    );
 
    protected $currencies = [
        'TRY' => 'YT',
        'USD' => 'US',
        'EUR' => 'EU'
    ];

    public function getData() {

        //$this->validate('card');
        //$this->getCard()->validate();
		 
        $currency             = $this->getCurrency();

        $data['amount']       = $this->getAmountInteger();
        $data['orderID']      = $this->getOrderId();
        $data['currencyCode'] = $this->currencies[$currency];
        $data['installment']  = $this->getInstallment();
 
        return $data;
    }

    public function sendData($data) {
  		 
		$email             = $this->getCard()->getEmail();
		$payment_amount    = $this->getAmountInteger();

		$no_installment    = 0; 
		$max_installment   = $this->getInstallment(); 
		$user_name         = $this->getCard()->getFirstName() . " " . $this->getCard()->getLastName(); 
		$user_address      = $this->getCard()->getBillingAddress1() . " " . $this->getCard()->getBillingAddress2(); 
		$user_phone        = $this->getCard()->getBillingPhone(); 

		$user_basket       = base64_encode(json_encode(array(
			array("Örnek ürün 1", "18.00", 1) // 1. ürün (Adı - Birim Fiyatı - Adet )
		)));

		$url 			       = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$hash          		   = $this->getMerchantNo() . $this->getIp() . $this->getOrderId() . $email .$payment_amount .$user_basket.$no_installment.$max_installment;
		$token       	       = base64_encode(hash_hmac('sha256',$hash.$this->getMerchantSalt(),$this->getMerchantKey(),true));

		$post_vals = array(
			'merchant_id'      => $this->getMerchantNo(),
			'user_ip'          => $this->getIp(),
			'merchant_oid'     => $this->getOrderId(),
			'email'            => $email,
			'payment_amount'   => $payment_amount,
			'paytr_token'      => $token,
			'user_basket'      => $user_basket,
			'debug_on'         => $this->getTestMode() ? 1 : 0,
			'no_installment'   => $no_installment,
			'max_installment'  => $max_installment,
			'user_name'        => $user_name,
			'user_address'     => $user_address,
			'user_phone'       => $user_phone,
			'merchant_ok_url'  => $url."",
			'merchant_fail_url'=> $url.""
		);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $this->endpoints['purchase']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		
		$result = @curl_exec($ch);
		  
		curl_close($ch);

        return $this->response = new Response($this, $result);
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
    public function getNoInstallment() {
        return $this->getParameter('no_installment');
    }

    public function setNoInstallment($value) {
        return $this->setParameter('no_installment', $value);
    }

    public function getIp() {
        return $this->getParameter('ip');
    }

    public function setIp($value) {
        return $this->setParameter('ip', $value);
    }

    public function getTransId() {
        return $this->getParameter('transId');
    }

    public function setTransId($value) {
        return $this->setParameter('transId', $value);
    }

    public function getOrderId() {
        return $this->getParameter('orderid');
    }

    public function setOrderId($value) {
        return $this->setParameter('orderid', $value);
    }

}
