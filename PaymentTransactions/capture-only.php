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

  //===================================================================
  // This transaction type is used when you have an auth code that was
  // obtained from a system other than authorize.net for example a 
  // verbal authorization over the telephone
  //====================================================================

  // Create the payment data for a credit card
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

  //create a captureOnly transaction
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "captureOnlyTransaction");
  $transactionRequestType->setAmount(151);
  // This is the Auth Code from another payment channel
  $transactionRequestType->setAuthCode("ABC123");
  $transactionRequestType->setPayment($paymentOne);
  $transactionRequestType->setOrder($order);
  
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
      echo "Capture Funds TRANS ID  : " . $tresponse->getTransId() . "\n";
    }
    else
    {
      echo  " Capture Funds Authorized Through Another Channel:  Invalid response\n";
    }
  }
  else
  {
    echo  "Capture Funds Authorized Through Another Channel NULL response Error\n";
  }
?>
