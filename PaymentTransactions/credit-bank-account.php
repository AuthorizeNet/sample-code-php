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
  $bankAccount->setRoutingNumber('125000024');
  $bankAccount->setAccountNumber('12345678');
  $bankAccount->setNameOnAccount('John Doe');
  
  $paymentBank= new AnetAPI\PaymentType();
  $paymentBank->setBankAccount($bankAccount);

// Order info
  $order = new AnetAPI\OrderType();
  $order->setInvoiceNumber("101");
  $order->setDescription("Golf Shirts");

    //create a debit card Bank transaction
  
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "refundTransaction"); 
  $transactionRequestType->setAmount(250.75);
  $transactionRequestType->setPayment($paymentBank);
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
      echo  "Credit Bank Account APPROVED  :" . "\n";
      echo  "Credit Bank Account AUTH CODE : " . $tresponse->getAuthCode() . "\n";
      echo  "Credit Bank Account TRANS ID  : " . $tresponse->getTransId() . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
    {
      echo  "Credit Bank Account ERROR : DECLINED" . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
    {
        echo  "Credit Bank Account ERROR: HELD FOR REVIEW:"  . "\n";
    }
  }
  else
  {
    echo  "Credit Bank Account No response returned";
  }
?>
