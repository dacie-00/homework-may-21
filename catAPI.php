<?php

require_once "functions.php";

$curlHandle = curl_init();
curl_setopt_array($curlHandle, [
    CURLOPT_URL => "https://cat-fact.herokuapp.com/facts/random?animal_type=cat&amount=1",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = executeCurl($curlHandle);
curl_close($curlHandle);
$fact = decodeJson($response);

$verified = $fact->status->verified != Null ? "verified" : "not verified";
echo "Here's a fun cat fact:\n";
echo $fact->text . " ($verified)\n";
