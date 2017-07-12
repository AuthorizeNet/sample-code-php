<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

function debitBankAccount($amount)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Create the payment data for a Bank Account
    $bankAccount = new AnetAPI\BankAccountType();
    $bankAccount->setAccountType('checking');
    // $bankAccount->setEcheckType('WEB');
    $bankAccount->setRoutingNumber('121042882');
    $bankAccount->setAccountNumber('12345678');
    $bankAccount->setNameOnAccount('John Doe');
    $bankAccount->setBankName('Bank of the Earth');

    $paymentBank= new AnetAPI\PaymentType();
    $paymentBank->setBankAccount($bankAccount);

    // Order info
    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber("101");
    $order->setDescription("Golf Shirts");

    //create a bank debit transaction
    
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction");
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setPayment($paymentBank);
    $transactionRequestType->setOrder($order);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest($transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null) {
        if ($response->getMessages()->getResultCode() == \SampleCode\Constants::RESPONSE_OK) {
            $tresponse = $response->getTransactionResponse();
        
            if ($tresponse != null && $tresponse->getMessages() != null) {
                echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                echo " Debit Bank Account APPROVED  :" . "\n";
                echo " Debit Bank Account AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                echo " Debit Bank Account TRANS ID  : " . $tresponse->getTransId() . "\n";
                echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n";
                echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
            } else {
                echo "Transaction Failed \n";
                if ($tresponse->getErrors() != null) {
                    echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                    echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                }
            }
        } else {
            echo "Transaction Failed \n";
            $tresponse = $response->getTransactionResponse();
            if ($tresponse != null && $tresponse->getErrors() != null) {
                echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
            } else {
                echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
            }
        }
    } else {
        echo  "No response returned \n";
    }

    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    debitBankAccount(5.29);
}
