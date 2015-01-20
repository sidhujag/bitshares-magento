# License

The MIT License (MIT)

Copyright (c) 2011-2015 Bitshares

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

bitshares/magento-plugin
========================

# About
	
+ Bitshares payments for Magento.
	
# System Requirements

+ Magento 1.6+
+ PHP 5+

# Installation

1. Copy these files into your magento root directory.<br />
2. Copy Bitshares Checkout (https://github.com/sidhujag/bitsharescheckout) files into your magento root directory, overwrite any existing files.<br />

# Configuration

1. Upload files to the root directory of your magento installation.<br />
2. In Admin panel under "System > Configuration > Sales > Payment Methods > Bitshares":<br />
	a. Verify that the module is enabled.
4. Fill out config.php with appropriate information and configure Bitshares Checkout<br />
    - See the readme at https://github.com/sidhujag/bitsharescheckout



Usage
-----
When a shopper chooses the Bitshares payment method, they will be redirected to Bitshares Checkout where they will pay an invoice.  Bitshares Checkout will then notify your system that the order was paid for.  The customer will be redirected back to your store.  


Change Log
----------
Version 1
  - Initial version, tested against Magento 1.9.1.0
  
# Support

## Plugin Support

* [GitHub Issues](https://github.com/sidhujag/bitshares-magento/issues)
  * Open an issue if you are having issues with this plugin.


## Magento Support

* [Homepage](http://magento.com)
* [Documentation](http://docs.magentocommerce.com)
* [Community Edition Support Forums](https://www.magentocommerce.com/support/ce/)

# Troubleshooting

1. Ensure a valid SSL certificate is installed on your server. Also ensure your root CA cert is updated. If your CA cert is not current, you will see curl SSL verification errors.
2. Verify that your web server is not blocking POSTs from servers it may not recognize. Double check this on your firewall as well, if one is being used.

***TIP:*** When contacting support it will help us is you provide:

* Magento CE Version (Found at the bottom page in the Administration section)
* Other extensions you have installed
  * Some extensions do not play nice
* Configuration settings for the extension (Most merchants take screen grabs)
* Any log files that will help
  * web server error logs
  * enabled debugging for this extension and send us `var/log/payment_bitshares.log`
* Screen grabs of error message if applicable.

# Contribute

To contribute to this project, please fork and submit a pull request.