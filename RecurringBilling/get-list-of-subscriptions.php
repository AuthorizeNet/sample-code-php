<?php
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function getListOfSubscriptions()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $sorting = new AnetAPI\ARBGetSubscriptionListSortingType();
    $sorting->setOrderBy("id");
    $sorting->setOrderDescending(false);

    $paging = new AnetAPI\PagingType();
    $paging->setLimit("1000");
    $paging->setOffset("1");

    $request = new AnetAPI\ARBGetSubscriptionListRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSearchType("subscriptionInactive");
    $request->setSorting($sorting);
    $request->setPaging($paging);


    $controller = new AnetController\ARBGetSubscriptionListController($request);

    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo "SUCCESS: Subscription Details:" . "\n";
        foreach ($response->getSubscriptionDetails() as $subscriptionDetails) {
            echo "Subscription ID: " . $subscriptionDetails->getId() . "\n";
        }
        echo "Total Number In Results:" . $response->getTotalNumInResultSet() . "\n";
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    getListOfSubscriptions();
}
