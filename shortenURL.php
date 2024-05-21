<?php

require_once "functions.php";

while (true) {
    $url = readline("Please enter URL to shorten: ");
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = strpos($url, 'http') !== 0 ? "http://$url" : $url;
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        echo "Invalid url!\n";
        continue;
    }
    break;
}

$curlHandle = curl_init();
curl_setopt_array($curlHandle, [
    CURLOPT_URL => "https://cleanuri.com/api/v1/shorten",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => "url=$url",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$response = executeCurl($curlHandle);
curl_close($curlHandle);
$response = decodeJson($response);

if (isset($response->error)) {
    exit($response->error . "\n");
}
echo $response->result_url . "\n";