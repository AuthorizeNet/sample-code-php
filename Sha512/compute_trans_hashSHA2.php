<?php

$apiLogin="2Kfn5S7x7D";
$transId="60115585081";
$amount='12.00';
$signatureKey='56E529FE6C63D60E545F84686096E6AA01D5E18A119F18A130F7CFB3983104216979E95D84C91BDD382AA0875264A63940A2D0AA5548F6023B4C78A9D52C18DA';
$textToHash="^". $apiLogin."^". $transId ."^". $amount."^";

function generateSH512($textToHash, $signatureKey)
{
    if ($textToHash != null && $signatureKey != null) {
                    $sig = hash_hmac('sha512', $textToHash, hex2bin($signatureKey));
                    echo " Computed SHA512 Hash: " . strtoupper($sig) . "\n";
                } else {
                    echo "Either Signature key or the text to hash is empty \n";
                }
}

generateSH512($textToHash, $signatureKey);
?>
