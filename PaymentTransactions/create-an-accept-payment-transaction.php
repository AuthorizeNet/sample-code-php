<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function createAnAcceptPaymentTransaction($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    // Create the payment object for a payment nonce
    $paymentNonce = new AnetAPI\OpaqueDataType();
    $paymentNonce->setDataDescriptor("COMMON.ACCEPT.INAPP.PAYMENT");
    $paymentNonce->setDataValue("119eyJjb2RlIjoiNTBfMl8wNjAwMDUyN0JEODE4RjQxOUEyRjhGQkIxMkY0MzdGQjAxQUIwRTY2NjhFNEFCN0VENzE4NTUwMjlGRUU0M0JFMENERUIwQzM2M0ExOUEwMDAzNzlGRDNFMjBCODJEMDFCQjkyNEJDIiwidG9rZW4iOiI5NDkwMjMyMTAyOTQwOTk5NDA0NjAzIiwidiI6IjEuMSJ9");
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setOpaqueData($paymentNonce);

    $order = new AnetAPI\OrderType();
    $order->setDescription("New Item");

    // Set the customer's Bill To address
    $customerAddress = new AnetAPI\CustomerAddressType();
    $customerAddress->setFirstName("Ellen");
    $customerAddress->setLastName("Johnson");
    $customerAddress->setCompany("Souveniropolis");
    $customerAddress->setAddress("14 Main Street");
    $customerAddress->setCity("Pecan Springs");
    $customerAddress->setState("TX");
    $customerAddress->setZip("44628");
    $customerAddress->setCountry("USA");

    // Set the customer's identifying information
    $customerData = new AnetAPI\CustomerDataType();
    $customerData->setType("individual");
    $customerData->setId("99999456654");
    $customerData->setEmail("EllenJohnson@example.com");

    //Add values for transaction settings
    $duplicateWindowSetting = new AnetAPI\SettingType();
    $duplicateWindowSetting->setSettingName("duplicateWindow");
    $duplicateWindowSetting->setSettingValue("600");

    // Create a TransactionRequestType object
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setOrder($order);
    $transactionRequestType->setPayment($paymentOne);
    $transactionRequestType->setBillTo($customerAddress);
    $transactionRequestType->setCustomer($customerData);
    $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId( $refId);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    

    if ($response != null)
    {
      if($response->getMessages()->getResultCode() == \SampleCode\Constants::RESPONSE_OK)
      {
        $tresponse = $response->getTransactionResponse();
        
        if ($tresponse != null && $tresponse->getMessages() != null)   
        {
          echo " Transaction Response Code : " . $tresponse->getResponseCode() . "\n";
          echo " Successfully created an authCapture transaction with Auth Code : " . $tresponse->getAuthCode() . "\n";
          echo " Transaction ID : " . $tresponse->getTransId() . "\n";
          echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
          echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
        }
        else
        {
          echo "Transaction Failed \n";
          if($tresponse->getErrors() != null)
          {
            echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
            echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";            
          }
        }
      }
      else
      {
        echo "Transaction Failed \n";
        $tresponse = $response->getTransactionResponse();
        
        if($tresponse != null && $tresponse->getErrors() != null)
        {
          echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
          echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";                      
        }
        else
        {
          echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
          echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
        }
      }      
    }
    else
    {
      echo  "No response returned \n";
    }

    return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      CreateAnAcceptPaymentTransaction(\SampleCode\Constants::SAMPLE_AMOUNT);
?>
