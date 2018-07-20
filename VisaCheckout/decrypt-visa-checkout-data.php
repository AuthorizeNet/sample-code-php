<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

function decryptVisaCheckoutData()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Create the payment data from a Visa Checkout blob
    $op = new AnetAPI\OpaqueDataType();
    $op->setDataDescriptor("COMMON.VCO.ONLINE.PAYMENT");
    $op->setDataValue("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");
    $op->setDataKey("JxAB39A9yUsf6wMD0jwGKCuY7mmzWaeEj85MH02AfKGUxOkJ00JK+o8vGG55cUF9voYU4QQ1Jec4p6nKmsxXmTREEaJwmQErotD4fpcFOyoqWTLMZfslrkHgiqbwru/V");
    
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
