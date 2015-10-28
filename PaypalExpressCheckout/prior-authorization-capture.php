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

  $payPalType = new AnetAPI\PayPalType();
  $payPalType->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
  $payPalType->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");

  $paymentType = new AnetAPI\PaymentType();
  $paymentType->setPayPal($payPalType);

  $transactionRequest = new AnetAPI\TransactionRequestType();
  $transactionRequest->setTransactionType("priorAuthCaptureTransaction");
  $transactionRequest->setPayment($paymentType);
  $transactionRequest->setAmount(floatval(19.45));
  $transactionRequest->setRefTransId(2241687191);

  $request = new AnetAPI\CreateTransactionRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setRefId( $refId);
  $request->setTransactionRequest($transactionRequest);
  
  $controller = new AnetController\CreateTransactionController($request);

  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
  	$tresponse = $response->getTransactionResponse();
    if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )
    {
      echo "Prior Authorization capture AUTH CODE : " . $tresponse->getAuthCode() . "\n";
    }
    else
    {
      echo  "Prior Authorization capture ERROR :  Invalid response\n";
    }
  }
  else
  {
  	echo "PriorAuthorizationCapture ERROR :  Invalid response\n";
  }

?>