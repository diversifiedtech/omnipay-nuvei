# Omnipay: Nuvei

**Nuvei driver for the Omnipay PHP payment processing library**


[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements Nuvei support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "nmc9/nuvei": "~3.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Nuvei Gateway Connect XML Integration

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## References

Nuvei Corporation is a global payment technology solutions company headquartered in Atlanta, Georgia,
United States.  Nuvei Corporation was incorporated in 1971. In 1980, American Express Information
Services Corporation (ISC) bought 80% of Nuvei.  Nuvei Corporation spun off from American Express
and went public in 1992.

The Nuvei Global Gateway Connect 2.0 is a simple payment solution for connecting an online store to
the Nuvei Global Gateway.  It provides redirect based payments (purchase() method with a corresponding
completePurchase() method).  It is referred to here as the "Nuvei Connect" gateway, currently at
version 2.0.

The Global Gateway was originally called the LinkPoint Gateway but since Nuvei's acquisition of
LinkPoint it is now known as the Nuvei Global Gateway. As of this writing the Global Gateway version
9.0 is supported. It is referred to here as the "Nuvei Webservice" gateway, more correctly speaking
it is the "Nuvei Global Web Services API", currently at version 9.0

The Nuvei Global Gateway e4 (previously referred to as "Nuvei Global", and so if you see
internet references to the Nuvei Global Gateway, they are probably referring to this one, distinguished
by having URLs like "api.globalgatewaye4.firstdata.com") is now called the Payeezy Gateway and is
referred to here as the "Nuvei Payeezy" Gateway.

The Connect, Global, and Payeezy gateways are implemented here although each have gone through a number
of API changes since their initial releases.

Nuvei APIs are listed here:

https://helpdesk.nuvei.com/doku.php?id=developer

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](),
or better yet, fork the library and submit a pull request.
