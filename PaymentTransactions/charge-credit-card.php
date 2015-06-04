<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
  $refId = 'ref' . time();
  // Create the payment data for a credit card
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "4111111111111111" );
  $creditCard->setExpirationDate( "2038-12");
  $paymentOne = new AnetAPI\PaymentType();
  $paymentOne->setCreditCard($creditCard);
  // Order info
  $order = new AnetAPI\OrderType();
  // Line Item Info
  $lineitem = new AnetAPI\LineItemType();
  // Tax info 
  $tax =  new AnetAPI\ExtendedAmountType();
  // Customer info 
  $customer = new AnetAPI\CustomerDataType();
  // PO Number
  $ponumber = "15";
  //Ship To Info
  $shipto = new AnetAPI\NameAndAddressType();
  // Bill To
  $billTo = new AnetAPI\CustomerAddressType();
  
  //create a transaction
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
  $transactionRequestType->setAmount(151.21);
  $transactionRequestType->setPayment($paymentOne);
  $transactionRequestType->setPayment($order);
  $transactionRequestType->addToLineItems($lineitem);
  $transactionRequestType->setTax($tax);
  $transactionRequestType->setPoNumber($ponumber);
  $transactionRequestType->setCustomer($customer);
  $transactionRequestType->setBillTo($billTo);
  $transactionRequestType->setShipTo($shipTo);

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
?>
