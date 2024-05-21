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
    CURLOPT_URL => "https://api.agify.io?name=$name",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = executeCurl($curlHandle);
curl_close($curlHandle);
$response = decodeJson($response);

if (isset($response->error)) {
    exit('Error: ' . ucfirst($response->error) . "\n");
}
if ($response->age === Null) {
    exit("No data available for $name\n");
}
echo "The average person with the name $name is $response->age years old.\n";