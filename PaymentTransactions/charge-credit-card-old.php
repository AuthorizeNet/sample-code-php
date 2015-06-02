<?php
  require 'vendor/autoload.php';
  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;
  define("AUTHORIZENET_API_LOGIN_ID", "556KThWQ6vf2");
  define("AUTHORIZENET_TRANSACTION_KEY", "9ac2932kQ7kN2Wzq" );
  define("AUTHORIZENET_SANDBOX", true);
  define("AUTHORIZENET_LOG_FILE", "phplog");
  $sale = new AuthorizeNetAIM;
  $sale->amount = $amount;
  $sale->card_num = '6011000000000012';
  $sale->exp_date           = '12/31';
  $sale->amount             = $amount = rand(1,99);
  $sale->description        = $description = "Sale description";
  $sale->first_name         = $first_name = "Jane";
  $sale->last_name          = $last_name = "Smith";
  $sale->company            = $company = "Jane Smith Enterprises Inc.";
  $sale->address            = $address = "20 Main Street";
  $sale->city               = $city = "San Francisco";
  $sale->state              = $state = "CA";
  $sale->zip                = $zip = "94110";
  $sale->country            = $country = "US";
  $sale->phone              = $phone = "415-555-5557";
  $sale->fax                = $fax = "415-555-5556";
  $sale->email              = $email = "foo@example.com";
  $sale->cust_id            = $customer_id = "55";
  $sale->customer_ip        = "98.5.5.5";
  $sale->invoice_num        = $invoice_number = "123";
  $sale->ship_to_first_name = $ship_to_first_name = "John";
  $sale->ship_to_last_name  = $ship_to_last_name = "Smith";
  $sale->ship_to_company    = $ship_to_company = "Smith Enterprises Inc.";
  $sale->ship_to_address    = $ship_to_address = "10 Main Street";
  $sale->ship_to_city       = $ship_to_city = "San Francisco";
  $sale->ship_to_state      = $ship_to_state = "CA";
  $sale->ship_to_zip        = $ship_to_zip_code = "94110";
  $sale->ship_to_country    = $ship_to_country = "US";
  $sale->tax                = $tax = "0.00";
  $sale->freight            = $freight = "Freight<|>ground overnight<|>12.95";
  $sale->duty               = $duty = "Duty1<|>export<|>15.00";
  $sale->tax_exempt         = $tax_exempt = "FALSE";
  $sale->po_num             = $po_num = "12";
  $response = $sale->authorizeAndCapture();
  if ($response->approved) {
      printf("Charge Credit Card - Authorization, Capture and Payment Approved for %f\n", $response->amount);
  } else
  {
     printf("Charge Credit Card - Authorization failed\n");
  }
?>