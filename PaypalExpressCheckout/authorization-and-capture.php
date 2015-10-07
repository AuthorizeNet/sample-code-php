<?php
	require 'vendor/autoload.php';
	use net\authorize\api\contract\v1 as AnetAPI;
	use net\authorize\api\controller as AnetController;
	define("AUTHORIZENET_LOG_FILE", "phplog");

	// Common setup for API credentials (with PayPal compatible merchant credentials)
	$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	$merchantAuthentication->setName("5KP3u95bQpv");
	$merchantAuthentication->setTransactionKey("4Ktq966gC55GAX7S");

	$payPalType=new AnetAPI\PayPalType();
	$payPalType->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
	$payPalType->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");

	$paymentOne = new AnetAPI\PaymentType();
	$paymentOne->setPayPal($payPalType);

	// Create an authorize and capture transaction
	$transactionRequestType = new AnetAPI\TransactionRequestType();
	$transactionRequestType->setTransactionType( "authCaptureTransaction");
	$transactionRequestType->setPayment($paymentOne);
	$transactionRequestType->setAmount(151);

	$request = new AnetAPI\CreateTransactionRequest();
	$request->setMerchantAuthentication($merchantAuthentication);
	$request->setTransactionRequest( $transactionRequestType);
	$controller = new AnetController\CreateTransactionController($request);
	$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

	if ($response != null)
	{
		$tresponse = $response->getTransactionResponse();
		if (($tresponse != null))
		{
			echo "Received response code: ".$tresponse->getResponseCode()."\n";
			//Valid response codes: 1=Approved, 2=Declined, 3=Error, 5=Need Payer Consent
			echo "Secure acceptance URL: ".$tresponse->getSecureAcceptance()->getSecureAcceptanceUrl()."\n";
			echo "Transaction ID: ".$tresponse->getTransId()."\n";
		}
		else
			echo "NULL transactionResponse Error\n";
	}
	else
		echo  "NULL response Error\n";
?>
