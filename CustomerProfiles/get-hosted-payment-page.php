<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function getHostedPaymentPage($customerprofileid = "123212")
  {
	  // Common setup for API credentials
      $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
      $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
      $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
      $refId = 'ref' . time();
    
      // Create the payment data for a credit card
      $creditCard = new AnetAPI\CreditCardType();
      $creditCard->setCardNumber("4111111111111111");
      $creditCard->setExpirationDate("1226");
      $creditCard->setCardCode("123");
      $paymentOne = new AnetAPI\PaymentType();
      $paymentOne->setCreditCard($creditCard);

      $order = new AnetAPI\OrderType();
      $order->setDescription("New Item");

      //create a transaction
      $transactionRequestType = new AnetAPI\TransactionRequestType();
      $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
      $transactionRequestType->setAmount("12.23");

	  // Use an existing payment profile ID for this Merchant name and Transaction key
	  
	  $setting = new AnetAPI\SettingType();
	  $setting->setSettingName("hostedPaymentButtonOptions");
	  $setting->setSettingValue("{\"text\": \"Pay\"}");
	  $setting2 = new AnetAPI\SettingType();
	  $setting2->setSettingName("hostedPaymentOrderOptions");
	  $setting2->setSettingValue("{\"show\": false}");
	  
	  //$alist = new AnetAPI\ArrayOfSettingType();
	  //$alist->addToSetting($setting);
	  
	  $request = new AnetAPI\GetHostedPaymentPageRequest();
	  $request->setMerchantAuthentication($merchantAuthentication);
	  $request->setTransactionRequest($transactionRequestType);

	  $request->addToHostedPaymentSettings($setting);
	  $request->addToHostedPaymentSettings($setting2);
	  
	  $controller = new AnetController\GetHostedPaymentPageController($request);
	  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
	  
	  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
	  {
		  echo $response->getToken()."\n";
	   }
	  else
	  {
		  echo "ERROR :  Failed to get hosted profile page\n";
		  $errorMessages = $response->getMessages()->getMessage();
		  echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
	  }
	  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      getHostedPaymentPage();    
?>
