<?php
require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

function payPalCredit($transactionId) {

    // Common setup for API credentials (Paypal compatible merchant)
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);

    $refId = 'ref' . time();
	//use transaction of already settled paypal checkout transaction
    $refTransId = $transactionId;

    // Create the payment data for a paypal account
    $payPalType = new AnetAPI\PayPalType();
    $payPalType->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    $payPalType->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setPayPal($payPalType);

    //create a refund transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "refundTransaction");
    $transactionRequestType->setAmount(181);
    $transactionRequestType->setPayment($paymentOne);
    ///refTransId of successfully settled transaction
    $transactionRequestType->setRefTransId($refTransId);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId( $refId);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null)
    {
        $tresponse = $response->getTransactionResponse();

        if (($tresponse != null)&& ($response->getMessages()->getResultCode()=="Ok"))
        {
            echo "Credit SUCCESS AUTH CODE : " . $tresponse->getAuthCode() . "\n";
            echo "Credit TRANS ID  : " . $tresponse->getTransId() . "\n";
        }
        else
        {
            echo  "Credit ERROR : " . $tresponse->getResponseCode() . "\n";
        }
    }
    else
    {
        echo  "No response returned";
    }

    return $response;
}

if(!defined('DONT_RUN_SAMPLES')){
	//use transaction id of already settled transaction
  payPalCredit("2241762126");
}
?>
