<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function validateCustomerPaymentProfile($customerProfileId= "36731856",
    $customerPaymentProfileId= "33211899"
) {
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
  
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
      $validationMessages = $response->getMessages()->getMessage();
      echo "Response : " . $validationMessages[0]->getCode() . "  " .$validationMessages[0]->getText() . "\n";
   }
  else
  {
      echo "ERROR :  Validate Customer Payment Profile: Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
  }
  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      validateCustomerPaymentProfile();
 ?>
