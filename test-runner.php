<?php
define("DONT_RUN_SAMPLES", "true");
define("SAMPLE_CODE_NAME_HEADING", "SampleCodeName");
require 'vendor/autoload.php';
$directories = array(
            'CustomerProfiles/',
            'RecurringBilling/',
            'PaypalExpressCheckout/',
            'PaymentTransactions/',
            'TransactionReporting/',
            'ApplePayTransactions/',
            'VisaCheckout/'
);
foreach ($directories as $directory) {
    foreach(glob($directory . "*.php") as $sample) {
        require_once $sample;
		echo $sample;
    }
}

class TestRunner extends PHPUnit_Framework_TestCase
{
	public static $apiLoginId = "5KP3u95bQpv";
	public static $transactionKey = "4Ktq966gC55GAX7S";
	public static $transactionID = "2245440957";
	public static $payerID = "LM6NCLZ5RAKBY";
	//random amount for transactions/subscriptions
	public static function getAmount(){
		return 12 + (rand(1, 10000)/12);
	}
	//random email for a new customer profile
	public static function getEmail(){
		return rand(0,10000) . "@test.com";
	}
	//random phonenumber for customer payment profile
	public static function getPhoneNumber(){
	    return self::toPhoneNumber(rand(0,9999999999));
	}
	public function getDay(){
		return rand(7, 365);
	}
	public function testAllSampleCodes(){
		$runTests = 0;
		$file = 'SampleCodeList.txt';
		$data = file($file) or die('\nCould not read SampleCodeList.');
		foreach ($data as $line)
		{
			$line=trim($line);
			if(trim($line))
			{
				list($apiName, $isDependent, $shouldRun)=explode(",",$line);
				$apiName = trim($apiName);
				echo "\nApi name: " . $apiName."\n";	
			}			
			if($apiName && (false === strpos($apiName,SAMPLE_CODE_NAME_HEADING)))
			{
				
				
				echo "should run:".$shouldRun."\n";
				if("0" === $shouldRun)
				{
					echo ":Skipping " . $sampleMethodName . "\n";
				}
				else
				{
					if("0" === $isDependent)
					{
						echo "not dependent\n";
						$sampleMethodName = $apiName;
						$sampleMethodName[0] = strtolower($sampleMethodName[0]);
					}
					else
					{
						$sampleMethodName = "TestRunner::run" . $apiName;
						echo " is dependent\n";
					}
					
					
					//request the api
					echo "Running sample: " . $sampleMethodName;
					
					$response = call_user_func($sampleMethodName);

					//response must be successful
					$this->assertNotNull($response);
					$this->assertEquals($response->getMessages()->getResultCode(), "Ok");
					$runTests++;
				}
			}
		}
		echo "Number of sample codes run: ". $runTests;
	}

	private static function toPhoneNumber($num)
	{
		$zeroPadded = sprintf("%10d", $num);
		return substr($zeroPadded,0,3)."-".substr($zeroPadded,3,3)."-".substr(6,4);
	}

	public static function runAuthorizeCreditCard()
	{
		return authorizeCreditCard(self::getAmount());
	}
	
	public static function runDebitBankAccount()
	{
		return debitBankAccount(self::getAmount());
	}
	
	public static function runChargeTokenizedCreditCard()
	{
		return chargeTokenizedCreditCard(self::getAmount());
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
		return refundTransaction(self::getAmount(), $response->getTransactionResponse()->getTransId());
	}

	public static function runVoidTransaction()
	{
		$response = authorizeCreditCard(self::getAmount());
		return voidTransaction($response->getTransactionResponse()->getTransId());
	}

	public static function runCreditBankAccount()
	{
		return creditBankAccount(self::$transactionID, self::getAmount());
	}

	public static function runChargeCustomerProfile()
	{
		$response = createCustomerProfile(self::getEmail());
		$paymentProfileResponse = createCustomerPaymentProfile($response->getCustomerProfileId());
		$chargeResponse = chargeCustomerProfile($response->getCustomerProfileId(), $paymentProfileResponse->getCustomerPaymentProfileId(), self::getAmount());
		deleteCustomerProfile($response->getCustomerProfileId());

		return $chargeResponse;
	}

	private static function runPayPalVoid() { 
		$response = payPalAuthorizeCapture(self::getAmount());
		return payPalVoid($response->getTransactionResponse()->getTransId());
	}

   private static function runPayPalAuthorizeCapture()
   {
		return payPalAuthorizeCapture(self::getAmount());
   }

