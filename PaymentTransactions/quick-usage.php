<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_API_LOGIN_ID", "556KThWQ6vf2");
  define("AUTHORIZENET_TRANSACTION_KEY", "9ac2932kQ7kN2Wzq" );
  define("AUTHORIZENET_SANDBOX", true);
  define("AUTHORIZENET_LOG_FILE", "phplog");
  $sale           = new AuthorizeNetAIM;
  $sale->amount   = "5.99";
  $sale->card_num = '6011000000000012';
  $sale->exp_date = '07/30';
  $response = $sale->authorizeAndCapture();
  if ($response->approved) {
     echo "Transaction Approved\n";
     $transaction_id = $response->transaction_id;
  } else {
     echo "Transaction Rejected\n";
  }  
?>