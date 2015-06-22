<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");

<<<<<<< HEAD

  $request = new AnetAPI\CreateCustomerProfileFromTransactionRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setTransId("2235382051");

  $controller = new AnetController\CreateCustomerProfileFromTransactionController($request);

  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "SUCCESS: PROFILE ID : " . $response->getCustomerProfileId() . "\n";
   }
  else
  {
      echo "ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getText();
=======
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
>>>>>>> 206701dbc422d0b196430469ff481ce4347d0a85
  }
?>
