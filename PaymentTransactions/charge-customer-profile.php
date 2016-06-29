<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function chargeCustomerProfile($profileid, $paymentprofileid, $amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
    $profileToCharge->setCustomerProfileId($profileid);
    $paymentProfile = new AnetAPI\PaymentProfileType();
    $paymentProfile->setPaymentProfileId($paymentprofileid);
    $profileToCharge->setPaymentProfile($paymentProfile);


    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
    $transactionRequestType->setAmount($amount);
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
      if (($tresponse != null) && ($tresponse->getResponseCode()== \SampleCode\Constants::RESPONSE_OK) )   
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
    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
    chargeCustomerProfile("36731856","32689274",12.23);
?>
