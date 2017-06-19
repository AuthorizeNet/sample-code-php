<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function getTransactionList()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the request's refId
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
  	  	echo "No Transaction to display in this Batch.";
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
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
    getTransactionList();
?>