<?php
// A small test script that sends ONE email job through RabbitMQ
// to emailListener.php, to show the email system works end to end:
// The way it works is pretty much like thi.  this script -> RabbitMQ (emailQueue) -> emailListener.php -> Gmail -> inbox

// How to run it if you guys want to try
//   php testEmailClient.php you@example.com
//   php testEmailClient.php you@example.com event_reminder

chdir(__DIR__ . '/../..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// $argv holds the words typed after "php testEmailClient.php".
// $argv[1] is the email address, $argv[2] is the template (optional).
$to = $argv[1] ?? '';
$template = $argv[2] ?? 'pick_confirmation';   // default if no template is given

// Stop with instructions if the email address is missing or not valid
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    die("Usage: php testEmailClient.php you@example.com [pick_confirmation or event_reminder]\n");
}

// Connect to RabbitMQ using the [emailServer] section of testRabbitMQ.ini.
// That sends our job to emailQueue, where the email listener is waiting,
// instead of testQueue, where the database listener is.
$client = new rabbitMQClient("testRabbitMQ.ini", "emailServer");

// The email job, in the exact format emailListener.php expects.
// The event details are made up, just for testing.
$request = [
    "type" => "send_email",
    "template" => $template,
    "to" => $to,
    "event" => [
        "name" => "Sample Concert",
        "date" => "2026-11-15",
        "venue" => "Madison Square Garden",
        "city" => "New York",
        "url" => "https://www.ticketmaster.com"
    ]
];

// Sends it through RabbitMQ and waits for the listener's answer
$response = $client->send_request($request);

// Prints the answer: success true/false and a message
print_r($response);
