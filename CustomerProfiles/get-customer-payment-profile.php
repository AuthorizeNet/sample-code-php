
<?php

require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

// Common setup for API credentials (merchant)
$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
$merchantAuthentication->setName("5KP3u95bQpv");
$merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
$refId = 'ref' . time();

//request requires customerProfileId and customerPaymentProfileId
$request = new AnetAPI\GetCustomerPaymentProfileRequest();
$request->setMerchantAuthentication($merchantAuthentication);
$request->setRefId( $refId);
$request->setCustomerProfileId("36731856");
$request->setCustomerPaymentProfileId("33211899");

$controller = new AnetController\GetCustomerPaymentProfileController($request);
$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
if(($response != null)){
    if ($response->getMessages()->getResultCode() == "Ok")
    {
        echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
        echo "Customer Payment Profile Id: " . $response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
    }
    else
    {
        echo "GetCustomerPaymentProfile ERROR :  Invalid response\n";
        echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
    }
}
else{
    echo "NULL Response Error";
}
?>
