<?php
	
namespace Omnipay\PayTR\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class CompleteRequest extends AuthorizeRequest
{
    public function sendData($data)
    {
        return $this->response = new CompleteResponse($this, $data);
    }
}