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
        //$this->validate('apiKey', 'paymentData');
        //$this->getCard()->validate();
		 
        $currency = $this->getCurrency();

        $data['orderID'] = $this->getOrderId();
        $data['currencyCode'] = $this->currencies[$currency];
        $data['installment'] = $this->getInstallment();
        
        $data['amount'] = $this->getAmountInteger();
        $data["IPAddress"] = $this->getClientIp();
 
        return $data;
    }

    public function sendData($data) {
 
 
		if( isset( $_GET["return"] )){
			echo "ok";die();
		}
  
        // Post to PayTR
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );


$user_ip 	       = "31.145.128.50"; //;$_SERVER['REMOTE_ADDR']; //!!! Eğer bu kodu sunucuda değil local makinanızda çalıştırıyorsanız buraya dış ip adresinizi(https://www.whatismyip.com/) yazmalısınız. 
$merchant_oid      = time(); //sipariş numarası: her işlemde benzersiz olmalıdır! Bu bilgi bildirim sayfanıza yapılacak bildirimde gönderilir.
$email             = "musteri@saglayici.com"; // Müşterinizin sitenizde kayıtlı eposta adresi
$payment_amount    = $this->getAmountInteger();//9.99 TL

$no_installment    = 0; // Taksit yapılmasını istemiyorsanız (Örn cep telefonu satışı) 1 yapın
$max_installment   = 9; // Sayfada görüntülenecek taksit adedini sınırlamak istiyorsanız (Örn altın vb kuyum satışı max 4 taksittir) uygun şekilde değiştirin.
$user_name         = "MusteriAdı MusteriSoyAdı"; // Müşterinizin sitenizde kayıtlı ad soyad bilgisi
$user_address      = "Müşterinizin açık adresi"; // Müşterinizin sitenizde kayıtlı adres bilgisi
$user_phone        = "05XXXXXXXXX"; // // Müşterinizin sitenizde kayıtlı telefon bilgisi

$user_basket       = base64_encode(json_encode(array(
	array("Örnek ürün 1", "18.00", 1), // 1. ürün (Adı - Birim Fiyatı - Adeti )
	array("Örnek ürün 2", "33.25", 2), // 2. ürün (Adı - Birim Fiyatı - Adeti )
	array("Örnek ürün 3", "45.42", 1) // 3. ürün (Adı - Birim Fiyatı - Adeti )
)));

$hash_str          = $this->getMerchantNo() .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment;
$paytr_token       = base64_encode(hash_hmac('sha256',$hash_str.$this->getMerchantSalt(),$this->getMerchantKey(),true));

$url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$post_vals = array(
	'merchant_id'      => $this->getMerchantNo(),
	'user_ip'          => $user_ip,
	'merchant_oid'     => $merchant_oid,
	'email'            => $email,
	'payment_amount'   => $payment_amount,
	'paytr_token'      => $paytr_token,
	'user_basket'      => $user_basket,
	'debug_on'         => $this->getTestMode() ? 1 : 0,
	'no_installment'   => $no_installment,
	'max_installment'  => $max_installment,
	'user_name'        => $user_name,
	'user_address'     => $user_address,
	'user_phone'       => $user_phone,
	'merchant_ok_url'  => $url."?return=true",
	'merchant_fail_url'=> $url."?return=false"
);
/*
        // Register the payment
        $this->httpClient->setConfig(array(
            'curl.options' => array(
                'CURLOPT_SSL_VERIFYHOST' => 0,
                'CURLOPT_SSL_VERIFYHOST' => 0,
                'CURLOPT_SSL_VERIFYPEER' => 0,
                'CURLOPT_RETURNTRANSFER' => 1,
                'CURLOPT_FRESH_CONNECT' => true,
                'CURLOPT_TIMEOUT' => 20,
                'CURLOPT_POSTFIELDS' => $post_vals,
                'CURLOPT_POST' => 1
            )
        ));
       
        //$xml = "xmldata=".$document->saveXML();
        $xml = "";
        
        $this->endpoint = $this->endpoints['purchase'];
        
        $httpResponse = $this->httpClient->post($this->endpoint, $headers, $xml)->send();*/

        // return $this->response = new Response($this, $httpResponse->getBody());
	    
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

    public function getIp() {
        return $this->getParameter('ip');
    }

    public function setIp($value) {
        return $this->setParameter('ip', $value);
    }

    public function getType() {
        return $this->getParameter('type');
    }

    public function setType($value) {
        return $this->setParameter('type', $value);
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
