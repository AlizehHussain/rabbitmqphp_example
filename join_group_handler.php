<?php

header("Content-Type: application/json");

require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

// read the request body
$data = json_decode(file_get_contents('php://input'), true);

// get the session token and invite token
$session_token = $data["session_token"] ?? "";
$invite_token = $data["invite_token"] ?? "";

// check if the required values are provided
if ($session_token === "" || $invite_token === "") {
    echo json_encode([
        "success" => false,
        "message" => "Session token and invite token are required"
    ]);
    exit;
}

// create the request
$request = [
    "type" => "join_group",
    "session_token" => $session_token,
    "invite_token" => $invite_token
];

try {
    // connect to rabbitmq
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

    // send the request
    $response = $client->send_request($request);

    // check if the response is valid
    if (!is_array($response)) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to join group"
        ]);
        exit;
    }

    // return the response
    echo json_encode($response);
}
catch (Throwable $e) {
    // log the error
    error_log("Joining a group was unsuccessful: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Internal server error"
    ]);
}
?>