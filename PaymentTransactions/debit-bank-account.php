<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function debitBankAccount($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Create the payment data for a Bank Account
    $bankAccount = new AnetAPI\BankAccountType();
    //$bankAccount->setAccountType('CHECKING');
    $bankAccount->setEcheckType('WEB');
    $bankAccount->setRoutingNumber('121042882');
    $bankAccount->setAccountNumber('123456789123');
    $bankAccount->setNameOnAccount('Jane Doe');
    $bankAccount->setBankName('Bank of the Earth');

    $paymentBank= new AnetAPI\PaymentType();
    $paymentBank->setBankAccount($bankAccount);


    //create a debit card Bank transaction
    
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setPayment($paymentBank);

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
        echo  "Debit Bank Account APPROVED  :" . "\n";
        echo " Debit Bank Account AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo " Debit Banlk Account TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
      {
        echo  "Debit Bank Account ERROR : DECLINED" . "\n";
        $errorMessages = $tresponse->getErrors();
        echo  "Error : " . $errorMessages[0]->getErrorText() . "\n";
      }
      elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
      {
          echo  "Debit Bank Account ERROR: HELD FOR REVIEW:"  . "\n";
      }
      else
      {
          echo  "Debit Bank Account 3 response returned";
      }
    }
    else
    {
      echo  "Debit Bank Account Null response returned";
    }
    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
    debitBankAccount(12.23);
?>
