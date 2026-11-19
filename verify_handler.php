<?php
// this file is meant to send the code to the listener through RabbitMQ and returns the session token if it’s right
// Second step of the MFA is checks the code the user got by email.
// Works the same way as login_handler.php, but sends "verify_mfa" instead of "login".
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// read the JSON the verify page sends and turn it into a PHP array
$data = json_decode(file_get_contents("php://input"), true);

$userId = $data['user_id'] ?? "";
$code = trim($data['code'] ?? "");

// the code must be exactly 6 digits, so don't even send anything else
if ($userId === "" || !preg_match('/^[0-9]{6}$/', $code)) {
    echo json_encode(["success" => false, "message" => "Enter the 6-digit code"]);
    exit;
}

// sends the code to the database listener through RabbitMQ
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$request = array();
$request['type'] = "verify_mfa";
$request['user_id'] = $userId;
$request['code'] = $code;
$response = $client->send_request($request);

// if the code was right, the listener created a session and sent back the token
if (isset($response["success"]) && $response["success"] === true
&& isset($response["session_token"]))
{
    echo json_encode([
        "success" => true,
        "session_token" => $response["session_token"],
        "user_id" => $response["user_id"]
    ]);
}
else
{
    echo json_encode(["success" => false, "message" => $response["message"] ?? "Invalid or expired code"]);
}
?>
