<?php
define("DONT_RUN_SAMPLES", "true");
define("SAMPLE_CODE_NAME_HEADING", "SampleCodeName");
require 'vendor/autoload.php';
require_once 'constants/SampleCodeConstants.php';

if ($_SERVER['argc'] != 3) {
    die('\n Usage: phpunit test-runner.php <SampleCodeDirectoryPath>');
}
$dirPath = $_SERVER['argv'][2];
echo $dirPath;
if (substr($dirPath, -1) != "/") {
    $dirPath = $dirPath."/";
}

$directories = array(
            'CustomerProfiles/',
            'RecurringBilling/',
            'PayPalExpressCheckout/',
            'PaymentTransactions/',
            'TransactionReporting/',
            'MobileInappTransactions/',
            'VisaCheckout/',
			'AcceptSuite/'
);

$errorlevel=error_reporting();
error_reporting($errorlevel & ~E_NOTICE); //turn off constant re-defined and other notices
foreach ($directories as $directory) {
    foreach (glob($dirPath.$directory . "*.php") as $sample) {
        require_once $sample;
        //echo $sample;
    }
}

error_reporting($errorlevel);
class TestRunner extends PHPUnit\Framework\TestCase
{
    public static $apiLoginId = "5KP3u95bQpv";
    public static $transactionKey = "346HZ32z3fP4hTG2";
    public static $transactionID = "2245440957";
    public static $payerID = "LM6NCLZ5RAKBY";
    //random amount for transactions/subscriptions
    public static function getAmount()
    {
        return 12 + (rand(1, 9000)/10);
    }
    //random email for a new customer profile
    public static function getEmail()
    {
        return rand(0, 10000) . "@test" .rand(0, 10000) .".com";
    }
    //random phonenumber for customer payment profile
    public static function getPhoneNumber()
    {
        return self::toPhoneNumber(rand(0, 9999999999));
    }
    public static function getDay()
    {
        return rand(7, 365);
    }
    public function testAllSampleCodes()
    {
        $runTests = 0;

        $file = $GLOBALS["dirPath"]."SampleCodeList.txt";
        $data = file($file) or die('\nCould not read SampleCodeList.');
        foreach ($data as $line) {
            $line=trim($line);
            if (trim($line)) {
                list($apiName, $isDependent, $shouldRun)=explode(",", $line);
                $apiName = trim($apiName);
                echo "\nApi name: " . $apiName."\n";
                fwrite(STDOUT, print_r("\nStarting Test: " . $apiName."\n", TRUE));
            }
            if ($apiName && (false === strpos($apiName, SAMPLE_CODE_NAME_HEADING))) {
                echo "should run:".$shouldRun."\n";
                if ("0" === $shouldRun) {
                    echo ":Skipping " . $apiName . "\n";
                } else {
                    //Try the request twice
                    for ($i=0; $i<=1; $i++) {
                        if ("0" === $isDependent) {
                            echo "not dependent\n";
                            $sampleMethodName = $apiName;
                            $sampleMethodName[0] = strtolower($sampleMethodName[0]);
                        } else {
                            $sampleMethodName = "TestRunner::run" . $apiName;
                            echo " is dependent\n";
                        }
                            
                        //request the api
                        echo "Running sample: " . $sampleMethodName . "\n";
                        
                        fwrite(STDOUT, print_r($apiName . "Start  Time: " . date("H:i:s."). gettimeofday()['usec'] . "\n", TRUE));
                        $response = call_user_func($sampleMethodName);
                        fwrite(STDOUT, print_r($apiName . "Finish Time: " . date("H:i:s."). gettimeofday()['usec'] . "\n", TRUE));

                        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
                            break;
                        }
                    }

                    //response must be successful
                    $this->assertNotNull($response);
                    $this->assertEquals($response->getMessages()->getResultCode(), "Ok");
                    echo $sampleMethodName . " - OK \n";
                    $runTests++;
                }
            }
        }
        echo "Number of sample codes run: ". $runTests;
    }

    private static function toPhoneNumber($num)
    {
        $zeroPadded = sprintf("%10d", $num);
        return substr($zeroPadded, 0, 3)."-".substr($zeroPadded, 3, 3)."-".substr(6, 4);
    }

    public static function runAuthorizeCreditCard()
    {
        return authorizeCreditCard(self::getAmount());
    }

    public static function runCaptureFundsAuthorizedThroughAnotherChannel()
    {
        return captureFundsAuthorizedThroughAnotherChannel(self::getAmount());
    }
    
    public static function runDebitBankAccount()
    {
        return debitBankAccount(self::getAmount()%98+1); //cannot debit more than 100
    }
    
    public static function runChargeTokenizedCreditCard()
    {
        return chargeTokenizedCreditCard(self::getAmount());
    }
    
    public static function runCreateAnAcceptPaymentTransaction()
    {
        return createAnAcceptPaymentTransaction(self::getAmount());
    }

    public static function runChargeCreditCard()
    {
        return chargeCreditCard(self::getAmount());
    }

    public static function runCapturePreviouslyAuthorizedAmount()
    {
        $response = authorizeCreditCard(self::getAmount());
        return capturePreviouslyAuthorizedAmount($response->getTransactionResponse()->getTransId());
    }

    public static function runRefundTransaction()
    {
        $response = authorizeCreditCard.run(self::getAmount());
        $response = capturePreviouslyAuthorizedAmount($response->getTransactionResponse()->getTransId());
        return refundTransaction(self::getAmount());
    }

    public static function runVoidTransaction()
    {
        $response = authorizeCreditCard(self::getAmount());
        return voidTransaction($response->getTransactionResponse()->getTransId());
    }

    public static function runCreditBankAccount()
    {
        return creditBankAccount(self::getAmount());
    }

    public static function runChargeCustomerProfile()
    {
        $response = createCustomerProfile(self::getEmail());
        $paymentProfileResponse = createCustomerPaymentProfile(
            $response->getCustomerProfileId(),
            self::getPhoneNumber()
        );
        $chargeResponse = chargeCustomerProfile(
            $response->getCustomerProfileId(),
            $paymentProfileResponse->getCustomerPaymentProfileId(),
            self::getAmount()
        );
        deleteCustomerProfile($response->getCustomerProfileId());

        return $chargeResponse;
    }

    private static function runPayPalVoid()
    {
        $response = payPalAuthorizeCapture(self::getAmount());
        return payPalVoid($response->getTransactionResponse()->getTransId());
    }

    private static function runPayPalAuthorizeCapture()
    {
            return payPalAuthorizeCapture(self::getAmount());
    }

    private static function runPayPalAuthorizeCaptureContinued()
    {
            $response = payPalAuthorizeCapture(self::getAmount());
            return payPalAuthorizeCaptureContinued($response->getTransactionResponse()->getTransId(), self::$payerID);
    }

    public static function runPayPalAuthorizeOnlyContinued()
    {
            return payPalAuthorizeOnlyContinued(self::$transactionID, self::$payerID);
    }

    public static function runPayPalCredit()
    {
            return payPalCredit(self::$transactionID);
    }

    public static function runPayPalAuthorizeOnly()
    {
            return payPalAuthorizeOnly(self::getAmount());
    }

    public static function runPayPalGetDetails()
    {
            $response = payPalAuthorizeCapture(self::getAmount());
            return payPalGetDetails($response->getTransactionResponse()->getTransId());
    }

    public static function runPayPalPriorAuthorizationCapture()
    {
            $response = payPalAuthorizeCapture(self::getAmount());
            return payPalPriorAuthorizationCapture($response->getTransactionResponse()->getTransId());
    }


    // ****ARB Subscription methods******
    public static function runCreateSubscription()
    {
            $response = createSubscription(self::getDay());
            cancelSubscription($response->getSubscriptionId());

            return $response;
    }

    public static function runCreateSubscriptionFromCustomerProfile()
    {
            $responseCustomerProfile = createCustomerProfile(self::getEmail());
            $responseCustomerPaymentProfile = createCustomerPaymentProfile(
                $responseCustomerProfile->getCustomerProfileId(),
                self::getPhoneNumber()
            );
            $responseCustomerShippingAddress = createCustomerShippingAddress(
                $responseCustomerProfile->getCustomerProfileId(),
                self::getPhoneNumber()
            );

            $response = createSubscription(
                self::getDay(),
                $responseCustomerProfile->getCustomerProfileId(),
                $responseCustomerPaymentProfile->getCustomerPaymentProfileId(),
                $responseCustomerShippingAddress->getCustomerAddressId()
            );

            cancelSubscription($response->getSubscriptionId());
            deleteCustomerProfile($responseCustomerProfile->getCustomerProfileId());

            return $response;
    }

    public static function runCancelSubscription()
    {
            $response = createSubscription(self::getDay());
            return cancelSubscription($response->getSubscriptionId());
    }

    public static function runGetSubscriptionStatus()
    {
            $response = createSubscription(self::getDay());
            $status_response = getSubscriptionStatus($response->getSubscriptionId());
            cancelSubscription($response->getSubscriptionId());

            return $status_response;
    }

    public static function runGetSubscription()
    {
            $response = createSubscription(self::getDay());
            $status_response = getSubscription($response->getSubscriptionId());
            cancelSubscription($response->getSubscriptionId());

            return $status_response;
    }

    public static function runUpdateSubscription()
    {
            $response = createSubscription(self::getDay());
            $update_response = updateSubscription($response->getSubscriptionId());
            cancelSubscription($response->getSubscriptionId());

            return $update_response;
    }

       //*****Customer Profiles methods********
    public static function runCreateCustomerProfile()
    {

        $response = createCustomerProfile(self::getEmail());
        deleteCustomerProfile($response->getCustomerProfileId());
        return $response;
    }

    public static function runDeleteCustomerProfile()
    {

        $responseCustomerProfile = createCustomerProfile(self::getEmail());
        return deleteCustomerProfile($responseCustomerProfile->getCustomerProfileId());
    }

    public static function runGetCustomerProfile()
    {

        $responseCustomerProfile = createCustomerProfile(self::getEmail());
        $response = getCustomerProfile($responseCustomerProfile->getCustomerProfileId());
        deleteCustomerProfile($responseCustomerProfile->getCustomerProfileId());
        return $response;
    }

    // *******Customer Profiles - Payment Profiles methods******

    public static function runCreateCustomerPaymentProfile()
    {
        $responseCustomerProfile = createCustomerProfile(self::getEmail());
        $response=createCustomerPaymentProfile(
            $responseCustomerProfile->getCustomerProfileId(),
            self::getPhoneNumber()
        );
        deleteCustomerProfile($responseCustomerProfile->getCustomerProfileId());
        return $response;
    }

    public static function runGetCustomerPaymentProfile()
    {
        $customerProfileId = createCustomerProfile(self::getEmail())->getCustomerProfileId();
        $customerPaymentProfileId = createCustomerPaymentProfile(
            $customerProfileId,
            self::getPhoneNumber()
        )->getCustomerPaymentProfileId();
        $response= getCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
        deleteCustomerProfile($customerProfileId);
        return $response;
    }

    public static function runValidateCustomerPaymentProfile()
    {
        $customerProfileId = createCustomerProfile(self::getEmail())->getCustomerProfileId();
        $customerPaymentProfileId = createCustomerPaymentProfile(
            $customerProfileId,
            self::getPhoneNumber()
        )->getCustomerPaymentProfileId();
        $response = validateCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
        deleteCustomerProfile($customerProfileId);
        return $response;
    }

    public static function runUpdateCustomerPaymentProfile()
    {
        $customerProfileId = createCustomerProfile(self::getEmail())->getCustomerProfileId();
        $customerPaymentProfileId = createCustomerPaymentProfile(
            $customerProfileId,
            self::getPhoneNumber()
        )->getCustomerPaymentProfileId();
        $response = updateCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
        deleteCustomerProfile($customerProfileId);
        return $response;
    }

    public static function runDeleteCustomerPaymentProfile()
    {
        $customerProfileId = createCustomerProfile(self::getEmail())->getCustomerProfileId();
        $customerPaymentProfileId = createCustomerPaymentProfile(
            $customerProfileId,
            self::getPhoneNumber()
        )->getCustomerPaymentProfileId();
        $response = deleteCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
        deleteCustomerProfile($customerProfileId);
        return $response;
    }

    // ****Customer Profiles - Shipping Address*****
    public static function runCreateCustomerShippingAddress()
    {
        $customerProfileId = createCustomerProfile(self::getEmail())->getCustomerProfileId();
        $response = createCustomerShippingAddress($customerProfileId, self::getPhoneNumber());
        deleteCustomerProfile($customerProfileId);
        return $response;
    }

    public static function runDeleteCustomerShippingAddress()
    {
        $customerProfileId = createCustomerProfile(self::getEmail())->getCustomerProfileId();
        $responseCreateShipping = createCustomerShippingAddress($customerProfileId, self::getPhoneNumber());
        $response = deleteCustomerShippingAddress($customerProfileId, $responseCreateShipping->getCustomerAddressId());
        deleteCustomerProfile($customerProfileId);
        return $response;
    }

    public static function runUpdateCustomerShippingAddress()
    {
        $response = createCustomerProfile(self::getEmail());
        $shippingResponse = createCustomerShippingAddress($response->getCustomerProfileId());
        $updateResponse = updateCustomerShippingAddress(
            $response->getCustomerProfileId(),
            $shippingResponse->getCustomerAddressId()
        );
        deleteCustomerProfile($response->getCustomerProfileId());

        return $updateResponse;
    }

    public static function runGetCustomerShippingAddress()
    {
        $response = createCustomerProfile(self::getEmail());
        $shippingResponse = createCustomerShippingAddress($response->getCustomerProfileId());

        $getResponse = getCustomerShippingAddress(
            $response->getCustomerProfileId(),
            $shippingResponse->getCustomerAddressId()
        );

        deleteCustomerProfile($response->getCustomerProfileId());

        return $getResponse;
    }
    //*****Accept - Accept Customer Profile Page********
    public static function runGetAcceptCustomerProfilePage()
    {
        $response = createCustomerProfile(self::getEmail());
        $profileResponse = GetAcceptCustomerProfilePage($response->getCustomerProfileId());
        deleteCustomerProfile($response->getCustomerProfileId());

        return $profileResponse;
    }
    
    // *****Transaction Reporting methods********
    public static function runGetTransactionDetails()
    {
            $response = authorizeCreditCard(self::getAmount());
            return getTransactionDetails($response->getTransactionResponse()->getTransId());
    }
    
    public static function runGetSettledBatchList()
    {
        $lastSettlementDate=new DateTime();  // current time
        $lastSettlementDate->format("Y-m-d\TH:i:s\Z");
        $lastSettlementDate->setTimezone(new DateTimeZone('UTC'));
        
        $firstSettlementDate=new DateTime();
        $firstSettlementDate->format("Y-m-d\TH:i:s\Z");
        $firstSettlementDate->setTimezone(new DateTimeZone('UTC'));
        $firstSettlementDate->sub(new DateInterval('P28D'));
        
        return getSettledBatchList($firstSettlementDate, $lastSettlementDate);
    }
    
    public static function runGetBatchStatistics()
    {
        $response = getBatchStatistics(self::runGetSettledBatchList()->getBatchList()[0]->getBatchId());
        return $response;
    }
}
