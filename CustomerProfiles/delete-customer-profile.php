<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  function deleteCustomerProfile($customerProfileId)
  {
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName("5KP3u95bQpv");
	  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
	  $refId = 'ref' . time();

	  // Delete an existing customer profile  
	  $request = new AnetAPI\DeleteCustomerProfileRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setCustomerProfileId( $customerProfileId );

	  $controller = new AnetController\DeleteCustomerProfileController($request);
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		echo "DeleteCustomerProfile SUCCESS : " .  "\n";
	  }
	  else
	  {
		echo "ERROR :  DeleteCustomerProfile: Invalid response\n";
		echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";

	  }

	  return $response;
  }
  
  if(!defined('DONT_RUN_SAMPLES'))
      deleteCustomerProfile("38958129");
?>
