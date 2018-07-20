<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function getBatchStatistics($batchId = "7927817") //only shows results for batches not older than 6 months
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
    
    // Creating a request 
    $request = new AnetAPI\GetBatchStatisticsRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setBatchId($batchId);
      
    //Creating the controller
    $controller = new AnetController\GetBatchStatisticsController($request);

    //Retrieving response
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
    {
    		echo "SUCCESS: Successfully got the list of subscriptions : \n\n";
  		echo "Batch ID : " . $response->getBatch()->getBatchId() . "\n";
  		echo "Settlement Time : " . date_format($response->getBatch()->getSettlementTimeUTC(),"Y/m/d H:i:s") . "\n";
  		echo "Settlement state : " . $response->getBatch()->getSettlementState() . "\n";
  		echo "Payment Method : " . $response->getBatch()->getPaymentMethod() . "\n";
  		echo "Statistic Details:";
  		//Displaying the details of each transaction in the list
  		foreach ($response->getBatch()->getStatistics() as $statistics) 
  		{
  			echo "		Account Type	: " . $statistics->getAccountType() . "\n"; 
  			echo "		Charge Amount	: " . $statistics->getChargeAmount() . "\n";
  			echo "		Charge Count	: " . $statistics->getChargeCount() . "\n";
  			echo "		Refund Amount	: " . $statistics->getRefundAmount() . "\n";
  			echo "		Refund Count	: " . $statistics->getRefundCount() . "\n";
  			echo "		Void Count		: " . $statistics->getRefundCount() . "\n";
  			echo "		Decline Count	: " . $statistics->getRefundCount() . "\n";
  			echo "		Error Count		: " . $statistics->getRefundCount() . "\n";
  		}
    }
    else
    {
        echo "ERROR :  Failed to get the batch statistics\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
    getBatchStatistics();

?>
