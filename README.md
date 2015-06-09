# Sample Code for the Authorize.Net PHP SDK
The goal of this code sample repository is to provide completely self-contained autonomous examples of using our PHP SDK to access the Authorize.Net API.

## Set Up
Clone this repository.
Run "composer update" in the root directory.
Run the individual samples e.g. 
````
php VisaCheckout/visacheckout-decrypt.php
````

## API Reference
The code samples are organized just like our API reference which you can access here http://developer.authorize.net/api/reference

06-04-2015 Greg White - Added charge-credit-card, refund-transaction, void-transaction, debit-bank-account, credit-bank-account, authorize-credit-card using new model

06-05-2015 Greg White - Added create-customer-profile.php to test CreateCustomerProfile Transaction with capture-previously-authed-amount and other API’s that use a customer profile. Testing capture-previously-authed.

06-08-2015 Greg White - Additional testing charge-customer-profile.php and charge-tokenized-credit-card.php. Removed .idea and phplog from project directory 

06-08-2015 Greg White - Additional testing charge-customer-profile.php. Fixed. Updated sample-code-ruby.  


