<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'vendor/autoload.php';
require_once "functions.php";


while (true) {
    echo "Please provide your API key.\n";
    $apiKey = readline("Key - ");
    if ($apiKey === "") {
        echo "API key cannot be empty.\n";
        continue;
    }
    break;
}

while (true) {
    $email = readline("Enter email to check: ");
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo "Input must be a valid email.\n";
        continue;
    }
    break;
}


$url = "https://api.emailvalidation.io/v1/info?apikey=$apiKey&email=" . urlencode($email);

$curlHandle = curl_init();
curl_setopt_array($curlHandle, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = executeCurl($curlHandle);
$response = decodeJson($response);

if (isset($response->message)) {
    echo $response->message . "\n";
    if (isset($response->errors)) {
        echo $response->errors->email[0] . "\n";
    }
    exit();
}

echo "The email $response->email is $response->state ($response->reason).\n";
if ($response->reason === "invalid_mailbox") {
    exit();
}
$disposability = $response->disposable ? "disposable" : "not disposable";
echo "The email is $disposability and has a score of $response->score.\n";


if (strtolower(readline("Send test email? (y/n) ")) != "y") {
    exit();
}

if (!file_exists("loginInfo.json")) {
    echo "Missing loginInfo.json with username and password!\n";
}
$loginData = json_decode(file_get_contents("loginInfo.json"));

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = 'mail.inbox.lv';
    $mail->SMTPAuth = true;
    $mail->Username = $loginData->username;
    $mail->Password = $loginData->password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom($loginData->username, 'Mailer');
    $mail->addAddress($email);

    $mail->Subject = 'Hello World!';
    $mail->Body = 'Hello World!';

    $mail->send();
    echo "Message has been sent\n";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}