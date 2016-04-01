<?php
	require 'vendor/autoload.php';
	
	use net\authorize\api\contract\v1 as AnetAPI;
	use net\authorize\api\controller as AnetController;

    define("AUTHORIZENET_LOG_FILE", "phplog");

	function getSubscription($subscriptionId) {

		// Common Set Up for API Credentials
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
		$merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
		$refId = 'ref' . time();
		
		// Creating the API Request with required parameters
		$request = new AnetAPI\ARBGetSubscriptionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setRefId($refId);
		$request->setSubscriptionId($subscriptionId);
		
		// Controller
		$controller = new AnetController\ARBGetSubscriptionController($request);
		
		// Getting the response
		$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
		
		if ($response != null) 
		{
			if($response->getMessages()->getResultCode() == "Ok")
			{
				// Success
				echo "SUCCESS: GetSubscription:" . "\n";
				// Displaying the details
				echo "Subscription Name: " . $response->getSubscription()->getName(). "\n";
				echo "Subscription amount: " . $response->getSubscription()->getAmount(). "\n";
				echo "Subscription status: " . $response->getSubscription()->getStatus(). "\n";
				echo "Subscription Description: " . $response->getSubscription()->getProfile()->getDescription(). "\n";
				echo "Customer Profile ID: " .  $response->getSubscription()->getProfile()->getCustomerProfileId() . "\n";
				echo "Customer payment Profile ID: ". $response->getSubscription()->getProfile()->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
			}
			else
			{
				// Error
				echo "ERROR :  Invalid response\n";	
				echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
		  
			}
		}
		else
		{
			// Failed to get response
			echo "Null Response Error";
		}

		return $response;
	}

	if(!defined('DONT_RUN_SAMPLES'))
		getSubscription("2930242");
 ?>
