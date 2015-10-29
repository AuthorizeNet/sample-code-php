<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  // Common Set Up for API Credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName( "8V4xFm3z");
  $merchantAuthentication->setTransactionKey("655AS4Ek7TJ42snq");

  $request = new AnetAPI\UpdateSplitTenderGroupRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setSplitTenderId("116468");
  $request->setSplitTenderStatus("voided");

  $controller = new AnetController\UpdateSplitTenderGroupController($request);

  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "SUCCESS : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
   }
  else
  {
      echo "ERROR :  Invalid response\n";
      echo "Response Code : " . $response->getMessages()->getMessage()[0]->getCode() . " Resposne text: " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
  ?>