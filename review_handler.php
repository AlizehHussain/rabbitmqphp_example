<?php

header("Content-Type: application/json");
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');
//read sent session token 
//file get contents reads raw data from body of request
//convert json into php array throug json decode
$data = json_decode(file_get_contents("php://input"), true);

//extracting from data
$eventID = $data['eventID'] ?? "";
$token = $data['sessionToken'] ?? "";
$review = trim($data['review'] ?? "");


//makes sure there is an eventID
if ($eventID === "" || $review === "") {
	echo json_encode(["success" => false,
	"message" => "Event and review required"]);
    exit;
}

// establishing communication to rabbitmq
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request['type'] = "save_review";
$request['token'] = $token;
$request['comment'] = $review;
$request['event_id'] = $eventID;

//send request to rabbitmq and wait for a reply from listner
$response = $client->send_request($request);

if (isset($response["success"]) && $response["success"] === true)
    {        
        //turning php array back into json to send to back
        echo json_encode(
            [
            "success" => true, 
	    "event" => $response["event"] ?? null
	    ]
            );
    }
else
    {
    
	    echo json_encode(["success" => false,
	    "message" => $response["message"] ?? "Could not save review"
	    ]);
}


?>
