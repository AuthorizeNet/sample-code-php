<?php
	require 'vendor/autoload.php';
	use net\authorize\api\contract\v1 as AnetAPI;
	use net\authorize\api\controller as AnetController;

    define("AUTHORIZENET_LOG_FILE", "phplog");

	function payPalAuthorizeCaptureContinue($refTransId, $payerID) {

		// Common setup for API credentials (with PayPal compatible merchant credentials)
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
	    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
        $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);

		$payPalType=new AnetAPI\PayPalType();
		$payPalType->setPayerID($payerID);

		$paymentOne = new AnetAPI\PaymentType();
		$paymentOne->setPayPal($payPalType);

		// Create an authorize and capture continue transaction
		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType( "authCaptureContinueTransaction");
		$transactionRequestType->setPayment($paymentOne);
		$transactionRequestType->setRefTransId($refTransId);

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
				echo "Transaction Response...\n";
				echo "Received response code: ".$tresponse->getResponseCode()."\n";
				//Valid response codes: 1=Approved, 2=Declined, 3=Error, 5=Need Payer Consent
				echo "Secure acceptance URL: ".$tresponse->getSecureAcceptance()->getSecureAcceptanceUrl()."\n";
				echo "Transaction ID: ".$tresponse->getTransId()."\n";
			}
			else
				echo "NULL transactionResponse Error\n";
			$messages=$response->getMessages();
			if (($messages != null))
			{
				echo "Messages...\n";
				echo "Result code: ".$messages->getResultCode()."\n";
				$message0=$messages->getMessage()[0];
				if($message0!=null)
					echo "Message: ".$message0->getCode().", ".$message0->getText()."\n";
			}
			else
				echo "NULL messages Error\n";
		}
		else
			echo  "NULL response Error\n";

		return $response;
	}

  	if(!defined('DONT_RUN_SAMPLES'))
    	payPalAuthorizeCaptureContinue("2241708986","6ZSCSYG33VP8Q");
?>
