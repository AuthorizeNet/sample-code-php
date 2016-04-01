<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

  function getUnsettledTransactionList() {
    // Common Set Up for API Credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName("4gf2H89Xc");
    $merchantAuthentication->setTransactionKey("3T32UcwyCL555U6J");

    $refId = 'ref' . time();


    $request = new AnetAPI\GetUnsettledTransactionListRequest();
    $request->setMerchantAuthentication($merchantAuthentication);


    $controller = new AnetController\GetUnsettledTransactionListController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
    {
		if(null != $response->getTransactions())
		{
			foreach($response->getTransactions() as $tx)
			{
			  echo "SUCCESS: TransactionID: " . $tx->getTransId() . "\n";
			}
        }
		else{
			echo "No unsettled transactions for the merchant." . "\n";
		}
    }
    else
    {
        echo "ERROR :  Invalid response\n";
        echo "Response : " . $response->getMessages()->getMessage()[0]->getCode() . "  " .$response->getMessages()->getMessage()[0]->getText() . "\n";
        
    }

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
    getUnsettledTransactionList();

?>
