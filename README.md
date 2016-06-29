# Sample PHP Code for Authorize.Net
[![Build Status](https://travis-ci.org/AuthorizeNet/sample-code-php.png?branch=master)]
(https://travis-ci.org/AuthorizeNet/sample-code-php)

This repository contains working code samples which demonstrate PHP integration with the Authorize.Net PHP SDK.
The samples are organized just like our API, which you can also try out directly here: http://developer.authorize.net/api/reference


##Using the Sample Code

The samples are all completely independent and self-contained so you can look at them to get a gist of how the method works, you can use the snippets to try in your own sample project, or you can run each sample from the command line.

##Running the Samples
Clone this repository.  
Run "composer update" in the root directory.  
Run the individual samples e.g.   
````
php PaymentTransactions/charge-credit-card.php
````

##What if I'm not using Composer?
We provide a custom `SPL` autoloader, just [download the SDK.](https://github.com/AuthorizeNet/sdk-php/releases):

```php
require 'path/to/anet_php_sdk/autoload.php';
```
