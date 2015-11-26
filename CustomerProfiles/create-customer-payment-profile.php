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

  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "4012888818888" );
  $creditCard->setExpirationDate( "2038-11");
  $creditCard->setCardCode( "123");
  $paymentCreditCard = new AnetAPI\PaymentType();
  $paymentCreditCard->setCreditCard($creditCard);

  // Create the Bill To info for new payment type
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Mrs Mary");
  $billto->setLastName("Doe");
  $billto->setCompany("My company");
  $billto->setAddress("123 Main St.");
  $billto->setCity("Bellevue");
  $billto->setState("WA");
  $billto->setZip("98004");
  $billto->setPhoneNumber("000-000-0000");
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
     echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
     
  }
?>
