<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");

  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("3Zw3Ru9nx");
  $merchantAuthentication->setTransactionKey("7Tbj6T3a9cPq4A5d");

  // An existing payment profile ID for this Merchant name and Transaction key
  //
  $customerprofileid = "37680862";
  $customerpaymentprofileid = "34249159";
  
  $setting = new AnetAPI\SettingType();
  $setting->setSettingName("hostedProfileReturnUrl");
  $setting->setSettingValue("https://returnurl.com/return/");
  
  //$alist = new AnetAPI\ArrayOfSettingType();
  //$alist->addToSetting($setting);
  
  $request = new AnetAPI\GetHostedProfilePageRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setCustomerProfileId($customerprofileid);
  $request->addToHostedProfileSettings($setting);
  
  $controller = new AnetController\GetHostedProfilePageController($request);
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  
  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
  {
      echo $response->getMessages()->getMessage()[0]->getCode()."\n";
	  echo $response->getMessages()->getMessage()[0]->getText()."\n";
	  
	  echo $response->getToken()."\n";
   }
  else
  {
      echo "ERROR :  Failed to get hosted profile page\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
  }
?>