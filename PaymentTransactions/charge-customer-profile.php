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

  $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
  $profileToCharge->setCustomerProfileId("36731856");
  $paymentProfile = new AnetAPI\PaymentProfileType();
  $paymentProfile->setPaymentProfileId("33211899");
  $profileToCharge->setPaymentProfile($paymentProfile);


  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
  $transactionRequestType->setAmount(100.50);
  $transactionRequestType->setProfile($profileToCharge);

  $request = new AnetAPI\CreateTransactionRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setRefId( $refId);
  $request->setTransactionRequest( $transactionRequestType);
  $controller = new AnetController\CreateTransactionController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if ($response != null)
  {
    $tresponse = $response->getTransactionResponse();
    if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )   
    {
      echo  "Charge Customer Profile APPROVED  :" . "\n";
      echo " Charge Customer Profile AUTH CODE : " . $tresponse->getAuthCode() . "\n";
      echo " Charge Customer Profile TRANS ID  : " . $tresponse->getTransId() . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
    {
      echo  "ERROR" . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
    {
        echo  "ERROR: HELD FOR REVIEW:"  . "\n";
    }
  }
  else
  {
    echo "no response returned";
  }
?>
