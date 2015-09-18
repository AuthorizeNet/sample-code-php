<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");

  // An existing customer profile id for this merchant name and transaction key
  $existingcustomerprofileid = "35843932";

  // Create the customer shipping address
  $customershippingaddress = new AnetAPI\CustomerAddressExType();
  $customershippingaddress->setFirstName("Jane");
  $customershippingaddress->setLastName("White");
  $customershippingaddress->setCompany("Addresses R Us");
  $customershippingaddress->setAddress("14 North Spring Street Suite 240");
  $customershippingaddress->setCity("Toms River");
  $customershippingaddress->setState("NJ");
  $customershippingaddress->setZip("08753");
  $customershippingaddress->setCountry("USA");
  $customershippingaddress->setPhoneNumber("201-000-0000");
  $customershippingaddress->setFaxNumber("973-999-9999");

  // Update an existing customer shipping address for an existing customer profile
  $request = new AnetAPI\UpdateCustomerShippingAddressRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId($existingcustomerprofileid);
  $request->setAddress($customershippingaddress);
  $controller = new AnetController\UpdateCustomerShippingAddressController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "Update Customer Shipping Address SUCCESS:" . "\n";
   }
  else
  {
      echo "Update Customer Shipping Address  ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
