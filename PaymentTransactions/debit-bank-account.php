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
  // Create the payment data for a Bank Account
  $bankAccount = new AnetAPI\BankAccountType();
//  $bankAccount->setAccountType('CHECKING');
  $bankAccount->setEcheckType('WEB');
  $bankAccount->setRoutingNumber('121042882');
  $bankAccount->setAccountNumber('123456789123');
  $bankAccount->setNameOnAccount('Jane Doe');
  $bankAccount->setBankName('Bank of the Earth');

  $paymentBank= new AnetAPI\PaymentType();
  $paymentBank->setBankAccount($bankAccount);

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

  //create a debit card Bank transaction
  
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
  $transactionRequestType->setAmount(250.75);
  $transactionRequestType->setPayment($paymentBank);
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
      echo  "Debit Bank Account APPROVED  :" . "\n";
      echo " Debit Bank Account AUTH CODE : " . $tresponse->getAuthCode() . "\n";
      echo " Debit Banlk Account TRANS ID  : " . $tresponse->getTransId() . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
    {
      echo  "Debit Bank Account ERROR : DECLINED" . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
    {
        echo  "Debit Bank Account ERROR: HELD FOR REVIEW:"  . "\n";
    }
  }
  else
  {
    echo  "Debit Bank Account No response returned";
  }
?>
