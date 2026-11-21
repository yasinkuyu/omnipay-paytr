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
     
## Other Omnipay packages

- **[Omnipay: NestPay (EST / Payten)](https://github.com/yasinkuyu/omnipay-nestpay)** — İş Bankası, Akbank, Halkbank, Ziraat, TEB, QNB, ING, Şekerbank, Anadolubank virtual POS; 3D Pay Hosting
- **[Omnipay: GVP (Garanti BBVA)](https://github.com/yasinkuyu/omnipay-gvp)** — Garanti BBVA virtual POS; hosted payment page
- **[Omnipay: Akbank](https://github.com/yasinkuyu/omnipay-akbank)** — Akbank virtual POS (new platform); hosted payment page
- **[Omnipay: QNB](https://github.com/yasinkuyu/omnipay-qnb)** — QNB virtual POS (PayFor); hosted payment page
- **[Omnipay: Paratika](https://github.com/yasinkuyu/omnipay-paratika)** — Paratika, VakıfPayS and ZiraatPay hosted payment page
- **[Omnipay: Paycell SDK](https://github.com/yasinkuyu/omnipay-paycell-sdk)** — Turkcell Paycell payment page (Web SDK)
- **[Omnipay: Paycell API](https://github.com/yasinkuyu/omnipay-paycell)** — Turkcell Paycell payment API
- **[Omnipay: iyzico](https://github.com/yasinkuyu/omnipay-iyzico)** — iyzico checkout form
- **[Omnipay: Posnet](https://github.com/yasinkuyu/omnipay-posnet)** — Yapı Kredi Posnet virtual POS
