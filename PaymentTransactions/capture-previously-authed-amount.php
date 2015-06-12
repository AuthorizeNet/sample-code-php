<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
  $refId = "123456";
  // Create the // Create the payment data for a credit card
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "4111111111111111");
  $creditCard->setExpirationDate( "2038-12");

  //$creditCard->setCardCode("999");
  $paymentOne = new AnetAPI\PaymentType();
  $paymentOne->setCreditCard($creditCard);
  // Order info
  $order = new AnetAPI\OrderType();
  $order->setInvoiceNumber("INV-12345");
  $order->setDescription("Product Description");

  //create a previously authorized capture transaction
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
  $transactionRequestType->setAmount(5.00);
  $transactionRequestType->setPayment($paymentOne);
  //Set the merchant assigned reference ID from the previous capture only transaction request
  $transactionRequestType->setRefTransId("2234918264");
 // $transactionRequestType->setAuthCode($capturedAuthCode);
  $transactionRequestType->setOrder($order);
  
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
      echo "Capture Previously Authorized Amount AUTH CODE : " . $tresponse->getAuthCode() . "\n";
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
?>
