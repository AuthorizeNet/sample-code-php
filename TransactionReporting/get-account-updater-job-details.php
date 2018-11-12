<?php
    require 'C:/php/Anet/new/sdk-php-master/vendor/autoload.php';
    require_once 'C:/php/Anet/new/sdk-php-master/sample-code-php/constants/SampleCodeConstants.php';
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;
  
define("AUTHORIZENET_LOG_FILE", "phplog");

function getAccountUpdaterJobDetails()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    

    // Set the request's refId
    $refId = '123456';

    // Set a valid month (and other parameters) for the request
    $month = "2018-08";
    $modifedTypeFilter = "all";
    $paging = new AnetAPI\PagingType;
    $paging->setLimit("1000");
    $paging->setOffset("2");

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
            
        } else {
            $details = new AnetAPI\ListOfAUDetailsType;
            $details = $response->getAuDetails();
            if (($details->getAuUpdate() == null) && ($details->getAuDelete() == null)) {
                echo "No Account Updater Details for this month.\n";
                
            }
        }

        // Displaying the details of each response in the list
        echo "Total Num in Result Set : " . $response->getTotalNumInResultSet() . "\n\n";
        $details = new AnetAPI\ListOfAUDetailsType;
        $details = $response->getAuDetails();
        echo "Updates:\n";
        foreach ($details->getAuUpdate() as $update) 
        {            
                
               
            echo "Profile ID : " . $update->getCustomerProfileID() . "\n";
            echo "Payment Profile ID   : " . $update->getCustomerPaymentProfileID() . "\n";
            echo "Update Time (UTC) : " . $update->getUpdateTimeUTC() . "\n";
            echo "Reason Code	: " . $update->getAuReasonCode() . "\n";
            echo "Reason Description : " . $update->getReasonDescription() . "\n";
            echo "\n";

            echo "\n";


            if ($update->getNewCreditCard()->getCardNumber() != null)
            {
                echo "Fetching New Card Details"."\n";
                // Fetching New Card Details
                echo "Card Number: ". $update->getNewCreditCard()->getCardNumber()."\n";
                echo "New Expiration Date: ". $update->getNewCreditCard()->getExpirationDate()."\n";
                echo "New Card Type: ". $update->getNewCreditCard()->getCardType()."\n";
                

            }

            if ($update->getOldCreditCard()->getCardNumber() != null)
            {
                echo "\n";
                echo "Fetching Old Card Details";
                echo "\n";
                // Fetching Old Card Details
                echo "Old Card Number: ". $update->getOldCreditCard()->getCardNumber()."\n";
                echo "Old Expiration Date: ".$update->getOldCreditCard()->getExpirationDate()."\n";
                echo "Old Card Type: ". $update->getOldCreditCard()->getCardType()."\n";
                echo "\n";
                
                
            } 
                if(!empty($update->getSubscriptionIdList()))
                {
                    echo "Subscription Id : ".implode("",$update->getSubscriptionIdList()). "\n"; 
                    echo "\n";
                }
                
        }
                echo "**** AU Update End ****"."\n";
                echo "\n";
                echo "\n";
                echo "\nDeletes:\n";
        foreach ($details->getAuDelete() as $delete) 
        {

            
            echo "Profile ID	: " . $delete->getCustomerProfileID() . "\n";

            echo "Payment Profile ID   : " . $delete->getCustomerPaymentProfileID() . "\n";
            echo "Update Time (UTC) : " . $delete->getUpdateTimeUTC() . "\n";
            echo "Reason Code	: " . $delete->getAuReasonCode() . "\n";
            echo "Reason Description : " . $delete->getReasonDescription() . "\n";
            echo "\n";



            if($delete->getCreditCard()->getCardNumber() != null)
            {
                echo "Fetching Card Details"."\n";
                // Fetching New Card Details
                echo "Card Number: ". $delete->getCreditCard()->getCardNumber()."\n";
                echo "Expiration Date: ". $delete->getCreditCard()->getExpirationDate()."\n";
                echo "Card Type: ". $delete->getCreditCard()->getCardType()."\n";
            }

            if(!empty($delete->getSubscriptionIdList()))
            {
                echo "Subscription Id :".implode("",$delete->getSubscriptionIdList());
                echo "\n";
            }
            echo "\n";
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

if (!defined('DONT_RUN_SAMPLES')) {
    getAccountUpdaterJobDetails();
}
