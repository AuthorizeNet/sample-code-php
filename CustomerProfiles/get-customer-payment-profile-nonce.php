<?php
  
  require 'vendor/autoload.php';
  require_once 'constants/SampleCodeConstants.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  
  define("AUTHORIZENET_LOG_FILE", "phplog");

function getCustomerPaymentProfileNonce($ProfileId="36731856", $PaymentProfileId="33211899")
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the Access token
    $accessToken = "eyJraWQiOiI1YWI2NTIxNDBlZGU3ZWZkMDAwMDAwMDA1NGNlOWRhOCIsImFsZyI6IlJTMjU2In0.eyJqdGkiOiIyMGIyYWU1Ni1hZjk4LTQ5OWMtOTczOS04ZDg1MWQ3YjBkMDIiLCJzY29wZXMiOlsicmVhZCIsIndyaXRlIl0sImlhdCI6MTU0MzM5OTYwOTU0NiwiYXNzb2NpYXRlZF9pZCI6IjM3ODciLCJjbGllbnRfaWQiOiJ4ZVFmcFJSSTVYIiwibWVyY2hhbnRfaWQiOiI2NjgzOTQiLCJhZGRpdGlvbmFsSW5mbyI6IntcImFwaUxvZ2luSWRcIjpcIjI1TDdLVmd3NyAgICAgICAgICAgXCIsXCJyb3V0aW5nSWRcIjpcIiQkMjVMN0tWZ3c3JCRcIn0iLCJleHBpcmVzX2luIjoxNTQzNDI4NDA5NTQ4LCJncmFudF90eXBlIjoiYXV0aG9yaXphdGlvbl9jb2RlIiwic29sdXRpb25faWQiOiJBQUExMDI5MjIifQ.JQL3YovrTOuh3UaBGLxP8RNbzGGeJ1Id309lysnMcRJEYDCpv6999A4n6Yznr6uzePjpEwbiyd2osDoGnrP_wQmpLwGPR3eBb3DIOiAhKuAbc1YdpsNa3rd2qbVHPFO95_x2y6r7yRCvgNiRx01GFOXphZ3gPrSuHd93U-h0OLd6nt2GKQQcZ8IQ7f-44fViNgLEH_FTPETKAaooSK8v4XFa7Fh3rYM-jd5snrK4dnp7L2xcLb3JivKwsVXCtLGkNbjXu6DQFtlbzEyVknv9j7GBJgOTvsE_lBqmQaFIdNrYiOf6bH0xAfelgNy_7db77zvSPfvrH9afb5DB_pTl-Q";

    $request = new AnetAPI\GetCustomerPaymentProfileNonceRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setConnectedAccessToken($accessToken);
    $request->setCustomerProfileId($ProfileId);
    $request->setCustomerPaymentProfileId($PaymentProfileId);

    $controller = new AnetController\GetCustomerPaymentProfileNonceController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
    {
        echo "\n GetCustomerPaymentProfileNonce Success: \n";
        echo "Data Descriptor : " . $response->getOpaqueData()->getDataDescriptor() . "\n";
        echo "Data Value : " . $response->getOpaqueData()->getDataValue() . "\n";
        echo "Expiration Time Stamp:  " . $response->getOpaqueData()->getExpirationTimeStamp()->format('Y-m-d H:i:s') . "\n";
    }
    else
    {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    

    return $response;
  }

  if(!defined('DONT_RUN_SAMPLES'))
    getCustomerPaymentProfileNonce();
?>

