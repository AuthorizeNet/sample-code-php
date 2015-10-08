<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  // Common Set Up for API Credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName( "5KP3u95bQpv"); 
  $merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");

  $refId = 'ref' . time();

  //Setting a valid batch Id for the Merchant
  $batchId = "4606008";
  $request = new AnetAPI\GetTransactionListRequest();
  $request->setMerchantAuthentication($merchantAuthentication);
  $request->setBatchId($batchId);

  $controller = new AnetController\GetTransactionListController($request);

  //Retrieving transaction list for the given Batch Id
  $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

  if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
  {
  		echo "SUCCESS: Get Transaction List for BatchID : " . $batchId  . "\n\n";
	  if ($response->getTransactions() == null) {
	  	echo "No Transaction to dispaly in this Batch.";
	  	return ;
	  }
	  //Displaying the details of each transaction in the list
	  foreach ($response->getTransactions() as $transaction) {
	  	echo "		->Transaction Id	: " . $transaction->getTransId() . "\n"; 
	  	echo "		Submitted on (Local)	: " . date_format($transaction->getSubmitTimeLocal(), 'Y-m-d H:i:s') . "\n";
	  	echo "		Status			: " . $transaction->getTransactionStatus() . "\n";
	  	echo "		Settle amount		: " . number_format($transaction->getSettleAmount(), 2, '.', '') . "\n";
	  }
   }
  else
  {
      echo "ERROR :  Invalid response\n";
      echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
      
  }
  ?>