<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

  function updateSubscription($subscriptionId) {

    // Common Set Up for API Credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    $refId = 'ref' . time();

    $subscription = new AnetAPI\ARBSubscriptionType();

    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4111111111111111");
    $creditCard->setExpirationDate("2020-12");

    $payment = new AnetAPI\PaymentType();
    $payment->setCreditCard($creditCard);

    //set profile information
    $profile = new AnetAPI\CustomerProfileIdType();
    $profile->setCustomerProfileId("121212");
    $profile->setCustomerPaymentProfileId("131313");
    $profile->setCustomerAddressId("141414");

    $subscription->setPayment($payment);

    //set customer profile information
    //$subscription->setProfile($profile);
    
    $request = new AnetAPI\ARBUpdateSubscriptionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSubscriptionId($subscriptionId);
    $request->setSubscription($subscription);

    $controller = new AnetController\ARBUpdateSubscriptionController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        echo "SUCCESS, Response Code: " . $response->getMessages()->getMessage()[0]->getCode() . " Response Text: " .$response->getMessages()->getMessage()[0]->getText() . "\n";
     }
    else
    {
        echo "ERROR :  Invalid response\n";
        echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";   
    }

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
      updateSubscription("3056948");
?>
