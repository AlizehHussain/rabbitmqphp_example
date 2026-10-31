<?php

header("Content-Type: application/json");

require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

// read the request body
$data = json_decode(file_get_contents('php://input'), true);

// get the session token and hangout id
$session_token = $data["session_token"] ?? "";
$hangout_id = $data["hangout_id"] ?? "";

// check if the required values are provided
if ($session_token === "" || $hangout_id === "") {
    echo json_encode([
        "success" => false,
        "message" => "Session token and hangout id are required"
    ]);
    exit;
}

// create the request
$request = [
    "type" => "get_hangout_members",
    "session_token" => $session_token,
    "hangout_id" => $hangout_id
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
            "message" => "Unable to retrieve hangout members"
        ]);
        exit;
    }

    // return the response
    echo json_encode($response);
}
catch (Throwable $e) {
    // log the error
    error_log("Getting hangout members was unsuccessful: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Internal server error"
    ]);
}
?>