<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function createCustomerPaymentProfile($existingcustomerprofileid, $phoneNumber){
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
      $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
      $refId = 'ref' . time();

	  $creditCard = new AnetAPI\CreditCardType();
	  $creditCard->setCardNumber( "4242424242424242");
	  $creditCard->setExpirationDate( "2038-12");
	  $creditCard->setCardCode( "142");
	  $paymentCreditCard = new AnetAPI\PaymentType();
	  $paymentCreditCard->setCreditCard($creditCard);

	  // Create the Bill To info for new payment type
	  $billto = new AnetAPI\CustomerAddressType();
	  $billto->setFirstName("Mrs Mary".$phoneNumber);
	  $billto->setLastName("Doe");
	  $billto->setCompany("My company");
	  $billto->setAddress("123 Main St.");
	  $billto->setCity("Bellevue");
	  $billto->setState("WA");
	  $billto->setZip("98004");
	  $billto->setPhoneNumber($phoneNumber);
	  $billto->setfaxNumber("999-999-9999");
	  $billto->setCountry("USA");

	  // Create a new Customer Payment Profile
	  $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
	  $paymentprofile->setCustomerType('individual');
	  $paymentprofile->setBillTo($billto);
	  $paymentprofile->setPayment($paymentCreditCard);

	  $paymentprofiles[] = $paymentprofile;

	  // Submit a CreateCustomerPaymentProfileRequest to create a new Customer Payment Profile
	  $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
	  $paymentprofilerequest->setMerchantAuthentication($merchantAuthentication);
	  //Use an existing profile id
	  $paymentprofilerequest->setCustomerProfileId( $existingcustomerprofileid );
	  $paymentprofilerequest->setPaymentProfile( $paymentprofile );
	  $paymentprofilerequest->setValidationMode("liveMode");
	  $controller = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		 echo "Create Customer Payment Profile SUCCESS: " . $response->getCustomerPaymentProfileId() . "\n";
	   }
	  else
	  {
		 echo "Create Customer Payment Profile: ERROR Invalid response\n";
		 $errorMessages = $response->getMessages()->getMessage();
		 echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
		 
	  }
	  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      createCustomerPaymentProfile("36152127","000-000-0009");
?>
