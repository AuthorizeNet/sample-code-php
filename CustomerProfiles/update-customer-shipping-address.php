<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
function updateCustomerShippingAddress($customerprofileid, $customeraddressid)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

  // An existing customer profile id for this merchant name and transaction key
  $existingcustomerprofileid = $customerprofileid;

  // Create the customer shipping address
  $customershippingaddress = new AnetAPI\CustomerAddressExType();
  $customershippingaddress->setFirstName("Jane");
  $customershippingaddress->setLastName("White");
  $customershippingaddress->setCompany("Addresses R Us");
  $customershippingaddress->setAddress("14 North Spring Street Suite 240");
  $customershippingaddress->setCity("Toms River");
  $customershippingaddress->setState("NJ");
  $customershippingaddress->setZip("08753");
  $customershippingaddress->setCountry("USA");
  $customershippingaddress->setPhoneNumber("201-000-0000");
  $customershippingaddress->setFaxNumber("973-999-9999");
  $customershippingaddress->setCustomerAddressId($customeraddressid);

  // Update an existing customer shipping address for an existing customer profile
  $request = new AnetAPI\UpdateCustomerShippingAddressRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId($existingcustomerprofileid);
  $request->setAddress($customershippingaddress);
  $controller = new AnetController\UpdateCustomerShippingAddressController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "Update Customer Shipping Address SUCCESS.\n";
   }
  else
  {
      echo "Update Customer Shipping Address  ERROR :  Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
  }
  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      updateCustomerShippingAddress( "36152127","36976566");
?>
