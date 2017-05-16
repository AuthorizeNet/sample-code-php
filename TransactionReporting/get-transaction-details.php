<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function getTransactionDetails($transactionId)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $request = new AnetAPI\GetTransactionDetailsRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setTransId($transactionId);

    $controller = new AnetController\GetTransactionDetailsController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
    {
        echo "SUCCESS: Transaction Status:" . $response->getTransaction()->getTransactionStatus() . "\n";
        echo "                Auth Amount:" . $response->getTransaction()->getAuthAmount() . "\n";
        echo "                   Trans ID:" . $response->getTransaction()->getTransId() . "\n";
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
    getTransactionDetails("2238968786");
?>