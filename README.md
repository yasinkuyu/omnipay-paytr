# Omnipay: PayTR

**PayTR iFrame (Bonus, World, Axess, Cardfinans, Maximum, AsyaCard, Paraf sanal pos) gateway for Omnipay payment processing library**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paytr/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paytr) 
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paytr/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paytr) 
[![Latest Unstable Version](https://poser.pugx.org/yasinkuyu/omnipay-paytr/v/unstable)](https://packagist.org/packages/yasinkuyu/omnipay-paytr) 
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paytr/license)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayTR (Turkish Payment Gateways) support for Omnipay.

PayTR (Bonus, World, Axess, Cardfinans, Maximum, AsyaCard, Paraf) sanal pos hizmeti için omnipay kütüphanesi.

(Türkçe açıklamalar için http://yasinkuyu.net/omnipay-coklu-odeme-sistemi)


## Composer Installation

###### Create directory
	mkdir omnipay-paytr-example

###### Go to directory
	cd omnipay-paytr-example

###### Install composer package
	composer require yasinkuyu/omnipay-paytr

###### Run using the built-in PHP web server
	php -S localhost:8000 


## (or) Manual Installation

###### Clone project
	git clone https://github.com/yasinkuyu/omnipay-paytr.git omnipay-paytr-example

###### Go to project directory
	cd omnipay-paytr-example

###### Install dependencies
	composer install

###### Run using the built-in PHP web server
	php -S localhost:8000 

		 
* PayTR
    - Bonus
    - World
    - Axess
    - Cardfinans
    - Maximum
    - AsyaCard
    - Paraf

Gateway Methods

* purchase($options) - authorize and immediately capture an amount on the customer's card
 
 
## Sample App
     
## Posnet
Posnet (Yapı Kredi, Vakıfbank, Anadolubank) gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-posnet
    
## NestPay (EST)
(İş Bankası, Akbank, Finansbank, Denizbank, Kuveytturk, Halkbank, Anadolubank, ING Bank, Citibank, Cardplus) gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-nestpay

##Iyzico
Iyzico gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-iyzico

## GVP (Granti Sanal Pos)
Gvp (Garanti, Denizbank, TEB, ING, Şekerbank, TFKB) gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-gvp

## BKM Express
BKM Express gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-bkm


