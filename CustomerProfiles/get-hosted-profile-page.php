<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  function getHostedProfilePage($customerprofileid = "123212")
  {
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
      $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
	  // Use an existing payment profile ID for this Merchant name and Transaction key
	  
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
	  return $response;
  }
  if(!defined('DONT_RUN_SAMPLES'))
      getHostedProfilePage();    
?>