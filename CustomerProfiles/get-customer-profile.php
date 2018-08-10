<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function getCustomerProfile()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

		// Create the payment data for a credit card
	  $creditCard = new AnetAPI\CreditCardType();
	  $creditCard->setCardNumber( "4111111111111111" );
	  $creditCard->setExpirationDate("2038-12");
	  $paymentCreditCard = new AnetAPI\PaymentType();
	  $paymentCreditCard->setCreditCard($creditCard);

	  // Create the Bill To info
	  $billto = new AnetAPI\CustomerAddressType();
	  $billto->setFirstName("Ellen");
	  $billto->setLastName("Johnson");
	  $billto->setCompany("Souveniropolis");
	  $billto->setAddress("14 Main Street");
	  $billto->setCity("Pecan Springs");
	  $billto->setState("TX");
	  $billto->setZip("44628");
	  $billto->setCountry("USA");
  
	  // Create a Customer Profile Request
	  //  1. create a Payment Profile
	  //  2. create a Customer Profile   
	  //  3. Submit a CreateCustomerProfile Request
	  //  4. Validate Profiiel ID returned

	  $paymentprofile = new AnetAPI\CustomerPaymentProfileType();

	  $paymentprofile->setCustomerType('individual');
	  $paymentprofile->setBillTo($billto);
	  $paymentprofile->setPayment($paymentCreditCard);
	  $paymentprofiles[] = $paymentprofile;
	  $customerprofile = new AnetAPI\CustomerProfileType();
	  $customerprofile->setDescription("Customer 2 Test PHP");
	  $merchantCustomerId = time().rand(1,150);
	  $customerprofile->setMerchantCustomerId($merchantCustomerId);
	  $customerprofile->setEmail("test2@domain.com");
	  $customerprofile->setPaymentProfiles($paymentprofiles);

	  $request = new AnetAPI\CreateCustomerProfileRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setRefId( $refId);
	  $request->setProfile($customerprofile);
	  $controller = new AnetController\CreateCustomerProfileController($request);
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		  echo "SUCCESS: CreateCustomerProfile PROFILE ID : " . $response->getCustomerProfileId() . "\n";

		  $profileIdRequested = $response->getCustomerProfileId();
	   }
	  else
	  {
		  echo "ERROR :  CreateCustomerProfile: Invalid response\n";
		  $errorMessages = $response->getMessages()->getMessage();
		  echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
	  }
	  // Retrieve an existing customer profile along with all the associated payment profiles and shipping addresses

	  $request = new AnetAPI\GetCustomerProfileRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setCustomerProfileId($profileIdRequested);
	  $controller = new AnetController\GetCustomerProfileController($request);
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		echo "GetCustomerProfile SUCCESS : " .  "\n";
		$profileSelected = $response->getProfile();
		$paymentProfilesSelected = $profileSelected->getPaymentProfiles();
		echo "Profile Has " . count($paymentProfilesSelected). " Payment Profiles" . "\n";

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
		echo "ERROR :  GetCustomerProfile: Invalid response\n";
		$errorMessages = $response->getMessages()->getMessage();
		echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
	  }
	  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
  {
	getCustomerProfile();
  }
?>
