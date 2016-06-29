<?php
 require 'vendor/autoload.php';
 use net\authorize\api\contract\v1 as AnetAPI;
 use net\authorize\api\controller as AnetController;

 define("AUTHORIZENET_LOG_FILE", "phplog");

 function capturePreviouslyAuthorizedAmount($transactionid){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Now capture the previously authorized  amount
    echo "Capturing the Authorization with transaction ID : " . $transactionid . "\n";
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
    $transactionRequestType->setRefTransId($transactionid);

    
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== \SampleCode\Constants::RESPONSE_OK) )
      {
        echo "Successful." . "\n";
        echo "Capture Previously Authorized Amount, Trans ID : " . $tresponse->getRefTransId() . "\n";
      }
      else
      {
        echo  " Capture Previously Authorized Amount: Invalid response\n";
      }
    }
    else
    {
      echo  "Capture Previously Authorized Amount, NULL Response Error\n";
    }
    return $response;
  }
 if(!defined('DONT_RUN_SAMPLES'))
    capturePreviouslyAuthorizedAmount(2249839471);
?>
