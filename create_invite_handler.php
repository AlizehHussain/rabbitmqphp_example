<?php

header("Content-Type: application/json");

require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

// get the information sent from the frontend
$data = json_decode(file_get_contents('php://input'), true);

// get the session token and group id
$session_token = $data["session_token"] ?? "";
$group_id = $data["group_id"] ?? "";

// check if the required information is missing
if ($session_token === "" || $group_id === "") {
    echo json_encode([
        "success" => false,
        "message" => "Session token and group id are required"
    ]);
    exit;
}

try {

    // create the request to send to the listener
    $request = [
        "type" => "create_invite",
        "session_token" => $session_token,
        "group_id" => $group_id
    ];

    // connect to RabbitMQ and send the request
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);

    // check if the response is valid
    if (!is_array($response)) {
        echo json_encode([
            "success" => false,
            "message" => "Unable to create invite"
        ]);
        exit;
    }

    // return the response
    echo json_encode($response);

}
catch (Throwable $e) {

    // log the error
    error_log("Creating invite was unsuccessful: " . $e->getMessage());

    echo json_encode([
        "success" => false,
        "message" => "Internal server error"
    ]);
}
?>