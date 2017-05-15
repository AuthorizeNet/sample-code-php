<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function createCustomerShippingAddress($existingcustomerprofileid = "36152127", 
    $phoneNumber="000-000-0000"
) {
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
    
    // Use An existing customer profile id for this merchant name and transaction key

    // Create the customer shipping address
    $customershippingaddress = new AnetAPI\CustomerAddressType();
    $customershippingaddress->setFirstName("James");
    $customershippingaddress->setLastName("White");
    $customershippingaddress->setCompany("Addresses R Us");
    $customershippingaddress->setAddress(rand() . " North Spring Street");
    $customershippingaddress->setCity("Toms River");
    $customershippingaddress->setState("NJ");
    $customershippingaddress->setZip("08753");
    $customershippingaddress->setCountry("USA");
    $customershippingaddress->setPhoneNumber($phoneNumber);
    $customershippingaddress->setFaxNumber("999-999-9999");

    // Create a new customer shipping address for an existing customer profile

    $request = new AnetAPI\CreateCustomerShippingAddressRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setCustomerProfileId($existingcustomerprofileid);
    $request->setAddress($customershippingaddress);
    $controller = new AnetController\CreateCustomerShippingAddressController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
        echo "Create Customer Shipping Address SUCCESS: ADDRESS ID : " . $response-> getCustomerAddressId() . "\n";
    } else {
        echo "Create Customer Shipping Address  ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
}
if (!defined('DONT_RUN_SAMPLES')) {
      createCustomerShippingAddress();
}
?>
