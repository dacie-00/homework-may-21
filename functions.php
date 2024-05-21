<?php

function executeCurl($handle)
{
    $response = curl_exec($handle);
    if (curl_errno($handle)) {
        echo 'Error: ' . ucfirst(curl_error($handle)) . "\n";
        curl_close($handle);
        exit;
    }
    return $response;
}

function decodeJson($data)
{
    $response = json_decode($data);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error parsing response: " . json_last_error_msg() . "\n";
        exit;
    }
    return $response;
}