<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  date_default_timezone_set('America/Los_Angeles');
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function createSubscription($intervalLength)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Subscription Type Info
    $subscription = new AnetAPI\ARBSubscriptionType();
    $subscription->setName("Sample Subscription");

    $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
    $interval->setLength($intervalLength);
    $interval->setUnit("days");

    $paymentSchedule = new AnetAPI\PaymentScheduleType();
    $paymentSchedule->setInterval($interval);
    $paymentSchedule->setStartDate(new DateTime('2020-08-30'));
    $paymentSchedule->setTotalOccurrences("12");
    $paymentSchedule->setTrialOccurrences("1");

    $subscription->setPaymentSchedule($paymentSchedule);
    $subscription->setAmount(rand(1,99999)/12.0*12);
    $subscription->setTrialAmount("0.00");
    
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4111111111111111");
    $creditCard->setExpirationDate("2038-12");

    $payment = new AnetAPI\PaymentType();
    $payment->setCreditCard($creditCard);
    $subscription->setPayment($payment);

    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber("1234354");        
    $order->setDescription("Description of the subscription"); 
    $subscription->setOrder($order); 
    
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
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
    createSubscription(23);

?>
