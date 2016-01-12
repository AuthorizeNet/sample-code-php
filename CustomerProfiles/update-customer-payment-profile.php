<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("5KP3u95bQpv");
  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
  $refId = 'ref' . time();

  //Set profile ids of profile to be updated
  $request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId("36731856");
  $controller = new AnetController\GetCustomerProfileController($request);


  // We're updating the billing address but everything has to be passed in an update
  // For card information you can pass exactly what comes back from an GetCustomerPaymentProfile
  // if you don't need to update that info
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "XXXX1111" );
  $creditCard->setExpirationDate( "XXXX");
  $paymentCreditCard = new AnetAPI\PaymentType();
  $paymentCreditCard->setCreditCard($creditCard);

  // Create the Bill To info for new payment type
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Mrs Mary");
  $billto->setLastName("Doe");
  $billto->setAddress("1 New St.");
  $billto->setCity("Brand New City");
  $billto->setState("WA");
  $billto->setZip("98004");
  $billto->setPhoneNumber("000-000-0000");
  $billto->setfaxNumber("999-999-9999");
  $billto->setCountry("USA");
  

  // Create the Customer Payment Profile object
  $paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
  $paymentprofile->setCustomerPaymentProfileId("33211899");
  $paymentprofile->setBillTo($billto);
  $paymentprofile->setPayment($paymentCreditCard);

  // Submit a UpdatePaymentProfileRequest
  $request->setPaymentProfile( $paymentprofile );

  $controller = new AnetController\UpdateCustomerPaymentProfileController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
     echo "Update Customer Payment Profile SUCCESS: " . "\n";
     
     // Update only returns success or fail, if success
     // confirm the update by doing a GetCustomerPaymentProfile
     $getRequest = new AnetAPI\GetCustomerPaymentProfileRequest();
     $getRequest->setMerchantAuthentication($merchantAuthentication);
     $getRequest->setRefId( $refId);
     $getRequest->setCustomerProfileId("36731856");
     $getRequest->setCustomerPaymentProfileId("33211899");

     $controller = new AnetController\GetCustomerPaymentProfileController($getRequest);
     $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
     if(($response != null)){
          if ($response->getMessages()->getResultCode() == "Ok")
          {
              echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
              echo "Customer Payment Profile Id: " . $response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
              echo "Customer Payment Profile Billing Address: " . $response->getPaymentProfile()->getbillTo()->getAddress(). "\n";
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

   }
  else
  {
      echo "Update Customer Payment Profile: ERROR Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
