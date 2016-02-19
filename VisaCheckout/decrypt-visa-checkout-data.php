<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function decryptVisaCheckoutData(){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Create the payment data from a Visa Checkout blob
    $op = new AnetAPI\OpaqueDataType();
    $op->setDataDescriptor(\SampleCode\Constants::VC_DATA_DESCRIPTOR);
    $op->setDataValue( \SampleCode\Constants::VC_DATA_VALUE);
    $op->setDataKey( \SampleCode\Constants::VC_DATA_KEY);
    
    //create a decrypt request
    $decryptRequest = new AnetAPI\DecryptPaymentDataRequest();
    $decryptRequest->setRefId( $refId);
    $decryptRequest->setMerchantAuthentication($merchantAuthentication);
    $decryptRequest->setOpaqueData($op);
    $decryptRequest->setCallId("9004180129978687101");


    $controller = new AnetController\DecryptPaymentDataController($decryptRequest);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null)
    {
      if ($response->getMessages()->getResultCode() == "Ok")
      {
          echo "Card Number  : " . $response->getCardInfo()->getCardNumber() . "\n";
          echo "Amount : " . $response->getPaymentDetails()->getAmount() . "\n";
      }
    }
    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
    decryptVisaCheckoutData();

?>
