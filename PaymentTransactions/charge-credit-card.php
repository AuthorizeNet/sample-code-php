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
  $order->setInvoiceNumber("101");
  $order->setDescription("Golf Shirts");

  // Line Item Info
  $lineitem = new AnetAPI\LineItemType();
  $lineitem->setItemId("Shirts");
  $lineitem->setName("item1");
  $lineitem->setDescription("golf shirt");
  $lineitem->setQuantity("1");
  $lineitem->setUnitPrice(20.95);
  $lineitem->setTaxable(false);

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
  $shipto->setFirstName("Bayles");
  $shipto->setLastName("China");
  $shipto->setCompany("Thyme for Tea");
  $shipto->setAddress("12 Main Street");
  $shipto->setCity("Pecan Springs");
  $shipto->setState("TX");
  $shipto->setZip("44628");
  $shipto->setCountry("USA");

  // Bill To
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Ellen");
  $billto->setLastName("Johnson");
  $billto->setCompany("Souveniropolis");
  $billto->setAddress("14 Main Street");
  $billto->setCity("Pecan Springs");
  $billto->setState("TX");
  $billto->setZip("44628");
  $billto->setCountry("USA");
  
  //create a transaction
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
  $transactionRequestType->setAmount(151.51);
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
      echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
      echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
    }
    else
    {
        echo  "Charge Credit Card ERROR :  Invalid response\n";
    }
    
  }
  else
  {
    echo  "Charge Credit card Null response returned";
  }
?>
