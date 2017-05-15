<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function createCustomerPaymentProfile($existingcustomerprofileid, $phoneNumber)
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

    // Set credit card information for payment profile
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4242424242424242");
    $creditCard->setExpirationDate("2038-12");
    $creditCard->setCardCode("142");
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);

    // Create the Bill To info for new payment type
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName("Ellen".$phoneNumber);
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
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofile->setDefaultPaymentProfile(true);

    $paymentprofiles[] = $paymentprofile;

    // Assemble the complete transaction request
    $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
    $paymentprofilerequest->setMerchantAuthentication($merchantAuthentication);

    // Add an existing profile id to the request
    $paymentprofilerequest->setCustomerProfileId($existingcustomerprofileid);
    $paymentprofilerequest->setPaymentProfile($paymentprofile);
    $paymentprofilerequest->setValidationMode("liveMode");

    // Create the controller and get the response
    $controller = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
        echo "Create Customer Payment Profile SUCCESS: " . $response->getCustomerPaymentProfileId() . "\n";
    } else {
        echo "Create Customer Payment Profile: ERROR Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";

    }
    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
      createCustomerPaymentProfile("1807545561", "000-000-0009");
}
?>
