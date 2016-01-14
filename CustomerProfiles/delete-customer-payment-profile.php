<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function deleteCustomerPaymentProfile($customerProfileId="38900575", $customerpaymentprofileid = "35366197")
  {
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName("556KThWQ6vf2");
	  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");

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
		  echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
	  }
	  return $response;
  }
  if(!defined(DONT_RUN_SAMPLES))
      deleteCustomerPaymentProfile();
 ?>
