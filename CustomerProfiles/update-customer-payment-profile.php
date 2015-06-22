<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
  $refId = 'ref' . time();
  //Use an existing profile id
  $existingcustomerprofileid = "35858366";

  // Retrieve an existing customer profile id along with all the associated payment profiles and shipping addresses
  $request = new AnetAPI\GetCustomerProfileRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId( $existingcustomerprofileid );
  $controller = new AnetController\GetCustomerProfileController($request);

  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
 //   echo "SUCCESS: PROFILE RETRIEVED : " . $response->getProfile() . "\n";
    $getcustomerprofileid = $response->getProfile();
  }
  else
  {
    echo "GetCustomerProfileRequest ERROR :  Invalid response\n";
  }
  // Create the payment data for a credit card
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "4012888818888" );
  $creditCard->setExpirationDate( "2038-11");
  $paymentCreditCard = new AnetAPI\PaymentType();
  $paymentCreditCard->setCreditCard($creditCard);

  // Create the Bill To info for new payment type
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Jane");
  $billto->setLastName("Doe");
  $billto->setCompany("My company");
  $billto->setAddress("588 Willis Ct");
  $billto->setCity("Vacaville");
  $billto->setState("CA");
  $billto->setZip("95688");
  $billto->setPhoneNumber("555-555-1212");
  $billto->setfaxNumber("999-999-9999");
  $billto->setCountry("USA");

  // Create a new Customer Payment Profile
  $paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
  $paymentprofile->setCustomerType('individual');
  $paymentprofile->setBillTo($billto);
  $paymentprofile->setPayment($paymentCreditCard);
  $paymentprofile->setCustomerPaymentProfileId( $getcustomerprofileid);
  $paymentprofiles[] = $paymentprofile;

  // Submit a UpdatePaymentProfileRequest to update an existing create a new Customer Payment Profile
  $paymentprofilerequest = new AnetAPI\UpdateCustomerPaymentProfileRequest();
  $paymentprofilerequest->setMerchantAuthentication($merchantAuthentication);
  $paymentprofilerequest->setCustomerProfileId( $getcustomerprofileid  );
  $paymentprofilerequest->setPaymentProfile( $paymentprofile );
  $paymentprofilerequest->setRefId($refId);
  $paymentprofilerequest->setValidationMode("liveMode");
  $controller = new AnetController\UpdateCustomerPaymentProfileController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
     echo "Update Customer Payment Profile SUCCESS: " . "\n";
     $retrievedcustomerprofile = $response->getProfile();
   }
  else
  {
      echo "Update Customer Payment Profile: ERROR Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
