<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

function createCustomerProfileWithAcceptNonce($email)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Create a Customer Profile Request
    //  1. (Optionally) create a Payment Profile
    //  2. (Optionally) create a Shipping Profile
    //  3. Create a Customer Profile (or specify an existing profile)
    //  4. Submit a CreateCustomerProfile Request
    //  5. Validate Profile ID returned

    // Set the payment data for the payment profile to a token obtained from Accept.js
    $op = new AnetAPI\OpaqueDataType();
    $op->setDataDescriptor("COMMON.ACCEPT.INAPP.PAYMENT");
    $op->setDataValue("119eyJjb2RlIjoiNTBfMl8wNjAwMDUyN0JEODE4RjQxOUEyRjhGQkIxMkY0MzdGQjAxQUIwRTY2NjhFNEFCN0VENzE4NTUwMjlGRUU0M0JFMENERUIwQzM2M0ExOUEwMDAzNzlGRDNFMjBCODJEMDFCQjkyNEJDIiwidG9rZW4iOiI5NDkwMjMyMTAyOTQwOTk5NDA0NjAzIiwidiI6IjEuMSJ9");
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setOpaqueData($op);


    // Create the Bill To info for new payment type
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName("Ellen");
    $billto->setLastName("Johnson");
    $billto->setCompany("Souveniropolis");
    $billto->setAddress("14 Main Street");
    $billto->setCity("Pecan Springs");
    $billto->setState("TX");
    $billto->setZip("44628");
    $billto->setCountry("USA");
    $billto->setPhoneNumber($phoneNumber);
    $billto->setfaxNumber("999-999-9999");

    // Create a new Customer Payment Profile object
    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentOne);
    $paymentprofile->setDefaultPaymentProfile(true);

    $paymentprofiles[] = $paymentprofile;

    // Create a new CustomerProfileType and add the payment profile object
    $customerprofile = new AnetAPI\CustomerProfileType();
    $customerprofile->setDescription("Customer Test PHP Accept Test");

    $customerprofile->setMerchantCustomerId("M_".$email);
    $customerprofile->setEmail($email);
    $customerprofile->setPaymentProfiles($paymentprofiles);

    // Assemble the complete transaction request
    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setProfile($customerprofile);

    // Create the controller and get the response
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
        echo "Succesfully created customer profile : " . $response->getCustomerProfileId() . "\n";
        $paymentProfiles = $response->getCustomerPaymentProfileIdList();
        echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    createCustomerProfileWithAcceptNonce("test123@test.com");
}
?>
