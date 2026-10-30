<?php
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');
//read sent session token 
//file get contents reads raw data from body of request
//convert json into php array throug json decode
$data = json_decode(file_get_contents("php://input"), true);

//extracting session token from the data
$type = $data['check_eventID'] ?? "";
$eventID = $data['event_ID'] ?? "";

//makes sure there is an eventID
if ($eventID === "") {
    echo json_encode(["success" => false]);
    exit;
}

// establishing communication to rabbitmq
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request['type'] = $type;
$request['event_ID'] = $eventID;
//send request to rabbitmq and wait for a reply from listner
$response = $client->send_request($request);

if (isset($response["success"]) && $response["success"] === true)
    {        
        //turning php array back into json to send to back
        echo json_encode(
            [
            "success" => true, 
            ]
            );
        
    }
else
    {
    
    echo json_encode(["success" => false]);
}


?>
