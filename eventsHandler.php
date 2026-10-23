<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
header('Content-Type: application/json');

function reply($arr) {
    echo json_encode($arr);
    exit;
}

$data  = json_decode(file_get_contents("php://input"), true) ?? [];
$type  = $data['typeRequest'] ?? "";
$token = $data['session_token'] ?? "";

// only allow the two event request types
if (!in_array($type, ["search_events", "get_event"], true)) {
    reply(["success" => false, "message" => "Invalid request"]);
}

// must be logged in
if ($token === "") {
    reply(["success" => false, "message" => "Not logged in"]);
}

$client  = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$session = $client->send_request(["type" => "validate_session", "session_token" => $token]);
if (empty($session["success"])) {
    reply(["success" => false, "message" => "Not logged in"]);
}

// build the request for the db listener
if ($type === "search_events") {
    $request = [
        "type"     => "search_events",
        "keyword"  => substr(trim($data['keyword']  ?? ""), 0, 100),
        "city"     => substr(trim($data['city']     ?? ""), 0, 100),
        "category" => substr(trim($data['category'] ?? ""), 0, 50)
    ];
} else {
    $eventId = $data['event_id'] ?? "";
    if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', $eventId)) {
        reply(["success" => false, "message" => "Invalid event"]);
    }
    $request = ["type" => "get_event", "event_id" => $eventId];
}

$client   = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

if (empty($response["success"])) {
    reply(["success" => false, "message" => $response["message"] ?? "Request failed"]);
}

if ($type === "search_events") {
    reply(["success" => true, "events" => $response["events"] ?? []]);
}
reply(["success" => true, "event" => $response["event"] ?? null]);
