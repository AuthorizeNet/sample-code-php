<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function chargeTokenizedCreditCard($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4111111111111111" );
    $creditCard->setExpirationDate("2038-12");
    //Set the cryptogram 
    $creditCard->setCryptogram("EjRWeJASNFZ4kBI0VniQEjRWeJA=");
    
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    
    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction"); 
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
        echo "Charge Tokenized Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo "Charge Tokenized Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      else
      {
        echo  "Charge Tokenized Credit Card ERROR :  Invalid response\n";
      }
    }
    else
    {
      echo  "Charge Tokenized Credit Card Null response returned";
    }
    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
    chargeTokenizedCreditCard(12.23);
?>
