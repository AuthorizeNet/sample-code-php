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
  //Set the cryptogram 
  $creditCard->setCryptogram("EjRWeJASNFZ4kBI0VniQEjRWeJA=");
  
  $paymentOne = new AnetAPI\PaymentType();
  $paymentOne->setCreditCard($creditCard);
  // Order info
  $order = new AnetAPI\OrderType();
  $order->setInvoiceNumber("106");
  $order->setDescription("Hunting Boots");

  // Line Item Info
  $lineitem = new AnetAPI\LineItemType();
  $lineitem->setItemId("Boots");
  $lineitem->setName("item7");
  $lineitem->setDescription("huntimng boots");
  $lineitem->setQuantity("1");
  $lineitem->setUnitPrice(85.45);
  $lineitem->setTaxable("Y");

  // Tax info 
  $tax =  new AnetAPI\ExtendedAmountType();
  $tax->setName("level 2 tax name");
  $tax->setAmount(4.50);
  $tax->setDescription("level 2 tax");

  // Customer info 
  $customer = new AnetAPI\CustomerDataType();
  $customer->setId("15");
  $customer->setEmail("foo@example.com");

  // PO Number
  $ponumber = "15";
  //Ship To Info
  $shipto = new AnetAPI\NameAndAddressType();
  $shipto->setFirstName("Bean");
  $shipto->setLastName("L.L.");
  $shipto->setCompany("L.L. Bean");
  $shipto->setAddress("115 North French Drive");
  $shipto->setCity("Prescott");
  $shipto->setState("AZ");
  $shipto->setZip("86303");
  $shipto->setCountry("USA");

  // Bill To
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Representative");
  $billto->setLastName("Customer");
  $billto->setCompany("L.L. Bean");
  $billto->setAddress("115 North French Drive");
  $billto->setCity("Prescott");
  $billto->setState("AZ");
  $billto->setZip("86305");
  $billto->setCountry("USA");
  
  //create a transaction
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType("authCaptureTransaction"); 
  $transactionRequestType->setAmount(151.21);
  $transactionRequestType->setPayment($paymentOne);
  $transactionRequestType->setOrder($order);
  $transactionRequestType->addToLineItems($lineitem);
  $transactionRequestType->setTax($tax);
  $transactionRequestType->setPoNumber($ponumber);
  $transactionRequestType->setCustomer($customer);
  $transactionRequestType->setBillTo($billto);
  $transactionRequestType->setShipTo($shipto);

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
?>
