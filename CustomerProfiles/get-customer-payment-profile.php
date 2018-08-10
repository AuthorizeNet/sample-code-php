<?php

require 'vendor/autoload.php';
require_once 'constants/SampleCodeConstants.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");
  
function getCustomerPaymentProfile($customerProfileId="36731856", 
    $customerPaymentProfileId= "33211899"
) {
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

	//request requires customerProfileId and customerPaymentProfileId
	$request = new AnetAPI\GetCustomerPaymentProfileRequest();
	$request->setMerchantAuthentication($merchantAuthentication);
	$request->setRefId( $refId);
	$request->setCustomerProfileId($customerProfileId);
	$request->setCustomerPaymentProfileId($customerPaymentProfileId);

	$controller = new AnetController\GetCustomerPaymentProfileController($request);
	$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	if(($response != null)){
		if ($response->getMessages()->getResultCode() == "Ok")
		{
			echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
			echo "Customer Payment Profile Id: " . $response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
			echo "Customer Payment Profile Billing Address: " . $response->getPaymentProfile()->getbillTo()->getAddress(). "\n";
			echo "Customer Payment Profile Card Last 4 " . $response->getPaymentProfile()->getPayment()->getCreditCard()->getCardNumber(). "\n";

			if($response->getPaymentProfile()->getSubscriptionIds() != null) 
			{
				if($response->getPaymentProfile()->getSubscriptionIds() != null)
				{

					echo "List of subscriptions:";
					foreach($response->getPaymentProfile()->getSubscriptionIds() as $subscriptionid)
						echo $subscriptionid . "\n";
				}
			}
		}
		else
		{
			echo "GetCustomerPaymentProfile ERROR :  Invalid response\n";
			$errorMessages = $response->getMessages()->getMessage();
		    echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
		}
	}
	else{
		echo "NULL Response Error";
	}
	return $response;
}
if(!defined('DONT_RUN_SAMPLES'))
    getCustomerPaymentProfile();
?>
