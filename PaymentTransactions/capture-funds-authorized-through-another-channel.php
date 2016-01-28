<?php
require 'vendor/autoload.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

function captureFundsAuthorizedThroughAnotherChannel($amount){
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();

    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( \SampleCode\Constants::CREDIT_CARD_NUMBER);
    $creditCard->setExpirationDate( \SampleCode\Constants::EXPIRY_DATE);

    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("captureOnlyTransaction");
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setPayment($paymentOne);

    //Auth code of the previously authorized  amount
    $transactionRequestType->setAuthCode("ROHNFQ");


    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null)
    {
        $tresponse = $response->getTransactionResponse();
        if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )
        {
            echo "Successful." . "\n";
            echo "Capture funds authorized through another channel TRANS ID  : " . $tresponse->getTransId() . "\n";
        }
        else
        {
            echo  "Capture funds authorized through another channel ERROR: Invalid response\n";
            echo "Response Code: " . $response->getMessages()->getMessage()[0]->getCode() . "  Response Text: " .$response->getMessages()->getMessage()[0]->getText() . "\n";
        }
    }
    else
    {
        echo  "Capture funds authorized through another channel NULL Response Error\n";
    }
    return $response;
}
if(!defined('DONT_RUN_SAMPLES'))
    captureFundsAuthorizedThroughAnotherChannel(5.0);
?>
