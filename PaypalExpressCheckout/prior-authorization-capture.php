<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function payPalPriorAuthorizationCapture($transactionId) {

    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
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
    $transactionRequest->setRefTransId($transactionId);

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

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
  {
    payPalPriorAuthorizationCapture("2249863278");
  }
?>