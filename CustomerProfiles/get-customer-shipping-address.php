<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("5KP3u95bQpv");
  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");

  // An existing customer profile id and shipping address id for this merchant name and transaction key
  $customerProfileId = "36731856";
  $customerAddressId = "35850995";

  $request = new AnetAPI\GetCustomerShippingAddressRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId($customerProfileId);
  $request->setCustomerAddressId($customerAddressId);
  
  $controller = new AnetController\GetCustomerShippingAddressController($request);
  
  //Retrieving existing customer shipping address
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "Get Customer Shipping Address SUCCESS" . "\n";
      echo "	FirstName 	: " . $response->getAddress()->getFirstName() . "\n";
      echo "	LastName 	: " . $response->getAddress()->getLastName() . "\n";
      echo "	Company 	: " . $response->getAddress()->getCompany() . "\n";
      echo "	Address 	: " . $response->getAddress()->getAddress() . "\n";
      echo "	City 		: " . $response->getAddress()->getCity() . "\n";
      echo "	State 		: " . $response->getAddress()->getState() . "\n";
      echo "	Zip 		: " . $response->getAddress()->getZip() . "\n";
      echo "	Country 	: " . $response->getAddress()->getCountry() . "\n";
      echo "	Phone Number 	: " . $response->getAddress()->getPhoneNumber() . "\n";
      echo "	FAX Number 	: " . $response->getAddress()->getFaxNumber() . "\n";
      echo "Customer AddressId 	: " . $response->getAddress()->getCustomerAddressId() . "\n";
   }
  else
  {
      echo "Get Customer Shipping Address  ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
