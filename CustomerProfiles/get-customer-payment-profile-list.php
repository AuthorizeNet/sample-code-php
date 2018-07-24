<?php

require 'vendor/autoload.php';
require_once 'constants/SampleCodeConstants.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");
  
function getCustomerPaymentProfileList()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();
        
        //Setting the paging
        $paging = new AnetAPI\PagingType();
        $paging->setLimit("1000");
        $paging->setOffset("1");
        
        //Setting the sorting
        $sorting = new AnetAPI\CustomerPaymentProfileSortingType();
        $sorting->setOrderBy("id");
        $sorting->setOrderDescending(false);
        
        //Creating the request with the required parameters
        $request = new AnetAPI\GetCustomerPaymentProfileListRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setPaging($paging);
        $request->setSorting($sorting);
        $request->setSearchType("cardsExpiringInMonth");
        $request->setMonth("2020-12");
    
        // Controller
        $controller = new AnetController\GetCustomerPaymentProfileListController($request);
        // Getting the response
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        
    if (($response != null)) {
        if ($response->getMessages()->getResultCode() == "Ok") {
            // Success
            echo "GetCustomerPaymentProfileList SUCCESS: " . "\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
            echo "Total number of Results in the result set" . $response->getTotalNumInResultSet() . "\n";
            // Displaying the customer payment profile list
            foreach ($response->getPaymentProfiles() as $paymentProfile) {
                echo "\nCustomer Profile ID: " . $paymentProfile->getCustomerProfileId() . "\n";
                echo "Payment profile ID: " . $paymentProfile->getCustomerPaymentProfileId() . "\n";
                echo "Credit Card Number: " . $paymentProfile->getPayment()->getCreditCard()->getCardNumber() . "\n";
                if ($paymentProfile->getBillTo() != null) {
                    echo "First Name in Billing Address: " . $paymentProfile->getBillTo()->getFirstName() . "\n";
                }
            }
        } else {
            // Error
            echo "GetCustomerPaymentProfileList ERROR :  Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }
    } else {
        // Failed to get the response
        echo "NULL Response Error";
    }
    return $response;
}

if (!defined('DONT_RUN_SAMPLES')) {
    getCustomerPaymentProfileList();
}
