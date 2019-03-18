# PHP Sample Code for the Authorize.Net SDK
[![Travis CI Status](https://travis-ci.org/AuthorizeNet/sample-code-php.svg?branch=master)](https://travis-ci.org/AuthorizeNet/sample-code-php)

This repository contains working code samples which demonstrate PHP integration with the [Authorize.Net PHP SDK](https://github.com/AuthorizeNet/sdk-php).

The samples are organized into categories and common usage examples, just like our [API Reference Guide](http://developer.authorize.net/api/reference). Our API Reference Guide is an interactive reference for the Authorize.Net API. It explains the request and response parameters for each API method and has embedded code windows to allow you to send actual requests right within the API Reference Guide.


## Using the Sample Code

The samples are all completely independent and self-contained. You can analyze them to get an understanding of how a particular method works, or you can use the snippets as a starting point for your own project.

You can also run each sample directly from the command line.

## Running the Samples From the Command Line
* Clone this repository:
```
    $ git clone https://github.com/AuthorizeNet/sample-code-php.git
```
* Run composer with the "update" option in the root directory of the repository.
```
    $ composer update
```
* Run the individual samples by name. For example:
```
    $ php PaymentTransactions/[CodeSampleName]
```
e.g.
```
    $ php PaymentTransactions/charge-credit-card.php
```

### Installation Notes
Note: If during "composer update", you get the error "composer failed to open stream invalid argument", go to your php.ini file (present where you have installed PHP), and uncomment the following lines:
```
extension=php_openssl.dll
extension=php_curl.dll
```
On Windows systems, you also have to uncomment:
```
extension_dir = "ext"
```
Then run `composer update` again. You might have to restart your machine before the changes take effect.

### What if I'm not using Composer?
We provide a custom `SPL` autoloader. Just [download the SDK](https://github.com/AuthorizeNet/sdk-php/releases) and point to its `autoload.php` file:

```php
require 'path/to/anet_php_sdk/autoload.php';
```
