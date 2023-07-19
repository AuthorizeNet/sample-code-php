<?php
require 'vendor/autoload.php';
require_once 'constants/SampleCodeConstants.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

function createGooglePayTransaction()
{
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    $refId = 'ref' . time();

    $opaqueData = new AnetAPI\OpaqueDataType();
    $opaqueData->setDataDescriptor("COMMON.GOOGLE.INAPP.PAYMENT");
    $opaqueData->setDataValue("1234567890ABCDEF1111AAAA2222BBBB3333CCCC4444DDDD5555EEEE6666FFFF7777888899990000");
    $paymentType = new AnetAPI\PaymentType();
    $paymentType->setOpaqueData($opaqueData);

    $lineItem = new AnetAPI\LineItemType();
    $lineItem->setItemId("1");
    $lineItem->setName("vase");
    $lineItem->setDescription("Cannes logo");
    $lineItem->setQuantity(18);
    $lineItem->setUnitPrice(45.00);

    $lineItemsArray = array();
    $lineItemsArray[0] = $lineItem;

    $tax = new AnetAPI\ExtendedAmountType();
    $tax->setAmount(5.00);
    $tax->setName("level2 tax name");
    $tax->setDescription("level2 tax");

    $userField = new AnetAPI\UserFieldType();
    $userFields = array();

    $userField->setName("UserDefinedFieldName1");
    $userField->setValue("UserDefinedFieldValue1");
    $userFields[0] = $userField;

    $userField->setName("UserDefinedFieldName2");
    $userField->setValue("UserDefinedFieldValue2");
    $userFields[1] = $userField;

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction");
    $transactionRequestType->setAmount(151);
    $transactionRequestType->setPayment($paymentType);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);


    if ($response != null)
    {
      if($response->getMessages()->getResultCode() == "Ok")
      {
        $tresponse = $response->getTransactionResponse();
        
	      if ($tresponse != null && $tresponse->getMessages() != null)   
        {
          echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
          echo " AUTH CODE : " . $tresponse->getAuthCode() . "\n";
          echo " TRANS ID  : " . $tresponse->getTransId() . "\n";
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
    createGooglePayTransaction();
?>
