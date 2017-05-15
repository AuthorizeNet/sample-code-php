<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  define("AUTHORIZENET_LOG_FILE", "phplog");

function approveOrDeclineHeldTransaction()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCode\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCode\Constants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

      //create a transaction
      $transactionRequestType = new AnetAPI\HeldTransactionRequestType();
      $transactionRequestType->setAction("approve"); //other possible value: decline
      $transactionRequestType->setRefTransId("60012148205");
      

      $request = new AnetAPI\UpdateHeldTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setHeldTransactionRequest( $transactionRequestType);
      $controller = new AnetController\UpdateHeldTransactionController($request);
      $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
      

      if ($response != null)
      {
        if($response->getMessages()->getResultCode() == 'Ok')
        {
          $tresponse = $response->getTransactionResponse();
          
	        if ($tresponse != null && $tresponse->getMessages() != null)   
          {
            echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
            echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
            echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
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
      approveOrDeclineHeldTransaction();
?>
