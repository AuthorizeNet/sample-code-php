<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_API_LOGIN_ID", "556KThWQ6vf2");
  define("AUTHORIZENET_TRANSACTION_KEY", "9ac2932kQ7kN2Wzq" );
  define("AUTHORIZENET_SANDBOX", true);
  define("AUTHORIZENET_LOG_FILE", "phplog");
  $authandcapture = new AuthorizeNetAIM;
    $authandcapture->setFields(
            array(
            'amount' => rand(1, 1000),
            'card_num' => '6011000000000012',
            'exp_date' => '0730'
            )
    );
    $response = $authandcapture->authorizeAndCapture();
    if ($response->approved) {
        echo "Void Transaction Authorization and Cap[ture Approved\n";
        $void = new AuthorizeNetAIM;
        $void->setFields(
            array(
            'amount' => $amount,
            'card_num' => '6011000000000012',
            'trans_id' => $response->transaction_id,
            )
        );
        $void_response = $void->Void();
        if ($void_response->approved) {
            echo "Void Transaction Void response Approved \n";
        } else {
            echo "Void Trnsaction Void response Failed \n";  
        }   
    } else {
        echo "Void Transaction Authorization Denied\n";
    }  
?>