<?php
  require 'vendor/autoload.php';

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

function payPalGetDetails($transactionId)
{

    echo "PayPal Get Details Transaction\n";
    
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
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
      if($response->getMessages()->getResultCode() == \SampleCode\Constants::RESPONSE_OK)
      {
        $tresponse = $response->getTransactionResponse();
        
	      if ($tresponse != null && $tresponse->getMessages() != null)   
        {
          echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
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
  payPalGetDetails("60007107304");

?>