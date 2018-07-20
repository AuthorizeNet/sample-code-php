<?php
	require 'vendor/autoload.php';
	require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

    define("AUTHORIZENET_LOG_FILE", "phplog");

function payPalAuthorizeCapture($amount)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $payPalType=new AnetAPI\PayPalType();
    $payPalType->setCancelUrl("http://www.merchanteCommerceSite.com/Success/TC25262");
    $payPalType->setSuccessUrl("http://www.merchanteCommerceSite.com/Success/TC25262");

    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setPayPal($payPalType);

		// Create an authorize and capture transaction
		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType( "authCaptureTransaction");
		$transactionRequestType->setPayment($paymentOne);
		$transactionRequestType->setAmount($amount);

		$request = new AnetAPI\CreateTransactionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setTransactionRequest( $transactionRequestType);
		$controller = new AnetController\CreateTransactionController($request);
		$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

	    if ($response != null)
	    {
	      if($response->getMessages()->getResultCode() == "Ok")
	      {
	        $tresponse = $response->getTransactionResponse();
	        
	        if ($tresponse != null && $tresponse->getMessages() != null)   
	        {
          		echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
				//Valid response codes: 1=Approved, 2=Declined, 3=Error, 5=Need Payer Consent
				echo "Secure acceptance URL: ".$tresponse->getSecureAcceptance()->getSecureAcceptanceUrl()."\n";
				echo "Transaction ID: ".$tresponse->getTransId()."\n";
				
	          	echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
	          	echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
	        }
	        else
	        {
	          echo "Transaction Failed \n";
	          if($tresponse->getErrors() != null)
	          {
	            echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
	            echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";            
	          }
	        }
	      }
	      else
	      {
	        echo "Transaction Failed \n";
	        $tresponse = $response->getTransactionResponse();
	        

	        if($tresponse != null && $tresponse->getErrors() != null)
	        {
	          echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
	          echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";                      
	        }
	        else
	        {
	          echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
	          echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
	        }
	      }      
	    }
	    else
	    {
	      echo  "No response returned \n";
	    }

		return $response;
	}

  	if(!defined('DONT_RUN_SAMPLES'))
    	payPalAuthorizeCapture(12.322);
?>
