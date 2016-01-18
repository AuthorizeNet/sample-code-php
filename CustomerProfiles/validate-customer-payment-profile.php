<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function validateCustomerPaymentProfile($customerProfileId="36731856", $customerPaymentProfileId="33211899")
  {
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("5KP3u95bQpv");
  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
	  
  // Use an existing payment profile ID for this Merchant name and Transaction key
  //validationmode tests , does not send an email receipt
  $validationmode = "testMode";

  $request = new AnetAPI\ValidateCustomerPaymentProfileRequest();
  
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId($customerProfileId);
  $request->setCustomerPaymentProfileId($customerPaymentProfileId);
  $request->setValidationMode($validationmode);
  
  $controller = new AnetController\ValidateCustomerPaymentProfileController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo $response->getMessages()->getMessage()[0]->getText();
   }
  else
  {
      echo "ERROR :  Validate Customer Payment Profile: Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
  return $response;
  }
  if(!defined(DONT_RUN_SAMPLES))
      validateCustomerPaymentProfile();
 ?>