   private static function runPayPalAuthorizeCaptureContinue() 
   {
		$response = payPalAuthorizeCapture(self::getAmount());
        return payPalAuthorizeCaptureContinue($response->getTransactionResponse()->getTransId(), self::$payerID);
   }

   public static function runPayPalAuthorizeOnlyContinue() 
   {
		return payPalAuthorizeOnlyContinue(self::$transactionID, self::$payerID);
   }

   public static function runPayPalCredit() { 
		return payPalCredit(self::$transactionID);
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

   public static function runGetTransactionDetails()
   {
		$response = authorizeCreditCard(self::getAmount());
		return getTransactionDetails($response.getTransactionResponse().getTransId());
   }


   public static function runCreateSubscription()
   {
		$response = createSubscription(self::getDay());
		cancelSubscription($response->getSubscriptionId());

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

   	//customer profiles methods
	public static function runCreateCustomerProfile(){
		$response = createCustomerProfile(self::randEmail());
		$customerProfileId = $response->getCustomerProfileId();
		deleteCustomerProfileWithId($customerProfileId);
		return $response;
	}
	public static function runGetCustomerProfile(){
		$responseCustomerProfile = createCustomerProfile(self::randEmail());
		$existingCustomerProfileId = $responseCustomerProfile->getCustomerProfileId();
		$response = getCustomerProfile($existingCustomerProfileId);
		return $response;
	}
	public static function runUpdateCustomerProfile(){
		$responseCustomerProfile = createCustomerProfile(self::randEmail());
		$customerProfileId = $responseCustomerProfile->getCustomerProfileId();
		$response = updateCustomerProfileById($customerProfileId);
		deleteCustomerProfileWithId($customerProfileId);
		return $response;
	}
	//customer profiles - payment profiles methods

	public static function runCreateCustomerPaymentProfile()
	{
		$responseCustomerProfile = createCustomerProfile(self::randEmail());
		$existingCustomerProfileId = $responseCustomerProfile->getCustomerProfileId();
		$response=createCustomerPaymentProfile($existingCustomerProfileId, self::randPhoneNumber());
		return $response;
	}
	public static function runGetCustomerPaymentProfile(){
		$customerProfileId = createCustomerProfile(self::randEmail())->getCustomerProfileId();
		$customerPaymentProfileId = createCustomerPaymentProfile($customerProfileId, self::randPhoneNumber())
			->getCustomerPaymentProfileId();
		$response= getCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		deleteCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		return $response;
	}
	public static function runValidateCustomerPaymentProfile(){
		$customerProfileId = createCustomerProfile(self::randEmail())->getCustomerProfileId();
		$customerPaymentProfileId = createCustomerPaymentProfile($customerProfileId, self::randPhoneNumber())
			->getCustomerPaymentProfileId();
		$response= getCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		validateCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		return $response;
	}
	public static function runUpdateCustomerPaymentProfile(){
		$customerProfileId = createCustomerProfile(self::randEmail())->getCustomerProfileId();
		$customerPaymentProfileId = createCustomerPaymentProfile($customerProfileId, self::randPhoneNumber())->getCustomerPaymentProfileId();
		$response= getCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		updateCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		return $response;
	}
	public static function runDeleteCustomerPaymentProfile(){
		$customerProfileId = createCustomerProfile(self::randEmail())->getCustomerProfileId();
		$customerPaymentProfileId = createCustomerPaymentProfile($customerProfileId, self::randPhoneNumber())
			->getCustomerPaymentProfileId();
		$response= getCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		deleteCustomerPaymentProfile($customerProfileId, $customerPaymentProfileId);
		return $response;
	}
	//customer profiles - shipping address
	public static function runCreateCustomerShippingAddress(){
		$customerProfileId = createCustomerProfile(self::randEmail())->getCustomerProfileId();
		$response = createCustomerShippingAddress($customerProfileId, self::randPhoneNumber());
		deleteCustomerShippingAddress($customerProfileId, $response->getCustomerShippingAddressId());
		return $response;
	}
	public static function runDeleteCustomerShippingAddress(){
		$customerProfileId = createCustomerProfile(self::randEmail())->getCustomerProfileId();
		$responseCreateShipping = createCustomerShippingAddress($customerProfileId, self::randPhoneNumber());
		return deleteCustomerShippingAddress($customerProfileId, $responsCreateShippinge->getCustomerShippingAddressId());
	} 
}