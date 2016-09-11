<?php

namespace Omnipay\PayTR\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * PayTR Authorize Request
 * 
 * (c) Yasin Kuyu
 * 2015, insya.com
 * http://www.github.com/yasinkuyu/omnipay-paytr
 */
class AuthorizeRequest extends AbstractRequest {

    public function getData() {
  
  	
		$post = $_POST;
		
		$hash = base64_encode(hash_hmac('sha256',$post["merchant_oid"]."F8JsrTSYn7GrzsIQ".$post["status"].$post["total_amount"],"wwNlhCPy4Gsu2613",true));
		
		$result = "";
		
		if( $hash != $post["hash"])
			$result = 'PAYTR gerçersiz yanıt.';
		
		if( $post["status"] == 'success')
		{
			$result = "//ödeme başarılı: siparişi onayla, müşteriye mail/mesaj ile bilgi ver vs";
		}
		else
		{
			$result = $post["failed_reason_msg"];
		}
		
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
