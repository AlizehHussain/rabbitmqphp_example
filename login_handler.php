<?php
// read the input the login page sends
$data = json_decode(file_get_contents("php://input"), true);
// sends back a reply saying the login was sucessful and a token for it
//placeholder untill rabbitmq and database are connected
//echo json_encode(["success" => true, "token" => "abc123"]);

//extracting login id and pwd from the data array
$loginID = $data['loginID'];
$password = $data['password'];

require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

// establishing communication between new client and the server
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");


$request = array();
$request['type'] = "login";
$request['username'] = $loginID;
$request['password'] = $password;
//send request to rabbitmq and wait for a repky from listner
$response = $client->send_request($request);

// check if login was successful and includes a token
if (isset($response["success"]) && $response["success"] === true 
&& isset ($response['session_token']) )
    {
        //output login was successful and the token is sent to login page
        echo json_encode(["success" => true, "session_token" => $response["session_token"], "user_id" => $response["user_id"]]);
        
    }
else{
    //output login was unsuccesful so failure reply sent with no token
    echo json_encode(["success" => false]);
}

?>
