<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function getSettledBatchList()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $request = new AnetAPI\GetSettledBatchListRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setIncludeStatistics(true);
    
    // both the first and last dates must be in the same time zone
    $firstSettlementDate=new DateTime("2015-08-25T06:00:00Z");
    // a date constructed from an ISO8601 format date string
    $request->setFirstSettlementDate($firstSettlementDate);
    
    // a date constructed manually
    $lastSettlementDate=new DateTime();
    $lastSettlementDate->setDate(2015,9,20);
    $lastSettlementDate->setTime(13,33,59);
    $lastSettlementDate->setTimezone(new DateTimeZone('UTC'));
    $request->setLastSettlementDate($lastSettlementDate);

    $controller = new AnetController\GetSettledBatchListController ($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
    {
        foreach($response->getBatchList() as $batch)
        {
  		echo "\n\n";
          echo "Batch ID: " . $batch->getBatchId() . "\n";
  		echo "Batch settled on (UTC): " . $batch->getSettlementTimeUTC()->format('r') . "\n";
  		echo "Batch settled on (Local): " . $batch->getSettlementTimeLocal()->format('D, d M Y H:i:s') . "\n";
  		echo "Batch settlement state: " . $batch->getSettlementState() . "\n";
  		echo "Batch market type: " . $batch->getMarketType() . "\n";
  		echo "Batch product: " . $batch->getProduct() . "\n";
  		foreach($batch->getStatistics() as $statistics)
  		{
  			echo "Account type: ".$statistics->getAccountType()."\n";
  			echo "Total charge amount: ".$statistics->getChargeAmount()."\n";
  			echo "Charge count: ".$statistics->getChargeCount()."\n";
  			echo "Refund amount: ".$statistics->getRefundAmount()."\n";
  			echo "Refund count: ".$statistics->getRefundCount()."\n";
  			echo "Void count: ".$statistics->getVoidCount()."\n";
  			echo "Decline count: ".$statistics->getDeclineCount()."\n";
  			echo "Error amount: ".$statistics->getErrorCount()."\n";
  		}
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
    getSettledBatchList();

?>
