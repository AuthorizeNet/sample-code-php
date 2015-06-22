<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
  $refId = 'ref' . time();

  // Get all existing customer profile ID's
  $request = new AnetAPI\GetCustomerProfileIdsRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $controller = new AnetController\GetCustomerProfileIdsController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "GetCustomerProfileId's SUCCESS: " . "\n";
      $profileIds[] = $response->getIds();
      echo "There are " . count($profileIds[0]) . " Customer Profile ID's for this Merchant Name and Transaction Key" . "\n";
   }
  else
  {
      echo "GetCustomerProfileId's ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
