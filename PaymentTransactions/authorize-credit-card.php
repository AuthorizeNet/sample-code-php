<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_LOG_FILE", "phplog");
  // Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("556KThWQ6vf2");
  $merchantAuthentication->setTransactionKey("9ac2932kQ7kN2Wzq");
  $refId = 'ref' . time();

  $auth = new AuthorizeNetAIM;
  // Testing 
  print_r($auth);

  $auth->setFields(
            array(
            'amount' => rand(1, 1000),
            'card_num' => '6011000000000012',
            'exp_date' => '0420'
            )
  );
  $response = $auth->authorizeOnly();

  if ($response->approved) {
      echo 'Transaction Approved';
      $auth_code = $response->transaction_id;
  } else {
      echo "Transaction Rejected\n";
  }          
?>