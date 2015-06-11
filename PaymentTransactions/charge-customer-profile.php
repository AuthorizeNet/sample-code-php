<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
  $refId = 'ref' . time();

  // Create the payment data for a credit card
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "4111111111111111" );
  $creditCard->setExpirationDate( "2038-12");
  $paymentOne = new AnetAPI\PaymentType();
  $paymentOne->setCreditCard($creditCard);

  // Bill To
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Ellen");
  $billto->setLastName("Johnson");
  $billto->setCompany("Souveniropolis");
  $billto->setAddress("14 Main Street");
  $billto->setCity("Pecan Springs");
  $billto->setState("TX");
  $billto->setZip("44628");
  $billto->setCountry("USA");


// Create a Customer Profile Request
 //  1. create a Customer Payment Profile
 //  2. create a Customer Profile
 //  3. Submit a CreateCustomerProfile Request
 //  4. Validate the profile id returned

  $paymentprofile = new AnetAPI\CustomerPaymentProfileType();

  $paymentprofile->setCustomerType('individual');
  $paymentprofile->setBillTo($billto);
  $paymentprofile->setPayment($paymentOne);
  $paymentprofiles[] = $paymentprofile;

  $customerprofile = new AnetAPI\CustomerProfileType();
  $customerprofile->setDescription("Customer 2 Test PHP");
  $merchantCustomerId = time().rand(1,150);
  $customerprofile->setMerchantCustomerId($merchantCustomerId);
  $customerprofile->setEmail("test2@domain.com");
  $customerprofile->setPaymentProfiles($paymentprofiles);

  $request = new AnetAPI\CreateCustomerProfileRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setRefId( $refId);
  $request->setProfile($customerprofile);
  $controller = new AnetController\CreateCustomerProfileController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
    echo "CreateCustomerProfileRequest SUCCESS: PROFILE ID : " . $response->getCustomerProfileId() . "\n";
    $customerProfileId = $response->getCustomerProfileId();
  }
  else
  {
    echo "CreateCustomerProfileRequest ERROR :  Invalid response\n";
  }
  // Create a new order
  $order = new AnetAPI\OrderType();
  $order->setInvoiceNumber("102");
  $order->setDescription("Tennis Shirts");

  // Add line items
  $lineitem = new AnetAPI\LineItemType();
  $lineitem->setItemId("Shirts");
  $lineitem->setName("item2");
  $lineitem->setDescription("tennis shirt");
  $lineitem->setQuantity("1");
  $lineitem->setUnitPrice(22.89);
  $lineitem->setTaxable("Y");

  // Add new tax info
  $tax =  new AnetAPI\ExtendedAmountType();
  $tax->setName("level 2 tax name");
  $tax->setAmount(6.50);
  $tax->setDescription("level 2 tax");

  // New Ship To
  $shipto = new AnetAPI\CustomerAddressType();
  $shipto->setFirstName("Mary");
  $shipto->setLastName("Smith");
  $shipto->setCompany("Tennis Shirts Are Us");
  $shipto->setAddress("588 Willis Court");
  $shipto->setCity("Pecan Springs");
  $shipto->setState("TX");
  $shipto->setZip("44628");
  $shipto->setCountry("USA");

  // Set a new bill to address
  $billto = new AnetAPI\CustomerAddressType();
  $billto->setFirstName("Mary");
  $billto->setLastName("Smith");
  $billto->setCompany("Tennis Shirts Are Us");
  $billto->setAddress("588 Willis Court");
  $billto->setCity("Pecan Springs");
  $billto->setState("TX");
  $billto->setZip("44628");
  $billto->setCountry("USA");

  // Create additional payment data and add a new credit card
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber( "4007000000027" );
  $creditCard->setExpirationDate( "2038-12");
  $paymentTwo = new AnetAPI\PaymentType();
  $paymentTwo->setCreditCard($creditCard);

  $request = new AnetAPI\CreateCustomerPaymentProfileRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setRefId( $refId);
  $request->setCustomerProfileId($customerProfileId);

  $paymentprofile2 = new AnetAPI\CustomerPaymentProfileType();
  $paymentprofile2->setCustomerType('business');
  $paymentprofile2->setBillTo($billto);
  $paymentprofile2->setPayment($paymentTwo);
  $paymentprofiles2[] = $paymentprofile2;

  $request->setPaymentProfile($paymentprofile2);
  $controller = new AnetController\CreateCustomerPaymentProfileController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
     echo "CreateCustomerPaymentProfileRequest SUCCESS: PROFILE ID : " . $response->getCustomerPaymentProfileId() . "\n";
     $customerProfileId =  $response->getCustomerPaymentProfileId();
  }
  else
  {
    echo "CreateCustomerPaymentProfileRequest ERROR :  Invalid response\n";
  }

  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
  $transactionRequestType->setAmount(100.50);
  $transactionRequestType->setPayment($paymentprofile2->getPayment());
  $transactionRequestType->setOrder($order);
  $transactionRequestType->addToLineItems($lineitem);
  $transactionRequestType->setTax($tax);
  //$transactionRequestType->setPoNumber($ponumber);
  //$transactionRequestType->setCustomer($customer);
  $transactionRequestType->setBillTo($billto);
  $transactionRequestType->setShipTo($shipto);
  $request = new AnetAPI\CreateTransactionRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setRefId( $refId);
  $request->setTransactionRequest( $transactionRequestType);
  $controller = new AnetController\CreateTransactionController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  if ($response != null)
  {
    $tresponse = $response->getTransactionResponse();
    if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )   
    {
      echo  "Charge Customer Profile APPROVED  :" . "\n";
      echo " Charge Customer Profile AUTH CODE : " . $tresponse->getAuthCode() . "\n";
      echo " Charge Customer Profile TRANS ID  : " . $tresponse->getTransId() . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
    {
      echo  "ERROR" . "\n";
    }
    elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
    {
        echo  "ERROR: HELD FOR REVIEW:"  . "\n";
    }
  }
  else
  {
    echo "no response returned";
  }
?>
