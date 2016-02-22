<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function createCustomerProfileFromTransaction($transId= \SampleCode\Constants::TRANS_ID_TO_CREATE_CUSTOMER_PROFILE)
  {
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
      $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
      
	  $request = new AnetAPI\CreateCustomerProfileFromTransactionRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setTransId($transId);

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
  //provide a transaction that has customer information
  if(!defined('DONT_RUN_SAMPLES'))
      createCustomerProfileFromTransaction(\SampleCode\Constants::TRANS_ID_TO_CREATE_CUSTOMER_PROFILE);
?>
