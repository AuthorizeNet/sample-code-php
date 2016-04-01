<?php

require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");
  
function getCustomerPaymentProfile($customerProfileId="36731856", 
   $customerPaymentProfileId= "33211899")
{
	// Common setup for API credentials (merchant)
	$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	$merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
	$merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
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
				if(($response->getPaymentProfile()->getSubscriptionIds() != null) && 
						(!empty($response->getPaymentProfile()->getSubscriptionIds())))
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
			echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
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
