<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");

  // Use an existing customer profile and address id for this merchant name and transaction key
  $customerprofileid = "35894174";
  $customeraddressid = "32445389";

  // Delete an existing customer shipping address for an existing customer profile
  $request = new AnetAPI\DeleteCustomerShippingAddressRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId($customerprofileid);
  $request->setCustomerAddressId($customeraddressid);

  $controller = new AnetController\DeleteCustomerShippingAddressController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "Delete Customer Shipping Address SUCCESS:" . "\n";
   }
  else
  {
      echo "Delete Customer Shipping Address  ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
