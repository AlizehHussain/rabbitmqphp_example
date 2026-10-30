<?php

header("Content-Type: application/json");

require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

// get the information sent from the frontend
$data = json_decode(file_get_contents('php://input'), true);

// get the session token
$session_token = $data["session_token"] ?? "";

// check if the session token is empty
if ($session_token === "") {
    echo json_encode([
        "success" => false,
        "message" => "Session token is required"
    ]);
    exit;
}

try {

    // create the request to send to the listener
    $request = [
        "type" => "get_groups",
        "session_token" => $session_token
    ];

    // connect to RabbitMQ and send the request
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);

    // check if the response is valid
    if (!is_array($response)) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to retrieve groups"
        ]);
        exit;
    }

    // return the response
    echo json_encode($response);

}
catch (Throwable $e) {

    // log the error
    error_log("Getting groups was unsuccessful: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Internal server error"
    ]);
}
?>