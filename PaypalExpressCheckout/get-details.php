<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

  function payPalGetDetails($transactionId) {

    echo "PayPal Get Details Transaction\n";
    
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    $refId = 'ref' . time();

    //create a transaction of type get details
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "getDetailsTransaction"); 
    
    //replace following transaction ID with your transaction ID for which the details are required
    $transactionRequestType->setRefTransId($transactionId);

    // Create the payment data for a paypal account
    $payPalType = new AnetAPI\PayPalType();
    $payPalType->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    $payPalType->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setPayPal($payPalType);

    $transactionRequestType->setPayment($paymentOne);

    //create a transaction request
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId( $refId);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);

    //execute the api call to get transaction details
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      
	  if($tresponse != null)
      {
		if (($tresponse != null) && ($response->getMessages()->getResultCode()=="Ok"))
		{
			echo "Transaction Response...\n";
			echo "Received response code: ".$tresponse->getResponseCode()."\n";
			echo "Transaction ID: ".$tresponse->getTransId()."\n";
			//Valid response codes: 1=Approved, 2=Declined, 3=Error, 5=Need Payer Consent
			if(null != $tresponse->getSecureAcceptance())
			{
				echo "Payer ID : " . $tresponse->getSecureAcceptance()->getPayerID() . "\n";
			}
			//parse the shipping information from response
      		$shipping_response = $tresponse->getShipTo();
			if(null != $shipping_response)
			{
				echo "Shipping address : " . $shipping_response->getAddress() . ", " . $shipping_response->getCity()
					. ", " . $shipping_response->getState() . ", " . $shipping_response->getCountry() . "\n";
			}
		}
		else
			echo "NULL transactionResponse Error\n";
		$messages=$response->getMessages();
		if (($messages != null))
		{
			echo "Messages...\n";
			echo "Result code: ".$messages->getResultCode()."\n";
			$errorMessages = $messages->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
		}
		else
			echo "NULL messages Error\n";
      }
      else
      {
      	echo  "No response returned";
      }    
    }
    else
    {
      echo  "No response returned";
    }

    return $response;
  }

if(!defined('DONT_RUN_SAMPLES'))
  payPalGetDetails("2249863278");

?>