<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function authorizeCreditCard($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4111111111111111");
    $creditCard->setExpirationDate("1226");
    $creditCard->setCardCode("123");
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authOnlyTransaction"); 
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setPayment($paymentOne);

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
        echo " AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo " TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      else
      {
          echo  "ERROR : " . $tresponse->getResponseCode() . "\n";
      }
      
    }
    else
    {
      echo  "No response returned";
    }
    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
    authorizeCreditCard( \SampleCode\Constants::SAMPLE_AMOUNT);
?>