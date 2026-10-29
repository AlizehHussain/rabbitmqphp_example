
<?php
require_once(__DIR__ . '/path.inc');
require_once(__DIR__ . '/get_host_info.inc');
require_once(__DIR__ . '/rabbitMQLib.inc');

header('Content-Type: application/json');

function sendReply($answer) {
    echo json_encode($answer);
    exit;
}

$data  = json_decode(file_get_contents("php://input"), true) ?? [];
$type  = $data['typeRequest'] ?? "";
$token = $data['session_token'] ?? "";

if ($type !== "search_events" && $type !== "get_event" && $type !== "rate_event") {
    sendReply(["success" => false, "message" => "Invalid request"]);
}

if ($token === "") {
    sendReply(["success" => false, "message" => "Not logged in"]);
}

if ($type === "search_events") {
    $request = [
        "type"          => "search_events",
        "session_token" => $token,
        "keyword"       => substr(trim($data['keyword'] ?? ""), 0, 100),
        "city"          => substr(trim($data['city'] ?? ""), 0, 100),
        "category"      => substr(trim($data['category'] ?? ""), 0, 50)
    ];
} elseif ($type === "rate_event"){
	$request = [
		"type" => "rate_event",
		"session_token" => $token,
		"event_id"	=> (int) ($data['event_id'] ?? 0),
		"rating"	=> (int) ($data['rating'] ?? 0)

	];
} else {
    $eventId = trim($data['event_id'] ?? "");
    if ($eventId === "" || strlen($eventId) > 64) {
        sendReply(["success" => false, "message" => "Invalid event"]);
    }
    $request = [
        "type"          => "get_event",
        "session_token" => $token,
        "event_id"      => $eventId
    ];
}

try {
    $client   = new rabbitMQClient(__DIR__ . "/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
} catch (Throwable $e) {
    error_log("Events handler error: " . $e->getMessage());
    sendReply(["success" => false, "message" => "Unable to communicate with backend"]);
}

if (!is_array($response) || empty($response["success"])) {
    sendReply(["success" => false, "message" => $response["message"] ?? "Request failed"]);
}

if ($type === "search_events") {
    sendReply(["success" => true, "events" => $response["events"] ?? []]);
}

if ($type === "rate_event"){
	sendReply(["success" => true, "message" => "Rating saved"]);
}

sendReply(["success" => true, "event" => $response["event"] ?? null]);
