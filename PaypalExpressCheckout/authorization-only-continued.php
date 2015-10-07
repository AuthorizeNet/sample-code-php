<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  echo "PayPal Authorize Only Continue Transaction\n";
  
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("5KP3u95bQpv");
  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
  $refId = 'ref' . time();

  $paypal_type = new AnetAPI\PayPalType();
  $paypal_type->setPayerID("JJLRRB29QC7RU");
  $paypal_type->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");  
  $paypal_type->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
  
  $payment_type = new AnetAPI\PaymentType();
  $payment_type->setPayPal($paypal_type);

  //create a transaction
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authOnlyContinueTransaction"); 
  $transactionRequestType->setRefTransId("2241711631");
  $transactionRequestType->setAmount(125.34);
  $transactionRequestType->setPayment($payment_type);

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
      //echo " RESULT CODE : " . $response->getResultCode() . "\n";
      echo " TRANS ID  : " . $tresponse->getTransId() . "\n";
      echo "Payer ID : " . $tresponse->getSecureAcceptance()->getPayerID();      
    }
    else
    {
      //print_r($tresponse);
      echo  "ERROR : " . $tresponse->getResponseCode() . "\n";
    }
    
  }
  else
  {
    echo  "No response returned";
  }

?>
