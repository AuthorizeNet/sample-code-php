<?php
    require 'vendor/autoload.php';
    require_once 'constants/SampleCodeConstants.php';
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;

    define("AUTHORIZENET_LOG_FILE", "phplog");

function payPalAuthorizeOnlyContinued($transactionId, $payerId)
{

    echo "PayPal Authorize Only Continued Transaction\n";
    
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Set PayPal compatible merchant credentials
    $payPalType=new AnetAPI\PayPalType();
    $payPalType->setPayerID($payerID);

    $paypal_type->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    $paypal_type->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    
    $payment_type = new AnetAPI\PaymentType();
    $payment_type->setPayPal($paypal_type);

    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authOnlyContinueTransaction");
    $transactionRequestType->setRefTransId($transactionId);
    $transactionRequestType->setAmount(125.34);
    $transactionRequestType->setPayment($payment_type);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest($transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);

    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null) {
        if ($response->getMessages()->getResultCode() == "Ok") {
            $tresponse = $response->getTransactionResponse();
        
            if ($tresponse != null && $tresponse->getMessages() != null) {
                echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                echo "TRANS ID  : " . $tresponse->getTransId() . "\n";
                echo "Payer ID : " . $tresponse->getSecureAcceptance()->getPayerID();
                echo "Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
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
    payPalAuthorizeOnlyContinued("2241711631", "JJLRRB29QC7RU");
}
