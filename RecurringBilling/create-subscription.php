<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  date_default_timezone_set('America/Los_Angeles');
  // Common Set Up for API Credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName( "556KThWQ6vf2"); 
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");

  $refId = 'ref' . time();

  // Subscription Type Info
  $subscription = new AnetAPI\ARBSubscriptionType();
  $subscription->setName("Sample Subscription");

  $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
  $interval->setLength("1");
  $interval->setUnit("months");

  $paymentSchedule = new AnetAPI\PaymentScheduleType();
  $paymentSchedule->setInterval($interval);
  $paymentSchedule->setStartDate(new DateTime('2020-08-30'));
  $paymentSchedule->setTotalOccurrences("12");
  $paymentSchedule->setTrialOccurrences("1");

  $subscription->setPaymentSchedule($paymentSchedule);
  $subscription->setAmount("10.29");
  $subscription->setTrialAmount("0.00");
  
  $creditCard = new AnetAPI\CreditCardType();
  $creditCard->setCardNumber("4111111111111111");
  $creditCard->setExpirationDate("2020-12");

  $payment = new AnetAPI\PaymentType();
  $payment->setCreditCard($creditCard);

  $subscription->setPayment($payment);

  $billTo = new AnetAPI\NameAndAddressType();
  $billTo->setFirstName("John");
  $billTo->setLastName("Smith");

  $subscription->setBillTo($billTo);

  $request = new AnetAPI\ARBCreateSubscriptionRequest();
  $request->setmerchantAuthentication($merchantAuthentication);
  $request->setRefId($refId);
  $request->setSubscription($subscription);
  $controller = new AnetController\ARBCreateSubscriptionController($request);

  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo "SUCCESS: Subscription ID : " . $response->getSubscriptionId() . "\n";
   }
  else
  {
      echo "ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>
