<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  echo "PayPal Get Details Transaction\n";
  
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("5KP3u95bQpv");
  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");
  
  $refId = 'ref' . time();

  //create a transaction of type get details
  $transactionRequestType = new AnetAPI\TransactionRequestType();
  $transactionRequestType->setTransactionType( "getDetailsTransaction"); 
  
  //replace following transaction ID with your transaction ID for which the details are required
  $transactionRequestType->setRefTransId("2241687090");

  //create a transaction request
  $request = new AnetAPI\CreateTransactionRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setRefId( $refId);
  $request->setTransactionRequest( $transactionRequestType);

  $controller = new AnetController\CreateTransactionController($request);

  //execute the api call to get transaction details
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

  if ($response != null)
  {
    $tresponse = $response->getTransactionResponse();
    
    if($tresponse != null)
    {
    	if ($tresponse->getResponseCode()=="1")
    	{
			//parse the shipping information from response
    		$shipping_response = $tresponse->getShipTo();
    		echo "Shipping address : " . $shipping_response->getAddress() . ", " . $shipping_response->getCity()
    		. ", " . $shipping_response->getState() . ", " . $shipping_response->getCountry() . "\n";
    	
    		echo "Payer ID : " . $tresponse->getSecureAcceptance()->getPayerID();
    	}
    	else
    	{
    		echo  "ERROR : " . $tresponse->getResponseCode() . "\n";
    	}
    }
    else
    {
    	echo  "No response returned";
    }    
  }
  else
  {
    echo  "No response returned";
  }

?>