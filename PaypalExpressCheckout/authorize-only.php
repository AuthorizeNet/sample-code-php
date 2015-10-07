<?php
require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

// Common setup for API credentials (Paypal compatible merchant)
$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
$merchantAuthentication->setName("5KP3u95bQpv");
$merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
$refId = 'ref' . time();

// Create the payment data for a paypal account
$payPalType = new AnetAPI\PayPalType();
$payPalType->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
$payPalType->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
$paymentOne = new AnetAPI\PaymentType();
$paymentOne->setPayPal($payPalType);

//create a auth-only transaction
$transactionRequestType = new AnetAPI\TransactionRequestType();
$transactionRequestType->setTransactionType( "authOnlyTransaction");
$transactionRequestType->setAmount(921);
$transactionRequestType->setPayment($paymentOne);

$request = new AnetAPI\CreateTransactionRequest();
$request->setMerchantAuthentication($merchantAuthentication);
$request->setRefId( $refId);
$request->setTransactionRequest( $transactionRequestType);

$controller = new AnetController\CreateTransactionController($request);
$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

if (($response != null))
{
    $tresponse = $response->getTransactionResponse();
    if (($tresponse != null)&& ($response->getMessages()->getResultCode()=="Ok"))
    {
        echo "Received response code: ".$tresponse->getResponseCode()."\n";
        //Valid response codes: 1=Approved, 2=Declined, 3=Error, 5=Need Payer Consent\n";
        echo "Secure acceptance URL: ".$tresponse->getSecureAcceptance()->getSecureAcceptanceUrl()."\n";
    }
    else{
        echo  "ERROR : " . $tresponse->getResponseCode() . "\n";
    }
}
else
    echo  "NULL response Error\n";
?>
