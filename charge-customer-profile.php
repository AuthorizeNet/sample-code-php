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
 //  1. create a Payment Profile
 //  2. create a Customer Profile
 //  3. Submit a CreateCustomerProfile Request
 //  4. Validate Profiiel ID returned

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
      echo "Create Profile SUCCESS: PROFILE ID : " . $response->getCustomerProfileId() . "\n";
      $profileId = $response->getCustomerProfileId(); 
   }
  else
  {
      echo "Create Profile ERROR :  Invalid response\n";
  }

  // Order info
  $order = new AnetAPI\OrderType();
  $order->setInvoiceNumber("102");
  $order->setDescription("Tennis Shirts");

  // Line Item Info
  $lineitem = new AnetAPI\LineItemType();
  $lineitem->setItemId("Shirts");
  $lineitem->setName("item2");
  $lineitem->setDescription("tennis shirt");
  $lineitem->setQuantity("1");
  $lineitem->setUnitPrice(22.89);
  $lineitem->setTaxable("Y");

  // Tax info 
  $tax =  new AnetAPI\ExtendedAmountType();
  $tax->setName("level 2 tax name");
  $tax->setAmount(6.50);
  $tax->setDescription("level 2 tax");

  // Ship To
  $shipto = new AnetAPI\CustomerAddressType();
  $shipto->setFirstName("Mary");
  $shipto->setLastName("Smith");
  $shipto->setCompany("Tenis Shirts Are Us");
  $shipto->setAddress("588 Willis Court");
  $shipto->setCity("Pecan Springs");
  $shipto->setState("TX");
  $shipto->setZip("44628");
  $shipto->setCountry("USA");

  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
  $transactionRequestType->setAmount(100.50);
  $paymentprofile = $customerprofile->getPaymentProfiles($paymentprofiles);
  $transactionRequestType->setProfile($paymentprofile[0]);
  //$transactionRequestType->setPayment($paymentBank);
  $transactionRequestType->setOrder($order);
  $transactionRequestType->addToLineItems($lineitem);
  $transactionRequestType->setTax($tax);
  //$transactionRequestType->setPoNumber($ponumber);
  //$transactionRequestType->setCustomer($customer);
  //$transactionRequestType->setBillTo($billto);
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
      echo  "APPROVED  :" . "\n";
      echo " AUTH CODE : " . $tresponse->getAuthCode() . "\n";
      echo " TRANS ID  : " . $tresponse->getTransId() . "\n";
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
