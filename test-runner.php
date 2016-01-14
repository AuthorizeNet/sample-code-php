<?php
define("DONT_RUN_SAMPLES", "true");
define("SAMPLE_CODE_NAME_HEADING", "SampleCodeName");
require 'vendor/autoload.php';
$directories = array(
            'CustomerProfiles/'
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
	public static function randAmount(){
		return rand(0, 10000);
	}
	//random email for a new customer profile
	public static function randEmail(){
		return rand(0,10000) . "@test.com";
	}
	//random phonenumber for customer payment profile
	public static function randPhoneNumber(){
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
	public static function runCreateCustomerPaymentProfile(){
		$responseCustomerProfile = createCustomerProfile();
		$existingCustomerProfileId = $responseCustomerProfile->getCustomerProfileId();
		return createCustomerPaymentProfile($existingCustomerProfileId, self::randPhoneNumber());
	}
	private static function toPhoneNumber($num)
	{
		$zeroPadded = sprintf("%10d", $num);
		return substr($zeroPadded,0,3)."-".substr($zeroPadded,3,3)."-".substr(6,4);
		
	}
}