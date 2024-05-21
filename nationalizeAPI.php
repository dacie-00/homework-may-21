<?php

require_once "functions.php";

while (true) {
    $name = ucfirst(strtolower(readline("Enter a name: ")));
    if ($name == "") {
        echo "Name cannot be empty.\n";
        continue;
    }
    if (strlen($name) > 30) {
        echo "Name cannot be longer than 30 characters.\n";
        continue;
    }
    break;
}

$curlHandle = curl_init();
curl_setopt_array($curlHandle, [
    CURLOPT_URL => "https://api.nationalize.io?name=$name",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = executeCurl($curlHandle);
$response = decodeJson($response);

if (isset($response->error)) {
    exit('Error: ' . ucfirst($response->error) . "\n");
}
$nationality = $response->country[0]->country_id;
if ($nationality === Null) {
    exit("No data available for $name.\n");
}
$probability = (int)($response->country[0]->probability * 100);
echo "The nationality of the name $name is $nationality with a $probability% probability.\n";