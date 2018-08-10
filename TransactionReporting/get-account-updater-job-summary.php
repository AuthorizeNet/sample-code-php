<?php
    require 'vendor/autoload.php';
    require_once 'constants/SampleCodeConstants.php';
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

function getAccountUpdaterJobSummary()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the request's refId
    $refId = 'ref' . time();

    // Set a valid month for the request
    $month = "2017-07";

    // Build tbe request object
    $request = new AnetAPI\GetAUJobSummaryRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setMonth($month);

    $controller = new AnetController\GetAUJobSummaryController($request);

    // Get the response from the service (errors contained if any)
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo "SUCCESS: Get Account Updater Summary for Month : " . $month  . "\n\n";
        if ($response->getAuSummary() == null) {
            echo "No Account Updater summary for this month.\n";
            return ;
        }

        // Displaying the summary of each response in the list
        foreach ($response->getAuSummary() as $result) {
            echo "		Reason Code        : " . $result->getAuReasonCode() . "\n";
            echo "		Reason Description : " . $result->getReasonDescription() . "\n";
            echo "		# of Profiles updated for this reason : " . $result->getProfileCount() . "\n";
        }
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    getAccountUpdaterJobSummary();
}
