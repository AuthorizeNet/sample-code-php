<?php
    require 'vendor/autoload.php';
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;
  
define("AUTHORIZENET_LOG_FILE", "phplog");

function getAccountUpdaterJobDetails()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the request's refId
    $refId = 'ref' . time();

    // Set a valid month (and other parameters) for the request
    $month = "2017-07";
    $modifedTypeFilter = "all";
    $paging = new AnetAPI\PagingType;
    $paging->setLimit("1000");
    $paging->setOffset("1");

    // Build tbe request object
    $request = new AnetAPI\GetAUJobDetailsRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setMonth($month);
    $request->setModifiedTypeFilter($modifedTypeFilter);
    $request->setPaging($paging);

    $controller = new AnetController\GetAUJobDetailsController($request);

    // Retrieving details for the given month and parameters
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo "SUCCESS: Get Account Updater Details for Month : " . $month  . "\n\n";
        if ($response->getAuDetails() == null) {
            echo "No Account Updater Details for this month.\n";
            return ;
        } else {
            $details = new AnetAPI\ListOfAUDetailsType;
            $details = $response->getAuDetails();
            if (($details->getAuUpdate() == null) && ($details->getAuDelete() == null)) {
                echo "No Account Updater Details for this month.\n";
                return ;
            }
        }

        // Displaying the details of each response in the list
        echo "Total Num in Result Set : " . $response->getTotalNumInResultSet() . "\n\n";
        $details = new AnetAPI\ListOfAUDetailsType;
        $details = $response->getAuDetails();
        echo "Updates:\n";
        foreach ($details->getAuUpdate() as $update) {
            echo "		Profile ID / Payment Profile ID	: " . $update->getCustomerProfileID() . " / " . $update->getCustomerPaymentProfileID() . "\n";
            echo "		Update Time (UTC) : " . $update->getUpdateTimeUTC() . "\n";
            echo "		Reason Code	: " . $update->getAuReasonCode() . "\n";
            echo "		Reason Description : " . $update->getReasonDescription() . "\n";
            echo "\n";
        }
        echo "\nDeletes:\n";
        foreach ($details->getAuDelete() as $delete) {
            echo "		Profile ID / Payment Profile ID	: " . $delete->getCustomerProfileID() . " / " . $delete->getCustomerPaymentProfileID() . "\n";
            echo "		Update Time (UTC) : " . $delete->getUpdateTimeUTC() . "\n";
            echo "		Reason Code	: " . $delete->getAuReasonCode() . "\n";
            echo "		Reason Description : " . $delete->getReasonDescription() . "\n";
            echo "\n";
        }
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    getAccountUpdaterJobDetails();
}
