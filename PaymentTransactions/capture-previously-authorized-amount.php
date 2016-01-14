<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");

 function capturePreviouslyAuthorizedAmount($transactionid){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName("556KThWQ6vf2");
    $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
    $refId = "123456";

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
      if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )
      {
        echo "Successful." . "\n";
        echo "Capture Previously Authorized Amount TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      else
      {
        echo  " Capture Previously Authorized Amount: Invalid response\n";
      }
    }
    else
    {
      echo  "Capture Previously Authorized Amount NULL Response Error\n";
    }
    return $response;
  }
  if(!defined(DONT_RUN_SAMPLES))
    capturePreviouslyAuthorizedAmount();
?>
