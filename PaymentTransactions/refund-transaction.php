<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function refundTransaction($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
	$refId = 'ref' . time();

    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( \SampleCode\Constants::CREDIT_CARD_NUMBER );
    $creditCard->setExpirationDate( \SampleCode\Constants::EXPIRY_DATE);
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    //create a transaction
    $transactionRequest = new AnetAPI\TransactionRequestType();
    $transactionRequest->setTransactionType( "refundTransaction"); 
    $transactionRequest->setAmount($amount);
    $transactionRequest->setPayment($paymentOne);
    $customer = new AnetAPI\CustomerDataType();
    $customer->setId("CUST001");
    $transactionRequest->setCustomer($customer);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest( $transactionRequest);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== \SampleCode\Constants::RESPONSE_OK) )   
      {
        echo "Refund SUCCESS: " . $tresponse->getTransId() . "\n";
      }
      else
      {
        echo  "Refund ERROR : " . $tresponse->getResponseCode() . "\n";
      }
      
    }
    else
    {
      echo  "Refund Null response returned";
    }
    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
    refundTransaction( \SampleCode\Constants::SAMPLE_AMOUNT_REFUND);
?>