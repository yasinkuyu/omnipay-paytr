# Omnipay: PayTR

**PayTR iFrame (Bonus, World, Axess, Cardfinans, Maximum, AsyaCard, Paraf sanal pos) gateway for Omnipay payment processing library**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paytr/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paytr) 
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paytr/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paytr) 
[![Latest Unstable Version](https://poser.pugx.org/yasinkuyu/omnipay-paytr/v/unstable)](https://packagist.org/packages/yasinkuyu/omnipay-paytr) 
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paytr/license)](https://packagist.org/packages/yasinkuyu/omnipay-paytr)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayTR (Turkish Payment Gateways) support for Omnipay.


PayTR (Yapı Kredi, Vakıfbank, Anadolubank) sanal pos hizmeti için omnipay kütüphanesi.

(Türkçe açıklamalar için http://yasinkuyu.net/omnipay-coklu-odeme-sistemi)

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "yasinkuyu/omnipay-paytr": "~2.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* PayTR
    - Bonus
    - World
    - Axess
    - Cardfinans
    - Maximum
    - AsyaCard
    - Paraf

Gateway Methods

* authorize($options) - authorize an amount on the customer's card
* capture($options) - capture an amount you have previously authorized
* purchase($options) - authorize and immediately capture an amount on the customer's card
* refund($options) - refund an already processed transaction
* void($options) - generally can only be called up to 24 hours after submitting a transaction

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Unit Tests

PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.

## Sample App
         


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


## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project, or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/yasinkuyu/omnipay-paytr/issues),
or better yet, fork the library and submit a pull request.
