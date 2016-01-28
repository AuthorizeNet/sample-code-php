<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  function getHostedProfilePage($customerprofileid = "37680862")
  {
	  // Common setup for API credentials
	  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	  $merchantAuthentication->setName("5KP3u95bQpv");
	  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");

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
  if(!defined(DONT_RUN_SAMPLES))
      getHostedProfilePage();    
?>