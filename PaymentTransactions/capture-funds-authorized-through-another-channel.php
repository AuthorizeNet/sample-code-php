<?php
require 'vendor/autoload.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
define("AUTHORIZENET_LOG_FILE", "phplog");


// Common setup for API credentials
$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
$merchantAuthentication->setName("556KThWQ6vf2");
$merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
$refId = "123456";

$creditCard = new AnetAPI\CreditCardType();
$creditCard->setCardNumber( "4111111111111111");
$creditCard->setExpirationDate( "2038-12");

$paymentOne = new AnetAPI\PaymentType();
$paymentOne->setCreditCard($creditCard);

$transactionRequestType = new AnetAPI\TransactionRequestType();
$transactionRequestType->setTransactionType("captureOnlyTransaction");
$transactionRequestType->setAmount(5.00);
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
?>
