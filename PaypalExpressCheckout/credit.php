<?php
require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

// Common setup for API credentials (Paypal compatible merchant)
$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
// $merchantAuthentication->setName("mbld_api_-g9yGXH6");
// $merchantAuthentication->setTransactionKey("8b948Sk5Tk5jBB6w");
$merchantAuthentication->setName("5KP3u95bQpv");
$merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");

$refId = 'ref' . time();
$refTransId = "2241762126";

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
?>
