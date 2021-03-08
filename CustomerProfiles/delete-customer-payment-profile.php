<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function deleteCustomerPaymentProfile($customerProfileId= "1929905607", 
    $customerpaymentprofileid = "1842074814"
) {
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
    
	  // Use an existing payment profile ID for this Merchant name and Transaction key
	  
	  $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setCustomerProfileId($customerProfileId);
	  $request->setCustomerPaymentProfileId($customerpaymentprofileid);
	  $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		  echo "SUCCESS: Delete Customer Payment Profile  SUCCESS  :" . "\n";
	   }
	  else
	  {
		  echo "ERROR :  Delete Customer Payment Profile: Invalid response\n";
		  $errorMessages = $response->getMessages()->getMessage();
		  echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
	  }
	  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      deleteCustomerPaymentProfile();
 ?>
