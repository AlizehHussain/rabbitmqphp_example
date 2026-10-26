<?php
header ("Content-Type: application/json");
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

// read the input the hangout page sends
// file get contents reads raw data from body of  request
//&convert json into php array throug json decode
$data = json_decode(file_get_contents("php://input"), true);

//extracting token, event id and invitees from the data array
$session_token = $data['session_token'] ?? "";
$eventID = $data['event_id'] ?? "";
$usernames = $data['invitee_usernames'] ?? [];

if ($session_token === "" || $eventID === "" || empty($usernames)) {
    // dont let anyone create a hangout if the fields are empty!!!
    echo json_encode([
        "success" => false,
        "message" => "Missing event or username"
        ]);
    exit;
}

try {
    // establishing communication to rabbitmq
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

//create hangout request
$request = array();
$request['type'] = "create_hangout";
$request['session_token'] = $session_token;
$request['event_id'] = $eventID;
$request['invitee_usernames'] = $usernames;

//send request to rabbitmq and wait for a reply from listner
$response = $client->send_request($request);

if(!is_array($response)) {
    echo json_encode ([
        "success" => false,
        "message" => "Unknown error occured"
    ]);
}

echo json_encode([
        "success" => false,
        "message" => $response
        ]);
    
} catch (\Throwable $th) {
    error_log("Creation of hangout was unsuccessful");

    echo json_encode([
        "success" => false,
        "message" => $response
        ]);
}



?>
