<?php
// This page is pretty much the bridge between the events pages (events.php, event.php)
// and RabbitMQ. Browsers can't talk to RabbitMQ directly on their own, so the
// pages send theirs requests here, and this file passes them on
// to the database listener and sends the answer back.

// This file also pretty much handles two kinds of requests:
//   search_events: which is a list of events matching a search
//   get_event: Hwich is one event's details by its ID

// Load the RabbitMQ library from the professor's code
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Tell the browser our answer is JSON data, not an HTML page
header('Content-Type: application/json');

// Sends a JSON answer back to the page and stops this script.
// Used every time we're done, whether things worked or not.
function sendReply($answer) {
    echo json_encode($answer);
    exit;
}

// The page sends its data as JSON in the request body.
// file_get_contents("php://input") reads that body, and json_decode
// turns the JSON into a PHP array ("true" means array, not object).
// If the body was missing or broken, "?? []" uses an empty array instead.
$data = json_decode(file_get_contents("php://input"), true) ?? [];

// Pull out the request type and session token ("" if they weren't sent)
$type = $data['typeRequest'] ?? "";
$token = $data['session_token'] ?? "";

// Only allow our two request types. Anything else is rejected
// before it ever reaches RabbitMQ.
if ($type !== "search_events" && $type !== "get_event") {
    sendReply(["success" => false, "message" => "Invalid request"]);
}

// The user must be logged in, which means they must send a token
if ($token === "") {
    sendReply(["success" => false, "message" => "Not logged in"]);
}

// Check the token is real and not expired by asking the database listener.
// This is the same check the pages do, done again here because someone
// could skip the pages and send requests straight to this file.
$client  = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$session = $client->send_request(["type" => "validate_session", "session_token" => $token]);
if (empty($session["success"])) {
    sendReply(["success" => false, "message" => "Not logged in"]);
}

// Build the request for the database listener
if ($type === "search_events") {
// Search: trim extra spaces, and cut each box to a max length
// so nobody can send a huge amount of text
    $request = [
        "type"     => "search_events",
        "keyword"  => substr(trim($data['keyword'] ?? ""), 0, 100),
        "city"     => substr(trim($data['city'] ?? ""), 0, 100),
        "category" => substr(trim($data['category'] ?? ""), 0, 50)
    ];
} else {
// Single event: we need an ID, and real Ticketmaster IDs are short.
// The database uses a prepared statement with this ID, which keeps
// it safe from SQL injection, so a length check is enough here.
    $eventId = trim($data['event_id'] ?? "");
    if ($eventId === "" || strlen($eventId) > 64) {
        sendReply(["success" => false, "message" => "Invalid event"]);
    }
    $request = ["type" => "get_event", "event_id" => $eventId];
}

// Send the request through RabbitMQ and wait for the listener's answer
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

// If the listener says it failed, pass its message back to the page
if (empty($response["success"])) {
    sendReply(["success" => false, "message" => $response["message"] ?? "Request failed"]);
}
// Close the first connection before opening a new one. All clients share one
// reply queue, so if this one stayed open it could grab the search reply
// meant for the second client, and the page would wait forever.
unset($client);

// If tt worked send back the list of events, or the single event
if ($type === "search_events") {
    sendReply(["success" => true, "events" => $response["events"] ?? []]);
}
sendReply(["success" => true, "event" => $response["event"] ?? null]);
