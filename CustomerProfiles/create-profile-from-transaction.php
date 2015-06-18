<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");

  $transid = " 2235353928";

  $request = new AnetAPI\CreateCustomerProfileFromTransactionRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setTransId($transid);

  $controller = new AnetController\CreateCustomerProfileFromTransactionController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "Create Customer Profile From Transaction: TRANSID : " . $response->getTransId() . "\n";
   }
  else
  {
      echo "Create Customer Profile From Transaction :  Invalid response\n";
  }
?>
