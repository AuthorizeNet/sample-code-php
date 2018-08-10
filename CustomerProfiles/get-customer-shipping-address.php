<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function getCustomerShippingAddress($customerprofileid, $customeraddressid)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
    
	  // An existing customer profile id and shipping address id for this merchant name and transaction key
	  $customerProfileId = $customerprofileid;
	  $customerAddressId = $customeraddressid;

	  $request = new AnetAPI\GetCustomerShippingAddressRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setCustomerProfileId($customerProfileId);
	  $request->setCustomerAddressId($customerAddressId);
	  
	  $controller = new AnetController\GetCustomerShippingAddressController($request);
	  
	  //Retrieving existing customer shipping address
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		  echo "Get Customer Shipping Address SUCCESS" . "\n";
		  echo "	FirstName 	: " . $response->getAddress()->getFirstName() . "\n";
		  echo "	LastName 	: " . $response->getAddress()->getLastName() . "\n";
		  echo "	Company 	: " . $response->getAddress()->getCompany() . "\n";
		  echo "	Address 	: " . $response->getAddress()->getAddress() . "\n";
		  echo "	City 		: " . $response->getAddress()->getCity() . "\n";
		  echo "	State 		: " . $response->getAddress()->getState() . "\n";
		  echo "	Zip 		: " . $response->getAddress()->getZip() . "\n";
		  echo "	Country 	: " . $response->getAddress()->getCountry() . "\n";
		  echo "	Phone Number 	: " . $response->getAddress()->getPhoneNumber() . "\n";
		  echo "	FAX Number 	: " . $response->getAddress()->getFaxNumber() . "\n";
		  echo "Customer AddressId 	: " . $response->getAddress()->getCustomerAddressId() . "\n";

		if($response->getSubscriptionIds() != null) 
		{
			if($response->getSubscriptionIds() != null)
			{

				echo "List of subscriptions:";
				foreach($response->getSubscriptionIds() as $subscriptionid)
					echo $subscriptionid . "\n";
			}
		}
	   }
	  else
	  {
		  echo "Get Customer Shipping Address  ERROR :  Invalid response\n";
		  $errorMessages = $response->getMessages()->getMessage();
		  echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
	  }
	  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      getCustomerShippingAddress("36152127","36976566");
?>
