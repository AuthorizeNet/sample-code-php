<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function updateCustomerPaymentProfile($customerProfileId = "1916322670",
    $customerPaymentProfileId = "1829639667") 
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

	$request = new AnetAPI\GetCustomerPaymentProfileRequest();
	$request->setMerchantAuthentication($merchantAuthentication);
	$request->setRefId( $refId);
	$request->setCustomerProfileId($customerProfileId);
	$request->setCustomerPaymentProfileId($customerPaymentProfileId);
	  
	$controller = new AnetController\GetCustomerPaymentProfileController($request);
	$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
	{
		$billto = new AnetAPI\CustomerAddressType();
		$billto = $response->getPaymentProfile()->getbillTo();
		
		$creditCard = new AnetAPI\CreditCardType();
		$creditCard->setCardNumber( "4111111111111111" );
		$creditCard->setExpirationDate("2038-12");
		
		$paymentCreditCard = new AnetAPI\PaymentType();
		$paymentCreditCard->setCreditCard($creditCard);
		$paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
		$paymentprofile->setBillTo($billto);
		$paymentprofile->setCustomerPaymentProfileId($customerPaymentProfileId);
		$paymentprofile->setPayment($paymentCreditCard);	

		// We're updating the billing address but everything has to be passed in an update
		// For card information you can pass exactly what comes back from an GetCustomerPaymentProfile
		// if you don't need to update that info
		  
		// Update the Bill To info for new payment type
		$billto->setFirstName("Mrs Mary");
		$billto->setLastName("Doe");
		$billto->setAddress("9 New St.");
		$billto->setCity("Brand New City");
		$billto->setState("WA");
		$billto->setZip("98004");
		$billto->setPhoneNumber("000-000-0000");
		$billto->setfaxNumber("999-999-9999");
		$billto->setCountry("USA");
		 
		// Update the Customer Payment Profile object
		$paymentprofile->setBillTo($billto);

		// Submit a UpdatePaymentProfileRequest
		$request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setCustomerProfileId($customerProfileId);
		$request->setPaymentProfile( $paymentprofile );

		$controller = new AnetController\UpdateCustomerPaymentProfileController($request);
		$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
		if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
		{
			$Message = $response->getMessages()->getMessage();
			print_r($response);
			echo "Update Customer Payment Profile SUCCESS: " . $Message[0]->getCode() . "  " .$Message[0]->getText() . "\n";
		}
		else
		{
			echo "Failed to Update Customer Payment Profile :  " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
		}
	
		return $response;
	}
	else
	{
		echo "Failed to Get Customer Payment Profile :  " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
	}

	return $response;
}

if(!defined('DONT_RUN_SAMPLES'))
    updateCustomerPaymentProfile();
?>
