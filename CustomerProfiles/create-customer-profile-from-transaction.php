<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function createCustomerProfileFromTransaction()
  {
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName("5KP3u95bQpv");
	  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");



	  $request = new AnetAPI\CreateCustomerProfileFromTransactionRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setTransId("2238251168");

	  $controller = new AnetController\CreateCustomerProfileFromTransactionController($request);

	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		  echo "SUCCESS: PROFILE ID : " . $response->getCustomerProfileId() . "\n";
	   }
	  else
	  {
		  echo "ERROR :  Invalid response\n";
		  echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
	  }
	  return $response;
  }
  if(!defined(DONT_RUN_SAMPLES))
      createCustomerProfileFromTransaction();
?>
