<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function createCustomerProfileFromTransaction($transId= "2249066517")
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $customerProfile = new AnetAPI\CustomerProfileBaseType();
    $customerProfile->setMerchantCustomerId("123212");
    $customerProfile->setEmail(rand(0, 10000) . "@test" .".com");
    $customerProfile->setDescription(rand(0, 10000) ."sample description");
      
    $request = new AnetAPI\CreateCustomerProfileFromTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setTransId($transId);

    // You can either specify the customer information in form of customerProfileBaseType object
    $request->setCustomer($customerProfile);
    //  OR   
    // You can just provide the customer Profile ID
        //$request->setCustomerProfileId("123343");

    $controller = new AnetController\CreateCustomerProfileFromTransactionController($request);

    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
        echo "SUCCESS: PROFILE ID : " . $response->getCustomerProfileId() . "\n";
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
}

// Provide a transaction that has customer information
if (!defined('DONT_RUN_SAMPLES')) {
      createCustomerProfileFromTransaction("2249066517");
}

?>
