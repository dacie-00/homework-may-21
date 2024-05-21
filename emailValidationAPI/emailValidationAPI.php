<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'vendor/autoload.php';
require_once "functions.php";


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

while (true) {
    $email = readline("Enter email to check: ");
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo "Input must be a valid email.\n";
        continue;
    }
    break;
}

$apiKey = $_ENV['API_KEY'];
if ($_ENV["API_KEY"] === null) {
    exit("Missing API key in environment.\n");
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
curl_close($curlHandle);
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

if ($_ENV["USERNAME"] === null || $_ENV["PASSWORD"] === null) {
    exit("Missing username or password in environment.\n");
}

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = 'mail.inbox.lv';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV["USERNAME"];
    $mail->Password = $_ENV["PASSWORD"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom($_ENV["USERNAME"], 'Mailer');
    $mail->addAddress($email);

    $mail->Subject = 'Hello World!';
    $mail->Body = 'Hello World!';

    $mail->send();
    echo "Message has been sent\n";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}
