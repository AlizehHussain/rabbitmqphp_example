<?php
// ============================================================
// testEmailClient.php
// Sends one test email job through RabbitMQ to emailListener.php.
// Usage: php testEmailClient.php you@example.com [template]
// template is pick_confirmation (default) or event_reminder
// ============================================================

// The RabbitMQ library and .ini live two folders up, at the top of the repo.
// Move there first, since the library opens its files with relative paths.
chdir(__DIR__ . '/../..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Read the recipient and template from the command line
$to       = $argv[1] ?? '';
$template = $argv[2] ?? 'pick_confirmation';

// Make sure the recipient looks like a real email address
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    die("Usage: php testEmailClient.php you@example.com [pick_confirmation|event_reminder]\n");
}

// Connect using the [emailServer] section, so the job goes to emailQueue
// (not testQueue, where the database listener is)
$client = new rabbitMQClient("testRabbitMQ.ini", "emailServer");

// The email job, in the format emailListener.php expects
$request = [
    "type"     => "send_email",
    "template" => $template,
    "to"       => $to,
    "event"    => [
        "name"  => "Sample Concert",
        "date"  => "2026-11-15",
        "venue" => "Madison Square Garden",
        "city"  => "New York",
        "url"   => "https://www.ticketmaster.com"
    ]
];

// Send it and wait for the listener's reply
$response = $client->send_request($request);

// Show what came back: success true/false and a message
print_r($response);
